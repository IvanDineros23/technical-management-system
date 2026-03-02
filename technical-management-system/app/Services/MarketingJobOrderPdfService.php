<?php

namespace App\Services;

use App\Models\JobOrder;
use Exception;
use Symfony\Component\Process\Process;

class MarketingJobOrderPdfService
{
    private string $templatePath;
    private string $outputDir;

    public function __construct()
    {
        $this->templatePath = public_path('assets/GEI-MAR-F-1 Job Order Form Rev 1 ACROBAT.pdf');
        $this->outputDir = storage_path('app/public/generated');
    }

    public function generate(JobOrder $jobOrder): string
    {
        $jobOrder->loadMissing(['customer', 'items', 'creator.role', 'approver.role']);

        if (!file_exists($this->templatePath)) {
            throw new Exception('Job order template PDF not found: ' . $this->templatePath);
        }

        $formData = $this->buildFormData($jobOrder);
        $fdfPath = $this->createFdf($formData);

        $safeJoNumber = preg_replace('/[^A-Za-z0-9\-]/', '-', $jobOrder->job_order_number ?? 'job-order');
        $filename = "job-order-form-{$safeJoNumber}-" . now()->format('Y-m-d-His') . '.pdf';
        $outputPath = $this->outputDir . DIRECTORY_SEPARATOR . $filename;

        if (!is_dir($this->outputDir)) {
            mkdir($this->outputDir, 0755, true);
        }

        $this->fillPdfWithPdftk($fdfPath, $outputPath);
        @unlink($fdfPath);

        $jobOrder->update([
            'pdf_filename' => $filename,
            'pdf_path' => $outputPath,
        ]);

        return $outputPath;
    }

    private function buildFormData(JobOrder $jobOrder): array
    {
        $customer = $jobOrder->customer;
        $pdfOverrides = $this->extractPdfOverrides($jobOrder);
        $preparedBy = '';
        $currentUser = auth()->user();

        if ($jobOrder->approver && optional($jobOrder->approver->role)->slug === 'marketing') {
            $preparedBy = $jobOrder->approver->name;
        } elseif ($jobOrder->creator && optional($jobOrder->creator->role)->slug === 'marketing') {
            $preparedBy = $jobOrder->creator->name;
        } elseif ($currentUser && optional($currentUser->role)->slug === 'marketing') {
            $preparedBy = $currentUser->name;
        }

        $data = [
            'Date' => optional($jobOrder->request_date)->format('m/d/Y') ?: now()->format('m/d/Y'),
            'JO No' => $jobOrder->job_order_number ?? '',
            'Company Name' => $pdfOverrides['company_name'] ?? ($customer?->business_name ?: ($customer?->name ?? '')),
            'Company TIN' => $pdfOverrides['company_tin'] ?? ($customer?->tax_id ?? ''),
            'Address 1' => $pdfOverrides['address'] ?? ($customer?->address ?? ''),
            'Address 2' => implode(', ', array_filter([
                $customer?->city,
                $customer?->state,
                $customer?->postal_code,
            ])),
            'Contact No' => $pdfOverrides['contact_no'] ?? ($customer?->phone ?? ''),
            'Contact Person' => $jobOrder->requested_by ?: ($customer?->contact_person ?? ''),
            'Calibration Site Address 1' => $jobOrder->service_address ?? '',
            'Calibration Site Address 2' => implode(', ', array_filter([
                $jobOrder->city,
                $jobOrder->province,
                $jobOrder->postal_code,
            ])),
            'Service Invoice Number' => $jobOrder->service_invoice_number ?? '',
            'Others' => $jobOrder->other_details ?? '',
            'REMARKS' => $jobOrder->notes ?? '',
            'Client PO Ctrl No 1' => $jobOrder->client_po_ctrl_no ?? '',
            'Client PO Ctrl No 2' => $jobOrder->terms ?? '',
            'Client PO Ctrl No 3' => $preparedBy,
            'Rcvd by' => '',
            'Rcvd by_2' => '',
            'undefined' => $preparedBy,
            'Approved by' => $jobOrder->approver?->name ?? '',
            'Approved by_2' => '',
        ];

        $items = $jobOrder->items()->orderBy('item_number')->limit(10)->get();
        foreach ($items as $index => $item) {
            $row = $index + 1;
            $data["Item NoRow{$row}"] = (string) ($item->item_number ?? $row);
            $data["QtyRow{$row}"] = (string) ($item->quantity ?? 1);
            $data["Equipment NameRow{$row}"] = (string) ($item->equipment_type ?? '');
            $data["ModelRow{$row}"] = (string) ($item->model ?? '');
            $data["Serial NoRow{$row}"] = (string) ($item->serial_number ?? '');
            $data["CapacityRow{$row}"] = (string) ($item->range ?? '');
        }

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

        if ($checkboxes['Check Box6']) {
            $data['Others'] = $jobOrder->service_type;
        }

        return $data;
    }

    private function extractPdfOverrides(JobOrder $jobOrder): array
    {
        $specialInstructions = $jobOrder->special_instructions;
        if (!is_string($specialInstructions) || trim($specialInstructions) === '') {
            return [];
        }

        $decoded = json_decode($specialInstructions, true);
        if (!is_array($decoded)) {
            return [];
        }

        $overrides = $decoded['marketing_pdf_overrides'] ?? null;
        return is_array($overrides) ? $overrides : [];
    }

    private function createFdf(array $data): string
    {
        $fdfPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'job_order_' . uniqid('', true) . '.fdf';

        $fdf = "%FDF-1.2\n";
        $fdf .= "1 0 obj\n";
        $fdf .= "<<\n";
        $fdf .= "/FDF << /Fields [\n";

        foreach ($data as $fieldName => $fieldValue) {
            $escapedName = $this->escapeFdfValue((string) $fieldName);
            $escapedValue = $this->escapeFdfValue((string) $fieldValue);
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

    private function escapeFdfValue(string $value): string
    {
        $value = str_replace('\\', '\\\\', $value);
        $value = str_replace('(', '\\(', $value);
        $value = str_replace(')', '\\)', $value);
        $value = str_replace("\r", '\\r', $value);
        $value = str_replace("\n", '\\n', $value);

        return $value;
    }

    private function fillPdfWithPdftk(string $fdfPath, string $outputPath): void
    {
        $process = new Process([
            $this->findPdftk(),
            $this->templatePath,
            'fill_form',
            $fdfPath,
            'output',
            $outputPath,
            'flatten',
        ]);

        $process->setTimeout(60);
        $process->run();

        if (!$process->isSuccessful()) {
            throw new Exception('pdftk failed: ' . trim($process->getErrorOutput()));
        }

        if (!file_exists($outputPath)) {
            throw new Exception('Failed to generate job order PDF output file.');
        }
    }

    private function findPdftk(): string
    {
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

            return 'pdftk';
        }

        return 'pdftk';
    }
}
