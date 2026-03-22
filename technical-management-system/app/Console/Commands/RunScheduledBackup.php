<?php

namespace App\Console\Commands;

use App\Services\BackupArchiveService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RunScheduledBackup extends Command
{
    protected $signature = 'backup:run-scheduled {--force : Run backup even if not due} {--dry-run : Check schedule without creating backup}';

    protected $description = 'Run automatic backup if schedule settings say it is due';

    public function __construct(private BackupArchiveService $backupArchiveService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $settings = $this->loadScheduleSettings();
        if (!$settings['enabled'] && !$this->option('force')) {
            return self::SUCCESS;
        }

        $timezone = (string) config('app.timezone', 'UTC');
        $now = Carbon::now($timezone);

        if (!$this->option('force') && !$this->isDueNow($settings, $now)) {
            return self::SUCCESS;
        }

        $currentPeriodKey = $this->currentPeriodKey($settings['frequency'], $now);
        if (!$this->option('force') && ($settings['last_run_period_key'] ?? null) === $currentPeriodKey) {
            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->line('Scheduled backup is due now. Dry run only.');
            return self::SUCCESS;
        }

        try {
            $backup = $this->backupArchiveService->createBackupArchive();

            $settings['last_run_at'] = $now->toIso8601String();
            $settings['last_run_period_key'] = $currentPeriodKey;
            $settings['last_run_status'] = 'success';
            $settings['last_run_file'] = $backup['filename'];
            $settings['last_error'] = null;

            $this->saveScheduleSettings($settings);

            $message = 'Scheduled backup created: ' . $backup['filename'];
            $this->info($message);
            Log::info($message);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $settings['last_run_at'] = $now->toIso8601String();
            $settings['last_run_status'] = 'failed';
            $settings['last_error'] = $e->getMessage();

            $this->saveScheduleSettings($settings);

            $this->error('Scheduled backup failed: ' . $e->getMessage());
            Log::error('Scheduled backup failed', ['error' => $e->getMessage()]);

            return self::FAILURE;
        }
    }

    private function isDueNow(array $settings, Carbon $now): bool
    {
        if ($now->format('H:i') !== $settings['time']) {
            return false;
        }

        return match ($settings['frequency']) {
            'daily' => true,
            'weekly' => (int) $now->dayOfWeek === (int) $settings['day_of_week'],
            'monthly' => (int) $now->day === (int) $settings['day_of_month'],
            default => false,
        };
    }

    private function currentPeriodKey(string $frequency, Carbon $now): string
    {
        return match ($frequency) {
            'daily' => $now->format('Y-m-d'),
            'weekly' => $now->format('o-W'),
            'monthly' => $now->format('Y-m'),
            default => $now->format('Y-m-d-H-i'),
        };
    }

    private function loadScheduleSettings(): array
    {
        $defaults = [
            'enabled' => false,
            'frequency' => 'daily',
            'time' => '02:00',
            'day_of_week' => 0,
            'day_of_month' => 1,
            'last_run_at' => null,
            'last_run_period_key' => null,
            'last_run_status' => null,
            'last_run_file' => null,
            'last_error' => null,
        ];

        if (!Storage::disk('local')->exists('backup_schedule.json')) {
            return $defaults;
        }

        $decoded = json_decode((string) Storage::disk('local')->get('backup_schedule.json'), true);
        if (!is_array($decoded)) {
            return $defaults;
        }

        return array_merge($defaults, $decoded);
    }

    private function saveScheduleSettings(array $settings): void
    {
        Storage::disk('local')->put('backup_schedule.json', json_encode($settings, JSON_PRETTY_PRINT));
    }
}
