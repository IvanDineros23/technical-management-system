<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\JobOrder;
use App\Models\User;
use Symfony\Component\Process\Process;
use Exception;

class JobOrderPdfService
{
    private string $templatePath;
    private string $outputDir;

    public function __construct()
    {
        $publicAcrobatTemplate = public_path('assets/GEI-MAR-F-3 Customer Request Form Rev 2 ACROBAT.pdf');
        $storageTemplate = storage_path('app/templates/GEI-MAR-F-3 Customer Request Form Rev 2.pdf');

        $this->templatePath = file_exists($publicAcrobatTemplate)
            ? $publicAcrobatTemplate
            : $storageTemplate;
        $this->outputDir = storage_path('app/public/generated');
    }

    /**
     * Generate PDF by filling form fields using pdftk
     */
    public function generate(JobOrder $jobOrder, ?User $generatedByUser = null): string
    {
        try {
            // Build form data
            $formData = $this->buildFormData($jobOrder, $generatedByUser);
            
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
    private function buildFormData(JobOrder $jobOrder, ?User $generatedByUser = null): array
    {
        $jobOrder->loadMissing(['customer.contacts', 'items']);
        $customer = $jobOrder->customer;
        $overrides = $this->extractCustomerRequestOverrides($jobOrder);
        $marketingReceiver = $this->resolveMarketingReceiverDetails($jobOrder, $generatedByUser);
        $marketingReceiverName = $overrides['name_signature'] ?? $marketingReceiver['name'];
        $hasMarketingReceiver = trim($marketingReceiverName) !== '';

        $contact = $customer?->contacts?->first();

        $data = [
            'Company Name' => $overrides['company_name'] ?? ($customer?->business_name ?: ($customer?->name ?? '')),
            'Address 1' => $overrides['address_1'] ?? ($customer?->address ?? ''),
            'Address 2' => $overrides['address_2'] ?? implode(', ', array_filter([
                $customer?->city,
                $customer?->state,
                $customer?->postal_code,
            ])),
            'Company TIN' => $overrides['company_tin'] ?? ($customer?->tax_id ?? ''),
            'Contact Person' => $overrides['contact_person'] ?? ($jobOrder->requested_by ?: ($customer?->contact_person ?? $customer?->name ?? '')),
            'Email Address' => $overrides['email_address'] ?? ($customer?->email ?: ($contact?->email ?? '')),
            'Contact Number' => $overrides['contact_number'] ?? ($customer?->phone ?: ($contact?->phone ?? '')),
            'Date' => $overrides['request_date'] ?? ($jobOrder->request_date ? $jobOrder->request_date->format('m/d/Y') : now()->format('m/d/Y')),
            'Calibration Site Address 1' => $overrides['calibration_site_address_1'] ?? ($jobOrder->service_address ?? ($customer?->address ?? '')),
            'Calibration Site Address 2' => $overrides['calibration_site_address_2'] ?? implode(', ', array_filter([
                $jobOrder->city,
                $jobOrder->province,
                $jobOrder->postal_code,
            ])),
            'Others' => '',
            'REMARKS' => $overrides['remarks'] ?? implode("\n", array_filter([
                $jobOrder->service_description,
                $jobOrder->notes,
            ])),
            'Name and Signature' => $marketingReceiverName,
            'Date_2' => $overrides['date_2'] ?? ($hasMarketingReceiver
                ? ($marketingReceiver['date'] !== ''
                    ? $marketingReceiver['date']
                    : ($jobOrder->request_date ? $jobOrder->request_date->format('m/d/Y') : now()->format('m/d/Y')))
                : ''),
            'Name and Signature2' => $overrides['name_signature_2'] ?? '',
            'Date_3' => $overrides['date_3'] ?? '',
            'Noted by' => $overrides['noted_by'] ?? '',
            'Date_4' => $overrides['date_4'] ?? '',
        ];

        $serviceType = strtolower((string) ($jobOrder->service_type ?? ''));
        $checkboxes = [
            'Check Box1' => str_contains($serviceType, 'inspection'),
            'Check Box2' => str_contains($serviceType, 'repair'),
            'Check Box3' => str_contains($serviceType, 'installation'),
            'Check Box4' => str_contains($serviceType, 'demonstration'),
            'Check Box5' => str_contains($serviceType, 'calibration'),
        ];

        $hasKnownService = collect($checkboxes)->contains(true);
        $checkboxes['Check Box6'] = !$hasKnownService && $serviceType !== '';

        foreach ($checkboxes as $fieldName => $isChecked) {
            $data[$fieldName] = $isChecked ? 'Yes' : 'Off';
        }

        $otherValue = trim((string) ($overrides['others'] ?? ''));

        if ($checkboxes['Check Box6']) {
            $data['Others'] = $otherValue !== '' ? $otherValue : (string) ($jobOrder->service_type ?? '');
        } else {
            $normalizedOtherValue = strtolower($otherValue);
            $knownServices = ['inspection', 'repair', 'installation', 'demonstration', 'calibration'];
            $containsKnownService = false;

            foreach ($knownServices as $knownService) {
                if (str_contains($normalizedOtherValue, $knownService)) {
                    $containsKnownService = true;
                    break;
                }
            }

            $data['Others'] = $containsKnownService ? '' : $otherValue;
        }

        $items = $jobOrder->items()->orderBy('item_number')->limit(8)->get();
        foreach ($items as $index => $item) {
            $row = $index + 1;
            $data["QtyRow{$row}"] = (string) ($item->quantity ?? 1);
            $data["Equipment NameRow{$row}"] = (string) ($item->equipment_type ?? '');
            $data["ModelRow{$row}"] = (string) ($item->model ?? '');
            $data["Serial NoRow{$row}"] = (string) ($item->serial_number ?? '');
            $data["CapacityRow{$row}"] = (string) ($item->range ?? '');
        }

        return $data;
    }

    private function extractCustomerRequestOverrides(JobOrder $jobOrder): array
    {
        $specialInstructions = $jobOrder->special_instructions;
        if (!is_string($specialInstructions) || trim($specialInstructions) === '') {
            return [];
        }

        $decoded = json_decode($specialInstructions, true);
        if (!is_array($decoded)) {
            return [];
        }

        $overrides = $decoded['customer_request_pdf_overrides'] ?? null;
        return is_array($overrides) ? $overrides : [];
    }

    private function resolveMarketingReceiverDetails(JobOrder $jobOrder, ?User $generatedByUser = null): array
    {
        // Only marketing users should appear in the "FOR GEI - MARKETING ONLY" signature field.
        if ($generatedByUser && optional($generatedByUser->role)->slug === 'marketing') {
            return [
                'name' => (string) ($generatedByUser->name ?? ''),
                'date' => now()->setTimezone('Asia/Manila')->format('m/d/Y'),
            ];
        }

        $creator = $jobOrder->creator;
        if ($creator && optional($creator->role)->slug === 'marketing') {
            return [
                'name' => (string) ($creator->name ?? ''),
                'date' => $jobOrder->request_date ? $jobOrder->request_date->format('m/d/Y') : '',
            ];
        }

        $marketingApprovalAudit = AuditLog::with('user.role')
            ->where('model_type', 'JobOrder')
            ->where('model_id', $jobOrder->id)
            ->where('action', 'APPROVE')
            ->whereHas('user.role', function ($roleQuery) {
                $roleQuery->where('slug', 'marketing');
            })
            ->latest('created_at')
            ->first();

        if ($marketingApprovalAudit && $marketingApprovalAudit->user) {
            return [
                'name' => (string) ($marketingApprovalAudit->user->name ?? ''),
                'date' => $marketingApprovalAudit->created_at
                    ? $marketingApprovalAudit->created_at->setTimezone('Asia/Manila')->format('m/d/Y')
                    : '',
            ];
        }

        return ['name' => '', 'date' => ''];
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
