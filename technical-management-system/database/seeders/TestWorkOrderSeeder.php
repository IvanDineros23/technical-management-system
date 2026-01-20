<?php

namespace Database\Seeders;

use App\Models\JobOrder;
use App\Models\Customer;
use Illuminate\Database\Seeder;

class TestWorkOrderSeeder extends Seeder
{
    public function run(): void
    {
        // Get or create a test customer
        $customer = Customer::firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test Customer',
                'phone' => '555-0100',
                'address' => '123 Test Street',
                'city' => 'Test City',
                'state' => 'Test State',
            ]
        );

        // Create sample work orders with various statuses
        $workOrders = [
            [
                'job_order_number' => 'WO-20260101001',
                'customer_id' => $customer->id,
                'priority' => 'high',
                'status' => 'pending',
                'request_date' => now()->subDays(5),
                'required_date' => now()->subDay(),
                'service_type' => 'Calibration',
                'service_description' => 'Equipment calibration service',
                'created_by' => 1,
            ],
            [
                'job_order_number' => 'WO-20260102001',
                'customer_id' => $customer->id,
                'priority' => 'urgent',
                'status' => 'pending',
                'request_date' => now()->subDays(8),
                'required_date' => now()->subDays(2),
                'service_type' => 'Maintenance',
                'service_description' => 'Equipment maintenance',
                'created_by' => 1,
            ],
            [
                'job_order_number' => 'WO-20260103001',
                'customer_id' => $customer->id,
                'priority' => 'normal',
                'status' => 'pending',
                'request_date' => now()->subDays(2),
                'required_date' => now()->addDays(3),
                'service_type' => 'Inspection',
                'service_description' => 'Equipment inspection',
                'created_by' => 1,
            ],
            [
                'job_order_number' => 'WO-20260104001',
                'customer_id' => $customer->id,
                'priority' => 'high',
                'status' => 'in_progress',
                'request_date' => now()->subDays(3),
                'required_date' => now()->addDays(2),
                'service_type' => 'Testing',
                'service_description' => 'Equipment testing',
                'created_by' => 1,
            ],
            [
                'job_order_number' => 'WO-20260105001',
                'customer_id' => $customer->id,
                'priority' => 'normal',
                'status' => 'completed',
                'request_date' => now()->subDays(10),
                'required_date' => now()->subDays(2),
                'service_type' => 'Repair',
                'service_description' => 'Equipment repair',
                'created_by' => 1,
            ],
        ];

        foreach ($workOrders as $workOrder) {
            JobOrder::firstOrCreate(
                ['job_order_number' => $workOrder['job_order_number']],
                $workOrder
            );
        }
    }
}
