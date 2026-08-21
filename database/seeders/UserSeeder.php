<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('users')->insert([
            [
                'name' => 'Admin User',
                'email' => 'admin@ittefaq.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Sana Akhtar',
                'email' => 'sana@superittefaq.co.uk',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Usman Bhatti',
                'email' => 'usman@superittefaq.co.uk',
                'password' => bcrypt('password'),
                'role' => 'dispatcher',
                'status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nadia Zafar',
                'email' => 'nadia@superittefaq.co.uk',
                'password' => bcrypt('password'),
                'role' => 'accounts',
                'status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rizwan Khalid',
                'email' => 'rizwan@superittefaq.co.uk',
                'password' => bcrypt('password'),
                'role' => 'manager',
                'status' => 'suspended',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
