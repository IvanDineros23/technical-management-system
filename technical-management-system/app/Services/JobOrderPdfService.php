<?php

namespace App\Services;

use App\Models\JobOrder;
use Symfony\Component\Process\Process;
use Exception;

class JobOrderPdfService
{
    private string $templatePath;
    private string $outputDir;

    public function __construct()
    {
        $this->templatePath = storage_path('app/templates/GEI-MAR-F-3 Customer Request Form Rev 2.pdf');
        $this->outputDir = storage_path('app/public/generated');
    }

    /**
     * Generate PDF by filling form fields using pdftk
     */
    public function generate(JobOrder $jobOrder): string
    {
        try {
            // Build form data
            $formData = $this->buildFormData($jobOrder);
            
            // Create FDF file
            $fdfPath = $this->createFdf($formData);
            
            // Generate output filename
            $filename = "job-order-{$jobOrder->job_order_number}-" . now()->format('Y-m-d-His') . ".pdf";
            $outputPath = $this->outputDir . '/' . $filename;
            
            // Ensure directory exists
            if (!is_dir($this->outputDir)) {
                mkdir($this->outputDir, 0755, true);
            }
            
            // Fill PDF using pdftk
            $this->fillPdfWithPdftk($fdfPath, $outputPath);
            
            // Clean up temp FDF
            @unlink($fdfPath);
            
            // Update job order
            $jobOrder->update([
                'pdf_filename' => $filename,
                'pdf_path' => $outputPath,
            ]);
            
            return asset('storage/generated/' . $filename);
            
        } catch (Exception $e) {
            \Log::error('PDF Generation Failed: ' . $e->getMessage(), [
                'job_order_id' => $jobOrder->id,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Build form field data from job order
     */
    private function buildFormData(JobOrder $jobOrder): array
    {
        $data = [];
        
        // Map JobOrder data to PDF form fields
        // Field names will be discovered using: pdftk template.pdf dump_data_fields
        
        if ($jobOrder->customer) {
            $data['Company Name'] = $jobOrder->customer->name ?? '';
            $data['Address'] = $jobOrder->service_address ?? '';
        }
        
        $data['Contact Person'] = $jobOrder->requested_by ?? '';
        $data['Date'] = $jobOrder->request_date ? $jobOrder->request_date->format('m/d/Y') : '';
        
        if ($jobOrder->customer && $jobOrder->customer->contacts->first()) {
            $contact = $jobOrder->customer->contacts->first();
            $data['Contact Number'] = $contact->phone ?? '';
            $data['Email Address'] = $contact->email ?? '';
        }
        
        // Service type checkbox - need to map to exact export value
        $data['Calibration'] = ($jobOrder->service_type === 'Calibration') ? 'Yes' : 'Off';
        $data['Repair'] = ($jobOrder->service_type === 'Repair') ? 'Yes' : 'Off';
        $data['Installation'] = ($jobOrder->service_type === 'Installation') ? 'Yes' : 'Off';
        $data['Maintenance'] = ($jobOrder->service_type === 'Maintenance') ? 'Yes' : 'Off';
        
        return $data;
    }

    /**
     * Create FDF file for form data
     */
    private function createFdf(array $data): string
    {
        $fdfPath = sys_get_temp_dir() . '/form_' . uniqid() . '.fdf';
        
        $fdf = "%FDF-1.2\n";
        $fdf .= "1 0 obj\n";
        $fdf .= "<<\n";
        $fdf .= "/FDF << /Fields [\n";
        
        foreach ($data as $fieldName => $fieldValue) {
            $escapedName = $this->escapeFdfValue($fieldName);
            $escapedValue = $this->escapeFdfValue($fieldValue);
            $fdf .= "<< /T ({$escapedName}) /V ({$escapedValue}) >>\n";
        }
        
        $fdf .= "] >>\n";
        $fdf .= ">>\n";
        $fdf .= "endobj\n";
        $fdf .= "trailer\n";
        $fdf .= "<< /Root 1 0 R >>\n";
        $fdf .= "%%EOF\n";
        
        file_put_contents($fdfPath, $fdf);
        
        return $fdfPath;
    }

    /**
     * Escape special characters for FDF format
     */
    private function escapeFdfValue(string $value): string
    {
        $value = str_replace('\\', '\\\\', $value);
        $value = str_replace('(', '\\(', $value);
        $value = str_replace(')', '\\)', $value);
        $value = str_replace("\r", '\r', $value);
        $value = str_replace("\n", '\n', $value);
        return $value;
    }

    /**
     * Find pdftk executable on the system
     */
    private function findPdftk(): string
    {
        // For Windows, check common installation paths
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $possiblePaths = [
                'C:\\Program Files (x86)\\PDFtk Server\\bin\\pdftk.exe',
                'C:\\Program Files\\PDFtk Server\\bin\\pdftk.exe',
                'C:\\Program Files (x86)\\PDFtk\\bin\\pdftk.exe',
                'C:\\Program Files\\PDFtk\\bin\\pdftk.exe',
                'C:\\pdftk\\bin\\pdftk.exe',
            ];
            
            foreach ($possiblePaths as $path) {
                if (file_exists($path)) {
                    return $path;
                }
            }
            
            // If not found in common paths, try from PATH
            $testProcess = new Process(['pdftk', '--version']);
            $testProcess->run();
            if ($testProcess->isSuccessful()) {
                return 'pdftk';
            }
            
            // Not found
            throw new \Exception(
                "pdftk is not available on this system.\n\n" .
                "Checked paths:\n" .
                "- C:\\Program Files (x86)\\PDFtk Server\\bin\\pdftk.exe\n" .
                "- C:\\Program Files\\PDFtk Server\\bin\\pdftk.exe\n" .
                "- C:\\Program Files (x86)\\PDFtk\\bin\\pdftk.exe\n" .
                "- C:\\Program Files\\PDFtk\\bin\\pdftk.exe\n" .
                "- System PATH\n\n" .
                "Install it using:\n" .
                "  Windows: Download from https://www.pdflabs.com/tools/pdftk-the-pdf-toolkit/\n\n" .
                "After installation, you may need to:\n" .
                "1. Restart VS Code completely (close and reopen)\n" .
                "2. Or add pdftk to your system PATH manually"
            );
        }
        
        // Unix-like systems - use pdftk from PATH
        return 'pdftk';
    }
    
    /**
     * Fill PDF using pdftk (Windows compatible)
     */
    private function fillPdfWithPdftk(string $fdfPath, string $outputPath): void
    {
        $pdftk = $this->findPdftk();
        
        $command = [
            $pdftk,
            $this->templatePath,
            'fill_form',
            $fdfPath,
            'output',
            $outputPath,
            'flatten',
        ];
        
        $process = new Process($command);
        $process->setTimeout(30);
        $process->run();
        
        if (!$process->isSuccessful()) {
            throw new Exception('pdftk failed: ' . $process->getErrorOutput() . "\nInstall pdftk from: https://www.pdflabs.com/tools/pdftk-the-pdf-toolkit/");
        }
        
        if (!file_exists($outputPath)) {
            throw new Exception('PDF was not created by pdftk');
        }
    }
    
    /**
     * Dump field names from PDF (for debugging)
     */
    public function dumpFields(): array
    {
        $pdftk = $this->findPdftk();
        
        $process = new Process([
            $pdftk,
            $this->templatePath,
            'dump_data_fields',
        ]);
        
        $process->run();
        
        if (!$process->isSuccessful()) {
            throw new Exception('pdftk not available');
        }
        
        $output = $process->getOutput();
        $fields = [];
        
        foreach (explode("\n", $output) as $line) {
            if (preg_match('/^FieldName: (.+)$/', trim($line), $matches)) {
                $fields[] = $matches[1];
            }
        }
        
        return $fields;
    }
}
