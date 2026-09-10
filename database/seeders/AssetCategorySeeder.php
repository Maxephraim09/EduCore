<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssetCategory;

class AssetCategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Electronics', 'code' => 'AST-ELEC', 'depreciation_rate' => 20, 'description' => 'Computers, laptops, printers, projectors'],
            ['name' => 'Furniture', 'code' => 'AST-FURN', 'depreciation_rate' => 10, 'description' => 'Desks, chairs, cabinets, shelves'],
            ['name' => 'Vehicles', 'code' => 'AST-VEHI', 'depreciation_rate' => 15, 'description' => 'Cars, buses, vans, trucks'],
            ['name' => 'Buildings', 'code' => 'AST-BLDG', 'depreciation_rate' => 5, 'description' => 'School buildings, classrooms, offices'],
            ['name' => 'Equipment', 'code' => 'AST-EQPT', 'depreciation_rate' => 12, 'description' => 'Lab equipment, sports equipment'],
            ['name' => 'Machinery', 'code' => 'AST-MACH', 'depreciation_rate' => 15, 'description' => 'Printing machines, generators'],
            ['name' => 'Software', 'code' => 'AST-SOFT', 'depreciation_rate' => 25, 'description' => 'Licenses, software subscriptions'],
        ];

        foreach ($categories as $category) {
            AssetCategory::create($category);
        }
    }
}