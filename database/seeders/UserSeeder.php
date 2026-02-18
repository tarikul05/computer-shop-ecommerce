<?php

namespace Database\Seeders;

use App\Models\User;
use App\Modules\User\Models\Address;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin User
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@computerstore.com',
            'phone' => '01700000000',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        Address::create([
            'user_id' => $admin->id,
            'type' => 'both',
            'name' => 'Admin User',
            'phone' => '01700000000',
            'address_line_1' => 'IT Building, Level 5',
            'address_line_2' => 'Motijheel',
            'city' => 'Dhaka',
            'state' => 'Dhaka',
            'postal_code' => '1000',
            'country' => 'Bangladesh',
            'is_default' => true,
        ]);

        // Create Staff User
        $staff = User::create([
            'name' => 'Staff Member',
            'email' => 'staff@computerstore.com',
            'phone' => '01700000001',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        // Create Regular Customers
        $customers = [
            [
                'name' => 'Rahim Ahmed',
                'email' => 'rahim@example.com',
                'phone' => '01712345678',
                'address' => [
                    'address_line_1' => 'House 25, Road 5',
                    'address_line_2' => 'Dhanmondi',
                    'city' => 'Dhaka',
                    'postal_code' => '1205',
                ],
            ],
            [
                'name' => 'Karim Hassan',
                'email' => 'karim@example.com',
                'phone' => '01812345678',
                'address' => [
                    'address_line_1' => 'Flat 3B, Sunshine Apartments',
                    'address_line_2' => 'Uttara Sector 7',
                    'city' => 'Dhaka',
                    'postal_code' => '1230',
                ],
            ],
            [
                'name' => 'Fatima Begum',
                'email' => 'fatima@example.com',
                'phone' => '01912345678',
                'address' => [
                    'address_line_1' => '45/A, GEC Circle',
                    'address_line_2' => 'Chattogram',
                    'city' => 'Chattogram',
                    'postal_code' => '4000',
                ],
            ],
            [
                'name' => 'Mohammad Ali',
                'email' => 'ali@example.com',
                'phone' => '01612345678',
                'address' => [
                    'address_line_1' => 'Shop 12, Computer City',
                    'address_line_2' => 'Elephant Road',
                    'city' => 'Dhaka',
                    'postal_code' => '1205',
                ],
            ],
            [
                'name' => 'Ayesha Khan',
                'email' => 'ayesha@example.com',
                'phone' => '01512345678',
                'address' => [
                    'address_line_1' => 'House 10, Block C',
                    'address_line_2' => 'Bashundhara R/A',
                    'city' => 'Dhaka',
                    'postal_code' => '1229',
                ],
            ],
        ];

        foreach ($customers as $customerData) {
            $user = User::create([
                'name' => $customerData['name'],
                'email' => $customerData['email'],
                'phone' => $customerData['phone'],
                'password' => Hash::make('password'),
                'role' => 'customer',
                'status' => 'active',
                'email_verified_at' => now(),
            ]);

            Address::create([
                'user_id' => $user->id,
                'type' => 'both',
                'name' => $customerData['name'],
                'phone' => $customerData['phone'],
                'address_line_1' => $customerData['address']['address_line_1'],
                'address_line_2' => $customerData['address']['address_line_2'],
                'city' => $customerData['address']['city'],
                'state' => $customerData['address']['city'],
                'postal_code' => $customerData['address']['postal_code'],
                'country' => 'Bangladesh',
                'is_default' => true,
            ]);
        }

        $this->command->info('Users seeded: 1 admin, 1 staff, 5 customers');
    }
}
