<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Grading;
use App\Models\GradeScale;
use App\Models\User;
use Illuminate\Support\Facades\Schema;

class GradingSeeder extends Seeder
{
    public function run()
    {
        // Check if tables exist
        if (!Schema::hasTable('gradings') || !Schema::hasTable('grade_scales')) {
            $this->command->error('❌ Required tables do not exist. Please run migrations first.');
            return;
        }

        // Create default grading settings
        if (!Grading::exists()) {
            $admin = User::where('email', 'admin@school.com')->first();
            
            Grading::create([
                'ca1_max' => 10,
                'ca2_max' => 10,
                'ca3_max' => 10,
                'exam_max' => 60,
                'practical_max' => 10,
                'passing_marks' => 40,
                'has_practical' => true,
                'total_max' => 100,
                'created_by' => $admin ? $admin->id : null,
            ]);
            $this->command->info('✅ Default grading settings created.');
        } else {
            $this->command->info('ℹ️ Grading settings already exist.');
        }

        // Create grade scales
        if (!GradeScale::exists()) {
            $grades = [
                [
                    'grade' => 'A',
                    'min_score' => 70,
                    'max_score' => 100,
                    'remark' => 'Excellent',
                    'color' => 'success',
                    'is_active' => true
                ],
                [
                    'grade' => 'B',
                    'min_score' => 60,
                    'max_score' => 69,
                    'remark' => 'Very Good',
                    'color' => 'primary',
                    'is_active' => true
                ],
                [
                    'grade' => 'C',
                    'min_score' => 50,
                    'max_score' => 59,
                    'remark' => 'Good',
                    'color' => 'info',
                    'is_active' => true
                ],
                [
                    'grade' => 'D',
                    'min_score' => 40,
                    'max_score' => 49,
                    'remark' => 'Pass',
                    'color' => 'warning',
                    'is_active' => true
                ],
                [
                    'grade' => 'F',
                    'min_score' => 0,
                    'max_score' => 39,
                    'remark' => 'Fail',
                    'color' => 'danger',
                    'is_active' => true
                ],
            ];

            foreach ($grades as $grade) {
                GradeScale::create($grade);
            }
            $this->command->info('✅ Default grade scales created.');
        } else {
            $this->command->info('ℹ️ Grade scales already exist.');
        }

        // Summary
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('✅ Grading seeder completed!');
        $this->command->info('📊 Grading Settings: ' . (Grading::exists() ? '✅ Created' : '⚠️ Skipped'));
        $this->command->info('📊 Grade Scales: ' . (GradeScale::exists() ? '✅ Created' : '⚠️ Skipped'));
        $this->command->info('═══════════════════════════════════════');
    }
}