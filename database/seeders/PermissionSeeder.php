<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard
            [
                'name' => 'View Dashboard',
                'slug' => 'view_dashboard',
                'module' => 'dashboard',
                'description' => 'Access to main dashboard',
            ],
            
            // Operations Module
            [
                'name' => 'View Jobs',
                'slug' => 'view_jobs',
                'module' => 'operations',
                'description' => 'View jobs and bookings',
            ],
            [
                'name' => 'Create Jobs',
                'slug' => 'create_jobs',
                'module' => 'operations',
                'description' => 'Create new jobs and bookings',
            ],
            [
                'name' => 'Edit Jobs',
                'slug' => 'edit_jobs',
                'module' => 'operations',
                'description' => 'Edit existing jobs and bookings',
            ],
            [
                'name' => 'Delete Jobs',
                'slug' => 'delete_jobs',
                'module' => 'operations',
                'description' => 'Delete jobs and bookings',
            ],
            
            [
                'name' => 'View Dispatch',
                'slug' => 'view_dispatch',
                'module' => 'operations',
                'description' => 'View planning and dispatch',
            ],
            [
                'name' => 'Create Dispatch',
                'slug' => 'create_dispatch',
                'module' => 'operations',
                'description' => 'Create dispatch plans',
            ],
            [
                'name' => 'Edit Dispatch',
                'slug' => 'edit_dispatch',
                'module' => 'operations',
                'description' => 'Edit dispatch plans',
            ],
            [
                'name' => 'Delete Dispatch',
                'slug' => 'delete_dispatch',
                'module' => 'operations',
                'description' => 'Delete dispatch plans',
            ],
            
            [
                'name' => 'View Trips',
                'slug' => 'view_trips',
                'module' => 'operations',
                'description' => 'View trips',
            ],
            [
                'name' => 'Create Trips',
                'slug' => 'create_trips',
                'module' => 'operations',
                'description' => 'Create new trips',
            ],
            [
                'name' => 'Edit Trips',
                'slug' => 'edit_trips',
                'module' => 'operations',
                'description' => 'Edit existing trips',
            ],
            [
                'name' => 'Delete Trips',
                'slug' => 'delete_trips',
                'module' => 'operations',
                'description' => 'Delete trips',
            ],
            
            [
                'name' => 'View Tracking',
                'slug' => 'view_tracking',
                'module' => 'operations',
                'description' => 'View live tracking',
            ],
            
            [
                'name' => 'View POD',
                'slug' => 'view_pod',
                'module' => 'operations',
                'description' => 'View proof of delivery',
            ],
            [
                'name' => 'Create POD',
                'slug' => 'create_pod',
                'module' => 'operations',
                'description' => 'Create proof of delivery',
            ],
            [
                'name' => 'Edit POD',
                'slug' => 'edit_pod',
                'module' => 'operations',
                'description' => 'Edit proof of delivery',
            ],
            [
                'name' => 'Delete POD',
                'slug' => 'delete_pod',
                'module' => 'operations',
                'description' => 'Delete proof of delivery',
            ],
            
            // Fleet & People Module
            [
                'name' => 'View Vehicles',
                'slug' => 'view_vehicles',
                'module' => 'fleet',
                'description' => 'View vehicles/fleet',
            ],
            [
                'name' => 'Create Vehicles',
                'slug' => 'create_vehicles',
                'module' => 'fleet',
                'description' => 'Add new vehicles',
            ],
            [
                'name' => 'Edit Vehicles',
                'slug' => 'edit_vehicles',
                'module' => 'fleet',
                'description' => 'Edit vehicle information',
            ],
            [
                'name' => 'Delete Vehicles',
                'slug' => 'delete_vehicles',
                'module' => 'fleet',
                'description' => 'Delete vehicles',
            ],
            
            [
                'name' => 'View Drivers',
                'slug' => 'view_drivers',
                'module' => 'fleet',
                'description' => 'View drivers',
            ],
            [
                'name' => 'Create Drivers',
                'slug' => 'create_drivers',
                'module' => 'fleet',
                'description' => 'Add new drivers',
            ],
            [
                'name' => 'Edit Drivers',
                'slug' => 'edit_drivers',
                'module' => 'fleet',
                'description' => 'Edit driver information',
            ],
            [
                'name' => 'Delete Drivers',
                'slug' => 'delete_drivers',
                'module' => 'fleet',
                'description' => 'Delete drivers',
            ],
            
            [
                'name' => 'View Maintenance',
                'slug' => 'view_maintenance',
                'module' => 'fleet',
                'description' => 'View maintenance records',
            ],
            [
                'name' => 'Create Maintenance',
                'slug' => 'create_maintenance',
                'module' => 'fleet',
                'description' => 'Create maintenance records',
            ],
            [
                'name' => 'Edit Maintenance',
                'slug' => 'edit_maintenance',
                'module' => 'fleet',
                'description' => 'Edit maintenance records',
            ],
            [
                'name' => 'Delete Maintenance',
                'slug' => 'delete_maintenance',
                'module' => 'fleet',
                'description' => 'Delete maintenance records',
            ],
            
            // Accounts Module
            [
                'name' => 'View Customers',
                'slug' => 'view_customers',
                'module' => 'accounts',
                'description' => 'View customers',
            ],
            [
                'name' => 'Create Customers',
                'slug' => 'create_customers',
                'module' => 'accounts',
                'description' => 'Add new customers',
            ],
            [
                'name' => 'Edit Customers',
                'slug' => 'edit_customers',
                'module' => 'accounts',
                'description' => 'Edit customer information',
            ],
            [
                'name' => 'Delete Customers',
                'slug' => 'delete_customers',
                'module' => 'accounts',
                'description' => 'Delete customers',
            ],
            
            [
                'name' => 'View Invoices',
                'slug' => 'view_invoices',
                'module' => 'accounts',
                'description' => 'View invoices',
            ],
            [
                'name' => 'Create Invoices',
                'slug' => 'create_invoices',
                'module' => 'accounts',
                'description' => 'Create new invoices',
            ],
            [
                'name' => 'Edit Invoices',
                'slug' => 'edit_invoices',
                'module' => 'accounts',
                'description' => 'Edit invoices',
            ],
            [
                'name' => 'Delete Invoices',
                'slug' => 'delete_invoices',
                'module' => 'accounts',
                'description' => 'Delete invoices',
            ],
            
            [
                'name' => 'View Expenses',
                'slug' => 'view_expenses',
                'module' => 'accounts',
                'description' => 'View expenses',
            ],
            [
                'name' => 'Create Expenses',
                'slug' => 'create_expenses',
                'module' => 'accounts',
                'description' => 'Create expense records',
            ],
            [
                'name' => 'Edit Expenses',
                'slug' => 'edit_expenses',
                'module' => 'accounts',
                'description' => 'Edit expense records',
            ],
            [
                'name' => 'Delete Expenses',
                'slug' => 'delete_expenses',
                'module' => 'accounts',
                'description' => 'Delete expense records',
            ],
            
            // System Module
            [
                'name' => 'View Documents',
                'slug' => 'view_documents',
                'module' => 'system',
                'description' => 'View documents',
            ],
            [
                'name' => 'Create Documents',
                'slug' => 'create_documents',
                'module' => 'system',
                'description' => 'Upload documents',
            ],
            [
                'name' => 'Edit Documents',
                'slug' => 'edit_documents',
                'module' => 'system',
                'description' => 'Edit document information',
            ],
            [
                'name' => 'Delete Documents',
                'slug' => 'delete_documents',
                'module' => 'system',
                'description' => 'Delete documents',
            ],
            [
                'name' => 'Download Documents',
                'slug' => 'download_documents',
                'module' => 'system',
                'description' => 'Download documents',
            ],
            
            [
                'name' => 'View Reports',
                'slug' => 'view_reports',
                'module' => 'system',
                'description' => 'View reports',
            ],
            
            [
                'name' => 'View Users',
                'slug' => 'view_users',
                'module' => 'system',
                'description' => 'View users and permissions',
            ],
            [
                'name' => 'Create Users',
                'slug' => 'create_users',
                'module' => 'system',
                'description' => 'Create new users',
            ],
            [
                'name' => 'Edit Users',
                'slug' => 'edit_users',
                'module' => 'system',
                'description' => 'Edit user information',
            ],
            [
                'name' => 'Delete Users',
                'slug' => 'delete_users',
                'module' => 'system',
                'description' => 'Delete users',
            ],
            [
                'name' => 'Manage Permissions',
                'slug' => 'manage_permissions',
                'module' => 'system',
                'description' => 'Assign and manage user permissions',
            ],
            
            [
                'name' => 'View Settings',
                'slug' => 'view_settings',
                'module' => 'system',
                'description' => 'View system settings',
            ],
            [
                'name' => 'Edit Settings',
                'slug' => 'edit_settings',
                'module' => 'system',
                'description' => 'Edit system settings',
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }
    }
}
