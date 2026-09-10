#!/usr/bin/env bash
echo "Testing Academic Flow..."

php artisan tinker --execute "\
use App\\Models\\AcademicYear;\
use App\\Models\\Term;\
use App\\Models\\ClassModel;\
use App\\Models\\Subject;\
use App\\Models\\Student;\
\
\
// 1. Create Academic Year and Terms\
\
\$year = AcademicYear::create(['name' => '2024/2025', 'start_date' => '2024-09-01', 'end_date' => '2025-07-31', 'is_current' => true, 'status' => 'active']);\
echo 'Academic Year created: ' . \$year->name . PHP_EOL;\
\$terms = ['First Term', 'Second Term', 'Third Term'];\
foreach (\$terms as \$i => \$n) {\
    Term::create([\
        'academic_year_id' => \$year->id,\
        'name' => \$n,\
        'sequence' => \$i + 1,\
        'start_date' => now(),\
        'end_date' => now()->addMonths(3),\
        'is_current' => \$i === 0,\
        'status' => 'active'\
    ]);\
    echo 'Created term: ' . \$n . PHP_EOL;\
}\
\
// 2. Create a class, subject and student\
\$class = ClassModel::create(['name' => 'JSS 1', 'section' => 'A', 'full_name' => 'JSS 1 A', 'is_active' => true]);\
echo 'Class: ' . \$class->full_class_name . PHP_EOL;\
\$subject = Subject::create(['name' => 'Mathematics', 'code' => 'MATH', 'is_active' => true]);\
echo 'Subject: ' . \$subject->name . PHP_EOL;\
\$student = Student::create(['first_name' => 'Test', 'last_name' => 'Student', 'admission_number' => 'TST001', 'class_id' => \$class->id, 'is_active' => true]);\
echo 'Student: ' . \$student->full_name . PHP_EOL;\
\"

echo "Test script finished. Run 'php artisan migrate' first if necessary." 
