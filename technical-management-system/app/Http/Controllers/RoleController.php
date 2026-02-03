<?php

namespace App\Http\Controllers;

use App\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->get();
        
        // Define all available modules/processes in the system
        $modules = [
            'View Job Orders' => 'view_job_orders',
            'Create Job Orders' => 'create_job_orders',
            'Edit Job Orders' => 'edit_job_orders',
            'Approve Job Orders' => 'approve_job_orders',
            'View Work Orders' => 'view_work_orders',
            'Create Work Orders' => 'create_work_orders',
            'Edit Work Orders' => 'edit_work_orders',
            'View Calibrations' => 'view_calibrations',
            'Perform Calibrations' => 'perform_calibrations',
            'Edit Calibrations' => 'edit_calibrations',
            'View Certificates' => 'view_certificates',
            'Create Certificates' => 'create_certificates',
            'Release Certificates' => 'release_certificates',
            'Manage Equipment' => 'manage_equipment',
            'View Inventory' => 'view_inventory',
            'View Maintenance' => 'view_maintenance',
            'View Reports' => 'view_reports',
            'Create Reports' => 'create_reports',
            'View Customers' => 'view_customers',
            'View Assignments' => 'view_assignments',
            'View Accounting' => 'view_accounting',
            'Create Accounting' => 'create_accounting',
            'Edit Accounting' => 'edit_accounting',
            'Approve Payments' => 'approve_payments',
            'View Analytics' => 'view_analytics',
            'View Timeline' => 'view_timeline',
            'View Calendar' => 'view_calendar',
            'Manage Assignments' => 'manage_assignments',
            'Manage Technicians' => 'manage_technicians',
            'Manage Schedule' => 'manage_schedule',
            'Manage Users' => 'manage_users',
            'Manage Roles' => 'manage_roles',
        ];
        
        // Define permissions for each role based on actual workflow
        $permissions = [
            'Administrator' => array_values($modules), // Full access
            'Marketing' => [
                'view_job_orders', 
                'create_job_orders', 
                'edit_job_orders',
                'view_customers',
                'view_reports',
            ],
            'Technical Personnel' => [
                'view_work_orders', 
                'create_work_orders',
                'edit_work_orders',
                'view_calibrations',
                'perform_calibrations',
                'manage_equipment',
                'view_inventory',
                'view_maintenance',
                'view_reports',
                'create_reports',
                'view_assignments',
                'view_calendar',
                'view_timeline',
            ],
            'Technical Head' => [
                'view_work_orders',
                'create_work_orders', 
                'edit_work_orders',
                'view_calibrations',
                'perform_calibrations',
                'edit_calibrations',
                'manage_equipment',
                'view_inventory',
                'view_maintenance',
                'view_reports',
                'create_reports',
                'view_certificates',
                'create_certificates',
                'release_certificates',
                'view_analytics',
                'view_timeline',
                'view_calendar',
                'view_assignments',
                'manage_assignments',
                'manage_technicians',
                'manage_schedule',
            ],
            'Signatory' => [
                'view_job_orders', 
                'approve_job_orders',
                'view_work_orders',
                'view_calibrations',
                'view_certificates',
                'create_certificates',
                'release_certificates',
                'view_reports',
                'view_timeline',
            ],
            'Accounting' => [
                'view_job_orders',
                'view_accounting',
                'create_accounting',
                'edit_accounting',
                'approve_payments',
                'view_certificates',
                'release_certificates',
                'view_reports',
                'view_timeline',
            ],
        ];
        
        return view('admin.roles', compact('roles', 'modules', 'permissions'));
    }
}
