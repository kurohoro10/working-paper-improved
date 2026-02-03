<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@endurego.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ADMIN->value,
            'phone' => '+1234567890',
            'is_active' => true,
        ]);

        // Create Internal Employee
        User::create([
            'name' => 'EndureGo Employee',
            'email' => 'employee@endurego.com',
            'password' => Hash::make('password'),
            'role' => UserRole::ENDUREGO_INTERNAL->value,
            'phone' => '+1234567891',
            'is_active' => true,
        ]);

        // Create Sample Clients
        User::create([
            'name' => 'John Smith',
            'email' => 'john.smith@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::CLIENT->value,
            'phone' => '+1234567892',
            'company_name' => 'Smith Enterprises Pty Ltd',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'Jane Doe',
            'email' => 'jane.doe@example.com',
            'password' => Hash::make('password'),
            'role' => UserRole::CLIENT->value,
            'phone' => '+1234567893',
            'company_name' => 'Doe Consulting',
            'is_active' => true,
        ]);

        User::create([
            'name' => 'ABC Corporation',
            'email' => 'accounts@abccorp.com',
            'password' => Hash::make('password'),
            'role' => UserRole::CLIENT->value,
            'phone' => '+1234567894',
            'company_name' => 'ABC Corporation Pty Ltd',
            'is_active' => true,
        ]);

        $this->command->info('Users seeded successfully!');
        $this->command->info('Default password for all users: password');
    }
}
