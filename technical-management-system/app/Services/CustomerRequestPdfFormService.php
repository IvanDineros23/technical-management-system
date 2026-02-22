<?php

namespace App\Services;

use App\Models\JobOrder;
use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Exception;

class CustomerRequestPdfFormService
{
    private array $config;
    private string $outputDir;
    private bool $pdftk_available = false;

    public function __construct()
    {
        $this->config = config('pdf-forms');
        $this->outputDir = storage_path('app/public/' . $this->config['output']['directory']);
        $this->checkPdftk();
    }

    /**
     * Check if pdftk is available
     */
    private function checkPdftk(): void
    {
        if (!$this->config['pdftk']['enabled']) {
            return;
        }

        try {
            $process = new Process([$this->config['pdftk']['command'], '--version']);
            $process->run();
            $this->pdftk_available = $process->isSuccessful();
        } catch (Exception $e) {
            $this->pdftk_available = false;
        }
    }

    /**
     * Generate filled PDF for a job order
     * 
     * @param JobOrder $jobOrder
     * @return string|null URL to the generated PDF or null if failed
     */
    public function export(JobOrder $jobOrder): ?string
    {
        if (!$this->config['enabled']) {
            throw new Exception('PDF form filling is disabled in configuration');
        }

        if (!$this->pdftk_available) {
            throw new Exception(
                'pdftk is not available. Install it using: apt-get install pdftk-java (Linux) or pdftk (Mac)'
            );
        }

        try {
            // Build field data from job order
            $fieldData = $this->buildFieldData($jobOrder);

            // Generate FDF
            $fdfContent = $this->buildFdf($fieldData);

            // Create temporary FDF file
            $tempFdfPath = tempnam(sys_get_temp_dir(), 'pdf_form_');
            file_put_contents($tempFdfPath, $fdfContent);

            // Generate filename
            $filename = "form-{$jobOrder->job_order_number}-" . now()->format('Y-m-d-His') . ".pdf";
            $outputPath = $this->outputDir . '/' . $filename;

            // Ensure directory exists
            if (!is_dir($this->outputDir)) {
                mkdir($this->outputDir, 0755, true);
            }

            // Fill PDF using pdftk
            $this->fillPdfWithPdftk($tempFdfPath, $outputPath);

            // Update job order with PDF path
            $jobOrder->update([
                'pdf_filename' => $filename,
                'pdf_path' => $outputPath,
            ]);

            // Cleanup temp FDF
            @unlink($tempFdfPath);

            return asset('storage/' . $this->config['output']['directory'] . '/' . $filename);
        } catch (Exception $e) {
            \Log::error('PDF Form Generation Failed: ' . $e->getMessage(), [
                'job_order_id' => $jobOrder->id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Build field data from job order
     * 
     * @param JobOrder $jobOrder
     * @return array
     */
    private function buildFieldData(JobOrder $jobOrder): array
    {
        $mapping = $this->config['field_mapping'];
        $data = [];

        // Customer Information
        if ($jobOrder->customer) {
            $customer = $jobOrder->customer;
            
            $data['Company Name'] = $customer->business_name ?: $customer->name;
            $data['Address 1'] = $customer->address ?? '';
            
            // Address line 2: City, State, Postal Code
            $address2Parts = array_filter([
                $customer->city,
                $customer->state,
                $customer->postal_code
            ]);
            $data['Address 2'] = implode(', ', $address2Parts);
            
            $data['Company TIN'] = $customer->tax_id ?? '';
            $data['Contact Person'] = $customer->contact_person ?: $jobOrder->requested_by;
            $data['Email Address'] = $customer->email ?? '';
            $data['Contact Number'] = $customer->phone ?? '';
        }

        // Request Date
        $data['Date'] = $jobOrder->request_date?->format('m/d/Y') ?: now()->format('m/d/Y');

        // Calibration Site Address
        if ($jobOrder->service_address) {
            $data['Calibration Site Address 1'] = $jobOrder->service_address;
            
            $siteAddress2Parts = array_filter([
                $jobOrder->city,
                $jobOrder->province,
                $jobOrder->postal_code
            ]);
            $data['Calibration Site Address 2'] = implode(', ', $siteAddress2Parts);
        }

        // Equipment rows from JobOrderItems (up to 8 rows)
        $items = $jobOrder->items()->with('equipment')->take(8)->get();
        
        foreach ($items as $index => $item) {
            $rowNum = $index + 1;
            
            $data["QtyRow{$rowNum}"] = $item->quantity ?? '';
            $data["Equipment NameRow{$rowNum}"] = $item->equipment?->name ?? $item->item_description;
            $data["ModelRow{$rowNum}"] = $item->equipment?->model ?? '';
            $data["Serial NoRow{$rowNum}"] = $item->equipment?->serial_number ?? '';
            $data["CapacityRow{$rowNum}"] = $item->equipment?->capacity ?? '';
        }

        // Notes/Remarks
        $data['REMARKS'] = $jobOrder->notes ?? $jobOrder->special_instructions ?? '';

        // Requested by signature
        if ($jobOrder->requested_by) {
            $data['Name and Signature'] = $jobOrder->requested_by;
        }

        return $data;
    }

    /**
     * Build FDF (Forms Data Format) string
     * 
     * @param array $fieldData
     * @return string
     */
    private function buildFdf(array $fieldData): string
    {
        $encoding = $this->config['fdf']['encoding'];
        
        $fdf = "%FDF-1.2\n";
        $fdf .= "1 0 obj\n";
        $fdf .= "<<\n";
        $fdf .= "/FDF << /Fields [\n";

        foreach ($fieldData as $fieldName => $fieldValue) {
            // Escape field values for FDF format
            $escapedValue = $this->escapeFdfValue($fieldValue);
            $fdf .= "<< /T ({$fieldName}) /V ({$escapedValue}) >>\n";
        }

        $fdf .= "] >>\n";
        $fdf .= ">>\n";
        $fdf .= "endobj\n";
        $fdf .= "trailer\n";
        $fdf .= "<< /Root 1 0 R >>\n";
        $fdf .= "%%EOF\n";

        return $fdf;
    }

    /**
     * Escape special characters in FDF values
     * 
     * @param string $value
     * @return string
     */
    private function escapeFdfValue(string $value): string
    {
        // Replace special characters
        $value = str_replace('\\', '\\\\', $value);  // Backslashes first
        $value = str_replace('(', '\\(', $value);     // Opening parenthesis
        $value = str_replace(')', '\\)', $value);     // Closing parenthesis
        $value = str_replace("\r", '\r', $value);     // Carriage return
        $value = str_replace("\n", '\n', $value);     // Newline
        
        return $value;
    }

    /**
     * Fill PDF using pdftk
     * 
     * @param string $fdfPath Path to FDF file
     * @param string $outputPath Path for output PDF
     */
    private function fillPdfWithPdftk(string $fdfPath, string $outputPath): void
    {
        $templatePath = $this->config['template_path'];

        if (!file_exists($templatePath)) {
            throw new Exception("Template PDF not found: {$templatePath}");
        }

        // Build pdftk command
        $command = [
            $this->config['pdftk']['command'],
            $templatePath,
            'fill_form',
            $fdfPath,
            'output',
            $outputPath,
        ];

        // Add flatten flag if enabled
        if ($this->config['pdftk']['flatten']) {
            $command[] = 'flatten';
        }

        // Execute pdftk
        $process = new Process($command);
        $process->setTimeout(30);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        if (!file_exists($outputPath)) {
            throw new Exception('PDF generation failed: output file was not created');
        }
    }

    /**
     * Dump PDF form field names (for debugging)
     * 
     * @return array
     */
    public function dumpFieldNames(): array
    {
        $templatePath = $this->config['template_path'];

        if (!file_exists($templatePath)) {
            throw new Exception("Template PDF not found: {$templatePath}");
        }

        if (!$this->pdftk_available) {
            throw new Exception('pdftk is not available');
        }

        // Run pdftk dump_data_fields
        $process = new Process([
            $this->config['pdftk']['command'],
            $templatePath,
            'dump_data_fields',
        ]);

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        // Parse output
        $output = $process->getOutput();
        $fields = [];

        foreach (explode("\n", $output) as $line) {
            if (preg_match('/^FieldName: (.+)$/', trim($line), $matches)) {
                $fields[] = $matches[1];
            }
        }

        return $fields;
    }

    /**
     * Check if pdftk is available
     * 
     * @return bool
     */
    public function isPdftkAvailable(): bool
    {
        return $this->pdftk_available;
    }
}
