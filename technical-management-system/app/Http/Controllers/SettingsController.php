<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function index()
    {
        $backups = $this->getBackupFiles();
        return view('admin.settings', compact('backups'));
    }

    public function updateGeneral(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'system_email' => 'required|email',
            'contact_phone' => 'required|string|max:50',
            'business_address' => 'required|string',
            'timezone' => 'required|string',
            'date_format' => 'required|string',
        ]);

        // Store in config or database as needed
        // For now, we'll store in a settings file or cache
        $settings = [
            'company_name' => $request->company_name,
            'system_email' => $request->system_email,
            'contact_phone' => $request->contact_phone,
            'business_address' => $request->business_address,
            'timezone' => $request->timezone,
            'date_format' => $request->date_format,
        ];

        Storage::put('settings.json', json_encode($settings));

        AuditLogHelper::log('UPDATE', 'Settings', 0, 'Updated general settings');

        return redirect()->back()->with('success', 'General settings updated successfully');
    }

    public function createBackup()
    {
        try {
            $filename = 'backup_' . date('Y_m_d_H_i') . '.sql';
            $path = storage_path('app/backups/' . $filename);

            // Create backups directory if it doesn't exist
            if (!File::exists(storage_path('app/backups'))) {
                File::makeDirectory(storage_path('app/backups'), 0755, true);
            }

            // Get database credentials
            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');

            // Create mysqldump command
            $command = sprintf(
                'mysqldump -h%s -u%s -p%s %s > %s',
                escapeshellarg($host),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($path)
            );

            exec($command, $output, $return);

            if ($return === 0) {
                AuditLogHelper::log('CREATE', 'Backup', 0, "Created database backup: {$filename}");
                return redirect()->back()->with('success', 'Database backup created successfully');
            } else {
                return redirect()->back()->with('error', 'Failed to create backup');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Backup failed: ' . $e->getMessage());
        }
    }

    public function downloadBackup($filename)
    {
        $path = storage_path('app/backups/' . $filename);

        if (!File::exists($path)) {
            return redirect()->back()->with('error', 'Backup file not found');
        }

        AuditLogHelper::log('VIEW', 'Backup', 0, "Downloaded backup: {$filename}");

        return response()->download($path);
    }

    public function deleteBackup($filename)
    {
        $path = storage_path('app/backups/' . $filename);

        if (File::exists($path)) {
            File::delete($path);
            AuditLogHelper::log('DELETE', 'Backup', 0, "Deleted backup: {$filename}");
            return redirect()->back()->with('success', 'Backup deleted successfully');
        }

        return redirect()->back()->with('error', 'Backup file not found');
    }

    public function restoreBackup(Request $request)
    {
        $request->validate([
            'backup_file' => 'required|file|mimes:sql,zip',
        ]);

        try {
            $file = $request->file('backup_file');
            $path = $file->getRealPath();

            $database = config('database.connections.mysql.database');
            $username = config('database.connections.mysql.username');
            $password = config('database.connections.mysql.password');
            $host = config('database.connections.mysql.host');

            $command = sprintf(
                'mysql -h%s -u%s -p%s %s < %s',
                escapeshellarg($host),
                escapeshellarg($username),
                escapeshellarg($password),
                escapeshellarg($database),
                escapeshellarg($path)
            );

            exec($command, $output, $return);

            if ($return === 0) {
                AuditLogHelper::log('UPDATE', 'Database', 0, 'Restored database from backup');
                return redirect()->back()->with('success', 'Database restored successfully');
            } else {
                return redirect()->back()->with('error', 'Failed to restore database');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Restore failed: ' . $e->getMessage());
        }
    }

    public function updateSecurity(Request $request)
    {
        $request->validate([
            'session_timeout' => 'required|integer|min:5|max:1440',
            'force_password_change' => 'boolean',
            'two_factor_auth' => 'boolean',
        ]);

        $settings = [
            'session_timeout' => $request->session_timeout,
            'force_password_change' => $request->has('force_password_change'),
            'two_factor_auth' => $request->has('two_factor_auth'),
        ];

        Storage::put('security_settings.json', json_encode($settings));

        AuditLogHelper::log('UPDATE', 'Settings', 0, 'Updated security settings');

        return redirect()->back()->with('success', 'Security settings updated successfully');
    }

    public function clearCache()
    {
        try {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');

            AuditLogHelper::log('DELETE', 'Cache', 0, 'Cleared system cache');

            return redirect()->back()->with('success', 'Cache cleared successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    public function optimizeDatabase()
    {
        try {
            $tables = DB::select('SHOW TABLES');
            $database = config('database.connections.mysql.database');

            foreach ($tables as $table) {
                $tableName = $table->{'Tables_in_' . $database};
                DB::statement("OPTIMIZE TABLE `{$tableName}`");
            }

            AuditLogHelper::log('UPDATE', 'Database', 0, 'Optimized database tables');

            return redirect()->back()->with('success', 'Database optimized successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to optimize database: ' . $e->getMessage());
        }
    }

    public function clearLogs()
    {
        try {
            // Clear Laravel log files older than 30 days
            $logPath = storage_path('logs');
            $files = File::files($logPath);
            $thirtyDaysAgo = now()->subDays(30);

            foreach ($files as $file) {
                if (File::lastModified($file) < $thirtyDaysAgo->timestamp) {
                    File::delete($file);
                }
            }

            // Optionally clear old audit logs
            DB::table('audit_logs')->where('created_at', '<', $thirtyDaysAgo)->delete();

            AuditLogHelper::log('DELETE', 'Logs', 0, 'Cleared old system logs (older than 30 days)');

            return redirect()->back()->with('success', 'Old logs cleared successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to clear logs: ' . $e->getMessage());
        }
    }

    public function terminateAllSessions()
    {
        try {
            DB::table('sessions')->where('user_id', '!=', auth()->id())->delete();

            AuditLogHelper::log('DELETE', 'Sessions', 0, 'Terminated all other sessions');

            return redirect()->back()->with('success', 'All other sessions terminated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to terminate sessions: ' . $e->getMessage());
        }
    }

    private function getBackupFiles()
    {
        $backupPath = storage_path('app/backups');

        if (!File::exists($backupPath)) {
            return [];
        }

        $files = File::files($backupPath);
        $backups = [];

        foreach ($files as $file) {
            $backups[] = [
                'filename' => $file->getFilename(),
                'size' => $this->formatBytes($file->getSize()),
                'date' => date('M d, Y h:i A', $file->getMTime()),
                'timestamp' => $file->getMTime(),
            ];
        }

        // Sort by timestamp descending (newest first)
        usort($backups, function ($a, $b) {
            return $b['timestamp'] - $a['timestamp'];
        });

        return $backups;
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
