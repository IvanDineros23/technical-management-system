<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    
    // Redirect based on role
    if ($user->role) {
        switch ($user->role->slug) {
            case 'marketing':
                return redirect()->route('marketing.dashboard');
            case 'tech_personnel':
                return redirect()->route('technician.dashboard');
            case 'tech_head':
                return redirect()->route('tech-head.dashboard');
            case 'signatory':
                return redirect()->route('signatory.dashboard');
            case 'accounting':
                return redirect()->route('accounting.dashboard');
            case 'admin':
                return redirect()->route('admin.dashboard');
        }
    }
    
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Marketing Routes
Route::middleware(['auth', 'verified'])->prefix('marketing')->name('marketing.')->group(function () {
    Route::get('/dashboard', function () {
        $totalJobOrders = \App\Models\JobOrder::count();
        $pendingJobOrders = \App\Models\JobOrder::where('status', 'pending')->count();
        $inProgressJobOrders = \App\Models\JobOrder::where('status', 'in_progress')->count();
        $completedJobOrders = \App\Models\JobOrder::where('status', 'completed')->count();
        $recentJobOrders = \App\Models\JobOrder::with('customer')
            ->latest()
            ->take(3)
            ->get();
        
        return view('marketing.dashboard', compact(
            'totalJobOrders',
            'pendingJobOrders',
            'inProgressJobOrders',
            'completedJobOrders',
            'recentJobOrders'
        ));
    })->name('dashboard');
    
    Route::get('/job-orders', function () {
        $query = \App\Models\JobOrder::with('customer')->latest();
        
        // Filter by status if provided
        if (request()->has('status') && request('status') != '') {
            $query->where('status', request('status'));
        }
        
        $jobOrders = $query->paginate(10);
        return view('marketing.job-orders', compact('jobOrders'));
    })->name('job-orders');
    
    Route::get('/create-job-order', function () {
        $customers = \App\Models\Customer::where('is_active', true)->get();
        return view('marketing.create-job-order', compact('customers'));
    })->name('create-job-order');

    // Customer details page
    Route::get('/customers/{customer}', function (\App\Models\Customer $customer) {
        $customer->loadCount('jobOrders');
        return view('marketing.customer-details', compact('customer'));
    })->name('customers.show');
    
    Route::post('/job-orders', function (Illuminate\Http\Request $request) {
        try {
            $validated = $request->validate([
                'customer_name' => 'required|string|max:255',
                'contact_person' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'phone' => 'nullable|string|max:20',
                'service_type' => 'required|string',
                'priority_level' => 'required|string',
                'service_description' => 'required|string',
                'expected_start_date' => 'nullable|date',
                'expected_completion_date' => 'nullable|date|after_or_equal:expected_start_date',
                'service_address' => 'required|string',
                'city' => 'nullable|string|max:100',
                'province' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:20',
            ]);

            // Create or find customer
            $customer = \App\Models\Customer::where('email', $validated['email'])
                ->orWhere('name', $validated['customer_name'])
                ->first();
                
            if (!$customer) {
                $customer = \App\Models\Customer::create([
                    'name' => $validated['customer_name'],
                    'email' => $validated['email'] ?? null,
                    'phone' => $validated['phone'] ?? null,
                    'address' => $validated['service_address'] . ', ' . ($validated['city'] ?? '') . ', ' . ($validated['province'] ?? ''),
                    'is_active' => true,
                ]);
            }

            // Generate job order number
            $lastJobOrder = \App\Models\JobOrder::latest('id')->first();
            $nextNumber = $lastJobOrder ? (intval(substr($lastJobOrder->job_order_number, 3)) + 1) : 1;
            $jobOrderNumber = 'JO-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            // Map priority level
            $priorityMap = [
                'Normal' => 'normal',
                'High' => 'high',
                'Urgent' => 'urgent'
            ];
            $priority = $priorityMap[$validated['priority_level']] ?? 'normal';

            // Create job order
            $jobOrder = \App\Models\JobOrder::create([
                'job_order_number' => $jobOrderNumber,
                'customer_id' => $customer->id,
                'service_type' => $validated['service_type'],
                'service_description' => $validated['service_description'],
                'expected_start_date' => $validated['expected_start_date'] ?? null,
                'expected_completion_date' => $validated['expected_completion_date'] ?? null,
                'service_address' => $validated['service_address'],
                'city' => $validated['city'] ?? null,
                'province' => $validated['province'] ?? null,
                'postal_code' => $validated['postal_code'] ?? null,
                'requested_by' => $validated['contact_person'] ?? $validated['customer_name'],
                'request_date' => now(),
                'required_date' => $validated['expected_completion_date'] ?? null,
                'priority' => $priority,
                'status' => 'pending',
                'created_by' => auth()->id() ?? 1, // Use auth user or default to 1
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Job Order created successfully!',
                'job_order_id' => $jobOrder->id
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating job order: ' . $e->getMessage()
            ], 500);
        }
    })->name('job-orders.store');
    
    Route::get('/customers', function () {
        $query = \App\Models\Customer::withCount('jobOrders');
        
        // Search functionality
        if (request()->has('search') && request('search') != '') {
            $searchTerm = request('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('email', 'like', '%' . $searchTerm . '%')
                  ->orWhere('phone', 'like', '%' . $searchTerm . '%')
                  ->orWhere('address', 'like', '%' . $searchTerm . '%');
            });
        }
        
        $customers = $query->get();
        return view('marketing.customers', compact('customers'));
    })->name('customers');
    
    Route::post('/customers', function () {
        try {
            $validated = request()->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:customers,email',
                'phone' => 'required|string|max:50',
                'address' => 'required|string',
                'city' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'contact_person' => 'nullable|string|max:255',
                'tax_id' => 'nullable|string|max:100',
            ]);
            
            $validated['is_active'] = true;
            \App\Models\Customer::create($validated);
            
            return response()->json(['success' => true, 'message' => 'Customer added successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    })->name('customers.store');
    
    Route::put('/customers/{customer}', function (\App\Models\Customer $customer) {
        try {
            $validated = request()->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:customers,email,' . $customer->id,
                'phone' => 'required|string|max:50',
                'address' => 'required|string',
                'city' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'contact_person' => 'nullable|string|max:255',
                'tax_id' => 'nullable|string|max:100',
            ]);
            
            $customer->update($validated);
            
            return response()->json(['success' => true, 'message' => 'Customer updated successfully']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], 500);
        }
    })->name('customers.update');
    
    Route::delete('/customers/{customer}', function (\App\Models\Customer $customer) {
        try {
            $customer->delete();
            return response()->json(['success' => true, 'message' => 'Customer deleted successfully']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Error deleting customer'
            ], 500);
        }
    })->name('customers.destroy');
    
    Route::get('/reports', function () {
        $totalRevenue = \App\Models\JobOrder::sum('grand_total');
        $completedJobs = \App\Models\JobOrder::where('status', 'completed')->count();
        $activeCustomers = \App\Models\Customer::where('is_active', true)->count();
        $avgJobValue = $completedJobs > 0 ? $totalRevenue / $completedJobs : 0;
        
        return view('marketing.reports', compact(
            'totalRevenue',
            'completedJobs',
            'activeCustomers',
            'avgJobValue'
        ));
    })->name('reports');
});

// Placeholder routes for other roles (to be implemented)
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/technician/dashboard', function () {
        return 'Technician Dashboard - Coming Soon';
    })->name('technician.dashboard');
    
    Route::get('/tech-head/dashboard', function () {
        return 'Tech Head Dashboard - Coming Soon';
    })->name('tech-head.dashboard');
    
    Route::get('/signatory/dashboard', function () {
        return 'Signatory Dashboard - Coming Soon';
    })->name('signatory.dashboard');
    
    Route::get('/accounting/dashboard', function () {
        return 'Accounting Dashboard - Coming Soon';
    })->name('accounting.dashboard');
    
    Route::get('/admin/dashboard', function () {
        return 'Admin Dashboard - Coming Soon';
    })->name('admin.dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
