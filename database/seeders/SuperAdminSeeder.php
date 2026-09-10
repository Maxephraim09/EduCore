<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->updateOrInsert(
            ['email' => 'admin@financialsystem.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'is_active' => true,
                'is_verified' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }
}
