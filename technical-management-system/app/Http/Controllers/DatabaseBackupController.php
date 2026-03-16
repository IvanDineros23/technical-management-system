<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class DatabaseBackupController extends Controller
{
    /**
     * Display the backup management page with available backup files.
     */
    public function index()
    {
        $backupDirectory = storage_path('app/backups');
        $backups = [];

        try {
            $this->ensureBackupDirectoryExists($backupDirectory);

            $files = File::files($backupDirectory);

            foreach ($files as $file) {
                if (strtolower($file->getExtension()) !== 'sql') {
                    continue;
                }

                $backups[] = [
                    'filename' => $file->getFilename(),
                    'size' => $this->formatBytes($file->getSize()),
                    'created_at' => date('Y-m-d H:i:s', $file->getMTime()),
                    'timestamp' => $file->getMTime(),
                ];
            }

            usort($backups, static function (array $a, array $b) {
                return $b['timestamp'] <=> $a['timestamp'];
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Unable to load backup files: ' . $e->getMessage());
        }

        return view('admin.settings', compact('backups'));
    }

    /**
     * Create a database backup using mysqldump.
     */
    public function createBackup()
    {
        $backupDirectory = storage_path('app/backups');

        try {
            $this->ensureBackupDirectoryExists($backupDirectory);

            $filename = 'backup_' . date('Y_m_d_H_i_s') . '.sql';
            $backupPath = $backupDirectory . DIRECTORY_SEPARATOR . $filename;

            $database = (string) env('DB_DATABASE');
            $username = (string) env('DB_USERNAME');
            $password = (string) env('DB_PASSWORD');
            $host = (string) env('DB_HOST', '127.0.0.1');
            $port = (string) env('DB_PORT', '3306');
            $mysqldumpBinary = $this->resolveBinaryPath('mysqldump', (string) env('MYSQLDUMP_BINARY', ''));

            $command = sprintf(
                '%s --host=%s --port=%s --user=%s --password=%s %s > %s 2>&1',
                escapeshellarg($mysqldumpBinary),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($backupPath)
            );

            exec($command, $output, $exitCode);

            if ($exitCode !== 0 || !File::exists($backupPath)) {
                $errorOutput = trim(implode(PHP_EOL, $output));
                throw new \RuntimeException($errorOutput !== '' ? $errorOutput : 'mysqldump command failed.');
            }

            return back()->with('success', 'Database backup created successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    /**
     * Download a backup file by filename.
     */
    public function downloadBackup(string $filename)
    {
        try {
            $backupPath = $this->resolveBackupPath($filename);

            if (!File::exists($backupPath)) {
                return back()->with('error', 'Backup file not found.');
            }

            return response()->download($backupPath);
        } catch (\Throwable $e) {
            return back()->with('error', 'Download failed: ' . $e->getMessage());
        }
    }

    /**
     * Delete a backup file by filename.
     */
    public function deleteBackup(string $filename)
    {
        try {
            $backupPath = $this->resolveBackupPath($filename);

            if (!File::exists($backupPath)) {
                return back()->with('error', 'Backup file not found.');
            }

            File::delete($backupPath);

            return back()->with('success', 'Backup deleted successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }

    /**
     * Restore a database backup by uploading and importing a .sql file.
     */
    public function restoreBackup(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql',
        ]);

        $tempDirectory = storage_path('app/backups/temp');

        try {
            $this->ensureBackupDirectoryExists($tempDirectory);

            $uploadedFile = $request->file('backup_file');
            $tempFilename = 'restore_' . date('Y_m_d_H_i_s') . '_' . uniqid() . '.sql';
            $uploadedFile->move($tempDirectory, $tempFilename);

            $tempPath = $tempDirectory . DIRECTORY_SEPARATOR . $tempFilename;

            $database = (string) env('DB_DATABASE');
            $username = (string) env('DB_USERNAME');
            $password = (string) env('DB_PASSWORD');
            $host = (string) env('DB_HOST', '127.0.0.1');
            $port = (string) env('DB_PORT', '3306');
            $mysqlBinary = $this->resolveBinaryPath('mysql', (string) env('MYSQL_BINARY', ''));

            $command = sprintf(
                '%s --host=%s --port=%s --user=%s --password=%s %s < %s 2>&1',
                escapeshellarg($mysqlBinary),
                escapeshellarg($host),
                escapeshellarg($port),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($tempPath)
            );

            exec($command, $output, $exitCode);

            File::delete($tempPath);

            if ($exitCode !== 0) {
                $errorOutput = trim(implode(PHP_EOL, $output));
                throw new \RuntimeException($errorOutput !== '' ? $errorOutput : 'mysql restore command failed.');
            }

            return back()->with('success', 'Database restored successfully.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }

    /**
     * Ensure a directory exists before writing backups.
     */
    private function ensureBackupDirectoryExists(string $directory): void
    {
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }
    }

    /**
     * Resolve and validate backup file path to prevent directory traversal.
     */
    private function resolveBackupPath(string $filename): string
    {
        $safeFilename = basename($filename);

        if ($safeFilename !== $filename || pathinfo($safeFilename, PATHINFO_EXTENSION) !== 'sql') {
            throw new \InvalidArgumentException('Invalid backup filename.');
        }

        $backupDirectory = storage_path('app/backups');
        $this->ensureBackupDirectoryExists($backupDirectory);

        $resolvedDirectory = realpath($backupDirectory);
        if ($resolvedDirectory === false) {
            throw new \RuntimeException('Backup directory not found.');
        }

        $candidatePath = $resolvedDirectory . DIRECTORY_SEPARATOR . $safeFilename;
        $resolvedCandidate = realpath($candidatePath);

        if ($resolvedCandidate !== false && strncmp($resolvedCandidate, $resolvedDirectory, strlen($resolvedDirectory)) !== 0) {
            throw new \InvalidArgumentException('Invalid backup path.');
        }

        return $candidatePath;
    }

    /**
     * Convert bytes to a human-readable value.
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $power = $bytes > 0 ? (int) floor(log($bytes, 1024)) : 0;
        $power = min($power, count($units) - 1);

        $value = $bytes / (1024 ** $power);

        return round($value, $precision) . ' ' . $units[$power];
    }

    /**
     * Resolve binary path from env value, common Windows install paths, or PATH fallback.
     */
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
