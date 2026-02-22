<?php

namespace App\Console\Commands;

use App\Services\CustomerRequestPdfFormService;
use Illuminate\Console\Command;

class DumpPdfFieldsCommand extends Command
{
    protected $signature = 'pdf:dump-fields';
    protected $description = 'Dump all form field names from the PDF template (for configuration mapping)';

    public function handle()
    {
        $this->info('=== PDF Form Fields ===');
        $this->newLine();

        try {
            $service = app(CustomerRequestPdfFormService::class);

            if (!$service->isPdftkAvailable()) {
                $this->error('❌ pdftk is not available on this system');
                $this->line('Install it using:');
                $this->line('  Linux: apt-get install pdftk-java');
                $this->line('  Mac: brew install pdftk-java');
                $this->line('  Windows: Download from https://www.pdflabs.com/tools/pdftk-the-pdf-toolkit/');
                return 1;
            }

            $fields = $service->dumpFieldNames();

            if (empty($fields)) {
                $this->warn('⚠ No form fields found in the PDF template.');
                $this->line('The template may not have fillable form fields (AcroForm).');
                return 1;
            }

            $this->info("✓ Found " . count($fields) . " form fields:");
            $this->newLine();

            foreach ($fields as $index => $field) {
                $this->line(($index + 1) . ". {$field}");
            }

            $this->newLine();
            $this->info('Update config/pdf-forms.php field_mapping with these names:');
            $this->line("\tconfig/pdf-forms.php -> 'field_mapping' => [");
            $this->line("\t    'your_model_field' => '" . ($fields[0] ?? 'FieldName') . "',");
            $this->line("\t    // ... etc");
            $this->line("\t]");

            return 0;
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return 1;
        }
    }
}
