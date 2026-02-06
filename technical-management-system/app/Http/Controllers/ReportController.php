<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogHelper;
use App\Models\JobOrder;
use App\Models\Customer;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Generate Job Orders Report
     */
    public function generateJobOrdersReport(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        
        $query = JobOrder::with('customer');
        
        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        
        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }
        
        $jobOrders = $query->get();
        
        // Log the report generation
        AuditLogHelper::log(
            'GENERATE',
            'Report',
            null,
            'Generated Job Orders Report',
            null,
            [
                'report_type' => 'Job Orders',
                'record_count' => $jobOrders->count(),
                'from_date' => $fromDate,
                'to_date' => $toDate,
            ],
            ['report_type', 'record_count', 'from_date', 'to_date']
        );
        
        $pdf = Pdf::loadView('marketing.reports.job-orders-pdf', [
            'jobOrders' => $jobOrders,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'generatedAt' => now(),
        ]);
        
        return $pdf->download('job-orders-report-' . now()->format('Y-m-d-His') . '.pdf');
    }
    
    /**
     * Generate Revenue Report
     */
    public function generateRevenueReport(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        
        $query = JobOrder::query();
        
        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        
        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }
        
        $jobOrders = $query->with('customer')->get();
        
        $totalRevenue = $jobOrders->sum('grand_total');
        $totalJobs = $jobOrders->count();
        $averageRevenue = $totalJobs > 0 ? $totalRevenue / $totalJobs : 0;
        
        // Group by customer for better insights
        $revenueByCustomer = $jobOrders->groupBy('customer_id')->map(function($orders) {
            return [
                'customer' => $orders->first()->customer,
                'total' => $orders->sum('grand_total'),
                'count' => $orders->count(),
            ];
        })->sortByDesc('total');
        
        // Log the report generation
        AuditLogHelper::log(
            'GENERATE',
            'Report',
            null,
            'Generated Revenue Report',
            null,
            [
                'report_type' => 'Revenue',
                'total_revenue' => $totalRevenue,
                'total_jobs' => $totalJobs,
                'from_date' => $fromDate,
                'to_date' => $toDate,
            ],
            ['report_type', 'total_revenue', 'total_jobs', 'from_date', 'to_date']
        );
        
        $pdf = Pdf::loadView('marketing.reports.revenue-pdf', [
            'jobOrders' => $jobOrders,
            'totalRevenue' => $totalRevenue,
            'totalJobs' => $totalJobs,
            'averageRevenue' => $averageRevenue,
            'revenueByCustomer' => $revenueByCustomer,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'generatedAt' => now(),
        ]);
        
        return $pdf->download('revenue-report-' . now()->format('Y-m-d-His') . '.pdf');
    }
    
    /**
     * Generate Customer Report
     */
    public function generateCustomerReport(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        
        $query = Customer::withCount('jobOrders');
        
        // Filter customers that have job orders in the date range
        if ($fromDate || $toDate) {
            $query->whereHas('jobOrders', function($q) use ($fromDate, $toDate) {
                if ($fromDate) {
                    $q->whereDate('created_at', '>=', $fromDate);
                }
                if ($toDate) {
                    $q->whereDate('created_at', '<=', $toDate);
                }
            });
        }
        
        $customers = $query->with('jobOrders')->get();
        
        $totalCustomers = $customers->count();
        $activeCustomers = $customers->where('is_active', true)->count();
        $totalRevenue = $customers->sum(function($customer) {
            return $customer->jobOrders->sum('grand_total');
        });
        
        // Log the report generation
        AuditLogHelper::log(
            'GENERATE',
            'Report',
            null,
            'Generated Customer Report',
            null,
            [
                'report_type' => 'Customer',
                'total_customers' => $totalCustomers,
                'active_customers' => $activeCustomers,
                'total_revenue' => $totalRevenue,
                'from_date' => $fromDate,
                'to_date' => $toDate,
            ],
            ['report_type', 'total_customers', 'active_customers', 'total_revenue', 'from_date', 'to_date']
        );
        
        $pdf = Pdf::loadView('marketing.reports.customer-pdf', [
            'customers' => $customers,
            'totalCustomers' => $totalCustomers,
            'activeCustomers' => $activeCustomers,
            'totalRevenue' => $totalRevenue,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'generatedAt' => now(),
        ]);
        
        return $pdf->download('customer-report-' . now()->format('Y-m-d-His') . '.pdf');
    }
    
    /**
     * Generate Performance Report
     */
    public function generatePerformanceReport(Request $request)
    {
        $fromDate = $request->input('from_date');
        $toDate = $request->input('to_date');
        
        $query = JobOrder::query();
        
        if ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        }
        
        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }
        
        $jobOrders = $query->with('customer')->get();
        
        $totalJobs = $jobOrders->count();
        $completedJobs = $jobOrders->where('status', 'completed')->count();
        $inProgressJobs = $jobOrders->where('status', 'in_progress')->count();
        $pendingJobs = $jobOrders->where('status', 'pending')->count();
        
        $completionRate = $totalJobs > 0 ? ($completedJobs / $totalJobs) * 100 : 0;
        
        // Calculate average revenue per job
        $totalRevenue = $jobOrders->sum('grand_total');
        $averageRevenue = $totalJobs > 0 ? $totalRevenue / $totalJobs : 0;
        
        // Job status breakdown
        $statusBreakdown = [
            'pending' => $pendingJobs,
            'in_progress' => $inProgressJobs,
            'completed' => $completedJobs,
        ];
        
        // Log the report generation
        AuditLogHelper::log(
            'GENERATE',
            'Report',
            null,
            'Generated Performance Report',
            null,
            [
                'report_type' => 'Performance',
                'total_jobs' => $totalJobs,
                'completed_jobs' => $completedJobs,
                'completion_rate' => round($completionRate, 2),
                'total_revenue' => $totalRevenue,
                'from_date' => $fromDate,
                'to_date' => $toDate,
            ],
            ['report_type', 'total_jobs', 'completed_jobs', 'completion_rate', 'total_revenue', 'from_date', 'to_date']
        );
        
        $pdf = Pdf::loadView('marketing.reports.performance-pdf', [
            'jobOrders' => $jobOrders,
            'totalJobs' => $totalJobs,
            'completedJobs' => $completedJobs,
            'inProgressJobs' => $inProgressJobs,
            'pendingJobs' => $pendingJobs,
            'completionRate' => $completionRate,
            'totalRevenue' => $totalRevenue,
            'averageRevenue' => $averageRevenue,
            'statusBreakdown' => $statusBreakdown,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'generatedAt' => now(),
        ]);
        
        return $pdf->download('performance-report-' . now()->format('Y-m-d-His') . '.pdf');
    }
}
