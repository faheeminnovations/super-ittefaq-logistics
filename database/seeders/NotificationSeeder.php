<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Notification;
use App\Models\User;

class NotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get admin user - try multiple possible emails
        $admin = User::where('email', 'admin@local')
            ->orWhere('email', 'admin@example.com')
            ->orWhere('role', 'admin')
            ->first();
        
        if (!$admin) {
            // If no admin found, get first user
            $admin = User::first();
        }
        
        if (!$admin) {
            $this->command->warn('No users found. Skipping notification seeding.');
            return;
        }

        // Create sample notifications
        $notifications = [
            [
                'type' => 'save',
                'title' => 'New Booking Created',
                'message' => 'Job #1234 has been created for MANGA WAREHOUSE',
                'module' => 'billing',
                'data' => ['billing_id' => 1, 'customer_name' => 'MANGA WAREHOUSE'],
                'is_read' => false,
                'sent_email' => false,
                'sent_whatsapp' => false,
            ],
            [
                'type' => 'update',
                'title' => 'Billing Record Updated',
                'message' => 'Billing record #456 has been updated with new payment information',
                'module' => 'billing',
                'data' => ['billing_id' => 456, 'amount' => 25000],
                'is_read' => false,
                'sent_email' => true,
                'sent_whatsapp' => false,
            ],
            [
                'type' => 'delete',
                'title' => 'Vehicle Maintenance Deleted',
                'message' => 'Maintenance record for vehicle ABC-123 has been removed',
                'module' => 'maintenance',
                'data' => ['vehicle_no' => 'ABC-123', 'maintenance_id' => 789],
                'is_read' => true,
                'sent_email' => false,
                'sent_whatsapp' => false,
            ],
            [
                'type' => 'save',
                'title' => 'New Customer Added',
                'message' => 'Customer "MUBASHIR TRADERS" has been successfully added to the system',
                'module' => 'customer',
                'data' => ['customer_name' => 'MUBASHIR TRADERS', 'customer_id' => 101],
                'is_read' => false,
                'sent_email' => false,
                'sent_whatsapp' => true,
            ],
            [
                'type' => 'update',
                'title' => 'Driver Status Changed',
                'message' => 'Driver Ahmed Khan status has been updated to "On Duty"',
                'module' => 'driver',
                'data' => ['driver_name' => 'Ahmed Khan', 'status' => 'on_duty'],
                'is_read' => true,
                'sent_email' => true,
                'sent_whatsapp' => true,
            ],
        ];

        foreach ($notifications as $notification) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => $notification['type'],
                'title' => $notification['title'],
                'message' => $notification['message'],
                'module' => $notification['module'],
                'data' => $notification['data'],
                'is_read' => $notification['is_read'],
                'sent_email' => $notification['sent_email'],
                'sent_whatsapp' => $notification['sent_whatsapp'],
            ]);
        }

        $this->command->info('Sample notifications created successfully.');
    }
}
