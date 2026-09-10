<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GradeScale;

class GradeScaleSeeder extends Seeder
{
    public function run()
    {
        $grades = [
            ['name' => 'Excellent', 'grade' => 'A', 'min_score' => 70, 'max_score' => 100, 'remark' => 'Excellent', 'color' => '#10b981'],
            ['name' => 'Very Good', 'grade' => 'B', 'min_score' => 60, 'max_score' => 69, 'remark' => 'Very Good', 'color' => '#3b82f6'],
            ['name' => 'Good', 'grade' => 'C', 'min_score' => 50, 'max_score' => 59, 'remark' => 'Good', 'color' => '#f59e0b'],
            ['name' => 'Pass', 'grade' => 'D', 'min_score' => 40, 'max_score' => 49, 'remark' => 'Pass', 'color' => '#8b5cf6'],
            ['name' => 'Fail', 'grade' => 'F', 'min_score' => 0, 'max_score' => 39, 'remark' => 'Fail', 'color' => '#ef4444'],
        ];

        foreach ($grades as $grade) {
            GradeScale::create($grade);
        }
    }
}