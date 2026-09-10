<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Student;
use App\Models\Employee;
use App\Models\ClassModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class TestUsersSeeder extends Seeder
{
    public function run()
    {
        // Check if tables exist
        $hasEmployees = Schema::hasTable('employees');
        $hasStudents = Schema::hasTable('students');
        $hasClasses = Schema::hasTable('classes');

        // Get column listings
        $employeeColumns = $hasEmployees ? Schema::getColumnListing('employees') : [];
        $studentColumns = $hasStudents ? Schema::getColumnListing('students') : [];

        // ==================== CREATE USERS ====================
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'admin@school.com',
'role' => 'super_admin',
            ],
            [
                'name' => 'School Administrator',
                'email' => 'admin@example.com',
                'role' => 'admin',
            ],
            [
                'name' => 'John Teacher',
                'email' => 'teacher@school.com',
                'role' => 'teacher',
            ],
            [
                'name' => 'Mary Accountant',
                'email' => 'accountant@school.com',
                'role' => 'accountant',
            ],
            [
                'name' => 'David Frontdesk',
                'email' => 'frontdesk@school.com',
                'role' => 'frontdesk',
            ],
            [
                'name' => 'Alice Student',
                'email' => 'student1@school.com',
                'role' => 'student',
            ],
            [
                'name' => 'Bob Student',
                'email' => 'student2@school.com',
                'role' => 'student',
            ],
            [
                'name' => 'Carol Student',
                'email' => 'student3@school.com',
                'role' => 'student',
            ],
            [
                'name' => 'John Parent',
                'email' => 'parent1@school.com',
                'role' => 'parent',
            ],
            [
                'name' => 'Jane Parent',
                'email' => 'parent2@school.com',
                'role' => 'parent',
            ],
            [
                'name' => 'Mike Parent',
                'email' => 'parent3@school.com',
                'role' => 'parent',
            ],
        ];

        $userIds = [];
        foreach ($users as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'role' => $userData['role'],
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );
            $userIds[$userData['email']] = $user->id;
            $this->command->info("✅ Created user: {$userData['name']} ({$userData['email']})");
        }

        // ==================== CREATE EMPLOYEES ====================
        if ($hasEmployees) {
            $employees = [
                [
                    'employee_id' => 'EMP-TCH-001',
                    'first_name' => 'John',
                    'last_name' => 'Teacher',
                    'email' => 'teacher@school.com',
                    'phone' => '08012345678',
                    'address' => '123 Teacher Street, Lagos',
                    'position' => 'Senior Teacher',
                    'department' => 'Science',
                    'base_salary' => 150000.00,
                    'allowances' => 50000.00,
                    'joining_date' => '2015-09-01',
                    'bank_name' => 'GTBank',
                    'account_number' => '0123456789',
                    'ifsc_code' => 'GTB001',
                    'pan_number' => 'PAN123456',
                    'is_active' => true,
                ],
                [
                    'employee_id' => 'EMP-ACC-001',
                    'first_name' => 'Mary',
                    'last_name' => 'Accountant',
                    'email' => 'accountant@school.com',
                    'phone' => '08087654321',
                    'address' => '456 Finance Avenue, Lagos',
                    'position' => 'Chief Accountant',
                    'department' => 'Finance',
                    'base_salary' => 200000.00,
                    'allowances' => 75000.00,
                    'joining_date' => '2016-01-15',
                    'bank_name' => 'Zenith Bank',
                    'account_number' => '9876543210',
                    'ifsc_code' => 'ZEN001',
                    'pan_number' => 'PAN789012',
                    'is_active' => true,
                ],
                [
                    'employee_id' => 'EMP-FDK-001',
                    'first_name' => 'David',
                    'last_name' => 'Frontdesk',
                    'email' => 'frontdesk@school.com',
                    'phone' => '08098765432',
                    'address' => '789 Frontdesk Road, Lagos',
                    'position' => 'Front Desk Officer',
                    'department' => 'Administration',
                    'base_salary' => 80000.00,
                    'allowances' => 20000.00,
                    'joining_date' => '2018-06-01',
                    'bank_name' => 'First Bank',
                    'account_number' => '5432109876',
                    'ifsc_code' => 'FBN001',
                    'pan_number' => 'PAN345678',
                    'is_active' => true,
                ],
            ];

            foreach ($employees as $employeeData) {
                $filteredData = [];
                foreach ($employeeData as $key => $value) {
                    if (in_array($key, $employeeColumns)) {
                        $filteredData[$key] = $value;
                    }
                }
                
                if (!empty($filteredData)) {
                    Employee::updateOrCreate(
                        ['employee_id' => $employeeData['employee_id']],
                        $filteredData
                    );
                    $this->command->info("✅ Created employee: {$employeeData['first_name']} {$employeeData['last_name']}");
                }
            }
        }

        // ==================== CREATE CLASSES ====================
        $class = null;
        if ($hasClasses) {
            $class = ClassModel::firstOrCreate(
                ['code' => 'JSS1A'],
                [
                    'name' => 'JSS 1',
                    'section' => 'A',
                    'full_name' => 'JSS 1 A',
                    'capacity' => 40,
                    'is_active' => true,
                    'academic_year' => date('Y') . '/' . (date('Y') + 1),
                ]
            );
            $this->command->info("✅ Created class: JSS 1 A (ID: {$class->id})");
        }

        // ==================== CREATE STUDENTS ====================
        if ($hasStudents && $class) {
            $className = $class->full_name ?? $class->name ?? 'JSS 1 A';

            $students = [
                [
                    'admission_number' => 'STU-2024-001',
                    'first_name' => 'Alice',
                    'last_name' => 'Student',
                    'middle_name' => 'Grace',
                    'email' => 'student1@school.com',
                    'phone' => '08012345679',
                    'phone_number' => '08012345679',
                    'address' => '123 Student Village, Lagos',
                    'home_address' => '123 Student Village, Lagos',
                    'date_of_birth' => '2012-05-10',
                    'gender' => 'Female',
                    'class' => $className,
                    'class_id' => $class->id,
                    'section' => 'A',
                    'roll_number' => '001',
                    'academic_year' => date('Y') . '/' . (date('Y') + 1),
                    'admission_date' => date('Y-m-d'),
                    'guardian_name' => 'Mr. John Student',
                    'guardian_phone' => '08012345680',
                    'guardian_email' => 'parent1@school.com',
                    'parent_name' => 'Mr. John Student',
                    'parent_phone' => '08012345680',
                    'parent_email' => 'parent1@school.com',
                    'father_name' => 'John Student',
                    'father_phone' => '08012345680',
                    'father_email' => 'parent1@school.com',
                    'mother_name' => 'Jane Student',
                    'mother_phone' => '08012345681',
                    'mother_email' => 'jane@school.com',
                    'nationality' => 'Nigerian',
                    'state_of_origin' => 'Lagos',
                    'local_government' => 'Ikeja',
                    'religion' => 'Christianity',
                    'blood_group' => 'A+',
                    'is_active' => true,
                    'total_fees' => 250000.00,
                    'paid_fees' => 100000.00,
                    'due_fees' => 150000.00,
                ],
                [
                    'admission_number' => 'STU-2024-002',
                    'first_name' => 'Bob',
                    'last_name' => 'Student',
                    'middle_name' => 'James',
                    'email' => 'student2@school.com',
                    'phone' => '08012345681',
                    'phone_number' => '08012345681',
                    'address' => '456 Student Avenue, Lagos',
                    'home_address' => '456 Student Avenue, Lagos',
                    'date_of_birth' => '2011-08-15',
                    'gender' => 'Male',
                    'class' => $className,
                    'class_id' => $class->id,
                    'section' => 'A',
                    'roll_number' => '002',
                    'academic_year' => date('Y') . '/' . (date('Y') + 1),
                    'admission_date' => date('Y-m-d'),
                    'guardian_name' => 'Mrs. Jane Student',
                    'guardian_phone' => '08012345682',
                    'guardian_email' => 'parent2@school.com',
                    'parent_name' => 'Mrs. Jane Student',
                    'parent_phone' => '08012345682',
                    'parent_email' => 'parent2@school.com',
                    'father_name' => 'Bob Student Sr.',
                    'father_phone' => '08012345683',
                    'father_email' => 'bob.sr@school.com',
                    'mother_name' => 'Jane Student',
                    'mother_phone' => '08012345684',
                    'mother_email' => 'jane.student@school.com',
                    'nationality' => 'Nigerian',
                    'state_of_origin' => 'Ogun',
                    'local_government' => 'Abeokuta',
                    'religion' => 'Christianity',
                    'blood_group' => 'O+',
                    'is_active' => true,
                    'total_fees' => 250000.00,
                    'paid_fees' => 150000.00,
                    'due_fees' => 100000.00,
                ],
                [
                    'admission_number' => 'STU-2024-003',
                    'first_name' => 'Carol',
                    'last_name' => 'Student',
                    'middle_name' => 'Elizabeth',
                    'email' => 'student3@school.com',
                    'phone' => '08012345683',
                    'phone_number' => '08012345683',
                    'address' => '789 Student Lane, Lagos',
                    'home_address' => '789 Student Lane, Lagos',
                    'date_of_birth' => '2012-11-20',
                    'gender' => 'Female',
                    'class' => $className,
                    'class_id' => $class->id,
                    'section' => 'A',
                    'roll_number' => '003',
                    'academic_year' => date('Y') . '/' . (date('Y') + 1),
                    'admission_date' => date('Y-m-d'),
                    'guardian_name' => 'Mr. Mike Student',
                    'guardian_phone' => '08012345684',
                    'guardian_email' => 'parent3@school.com',
                    'parent_name' => 'Mr. Mike Student',
                    'parent_phone' => '08012345684',
                    'parent_email' => 'parent3@school.com',
                    'father_name' => 'Mike Student',
                    'father_phone' => '08012345685',
                    'father_email' => 'mike@school.com',
                    'mother_name' => 'Mary Student',
                    'mother_phone' => '08012345686',
                    'mother_email' => 'mary@school.com',
                    'nationality' => 'Nigerian',
                    'state_of_origin' => 'Oyo',
                    'local_government' => 'Ibadan',
                    'religion' => 'Islam',
                    'blood_group' => 'B+',
                    'is_active' => true,
                    'total_fees' => 250000.00,
                    'paid_fees' => 50000.00,
                    'due_fees' => 200000.00,
                ],
            ];

            foreach ($students as $studentData) {
                $filteredData = [];
                foreach ($studentData as $key => $value) {
                    if (in_array($key, $studentColumns)) {
                        $filteredData[$key] = $value;
                    }
                }
                
                if (!empty($filteredData)) {
                    Student::updateOrCreate(
                        ['admission_number' => $studentData['admission_number']],
                        $filteredData
                    );
                    $this->command->info("✅ Created student: {$studentData['first_name']} {$studentData['last_name']}");
                } else {
                    $this->command->error("❌ Failed to create student: {$studentData['first_name']} - No matching columns found");
                }
            }
        }

        // ==================== SUMMARY ====================
        $this->command->newLine();
        $this->command->info('✅ All test users created successfully!');
        $this->command->newLine();
        $this->command->info('🔑 All passwords: password');
        $this->command->newLine();
        $this->command->info('==================== LOGIN CREDENTIALS ====================');
        $this->command->info('📧 SUPER ADMIN: admin@school.com');
        $this->command->info('📧 ADMIN: admin@example.com');
        $this->command->info('📧 TEACHER: teacher@school.com');
        $this->command->info('📧 ACCOUNTANT: accountant@school.com');
        $this->command->info('📧 FRONTDESK: frontdesk@school.com');
        $this->command->info('📧 STUDENT 1: student1@school.com');
        $this->command->info('📧 STUDENT 2: student2@school.com');
        $this->command->info('📧 STUDENT 3: student3@school.com');
        $this->command->info('📧 PARENT 1: parent1@school.com');
        $this->command->info('📧 PARENT 2: parent2@school.com');
        $this->command->info('📧 PARENT 3: parent3@school.com');
        $this->command->info('============================================================');
        $this->command->newLine();
        $this->command->info('📍 Login URL: http://127.0.0.1:8000/login');
    }
}