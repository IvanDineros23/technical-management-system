<?php

namespace Database\Seeders;

use App\Models\Invoice;
use App\Models\Customer;
use App\Models\JobOrder;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $customers = Customer::all();
        $jobOrders = JobOrder::all();
        
        if ($customers->isEmpty()) {
            return;
        }

        $invoices = [
            [
                'invoice_number' => 'INV-2024-001',
                'customer_id' => $customers->random()->id,
                'job_order_id' => $jobOrders->isNotEmpty() ? $jobOrders->random()->id : null,
                'issue_date' => '2024-01-05',
                'due_date' => '2024-01-20',
                'subtotal' => 11200.00,
                'tax_rate' => 12.00,
                'tax_amount' => 1344.00,
                'discount' => 0,
                'total' => 12544.00,
                'amount_paid' => 12544.00,
                'balance' => 0,
                'payment_status' => 'paid',
                'payment_terms' => 'Net 15',
                'notes' => 'Calibration services for industrial equipment',
                'created_by' => 1,
            ],
            [
                'invoice_number' => 'INV-2024-002',
                'customer_id' => $customers->random()->id,
                'job_order_id' => $jobOrders->isNotEmpty() ? $jobOrders->random()->id : null,
                'issue_date' => '2024-01-12',
                'due_date' => '2024-01-27',
                'subtotal' => 8000.00,
                'tax_rate' => 12.00,
                'tax_amount' => 960.00,
                'discount' => 60,
                'total' => 8900.00,
                'amount_paid' => 0,
                'balance' => 8900.00,
                'payment_status' => 'pending',
                'payment_terms' => 'Net 15',
                'notes' => 'Equipment maintenance and inspection',
                'created_by' => 1,
            ],
            [
                'invoice_number' => 'INV-2024-003',
                'customer_id' => $customers->random()->id,
                'job_order_id' => $jobOrders->isNotEmpty() ? $jobOrders->random()->id : null,
                'issue_date' => '2024-01-20',
                'due_date' => '2024-02-04',
                'subtotal' => 13500.00,
                'tax_rate' => 12.00,
                'tax_amount' => 1620.00,
                'discount' => 0,
                'total' => 15120.00,
                'amount_paid' => 0,
                'balance' => 15120.00,
                'payment_status' => 'pending',
                'payment_terms' => 'Net 15',
                'notes' => 'Annual calibration contract',
                'created_by' => 1,
            ],
            [
                'invoice_number' => 'INV-2024-004',
                'customer_id' => $customers->random()->id,
                'job_order_id' => $jobOrders->isNotEmpty() ? $jobOrders->random()->id : null,
                'issue_date' => '2023-12-15',
                'due_date' => '2023-12-30',
                'subtotal' => 5625.00,
                'tax_rate' => 12.00,
                'tax_amount' => 675.00,
                'discount' => 0,
                'total' => 6300.00,
                'amount_paid' => 0,
                'balance' => 6300.00,
                'payment_status' => 'overdue',
                'payment_terms' => 'Net 15',
                'notes' => 'Emergency repair services',
                'created_by' => 1,
            ],
            [
                'invoice_number' => 'INV-2024-005',
                'customer_id' => $customers->random()->id,
                'job_order_id' => $jobOrders->isNotEmpty() ? $jobOrders->random()->id : null,
                'issue_date' => '2024-01-25',
                'due_date' => '2024-02-09',
                'subtotal' => 19732.14,
                'tax_rate' => 12.00,
                'tax_amount' => 2367.86,
                'discount' => 0,
                'total' => 22100.00,
                'amount_paid' => 0,
                'balance' => 22100.00,
                'payment_status' => 'pending',
                'payment_terms' => 'Net 15',
                'notes' => 'Multiple equipment calibration',
                'created_by' => 1,
            ],
            [
                'invoice_number' => 'INV-2024-006',
                'customer_id' => $customers->random()->id,
                'job_order_id' => $jobOrders->isNotEmpty() ? $jobOrders->random()->id : null,
                'issue_date' => '2024-02-01',
                'due_date' => '2024-02-16',
                'subtotal' => 8750.00,
                'tax_rate' => 12.00,
                'tax_amount' => 1050.00,
                'discount' => 0,
                'total' => 9800.00,
                'amount_paid' => 9800.00,
                'balance' => 0,
                'payment_status' => 'paid',
                'payment_terms' => 'Net 15',
                'notes' => 'Laboratory equipment verification',
                'created_by' => 1,
            ],
        ];

        foreach ($invoices as $invoice) {
            Invoice::create($invoice);
        }
    }
}
