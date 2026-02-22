<?php

namespace App\Console\Commands;

use App\Services\CustomerRequestPdfOverlayService;
use Illuminate\Console\Command;

class TestPdfOverlay extends Command
{
    protected $signature = 'pdf:test-overlay';
    protected $description = 'Generate a test PDF with coordinate markers to help position text fields';

    public function handle()
    {
        $this->info('Generating test PDF overlay...');

        try {
            $service = app(CustomerRequestPdfOverlayService::class);
            $url = $service->exportTestOverlay();

            $this->info('✅ Test PDF generated successfully!');
            $this->line('URL: ' . $url);
            $this->newLine();
            $this->line('Open the PDF to see where each field will be positioned.');
            $this->line('Adjust coordinates in: app/Services/CustomerRequestPdfOverlayService.php');

            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
}
