<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a user to assign as created_by
        $user = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );

        // Create sample customers
        Customer::factory(20)->create([
            'created_by' => $user->id,
        ]);

        // Create some specific customers for testing
        Customer::create([
            'name' => 'John Smith',
            'company_name' => 'Tech Solutions Inc.',
            'email' => 'john@techsolutions.com',
            'postcode' => 'SW1A 1AA',
            'phone' => '+44 20 7123 4567',
            'address' => '10 Downing Street, London, UK',
            'created_by' => $user->id,
        ]);

        Customer::create([
            'name' => 'Sarah Johnson',
            'company_name' => 'Creative Design Studio',
            'email' => 'sarah@creativedesign.com',
            'postcode' => 'M1 1AA',
            'phone' => '+44 161 123 4567',
            'address' => '25 Market Street, Manchester, UK',
            'created_by' => $user->id,
        ]);
    }
} 