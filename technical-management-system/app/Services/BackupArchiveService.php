<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use ZipArchive;

class BackupArchiveService
{
    public function createBackupArchive(): array
    {
        $this->ensureBackupsDirectoryExists();

        $timestamp = now()->format('Y_m_d_H_i_s');
        $zipFileName = "backup_{$timestamp}.zip";

        // Build backup payload outside storage to avoid recursive self-copy.
        $tempRoot = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tms_backup_' . uniqid();
        $tempBackupRoot = $tempRoot . DIRECTORY_SEPARATOR . 'backup';
        $databaseDumpPath = $tempBackupRoot . DIRECTORY_SEPARATOR . 'database.sql';
        $zipAbsolutePath = storage_path('app/backups/' . $zipFileName);

        try {
            File::ensureDirectoryExists($tempBackupRoot);

            $database = (string) env('DB_DATABASE');
            $username = (string) env('DB_USERNAME');
            $password = (string) env('DB_PASSWORD');
            $host = (string) env('DB_HOST', '127.0.0.1');
            $port = (string) env('DB_PORT', '3306');
            $mysqldumpBinary = $this->resolveBinaryPath('mysqldump', (string) env('MYSQLDUMP_BINARY', ''));

            $dumpCommand = sprintf(
                '%s --host=%s --port=%s --user=%s --password=%s --add-drop-table --single-transaction --quick --lock-tables=false %s > %s 2>&1',
                escapeshellarg($mysqldumpBinary),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($databaseDumpPath)
            );

            $this->runShellCommand($dumpCommand, 'Database dump failed.');

            $storageSource = storage_path();
            $storageDestination = $tempBackupRoot . DIRECTORY_SEPARATOR . 'storage';
            if (!File::copyDirectory($storageSource, $storageDestination)) {
                throw new \RuntimeException('Failed to include storage directory in backup.');
            }

            $envPath = base_path('.env');
            if (File::exists($envPath)) {
                File::copy($envPath, $tempBackupRoot . DIRECTORY_SEPARATOR . '.env');
            }

            $zip = new ZipArchive();
            if ($zip->open($zipAbsolutePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new \RuntimeException('Unable to create backup ZIP file.');
            }

            $this->addPathToZip($zip, $tempBackupRoot, 'backup');
            $zip->close();

            return [
                'filename' => $zipFileName,
                'absolute_path' => $zipAbsolutePath,
            ];
        } finally {
            if (File::exists($tempRoot)) {
                File::deleteDirectory($tempRoot);
            }
        }
    }

    private function ensureBackupsDirectoryExists(): void
    {
        File::ensureDirectoryExists(storage_path('app/backups'));
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
}
