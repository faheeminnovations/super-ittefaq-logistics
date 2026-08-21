<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \DB::table('customers')->insert([
            [
                'name' => 'Nexus Retail Ltd',
                'contact_email' => 'info@nexusretail.co.uk',
                'city' => 'Manchester',
                'credit_limit' => 15000.00,
                'balance' => 4200.00,
                'status' => 'active',
                'phone' => '0161 123 4567',
                'address' => '123 Retail Park, Manchester M1 1AA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'BuildCo Supplies',
                'contact_email' => 'accounts@buildco.com',
                'city' => 'Birmingham',
                'credit_limit' => 25000.00,
                'balance' => 11800.00,
                'status' => 'active',
                'phone' => '0121 987 6543',
                'address' => '456 Construction Way, Birmingham B1 2BB',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'FreshMart Foods',
                'contact_email' => 'ops@freshmart.co.uk',
                'city' => 'London',
                'credit_limit' => 10000.00,
                'balance' => 9750.00,
                'status' => 'near_limit',
                'phone' => '020 7654 3210',
                'address' => '789 Food Street, London E1 4AB',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Nova Electronics',
                'contact_email' => 'billing@novaelec.com',
                'city' => 'Leeds',
                'credit_limit' => 20000.00,
                'balance' => 2300.00,
                'status' => 'active',
                'phone' => '0113 456 7890',
                'address' => '321 Tech Road, Leeds LS1 1RB',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Harborview Ltd',
                'contact_email' => 'finance@harborview.com',
                'city' => 'Glasgow',
                'credit_limit' => 8000.00,
                'balance' => 8420.00,
                'status' => 'on_hold',
                'phone' => '0141 234 5678',
                'address' => '654 Harbor Street, Glasgow G1 2TR',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
