<?php

namespace App\Console\Commands;

use App\Services\JobOrderPdfService;
use Illuminate\Console\Command;

class DumpPdfFields extends Command
{
    protected $signature = 'pdf:dump-fields';
    protected $description = 'Dump all form field names from the PDF template';

    public function handle()
    {
        $this->info('=== PDF Form Fields ===');
        $this->newLine();

        try {
            $service = app(JobOrderPdfService::class);
            $fields = $service->dumpFields();

            if (empty($fields)) {
                $this->warn('⚠ No form fields found');
                return 1;
            }

            $this->info("✓ Found " . count($fields) . " form fields:");
            $this->newLine();

            foreach ($fields as $index => $field) {
                $this->line(($index + 1) . ". {$field}");
            }

            $this->newLine();
            $this->info('Use these field names in buildFormData() method');

            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
