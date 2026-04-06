<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogHelper;
use App\Models\Certificate;
use App\Models\JobOrder;
use App\Services\JobOrderPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerPortalController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = $request->user();
        $customer = $user->customer;

        if (!$customer) {
            return view('customer.dashboard', [
                'customer' => null,
                'pendingRequests' => collect(),
                'pendingCertificates' => collect(),
                'stats' => [
                    'total_requests' => 0,
                    'pending_requests' => 0,
                    'total_certificates' => 0,
                    'pending_certificates' => 0,
                    'released_certificates' => 0,
                ],
            ]);
        }

        $minutesToShowCancelled = 60;

        $jobOrdersQuery = JobOrder::where('customer_id', $customer->id)->latest();
        $openRequestStatuses = ['pending', 'for_accounting_approval', 'approved', 'assigned', 'in_progress'];
        $visibleJobOrdersQuery = JobOrder::where('customer_id', $customer->id)
            ->where(function ($qq) use ($minutesToShowCancelled) {
                $qq->where('status', '!=', 'cancelled')
                   ->orWhere(function ($q2) use ($minutesToShowCancelled) {
                       $q2->where('status', 'cancelled')
                          ->where(function ($q3) use ($minutesToShowCancelled) {
                              $q3->whereExists(function ($sub) use ($minutesToShowCancelled) {
                                  $sub->selectRaw(1)
                                      ->from('job_order_statuses as jos')
                                      ->whereColumn('jos.job_order_id', 'job_orders.id')
                                      ->where('jos.status', 'cancelled')
                                      ->where('jos.changed_at', '>=', now()->subMinutes($minutesToShowCancelled));
                              })
                              ->orWhere('job_orders.updated_at', '>=', now()->subMinutes($minutesToShowCancelled));
                          });
                   });
            })
            ->latest();
        $certificatesQuery = Certificate::with('jobOrder')
            ->where('is_current', true)
            ->whereHas('jobOrder', function ($query) use ($customer) {
                $query->where('customer_id', $customer->id);
            });

        $stats = [
            'total_requests' => (clone $visibleJobOrdersQuery)->count(),
            'pending_requests' => (clone $jobOrdersQuery)->whereIn('status', $openRequestStatuses)->count(),
            'total_certificates' => (clone $certificatesQuery)->count(),
            'pending_certificates' => (clone $certificatesQuery)->whereNull('released_at')->count(),
            'released_certificates' => (clone $certificatesQuery)->whereNotNull('released_at')->count(),
        ];

        $pendingRequests = (clone $jobOrdersQuery)
            ->whereIn('status', $openRequestStatuses)
            ->take(5)
            ->get();

        $pendingCertificates = (clone $certificatesQuery)
            ->whereNull('released_at')
            ->latest('generated_at')
            ->take(5)
            ->get();

        return view('customer.dashboard', compact(
            'customer',
            'pendingRequests',
            'pendingCertificates',
            'stats'
        ));
    }

    public function requests(Request $request)
    {
        $user = $request->user();
        $customer = $user->customer;
        $missingProfileFields = $user->missingCustomerProfileFields();
        $isCustomerProfileComplete = $user->hasCompleteCustomerProfile();

        if (!$customer) {
            return view('customer.requests', [
                'customer' => null,
                'jobOrders' => collect(),
                'status' => '',
                'isCustomerProfileComplete' => false,
                'missingProfileFields' => $missingProfileFields,
            ]);
        }

        $status = $request->string('status')->toString();
        $minutesToShowCancelled = 60; // Change to 30 if you want 30 mins
        $query = JobOrder::with(['customer', 'items'])
            ->where('customer_id', $customer->id)
            ->latest();

        if ($status !== '') {
            $query->where('status', $status);
        }

        // Hide old cancelled in non-cancelled tabs
        if ($status !== 'cancelled') {
            $query->where(function ($qq) use ($minutesToShowCancelled) {
                $qq->where('status', '!=', 'cancelled')
                   ->orWhere(function ($q2) use ($minutesToShowCancelled) {
                       $q2->where('status', 'cancelled')
                          ->where(function ($q3) use ($minutesToShowCancelled) {
                              $q3->whereExists(function ($sub) use ($minutesToShowCancelled) {
                                  $sub->selectRaw(1)
                                      ->from('job_order_statuses as jos')
                                      ->whereColumn('jos.job_order_id', 'job_orders.id')
                                      ->where('jos.status', 'cancelled')
                                      ->where('jos.changed_at', '>=', now()->subMinutes($minutesToShowCancelled));
                              })
                              // fallback if no logs yet:
                              ->orWhere('job_orders.updated_at', '>=', now()->subMinutes($minutesToShowCancelled));
                          });
                   });
            });
        }

        $jobOrders = $query->paginate(20)->appends(['status' => $status]);

        return view('customer.requests', compact('customer', 'jobOrders', 'status', 'isCustomerProfileComplete', 'missingProfileFields'));
    }

    public function certificates(Request $request)
    {
        $user = $request->user();
        $customer = $user->customer;

        if (!$customer) {
            return view('customer.certificates', [
                'customer' => null,
                'certificates' => collect(),
                'status' => '',
            ]);
        }

        $status = $request->string('status')->toString();
        $query = Certificate::with(['jobOrder.customer'])
            ->where('is_current', true)
            ->whereHas('jobOrder', function ($jobOrders) use ($customer) {
                $jobOrders->where('customer_id', $customer->id);
            });

        if ($status === 'pending') {
            $query->whereNull('released_at');
        } elseif ($status === 'released') {
            $query->whereNotNull('released_at');
        } elseif ($status !== '') {
            $query->where('status', $status);
        }

        $certificates = $query->latest('generated_at')->paginate(20)->appends(['status' => $status]);

        return view('customer.certificates', compact('customer', 'certificates', 'status'));
    }

    public function requestPdf(Request $request, JobOrder $jobOrder)
    {
        $customer = $request->user()->customer;

        if (!$customer || $jobOrder->customer_id !== $customer->id) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $resolvedPath = null;

            if (!empty($jobOrder->pdf_path) && file_exists($jobOrder->pdf_path)) {
                $resolvedPath = $jobOrder->pdf_path;
            }

            if (!$resolvedPath && !empty($jobOrder->pdf_filename)) {
                $candidatePath = storage_path('app/public/generated/' . $jobOrder->pdf_filename);
                if (file_exists($candidatePath)) {
                    $resolvedPath = $candidatePath;
                }
            }

            if (!$resolvedPath) {
                app(JobOrderPdfService::class)->generate($jobOrder->fresh(['customer', 'customer.contacts', 'items']));
                $jobOrder->refresh();

                if (!empty($jobOrder->pdf_path) && file_exists($jobOrder->pdf_path)) {
                    $resolvedPath = $jobOrder->pdf_path;
                } elseif (!empty($jobOrder->pdf_filename)) {
                    $candidatePath = storage_path('app/public/generated/' . $jobOrder->pdf_filename);
                    if (file_exists($candidatePath)) {
                        $resolvedPath = $candidatePath;
                    }
                }
            }

            if (!$resolvedPath) {
                return response('Request form PDF is not available yet.', 404);
            }

            return response()->file($resolvedPath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . basename($resolvedPath) . '"',
            ]);
        } catch (\Throwable $e) {
            \Log::warning('Customer request PDF preview failed: ' . $e->getMessage(), [
                'job_order_id' => $jobOrder->id,
            ]);

            return response('Unable to load request form PDF right now.', 500);
        }
    }

    public function storeRequest(Request $request)
    {
        $user = $request->user();
        $customer = $user->customer;

        if (!$customer) {
            return back()->withErrors(['error' => 'No customer profile linked to your account. Please contact support.']);
        }

        $missingProfileFields = $user->missingCustomerProfileFields();
        if (!empty($missingProfileFields)) {
            return redirect()
                ->route('customer.requests')
                ->withErrors([
                    'error' => 'Please complete your customer profile first before creating a request. Missing: ' . implode(', ', $missingProfileFields),
                ]);
        }

        $validated = $request->validate([
            'service_type' => 'required|string|max:255',
            'priority' => 'required|in:normal,high,urgent',
            'service_description' => 'required|string',
            'service_address' => 'required|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'expected_completion_date' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string',
            'items' => 'nullable|array|max:8',
            'items.*.qty' => 'nullable|integer|min:1|max:9999',
            'items.*.equipment_name' => 'nullable|string|max:255',
            'items.*.model' => 'nullable|string|max:255',
            'items.*.serial_no' => 'nullable|string|max:255',
            'items.*.capacity' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            // Generate job order number from numeric id sequence to avoid format-parsing collisions.
            $nextNumber = ((int) JobOrder::max('id')) + 1;
            $jobOrderNumber = 'JO-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            // Create job order
            $jobOrder = JobOrder::create([
                'job_order_number' => $jobOrderNumber,
                'customer_id' => $customer->id,
                'service_type' => $validated['service_type'],
                'service_description' => $validated['service_description'],
                'service_address' => $validated['service_address'],
                'city' => $validated['city'] ?? null,
                'province' => $validated['province'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'requested_by' => $user->name,
                'request_date' => now(),
                'required_date' => $validated['expected_completion_date'] ?? null,
                'expected_completion_date' => $validated['expected_completion_date'] ?? null,
                'priority' => $validated['priority'],
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
                'created_by' => $user->id,
            ]);

            if (!empty($validated['items']) && is_array($validated['items'])) {
                foreach ($validated['items'] as $index => $item) {
                    $equipmentName = trim((string) ($item['equipment_name'] ?? ''));
                    $model = trim((string) ($item['model'] ?? ''));
                    $serialNo = trim((string) ($item['serial_no'] ?? ''));
                    $capacity = trim((string) ($item['capacity'] ?? ''));
                    $hasAnyValue = $equipmentName !== '' || $model !== '' || $serialNo !== '' || $capacity !== '';

                    if (!$hasAnyValue) {
                        continue;
                    }

                    DB::table('job_order_items')->insert([
                        'job_order_id' => $jobOrder->id,
                        'item_number' => $index + 1,
                        'equipment_type' => $equipmentName,
                        'manufacturer' => null,
                        'model' => $model !== '' ? $model : null,
                        'serial_number' => $serialNo !== '' ? $serialNo : null,
                        'id_number' => null,
                        'range' => $capacity !== '' ? $capacity : null,
                        'resolution' => null,
                        'accuracy' => null,
                        'calibration_type' => null,
                        'calibration_points' => null,
                        'quantity' => (int) ($item['qty'] ?? 1),
                        'unit_price' => null,
                        'total_price' => null,
                        'remarks' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Log the action
            AuditLogHelper::log(
                action: 'CREATE',
                modelType: 'JobOrder',
                modelId: $jobOrder->id,
                description: "Customer {$user->name} created service request {$jobOrderNumber}",
                newValues: [
                    'job_order_number' => $jobOrderNumber,
                    'customer_id' => $customer->id,
                    'service_type' => $validated['service_type'],
                    'status' => 'pending',
                ],
                changedFields: ['job_order_number', 'customer_id', 'service_type', 'status']
            );

            // Generate PDF using coordinate overlay
            $pdfUrl = null;
            try {
                $pdfService = app(JobOrderPdfService::class);
                $pdfUrl = $pdfService->generate($jobOrder);
            } catch (\Exception $e) {
                \Log::warning('PDF generation skipped: ' . $e->getMessage(), [
                    'job_order_id' => $jobOrder->id,
                ]);
                // Continue anyway - PDF is optional
            }

            DB::commit();

            return redirect()->route('customer.requests')
                ->with('status', 'Your service request has been submitted successfully! Our team will review it shortly.')
                ->with('pdf_url', $pdfUrl);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withErrors(['error' => 'Failed to submit request: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function cancelRequest(Request $request, JobOrder $jobOrder)
    {
        $user = $request->user();
        $customer = $user->customer;

        if (!$customer) {
            return back()->withErrors(['error' => 'No customer profile linked to your account. Please contact support.']);
        }

        if ($jobOrder->customer_id !== $customer->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($jobOrder->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending requests can be cancelled.']);
        }

        DB::beginTransaction();
        try {
            $jobOrder->update([
                'status' => 'cancelled',
                'rejected_at' => now(),
                'rejected_by' => $user->id,
                'rejection_reason' => 'Cancelled by customer',
            ]);

            AuditLogHelper::log(
                action: 'CANCEL',
                modelType: 'JobOrder',
                modelId: $jobOrder->id,
                description: "Customer {$user->name} cancelled service request {$jobOrder->job_order_number}",
                newValues: [
                    'status' => 'cancelled',
                    'rejection_reason' => 'Cancelled by customer',
                ],
                changedFields: ['status', 'rejection_reason']
            );

            DB::commit();

            $redirectStatus = $request->query('status', '');
            return redirect()->route('customer.requests', $redirectStatus !== '' ? ['status' => $redirectStatus] : [])
                ->with('status', 'Your request has been cancelled.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to cancel request: ' . $e->getMessage()]);
        }
    }

    public function editRequest(Request $request, JobOrder $jobOrder)
    {
        $user = $request->user();
        $customer = $user->customer;

        if (!$customer) {
            return back()->withErrors(['error' => 'No customer profile linked to your account. Please contact support.']);
        }

        if ($jobOrder->customer_id !== $customer->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($jobOrder->status !== 'pending') {
            return redirect()->route('customer.requests')->withErrors(['error' => 'Only pending requests can be edited.']);
        }

        $jobOrder->load('items');

        return view('customer.requests-edit', compact('customer', 'jobOrder'));
    }

    public function updateRequest(Request $request, JobOrder $jobOrder)
    {
        $user = $request->user();
        $customer = $user->customer;

        if (!$customer) {
            return back()->withErrors(['error' => 'No customer profile linked to your account. Please contact support.']);
        }

        if ($jobOrder->customer_id !== $customer->id) {
            abort(403, 'Unauthorized action.');
        }

        if ($jobOrder->status !== 'pending') {
            return back()->withErrors(['error' => 'Only pending requests can be edited.']);
        }

        $validated = $request->validate([
            'service_type' => 'required|string|max:255',
            'priority' => 'required|in:normal,high,urgent',
            'service_description' => 'required|string',
            'service_address' => 'required|string',
            'city' => 'nullable|string|max:100',
            'province' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'expected_completion_date' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string',
            'items' => 'nullable|array|max:8',
            'items.*.qty' => 'nullable|integer|min:1|max:9999',
            'items.*.equipment_name' => 'nullable|string|max:255',
            'items.*.model' => 'nullable|string|max:255',
            'items.*.serial_no' => 'nullable|string|max:255',
            'items.*.capacity' => 'nullable|string|max:255',
        ]);

        DB::beginTransaction();
        try {
            $jobOrder->update([
                'service_type' => $validated['service_type'],
                'service_description' => $validated['service_description'],
                'service_address' => $validated['service_address'],
                'city' => $validated['city'] ?? null,
                'province' => $validated['province'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'required_date' => $validated['expected_completion_date'] ?? null,
                'expected_completion_date' => $validated['expected_completion_date'] ?? null,
                'priority' => $validated['priority'],
                'notes' => $validated['notes'] ?? null,
            ]);

            DB::table('job_order_items')->where('job_order_id', $jobOrder->id)->delete();

            if (!empty($validated['items']) && is_array($validated['items'])) {
                foreach ($validated['items'] as $index => $item) {
                    $equipmentName = trim((string) ($item['equipment_name'] ?? ''));
                    $model = trim((string) ($item['model'] ?? ''));
                    $serialNo = trim((string) ($item['serial_no'] ?? ''));
                    $capacity = trim((string) ($item['capacity'] ?? ''));
                    $hasAnyValue = $equipmentName !== '' || $model !== '' || $serialNo !== '' || $capacity !== '';

                    if (!$hasAnyValue) {
                        continue;
                    }

                    DB::table('job_order_items')->insert([
                        'job_order_id' => $jobOrder->id,
                        'item_number' => $index + 1,
                        'equipment_type' => $equipmentName,
                        'manufacturer' => null,
                        'model' => $model !== '' ? $model : null,
                        'serial_number' => $serialNo !== '' ? $serialNo : null,
                        'id_number' => null,
                        'range' => $capacity !== '' ? $capacity : null,
                        'resolution' => null,
                        'accuracy' => null,
                        'calibration_type' => null,
                        'calibration_points' => null,
                        'quantity' => (int) ($item['qty'] ?? 1),
                        'unit_price' => null,
                        'total_price' => null,
                        'remarks' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            app(JobOrderPdfService::class)->generate($jobOrder->fresh(['customer', 'customer.contacts', 'items']));

            AuditLogHelper::log(
                action: 'UPDATE',
                modelType: 'JobOrder',
                modelId: $jobOrder->id,
                description: "Customer {$user->name} updated service request {$jobOrder->job_order_number}",
                newValues: [
                    'job_order_number' => $jobOrder->job_order_number,
                    'service_type' => $validated['service_type'],
                    'status' => $jobOrder->status,
                ],
                changedFields: ['service_type', 'service_description', 'service_address', 'city', 'province', 'postal_code', 'priority', 'notes', 'pdf_filename', 'pdf_path']
            );

            DB::commit();

            return redirect()->route('customer.requests')
                ->with('status', 'Your request has been updated successfully. PDF details were refreshed.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update request: ' . $e->getMessage()])->withInput();
        }
    }
}
