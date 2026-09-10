<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Grading;
use App\Models\GradeScale;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // ============ GRADING SETUP ============
        // Create default grading settings if not exists
        if (!Grading::exists()) {
            // Get admin user first
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

        // Create default grade scales if not exists
        if (!GradeScale::exists()) {
            $grades = [
                ['grade' => 'A', 'min_score' => 70, 'max_score' => 100, 'remark' => 'Excellent', 'color' => 'success', 'is_active' => true],
                ['grade' => 'B', 'min_score' => 60, 'max_score' => 69, 'remark' => 'Very Good', 'color' => 'primary', 'is_active' => true],
                ['grade' => 'C', 'min_score' => 50, 'max_score' => 59, 'remark' => 'Good', 'color' => 'info', 'is_active' => true],
                ['grade' => 'D', 'min_score' => 40, 'max_score' => 49, 'remark' => 'Pass', 'color' => 'warning', 'is_active' => true],
                ['grade' => 'F', 'min_score' => 0, 'max_score' => 39, 'remark' => 'Fail', 'color' => 'danger', 'is_active' => true],
            ];

            foreach ($grades as $grade) {
                GradeScale::create($grade);
            }
            $this->command->info('✅ Default grade scales created.');
        } else {
            $this->command->info('ℹ️ Grade scales already exist.');
        }

        // ============ USERS ============
        // Create admin user if not exists
        $admin = User::firstOrCreate(
            ['email' => 'admin@school.com'],
            [
                'name' => 'Admin User',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
                'remember_token' => Str::random(10),
            ]
        );
        $this->command->info('✅ Admin user created/verified.');

        // Create teacher user if not exists
        $teacher = User::firstOrCreate(
            ['email' => 'teacher@school.com'],
            [
                'name' => 'Teacher User',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'teacher',
                'is_active' => true,
                'remember_token' => Str::random(10),
            ]
        );
        $this->command->info('✅ Teacher user created/verified.');

        // ============ CLASSES ============
        // Check if classes table exists and create sample classes
        if (Schema::hasTable('classes') && DB::table('classes')->count() == 0) {
            $classes = [
                ['name' => 'Grade 7A', 'class_name' => '7A', 'full_class_name' => 'Grade 7A', 'is_active' => true],
                ['name' => 'Grade 7B', 'class_name' => '7B', 'full_class_name' => 'Grade 7B', 'is_active' => true],
                ['name' => 'Grade 8A', 'class_name' => '8A', 'full_class_name' => 'Grade 8A', 'is_active' => true],
                ['name' => 'Grade 8B', 'class_name' => '8B', 'full_class_name' => 'Grade 8B', 'is_active' => true],
                ['name' => 'Grade 9A', 'class_name' => '9A', 'full_class_name' => 'Grade 9A', 'is_active' => true],
                ['name' => 'Grade 9B', 'class_name' => '9B', 'full_class_name' => 'Grade 9B', 'is_active' => true],
                ['name' => 'Grade 10A', 'class_name' => '10A', 'full_class_name' => 'Grade 10A', 'is_active' => true],
                ['name' => 'Grade 10B', 'class_name' => '10B', 'full_class_name' => 'Grade 10B', 'is_active' => true],
                ['name' => 'Grade 11A', 'class_name' => '11A', 'full_class_name' => 'Grade 11A', 'is_active' => true],
                ['name' => 'Grade 11B', 'class_name' => '11B', 'full_class_name' => 'Grade 11B', 'is_active' => true],
                ['name' => 'Grade 12A', 'class_name' => '12A', 'full_class_name' => 'Grade 12A', 'is_active' => true],
                ['name' => 'Grade 12B', 'class_name' => '12B', 'full_class_name' => 'Grade 12B', 'is_active' => true],
            ];
            
            foreach ($classes as $class) {
                DB::table('classes')->insert(array_merge($class, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
            $this->command->info('✅ Created ' . count($classes) . ' classes.');
        }

        // ============ SUBJECTS ============
        if (Schema::hasTable('subjects') && DB::table('subjects')->count() == 0) {
            $subjects = [
                ['name' => 'Mathematics', 'code' => 'MATH101', 'description' => 'Basic Mathematics', 'is_active' => true],
                ['name' => 'English Language', 'code' => 'ENG101', 'description' => 'English Language', 'is_active' => true],
                ['name' => 'Physics', 'code' => 'PHY101', 'description' => 'Physics', 'is_active' => true],
                ['name' => 'Chemistry', 'code' => 'CHEM101', 'description' => 'Chemistry', 'is_active' => true],
                ['name' => 'Biology', 'code' => 'BIO101', 'description' => 'Biology', 'is_active' => true],
                ['name' => 'History', 'code' => 'HIS101', 'description' => 'History', 'is_active' => true],
                ['name' => 'Geography', 'code' => 'GEO101', 'description' => 'Geography', 'is_active' => true],
                ['name' => 'Economics', 'code' => 'ECO101', 'description' => 'Economics', 'is_active' => true],
                ['name' => 'Government', 'code' => 'GOV101', 'description' => 'Government', 'is_active' => true],
                ['name' => 'Literature', 'code' => 'LIT101', 'description' => 'Literature in English', 'is_active' => true],
                ['name' => 'CRS', 'code' => 'CRS101', 'description' => 'Christian Religious Studies', 'is_active' => true],
                ['name' => 'Islamic Studies', 'code' => 'ISL101', 'description' => 'Islamic Studies', 'is_active' => true],
                ['name' => 'French', 'code' => 'FRN101', 'description' => 'French Language', 'is_active' => true],
                ['name' => 'Computer Science', 'code' => 'CSC101', 'description' => 'Computer Science', 'is_active' => true],
                ['name' => 'Agricultural Science', 'code' => 'AGR101', 'description' => 'Agricultural Science', 'is_active' => true],
                ['name' => 'Physical Education', 'code' => 'PHE101', 'description' => 'Physical Education', 'is_active' => true],
            ];
            
            foreach ($subjects as $subject) {
                DB::table('subjects')->insert(array_merge($subject, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]));
            }
            $this->command->info('✅ Created ' . count($subjects) . ' subjects.');
        }

        // ============ STUDENTS ============
        // Skip student seeding to avoid the NOT NULL constraint error
        // You can add students manually or through a separate seeder
        $this->command->info('ℹ️ Skipping student seeding to avoid constraint errors.');
        $this->command->info('ℹ️ You can seed students separately if needed.');

        // ============ SUMMARY ============
        $this->command->info('═══════════════════════════════════════');
        $this->command->info('✅ Test data seeding completed!');
        $this->command->info('📊 Grading Settings: ' . (Grading::exists() ? '✅ Created' : '⚠️ Skipped'));
        $this->command->info('📊 Grade Scales: ' . (GradeScale::exists() ? '✅ Created' : '⚠️ Skipped'));
        $this->command->info('👤 Admin User: admin@school.com / password');
        $this->command->info('👤 Teacher User: teacher@school.com / password');
        $this->command->info('📚 Classes: ' . (Schema::hasTable('classes') ? DB::table('classes')->count() . ' created' : 'N/A'));
        $this->command->info('📖 Subjects: ' . (Schema::hasTable('subjects') ? DB::table('subjects')->count() . ' created' : 'N/A'));
        $this->command->info('═══════════════════════════════════════');
    }
}