<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogHelper;
use App\Services\BackupArchiveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class AdminBackupController extends Controller
{
    public function __construct(private BackupArchiveService $backupArchiveService)
    {
    }

    public function listBackups()
    {
        // Keep a single backup UI in the Settings page to avoid duplicate screens.
        return redirect()->route('admin.settings.index');
    }

    public function createBackup()
    {
        try {
            $backup = $this->backupArchiveService->createBackupArchive();
            $zipFileName = $backup['filename'];
            $zipAbsolutePath = $backup['absolute_path'];

            AuditLogHelper::log('CREATE', 'Backup', 0, "Backup created: {$zipFileName}");
            AuditLogHelper::log('VIEW', 'Backup', 0, "Backup downloaded: {$zipFileName}");

            return response()->download($zipAbsolutePath, $zipFileName);
        } catch (\Throwable $e) {
            return redirect()->route('admin.settings.index')->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function downloadBackup(string $file)
    {
        try {
            $safeFile = $this->sanitizeBackupFilename($file);
            $absolutePath = $this->backupDirectoryPath() . DIRECTORY_SEPARATOR . $safeFile;

            if (!File::exists($absolutePath)) {
                return redirect()->route('admin.settings.index')->with('error', 'Backup file not found.');
            }

            AuditLogHelper::log('VIEW', 'Backup', 0, "Backup downloaded: {$safeFile}");

            return response()->download($absolutePath);
        } catch (\Throwable $e) {
            return redirect()->route('admin.settings.index')->with('error', 'Download failed: ' . $e->getMessage());
        }
    }

    public function restoreBackup(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:zip|max:512000',
        ]);

        $uploadedRelativePath = null;
        $extractPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tms_restore_' . uniqid();

        try {
            $this->ensureBackupsDirectoryExists();

            $uploadedRelativePath = $request->file('backup_file')->storeAs(
                'backups/uploads',
                'restore_' . now()->format('Ymd_His') . '_' . uniqid() . '.zip',
                'local'
            );

            $uploadedAbsolutePath = Storage::disk('local')->path($uploadedRelativePath);
            File::ensureDirectoryExists($extractPath);

            $zip = new ZipArchive();
            if ($zip->open($uploadedAbsolutePath) !== true) {
                throw new \RuntimeException('Uploaded ZIP file cannot be opened.');
            }

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $entryName = $zip->getNameIndex($i);
                if ($entryName === false) {
                    throw new \RuntimeException('Invalid ZIP entry detected.');
                }

                if (str_contains($entryName, '..') || str_starts_with($entryName, '/') || preg_match('/^[A-Za-z]:\\\\/', $entryName)) {
                    throw new \RuntimeException('Invalid ZIP structure detected.');
                }
            }

            if (!$zip->extractTo($extractPath)) {
                $zip->close();
                throw new \RuntimeException('Failed to extract backup ZIP.');
            }
            $zip->close();

            $databaseSqlPath = $extractPath . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR . 'database.sql';
            if (!File::exists($databaseSqlPath)) {
                throw new \RuntimeException('database.sql not found inside backup package.');
            }

            $database = (string) env('DB_DATABASE');
            $username = (string) env('DB_USERNAME');
            $password = (string) env('DB_PASSWORD');
            $host = (string) env('DB_HOST', '127.0.0.1');
            $port = (string) env('DB_PORT', '3306');
            $mysqlBinary = $this->resolveBinaryPath('mysql', (string) env('MYSQL_BINARY', ''));

            $restoreCommand = sprintf(
                '%s --host=%s --port=%s --user=%s --password=%s %s < %s 2>&1',
                escapeshellarg($mysqlBinary),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($databaseSqlPath)
            );

            $this->runShellCommand($restoreCommand, 'Database restore failed.');

            $backupStoragePath = $extractPath . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR . 'storage';
            if (File::exists($backupStoragePath)) {
                if (File::exists(storage_path())) {
                    File::deleteDirectory(storage_path());
                }

                if (!File::copyDirectory($backupStoragePath, storage_path())) {
                    throw new \RuntimeException('Failed to restore storage folder.');
                }
            }

            $backupEnvPath = $extractPath . DIRECTORY_SEPARATOR . 'backup' . DIRECTORY_SEPARATOR . '.env';
            if (File::exists($backupEnvPath)) {
                File::copy($backupEnvPath, base_path('.env'));
            }

            AuditLogHelper::log('UPDATE', 'Backup', 0, 'Backup restored successfully');

            return redirect()->route('admin.settings.index')->with('status', 'Backup restored successfully.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.settings.index')->with('error', 'Restore failed: ' . $e->getMessage());
        } finally {
            if ($uploadedRelativePath && Storage::disk('local')->exists($uploadedRelativePath)) {
                Storage::disk('local')->delete($uploadedRelativePath);
            }

            if (File::exists($extractPath)) {
                File::deleteDirectory($extractPath);
            }
        }
    }

    public function deleteBackup(string $file)
    {
        try {
            $safeFile = $this->sanitizeBackupFilename($file);
            $absolutePath = $this->backupDirectoryPath() . DIRECTORY_SEPARATOR . $safeFile;

            if (!File::exists($absolutePath)) {
                return redirect()->route('admin.settings.index')->with('error', 'Backup file not found.');
            }

            File::delete($absolutePath);
            AuditLogHelper::log('DELETE', 'Backup', 0, "Backup deleted: {$safeFile}");

            return redirect()->route('admin.settings.index')->with('status', 'Backup deleted successfully.');
        } catch (\Throwable $e) {
            return redirect()->route('admin.settings.index')->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }

    private function ensureBackupsDirectoryExists(): void
    {
        File::ensureDirectoryExists($this->backupDirectoryPath());
    }

    private function backupDirectoryPath(): string
    {
        return storage_path('app/backups');
    }

    private function sanitizeBackupFilename(string $file): string
    {
        $decodedFile = urldecode(trim($file));
        $safeFile = basename($decodedFile);

        if (
            $safeFile !== $decodedFile
            || str_contains($safeFile, DIRECTORY_SEPARATOR)
            || str_contains($safeFile, '/')
            || !preg_match('/\.(zip|sql)$/i', $safeFile)
        ) {
            throw new \InvalidArgumentException('Invalid backup filename.');
        }

        return $safeFile;
    }

    private function runShellCommand(string $command, string $fallbackError): void
    {
        $output = [];
        $exitCode = 1;

        exec($command, $output, $exitCode);

        if ($exitCode !== 0) {
            $message = trim(implode(PHP_EOL, $output));
            throw new \RuntimeException($message !== '' ? $message : $fallbackError);
        }
    }

    private function addPathToZip(ZipArchive $zip, string $absolutePath, string $zipPath): void
    {
        if (is_dir($absolutePath)) {
            $zip->addEmptyDir($zipPath);

            $items = File::allFiles($absolutePath);
            foreach ($items as $item) {
                $itemPath = $item->getPathname();
                $relativePath = ltrim(str_replace($absolutePath, '', $itemPath), DIRECTORY_SEPARATOR);
                $zip->addFile($itemPath, $zipPath . '/' . str_replace('\\', '/', $relativePath));
            }

            $directories = File::directories($absolutePath);
            foreach ($directories as $directory) {
                $relativeDirectory = ltrim(str_replace($absolutePath, '', $directory), DIRECTORY_SEPARATOR);
                $zip->addEmptyDir($zipPath . '/' . str_replace('\\', '/', $relativeDirectory));
            }

            return;
        }

        $zip->addFile($absolutePath, $zipPath);
    }

    private function resolveBinaryPath(string $binaryName, string $configuredPath = ''): string
    {
        $configuredPath = trim($configuredPath);
        if ($configuredPath !== '') {
            return $configuredPath;
        }

        $commonWindowsPaths = [
            'C:\\xampp\\mysql\\bin\\' . $binaryName . '.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 8.0\\bin\\' . $binaryName . '.exe',
            'C:\\Program Files\\MySQL\\MySQL Server 5.7\\bin\\' . $binaryName . '.exe',
            'C:\\Program Files\\MariaDB 10.4\\bin\\' . $binaryName . '.exe',
        ];

        foreach ($commonWindowsPaths as $path) {
            if (File::exists($path)) {
                return $path;
            }
        }

        return $binaryName;
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $power = $bytes > 0 ? (int) floor(log($bytes, 1024)) : 0;
        $power = min($power, count($units) - 1);

        $value = $bytes / (1024 ** $power);

        return round($value, $precision) . ' ' . $units[$power];
    }
}
