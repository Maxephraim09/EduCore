<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            PermissionSeeder::class,
            SuperAdminSeeder::class,
            TestUsersSeeder::class,
        ]);

        // Create expense categories
        $expenseCategories = [
            ['name' => 'Utilities', 'code' => 'EXP-UTIL', 'is_active' => true],
            ['name' => 'Maintenance', 'code' => 'EXP-MAINT', 'is_active' => true],
            ['name' => 'Supplies', 'code' => 'EXP-SUPP', 'is_active' => true],
            ['name' => 'Travel', 'code' => 'EXP-TRAV', 'is_active' => true],
            ['name' => 'Training', 'code' => 'EXP-TRAIN', 'is_active' => true],
        ];

        foreach ($expenseCategories as $category) {
            DB::table('expense_categories')->insert($category);
        }

        // Create asset categories
        $assetCategories = [
            ['name' => 'Electronics', 'code' => 'AST-ELEC', 'depreciation_rate' => 20],
            ['name' => 'Furniture', 'code' => 'AST-FURN', 'depreciation_rate' => 10],
            ['name' => 'Vehicles', 'code' => 'AST-VEHI', 'depreciation_rate' => 15],
            ['name' => 'Buildings', 'code' => 'AST-BLDG', 'depreciation_rate' => 5],
        ];

        foreach ($assetCategories as $category) {
            DB::table('asset_categories')->insert($category);
        }

        // Create income categories
        $incomeCategories = [
            ['name' => 'Donations', 'code' => 'INC-DON'],
            ['name' => 'Grants', 'code' => 'INC-GRANT'],
            ['name' => 'Rentals', 'code' => 'INC-RENT'],
            ['name' => 'Investments', 'code' => 'INC-INV'],
        ];

        foreach ($incomeCategories as $category) {
            DB::table('income_categories')->insert($category);
        }

        // Create bank account
        DB::table('bank_accounts')->insert([
            'account_name' => 'Main Operating Account',
            'bank_name' => 'Example Bank',
            'account_number' => '1234567890',
            'ifsc_code' => 'EXMB123456',
            'current_balance' => 1000000,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
