<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Assignment;
use App\Models\JobOrder;
use App\Models\User;
use Carbon\Carbon;

class TestAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing job orders and technician
        $jobOrders = JobOrder::limit(3)->get();
        $technician = User::whereHas('role', function($q) {
            $q->where('slug', 'technician');
        })->first();
        
        // If no technician with specific role, just get any user
        if (!$technician) {
            $technician = User::first();
        }
        
        if ($jobOrders->isEmpty() || !$technician) {
            $this->command->warn('No job orders or users found. Please seed job orders and users first.');
            return;
        }

        // Create assignments for the job orders
        $assignments = [
            [
                'job_order_id' => $jobOrders[0]->id,
                'assigned_to' => $technician->id,
                'assigned_by' => 1, // Tech head user ID
                'scheduled_date' => Carbon::now()->addDays(2),
                'scheduled_time' => '09:00:00',
                'priority' => 'high',
                'status' => 'assigned',
                'notes' => 'First priority assignment',
                'created_at' => Carbon::now()->subDays(1),
            ],
            [
                'job_order_id' => $jobOrders[1]->id,
                'assigned_to' => $technician->id,
                'assigned_by' => 1,
                'scheduled_date' => Carbon::now()->addDays(3),
                'scheduled_time' => '10:30:00',
                'priority' => 'normal',
                'status' => 'assigned',
                'notes' => 'Regular testing assignment',
                'created_at' => Carbon::now()->subDays(2),
            ],
        ];

        if (isset($jobOrders[2])) {
            $assignments[] = [
                'job_order_id' => $jobOrders[2]->id,
                'assigned_to' => $technician->id,
                'assigned_by' => 1,
                'scheduled_date' => Carbon::now()->addDay(),
                'scheduled_time' => '14:00:00',
                'priority' => 'urgent',
                'status' => 'in_progress',
                'notes' => 'Urgent repair needed',
                'started_at' => Carbon::now()->subHours(2),
                'created_at' => Carbon::now()->subDays(3),
            ];
        }

        foreach ($assignments as $assignment) {
            Assignment::create($assignment);
        }

        $this->command->info('Test assignments created successfully!');
    }
}
