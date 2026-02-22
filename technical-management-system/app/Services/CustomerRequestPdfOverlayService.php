<?php

namespace App\Services;

use App\Models\JobOrder;
use setasign\Fpdi\Fpdi;
use Exception;

class CustomerRequestPdfOverlayService
{
    private array $config;
    private string $outputDir;

    // Coordinate mappings (X, Y positions on the PDF in mm from top-left)
    // These are approximate - adjust based on actual PDF layout
    private array $fieldCoordinates = [
        'company_name' => ['x' => 60, 'y' => 58],
        'address1' => ['x' => 60, 'y' => 65],
        'address2' => ['x' => 60, 'y' => 72],
        'company_tin' => ['x' => 60, 'y' => 79],
        'contact_person' => ['x' => 60, 'y' => 86],
        'contact_number' => ['x' => 60, 'y' => 93],
        'email_address' => ['x' => 150, 'y' => 86],
        'date' => ['x' => 180, 'y' => 93],
        'calibration_site_address1' => ['x' => 60, 'y' => 100],
        'calibration_site_address2' => ['x' => 60, 'y' => 107],
    ];

    public function __construct()
    {
        $this->config = config('pdf-forms');
        $this->outputDir = storage_path('app/public/' . $this->config['output']['directory']);
    }

    /**
     * Generate filled PDF for a job order using coordinate-based overlay
     * 
     * @param JobOrder $jobOrder
     * @return string|null URL to the generated PDF
     */
    public function export(JobOrder $jobOrder): ?string
    {
        if (!$this->config['enabled']) {
            throw new Exception('PDF form filling is disabled in configuration');
        }

        try {
            $templatePath = $this->config['template_path'];
            
            if (!file_exists($templatePath)) {
                throw new Exception("Template PDF not found: {$templatePath}");
            }

            // Create PDF instance
            $pdf = new Fpdi();
            $pdf->AddPage();
            
            // Import the template PDF (page 1)
            $pdf->setSourceFile($templatePath);
            $tplIdx = $pdf->importPage(1);
            $pdf->useTemplate($tplIdx);

            // Set font for text overlay
            $pdf->SetFont('Arial', '', 10);
            $pdf->SetTextColor(0, 0, 0);

            // Overlay customer data
            $this->overlayText($pdf, 'company_name', $jobOrder->customer_company_name ?? '');
            $this->overlayText($pdf, 'address1', $this->getAddress1($jobOrder));
            $this->overlayText($pdf, 'address2', $this->getAddress2($jobOrder));
            $this->overlayText($pdf, 'company_tin', $jobOrder->customer_tin ?? '');
            $this->overlayText($pdf, 'contact_person', $jobOrder->customer_contact_person ?? '');
            $this->overlayText($pdf, 'contact_number', $jobOrder->customer_contact_number ?? '');
            $this->overlayText($pdf, 'email_address', $jobOrder->customer_email ?? '');
            $this->overlayText($pdf, 'date', now()->format('Y-m-d'));
            $this->overlayText($pdf, 'calibration_site_address1', $jobOrder->calibration_site_address ?? '');

            // Add page 2 if template has it
            $pageCount = $pdf->setSourceFile($templatePath);
            if ($pageCount >= 2) {
                $pdf->AddPage();
                $tplIdx2 = $pdf->importPage(2);
                $pdf->useTemplate($tplIdx2);
            }

            // Generate filename
            $filename = "form-{$jobOrder->job_order_number}-" . now()->format('Y-m-d-His') . ".pdf";
            $outputPath = $this->outputDir . '/' . $filename;

            // Ensure directory exists
            if (!is_dir($this->outputDir)) {
                mkdir($this->outputDir, 0755, true);
            }

            // Save PDF
            $pdf->Output('F', $outputPath);

            // Update job order with PDF path
            $jobOrder->update([
                'pdf_filename' => $filename,
                'pdf_path' => $outputPath,
            ]);

            return asset('storage/' . $this->config['output']['directory'] . '/' . $filename);
        } catch (Exception $e) {
            \Log::error('PDF Overlay Generation Failed: ' . $e->getMessage(), [
                'job_order_id' => $jobOrder->id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Overlay text at specified coordinates
     */
    private function overlayText(Fpdi $pdf, string $field, string $text): void
    {
        if (!isset($this->fieldCoordinates[$field]) || empty($text)) {
            return;
        }

        $coords = $this->fieldCoordinates[$field];
        $pdf->SetXY($coords['x'], $coords['y']);
        $pdf->Cell(0, 5, $text, 0, 0, 'L');
    }

    /**
     * Get address line 1
     */
    private function getAddress1(JobOrder $jobOrder): string
    {
        $parts = [];
        if ($jobOrder->customer_street) $parts[] = $jobOrder->customer_street;
        if ($jobOrder->customer_barangay) $parts[] = $jobOrder->customer_barangay;
        return implode(', ', $parts);
    }

    /**
     * Get address line 2
     */
    private function getAddress2(JobOrder $jobOrder): string
    {
        $parts = [];
        if ($jobOrder->customer_city) $parts[] = $jobOrder->customer_city;
        if ($jobOrder->customer_province) $parts[] = $jobOrder->customer_province;
        if ($jobOrder->customer_zip_code) $parts[] = $jobOrder->customer_zip_code;
        return implode(', ', $parts);
    }

    /**
     * Test method to show where text will be placed
     */
    public function exportTestOverlay(): string
    {
        $templatePath = $this->config['template_path'];
        
        if (!file_exists($templatePath)) {
            throw new Exception("Template PDF not found: {$templatePath}");
        }

        $pdf = new Fpdi();
        $pdf->AddPage();
        $pdf->setSourceFile($templatePath);
        $tplIdx = $pdf->importPage(1);
        $pdf->useTemplate($tplIdx);

        // Draw red dots at each coordinate to help with positioning
        $pdf->SetTextColor(255, 0, 0);
        $pdf->SetFont('Arial', 'B', 8);
        
        foreach ($this->fieldCoordinates as $fieldName => $coords) {
            $pdf->SetXY($coords['x'], $coords['y']);
            $pdf->Cell(0, 5, "[$fieldName]", 0, 0, 'L');
        }

        $filename = "test-overlay-" . now()->format('Y-m-d-His') . ".pdf";
        $outputPath = $this->outputDir . '/' . $filename;

        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }

        $pdf->Output('F', $outputPath);

        return asset('storage/' . $this->config['output']['directory'] . '/' . $filename);
    }
}
