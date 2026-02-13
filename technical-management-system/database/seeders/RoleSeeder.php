<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Administrator',
                'slug' => 'admin',
                'description' => 'Full system access and management',
                'permissions' => json_encode([
                    'manage_users',
                    'manage_roles',
                    'manage_job_orders',
                    'view_all_reports',
                    'system_settings',
                    'approve_requests',
                    'manage_inventory'
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Marketing',
                'slug' => 'marketing',
                'description' => 'Create and manage customer requests and job orders',
                'permissions' => json_encode([
                    'create_job_orders',
                    'view_own_job_orders',
                    'edit_own_job_orders',
                    'view_customers'
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Technical Personnel',
                'slug' => 'tech_personnel',
                'description' => 'Execute assigned technical tasks and maintenance',
                'permissions' => json_encode([
                    'view_assigned_jobs',
                    'update_job_status',
                    'submit_reports',
                    'log_activities',
                    'view_equipment'
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Technical Head',
                'slug' => 'tech_head',
                'description' => 'Supervise technical team and assign tasks',
                'permissions' => json_encode([
                    'view_all_jobs',
                    'assign_technicians',
                    'review_reports',
                    'approve_technical_work',
                    'manage_schedules',
                    'view_team_performance'
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Signatory',
                'slug' => 'signatory',
                'description' => 'Review and approve job orders and reports',
                'permissions' => json_encode([
                    'view_pending_approvals',
                    'approve_job_orders',
                    'reject_job_orders',
                    'view_all_reports',
                    'add_comments'
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Customer',
                'slug' => 'customer',
                'description' => 'View own job requests and certificates',
                'permissions' => json_encode([
                    'view_job_orders',
                    'view_certificates'
                ]),
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($roles as $role) {
            DB::table('roles')->updateOrInsert(
                ['slug' => $role['slug']],
                $role
            );
        }
    }
}
