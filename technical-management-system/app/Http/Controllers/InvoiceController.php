<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Helpers\AuditLogHelper;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $query = Invoice::with(['customer', 'jobOrder']);

        // Search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('payment_status', $request->get('status'));
        }

        // Auto-update overdue invoices
        Invoice::where('payment_status', 'pending')
            ->where('due_date', '<', Carbon::today())
            ->update(['payment_status' => 'overdue']);

        $invoices = $query->orderBy('issue_date', 'desc')->paginate(20)->withQueryString();

        // Calculate stats
        $stats = [
            'totalRevenue' => Invoice::where('payment_status', 'paid')->sum('total'),
            'pendingAmount' => Invoice::whereIn('payment_status', ['pending', 'overdue'])->sum('balance'),
            'paidThisMonth' => Invoice::where('payment_status', 'paid')
                ->whereMonth('updated_at', Carbon::now()->month)
                ->whereYear('updated_at', Carbon::now()->year)
                ->sum('total'),
            'overdueAmount' => Invoice::where('payment_status', 'overdue')->sum('balance'),
            'pendingCount' => Invoice::whereIn('payment_status', ['pending', 'overdue'])->count(),
            'paidCount' => Invoice::where('payment_status', 'paid')
                ->whereMonth('updated_at', Carbon::now()->month)
                ->whereYear('updated_at', Carbon::now()->year)
                ->count(),
            'overdueCount' => Invoice::where('payment_status', 'overdue')->count(),
        ];

        return view('shared.accounting', compact('invoices', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'job_order_id' => 'nullable|exists:job_orders,id',
            'subtotal' => 'required|numeric|min:0',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'payment_terms' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // Generate invoice number
        $latestInvoice = Invoice::orderBy('created_at', 'desc')->first();
        $number = $latestInvoice ? (int)substr($latestInvoice->invoice_number, -3) + 1 : 1;
        $validated['invoice_number'] = 'INV-' . date('Y') . '-' . str_pad($number, 3, '0', STR_PAD_LEFT);
        
        // Calculate totals
        $validated['tax_rate'] = 12.00;
        $validated['discount'] = 0;
        $validated['tax_amount'] = $validated['subtotal'] * ($validated['tax_rate'] / 100);
        $validated['total'] = $validated['subtotal'] + $validated['tax_amount'] - $validated['discount'];
        $validated['amount_paid'] = 0;
        $validated['balance'] = $validated['total'];
        $validated['payment_status'] = 'pending';
        $validated['created_by'] = auth()->id();

        $invoice = Invoice::create($validated);

        AuditLogHelper::log(
            action: 'CREATE',
            modelType: 'Invoice',
            modelId: $invoice->id,
            description: "Created invoice: {$validated['invoice_number']}",
            newValues: $validated
        );

        return redirect()->back()->with('status', 'Invoice created successfully!');
    }

    public function markAsPaid(Invoice $invoice)
    {
        $oldStatus = $invoice->payment_status;
        
        $invoice->update([
            'payment_status' => 'paid',
            'amount_paid' => $invoice->total,
            'balance' => 0,
        ]);

        AuditLogHelper::log(
            action: 'UPDATE',
            modelType: 'Invoice',
            modelId: $invoice->id,
            description: "Marked invoice {$invoice->invoice_number} as paid",
            oldValues: ['payment_status' => $oldStatus, 'balance' => $invoice->total],
            newValues: ['payment_status' => 'paid', 'balance' => 0],
            changedFields: ['payment_status', 'amount_paid', 'balance']
        );

        return redirect()->back()->with('status', 'Invoice marked as paid!');
    }

    public function destroy(Invoice $invoice)
    {
        $invoiceNumber = $invoice->invoice_number;
        $invoiceId = $invoice->id;
        $invoiceData = $invoice->toArray();
        
        $invoice->delete();

        AuditLogHelper::log(
            action: 'DELETE',
            modelType: 'Invoice',
            modelId: $invoiceId,
            description: "Deleted invoice: {$invoiceNumber}",
            oldValues: $invoiceData
        );

        return redirect()->back()->with('status', "Invoice {$invoiceNumber} has been deleted successfully!");
    }
}
