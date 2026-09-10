<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\ClassModel;
use App\Models\FeePayment;
use App\Models\Student;
use App\Models\Subject;
use App\Models\SystemSetting;
use App\Models\Term;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PromotionAndReceiptTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('classes')) {
            Schema::create('classes', function ($table) {
                $table->id();
                $table->string('name');
                $table->string('code')->nullable();
                $table->string('full_name')->nullable();
                $table->string('section')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('academic_years')) {
            Schema::create('academic_years', function ($table) {
                $table->id();
                $table->string('name');
                $table->date('start_date');
                $table->date('end_date');
                $table->boolean('is_current')->default(false);
                $table->string('status')->default('active');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('terms')) {
            Schema::create('terms', function ($table) {
                $table->id();
                $table->foreignId('academic_year_id');
                $table->string('name');
                $table->string('slug');
                $table->date('start_date');
                $table->date('end_date');
                $table->boolean('is_current')->default(false);
                $table->string('status')->default('active');
                $table->integer('sequence')->default(1);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('subjects')) {
            Schema::create('subjects', function ($table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('exams')) {
            Schema::create('exams', function ($table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->enum('type', ['continuous_assessment', 'mid_term', 'end_term', 'promotion'])->nullable();
                $table->string('term')->nullable();
                $table->string('academic_year')->nullable();
                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('results')) {
            Schema::create('results', function ($table) {
                $table->id();
                $table->foreignId('student_id');
                $table->foreignId('exam_id');
                $table->foreignId('subject_id');
                $table->foreignId('class_id');
                $table->integer('total_score')->default(0);
                $table->string('grade')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('students')) {
            Schema::create('students', function ($table) {
                $table->id();
                $table->foreignId('class_id')->nullable();
                $table->string('admission_number')->nullable();
                $table->string('first_name');
                $table->string('last_name');
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->text('address')->nullable();
                $table->date('date_of_birth')->nullable();
                $table->string('class')->nullable();
                $table->string('parent_name')->nullable();
                $table->string('parent_phone')->nullable();
                $table->string('parent_email')->nullable();
                $table->string('academic_year')->nullable();
                $table->boolean('is_active')->default(true);
                $table->decimal('total_fees', 12, 2)->default(0);
                $table->decimal('paid_fees', 12, 2)->default(0);
                $table->decimal('due_fees', 12, 2)->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('users')) {
            Schema::create('users', function ($table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->string('role')->default('student');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('system_settings')) {
            Schema::create('system_settings', function ($table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->string('type')->default('text');
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('fee_payments')) {
            Schema::create('fee_payments', function ($table) {
                $table->id();
                $table->foreignId('student_id');
                $table->string('receipt_number')->unique();
                $table->string('transaction_ref')->nullable();
                $table->string('transaction_id')->nullable();
                $table->decimal('amount', 12, 2)->default(0);
                $table->decimal('amount_paid', 12, 2)->default(0);
                $table->decimal('balance', 12, 2)->default(0);
                $table->date('payment_date')->nullable();
                $table->string('payment_method')->nullable();
                $table->string('fee_type')->nullable();
                $table->string('term')->nullable();
                $table->string('academic_year')->nullable();
                $table->string('payment_status')->default('pending');
                $table->string('status')->default('completed');
                $table->foreignId('received_by')->nullable();
                $table->json('payment_details')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('paystack_transactions')) {
            Schema::create('paystack_transactions', function ($table) {
                $table->id();
                $table->foreignId('payment_id');
                $table->string('reference')->nullable();
                $table->decimal('amount', 12, 2)->default(0);
                $table->string('status')->default('pending');
                $table->timestamps();
            });
        }
    }

    public function test_promotions_preview_lists_eligible_students(): void
    {
        SystemSetting::setValue('promotion_enabled', 'true');
        SystemSetting::setValue('promotion_min_average_score', '50');
        SystemSetting::setValue('promotion_max_absenteeism', '10');
        SystemSetting::setValue('promotion_min_passing_subjects', '7');
        SystemSetting::setValue('promotion_core_subjects', 'English, Mathematics');
        SystemSetting::setValue('promotion_auto_promote', 'true');

        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('secret'),
            'role' => 'super_admin',
        ]);

        $academicYear = AcademicYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-09-01',
            'end_date' => '2026-07-31',
            'is_current' => true,
            'status' => 'active',
        ]);

        $term = Term::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'Third Term',
            'slug' => 'third-term',
            'start_date' => '2026-05-01',
            'end_date' => '2026-07-31',
            'is_current' => true,
            'status' => 'active',
            'sequence' => 3,
        ]);

        $class = ClassModel::create([
            'name' => 'JSS 2',
            'code' => 'JSS2',
            'full_name' => 'JSS 2',
            'section' => 'A',
            'is_active' => true,
        ]);

        $nextClass = ClassModel::create([
            'name' => 'JSS 3',
            'code' => 'JSS3',
            'full_name' => 'JSS 3',
            'section' => 'A',
            'is_active' => true,
        ]);

        $student = Student::create([
            'class_id' => $class->id,
            'class' => $class->full_class_name,
            'admission_number' => '001',
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'email' => 'ada@example.com',
            'phone' => '08000000001',
            'address' => '123 Test Street',
            'date_of_birth' => '2010-01-01',
            'parent_name' => 'Mr. Lovelace',
            'parent_phone' => '08000000099',
            'parent_email' => 'parent@example.com',
            'is_active' => true,
        ]);

        $subject = Subject::create(['code' => 'ENG', 'name' => 'English']);
        $math = Subject::create(['code' => 'MATH', 'name' => 'Mathematics']);
        $exam = \App\Models\Exam::create([
            'name' => 'Promotion Exam',
            'academic_year' => $academicYear->name,
            'term' => $term->name,
            'type' => 'promotion',
        ]);

        $student->results()->create([
            'exam_id' => $exam->id,
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'total_score' => 72,
            'grade' => 'A',
        ]);
        $student->results()->create([
            'exam_id' => $exam->id,
            'subject_id' => $math->id,
            'class_id' => $class->id,
            'total_score' => 66,
            'grade' => 'B',
        ]);

        $this->actingAs($user);

        $response = $this->get('/promotions');

        $response->assertOk();
        $response->assertSee('Ada Lovelace');
        $response->assertSee('Eligible');
        $response->assertSee('JSS 2');
        $response->assertSee('JSS 3');
    }

    public function test_selected_students_can_be_promoted_to_next_class(): void
    {
        SystemSetting::setValue('promotion_enabled', 'true');
        SystemSetting::setValue('promotion_min_average_score', '50');
        SystemSetting::setValue('promotion_max_absenteeism', '10');
        SystemSetting::setValue('promotion_min_passing_subjects', '7');
        SystemSetting::setValue('promotion_core_subjects', 'English, Mathematics');
        SystemSetting::setValue('promotion_auto_promote', 'true');

        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin2@example.com',
            'password' => bcrypt('secret'),
            'role' => 'super_admin',
        ]);

        $academicYear = AcademicYear::create([
            'name' => '2025/2026',
            'start_date' => '2025-09-01',
            'end_date' => '2026-07-31',
            'is_current' => true,
            'status' => 'active',
        ]);

        $term = Term::create([
            'academic_year_id' => $academicYear->id,
            'name' => 'Third Term',
            'slug' => 'third-term',
            'start_date' => '2026-05-01',
            'end_date' => '2026-07-31',
            'is_current' => true,
            'status' => 'active',
            'sequence' => 3,
        ]);

        $class = ClassModel::create([
            'name' => 'JSS 2',
            'code' => 'JSS2',
            'full_name' => 'JSS 2',
            'section' => 'A',
            'is_active' => true,
        ]);

        $nextClass = ClassModel::create([
            'name' => 'JSS 3',
            'code' => 'JSS3',
            'full_name' => 'JSS 3',
            'section' => 'A',
            'is_active' => true,
        ]);

        $student = Student::create([
            'class_id' => $class->id,
            'class' => $class->full_class_name,
            'admission_number' => '003',
            'first_name' => 'Alan',
            'last_name' => 'Turing',
            'email' => 'alan@example.com',
            'phone' => '08000000003',
            'address' => '789 Test Lane',
            'date_of_birth' => '2010-01-01',
            'parent_name' => 'Mr. Turing',
            'parent_phone' => '08000000098',
            'parent_email' => 'parent2@example.com',
            'is_active' => true,
        ]);

        $subject = Subject::create(['name' => 'English']);
        $math = Subject::create(['name' => 'Mathematics']);
        $exam = \App\Models\Exam::create([
            'name' => 'Promotion Exam',
            'academic_year' => $academicYear->name,
            'term' => 'Third Term',
            'type' => 'promotion',
        ]);

        $student->results()->create([
            'exam_id' => $exam->id,
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'total_score' => 72,
            'grade' => 'A',
        ]);
        $student->results()->create([
            'exam_id' => $exam->id,
            'subject_id' => $math->id,
            'class_id' => $class->id,
            'total_score' => 66,
            'grade' => 'B',
        ]);

        $this->actingAs($user);

        $response = $this->post('/promotions/promote', ['student_ids' => [$student->id]]);

        $response->assertRedirect();

        $student->refresh();
        $this->assertEquals($nextClass->id, $student->class_id);
        $this->assertEquals($nextClass->full_class_name, $student->class);
        $this->assertEquals('2026/2027', $student->academic_year);
    }

    public function test_receipt_verification_page_shows_receipt_details(): void
    {
        SystemSetting::setValue('school_name', 'Bright Future School');
        SystemSetting::setValue('school_address', '12 School Road');

        $student = Student::create([
            'class' => 'JSS 1',
            'admission_number' => '002',
            'first_name' => 'Grace',
            'last_name' => 'Hopper',
            'email' => 'grace@example.com',
            'phone' => '08000000002',
            'address' => '456 Test Avenue',
            'date_of_birth' => '2010-01-01',
            'parent_name' => 'Mrs. Hopper',
            'parent_phone' => '08000000097',
            'parent_email' => 'parent3@example.com',
            'is_active' => true,
        ]);

        $payment = FeePayment::create([
            'student_id' => $student->id,
            'receipt_number' => 'RCP-1001',
            'transaction_ref' => 'TX-1001',
            'amount' => 15000,
            'amount_paid' => 15000,
            'balance' => 0,
            'payment_date' => '2026-08-01',
            'payment_method' => 'paystack',
            'fee_type' => 'School Fees',
            'payment_status' => 'success',
            'term' => 'Third Term',
            'academic_year' => '2025/2026',
            'payment_details' => ['fee_type' => 'School Fees'],
        ]);

        $response = $this->get('/verify/receipt/' . $payment->id);

        $response->assertOk();
        $response->assertSee('Bright Future School');
        $response->assertSee('RCP-1001');
        $response->assertSee('Grace Hopper');
    }
}
