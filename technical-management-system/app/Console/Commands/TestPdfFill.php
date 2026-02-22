<?php

namespace App\Console\Commands;

use App\Models\JobOrder;
use App\Services\CustomerRequestPdfFormService;
use Illuminate\Console\Command;

class TestPdfFill extends Command
{
    protected $signature = 'pdf:test-fill {job_order_id?}';
    protected $description = 'Test PDF form filling with a specific job order or the latest one';

    public function handle()
    {
        $jobOrderId = $this->argument('job_order_id');

        if ($jobOrderId) {
            $jobOrder = JobOrder::with(['customer', 'items.equipment'])->find($jobOrderId);
            
            if (!$jobOrder) {
                $this->error("❌ Job Order #{$jobOrderId} not found");
                return 1;
            }
        } else {
            $jobOrder = JobOrder::with(['customer', 'items.equipment'])->latest()->first();
            
            if (!$jobOrder) {
                $this->error("❌ No job orders found in database");
                $this->line('Create a job order first or specify a job order ID');
                return 1;
            }
        }

        $this->info("Testing PDF generation for Job Order: {$jobOrder->job_order_number}");
        $this->line("Customer: " . ($jobOrder->customer->business_name ?? $jobOrder->customer->name ?? 'N/A'));
        $this->newLine();

        try {
            $service = app(CustomerRequestPdfFormService::class);
            
            $this->info('Generating PDF...');
            $url = $service->export($jobOrder);

            $this->info('✅ PDF generated successfully!');
            $this->line('URL: ' . $url);
            $this->line('File: ' . $jobOrder->pdf_path);
            
            return 0;
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            if ($this->output->isVerbose()) {
                $this->line($e->getTraceAsString());
            }
            return 1;
        }
    }
}
