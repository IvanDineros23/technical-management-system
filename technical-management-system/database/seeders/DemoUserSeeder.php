<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DemoUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get role IDs
        $roles = DB::table('roles')->pluck('id', 'slug');

        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@gemarcph.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['admin'],
                'department' => 'Administration',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'marketing@gemarcph.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['marketing'],
                'department' => 'Marketing',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Juan Dela Cruz',
                'email' => 'technician@gemarcph.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['tech_personnel'],
                'department' => 'Technical',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Robert Gonzales',
                'email' => 'techhead@gemarcph.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['tech_head'],
                'department' => 'Technical',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Ana Reyes',
                'email' => 'signatory@gemarcph.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['signatory'],
                'department' => 'Management',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Carlos Mendoza',
                'email' => 'accounting@gemarcph.com',
                'password' => Hash::make('password'),
                'role_id' => $roles['accounting'],
                'department' => 'Accounting',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('users')->insert($users);
    }
}
