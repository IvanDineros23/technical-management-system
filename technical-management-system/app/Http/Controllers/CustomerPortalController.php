<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogHelper;
use App\Models\Certificate;
use App\Models\JobOrder;
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

        $jobOrdersQuery = JobOrder::where('customer_id', $customer->id)->latest();
        $certificatesQuery = Certificate::with('jobOrder')
            ->where('is_current', true)
            ->whereHas('jobOrder', function ($query) use ($customer) {
                $query->where('customer_id', $customer->id);
            });

        $stats = [
            'total_requests' => (clone $jobOrdersQuery)->count(),
            'pending_requests' => (clone $jobOrdersQuery)->where('status', 'pending')->count(),
            'total_certificates' => (clone $certificatesQuery)->count(),
            'pending_certificates' => (clone $certificatesQuery)->whereNull('released_at')->count(),
            'released_certificates' => (clone $certificatesQuery)->whereNotNull('released_at')->count(),
        ];

        $pendingRequests = (clone $jobOrdersQuery)
            ->where('status', 'pending')
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

        if (!$customer) {
            return view('customer.requests', [
                'customer' => null,
                'jobOrders' => collect(),
                'status' => '',
            ]);
        }

        $status = $request->string('status')->toString();
        $query = JobOrder::with('customer')
            ->where('customer_id', $customer->id)
            ->latest();

        if ($status !== '') {
            $query->where('status', $status);
        }

        $jobOrders = $query->paginate(20)->appends(['status' => $status]);

        return view('customer.requests', compact('customer', 'jobOrders', 'status'));
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

    public function storeRequest(Request $request)
    {
        $user = $request->user();
        $customer = $user->customer;

        if (!$customer) {
            return back()->withErrors(['error' => 'No customer profile linked to your account. Please contact support.']);
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
        ]);

        DB::beginTransaction();
        try {
            // Generate job order number
            $lastJobOrder = JobOrder::latest('id')->first();
            $nextNumber = $lastJobOrder ? (intval(substr($lastJobOrder->job_order_number, 3)) + 1) : 1;
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

            DB::commit();

            return redirect()->route('customer.requests')
                ->with('status', 'Your service request has been submitted successfully! Our team will review it shortly.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to submit request: ' . $e->getMessage()])->withInput();
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
}
