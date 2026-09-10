<?php

namespace Tests\Feature;

use App\Models\ClassModel;
use App\Models\Exam;
use App\Models\GradeScale;
use App\Models\Result;
use App\Models\Student;
use App\Models\Subject;
use App\Models\ResultCheckerPin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ReportCardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_cards_can_be_generated_for_a_class(): void
    {
        [$exam, $class, $student, $subject] = $this->createReportCardFixture();
        $user = $this->createAuthenticatedUser();

        $response = $this->actingAs($user)->post('/report-cards/generate', [
            'exam_id' => $exam->id,
            'class_id' => $class->id,
        ]);

        $response->assertOk();
        $response->assertViewHas('exam', $exam);
        $response->assertViewHas('class', $class);
        $response->assertViewHas('students');
        $response->assertSee($student->full_name);
    }

    public function test_bulk_report_cards_can_be_downloaded_as_pdf(): void
    {
        [$exam, $class] = $this->createReportCardFixture();
        $user = $this->createAuthenticatedUser();

        $publishResponse = $this->actingAs($user)->post('/report-cards/publish', [
            'exam_id' => $exam->id,
            'class_id' => $class->id,
        ]);

        $publishResponse->assertRedirect();

        $response = $this->actingAs($user)->get('/report-cards/bulk/' . $exam->id . '/' . $class->id);

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_report_card_settings_page_loads_and_shows_students(): void
    {
        [$exam, $class, $student] = $this->createReportCardFixture();
        $user = $this->createAuthenticatedUser();

        $response = $this->actingAs($user)->get('/report-cards/settings?exam_id=' . $exam->id . '&class_id=' . $class->id);

        $response->assertOk();
        $response->assertViewHas('selectedExam', $exam);
        $response->assertViewHas('selectedClass', $class);
        $response->assertSee($student->full_name);
        $response->assertSee('Hidden from students');
        $response->assertSee('Publish');
    }

    public function test_report_card_publish_and_unpublish_workflow(): void
    {
        [$exam, $class, $student] = $this->createReportCardFixture();
        $user = $this->createAuthenticatedUser();

        $publishResponse = $this->actingAs($user)->post('/report-cards/publish', [
            'exam_id' => $exam->id,
            'class_id' => $class->id,
        ]);

        $publishResponse->assertRedirect();
        $this->assertDatabaseHas('report_cards', [
            'student_id' => $student->id,
            'exam_id' => $exam->id,
            'is_published' => true,
        ]);
        $this->assertDatabaseHas('results', [
            'student_id' => $student->id,
            'exam_id' => $exam->id,
            'is_published' => true,
        ]);

        $reportCard = \App\Models\ReportCard::where('student_id', $student->id)
            ->where('exam_id', $exam->id)
            ->first();

        $unpublishResponse = $this->actingAs($user)->post('/report-cards/unpublish', [
            'exam_id' => $exam->id,
            'class_id' => $class->id,
        ]);

        $unpublishResponse->assertRedirect();
        $this->assertDatabaseHas('report_cards', [
            'id' => $reportCard->id,
            'is_published' => false,
        ]);
        $this->assertDatabaseHas('results', [
            'student_id' => $student->id,
            'exam_id' => $exam->id,
            'is_published' => false,
        ]);
    }

    public function test_checker_can_download_a_published_report_with_legacy_unpublished_rows(): void
    {
        [$exam, $class, $student] = $this->createReportCardFixture();
        $user = $this->createAuthenticatedUser();

        $this->actingAs($user)->post('/report-cards/publish', [
            'exam_id' => $exam->id,
            'class_id' => $class->id,
        ]);

        Result::where('exam_id', $exam->id)->where('student_id', $student->id)->update(['is_published' => false]);
        $pin = ResultCheckerPin::create(['student_id' => $student->id, 'exam_id' => $exam->id, 'pin' => '123456']);

        $response = $this->post('/result-checker', [
            'registration_number' => $student->admission_number,
            'pin' => $pin->pin,
        ]);

        $response->assertOk()->assertViewIs('result-checker.result');
        $this->get('/result-checker/' . $pin->id . '/download')->assertOk()->assertHeader('content-type', 'application/pdf');
    }

    private function createReportCardFixture(): array
    {
        $class = ClassModel::create([
            'name' => 'Grade 10',
            'code' => 'G10',
            'section' => 'A',
            'full_name' => 'Grade 10 A',
            'is_active' => true,
        ]);

        $student = Student::create([
            'class_id' => $class->id,
            'first_name' => 'Ada',
            'last_name' => 'Lovelace',
            'admission_number' => 'ADM001',
            'email' => 'ada@example.com',
            'phone' => '08000000000',
            'address' => 'Test address',
            'date_of_birth' => '2008-01-01',
            'class' => 'Grade 10',
            'section' => 'A',
            'parent_name' => 'Parent',
            'parent_phone' => '08011111111',
            'parent_email' => 'parent@example.com',
            'is_active' => true,
        ]);

        $subject = Subject::create([
            'name' => 'Mathematics',
            'code' => 'MATH',
        ]);

        $exam = Exam::create([
            'name' => 'Mid Term Exam',
            'code' => 'MTE1',
            'type' => 'end_term',
            'term' => 'First Term',
            'academic_year' => '2025/2026',
            'start_date' => '2025-10-01',
            'end_date' => '2025-10-10',
            'exam_type' => 'end_of_term',
            'is_published' => true,
            'is_active' => true,
            'class_id' => $class->id,
        ]);

        GradeScale::create([
            'name' => 'A',
            'grade' => 'A',
            'min_score' => 70,
            'max_score' => 100,
            'remark' => 'Excellent',
            'color' => '#10b981',
            'is_active' => true,
        ]);

        Result::create([
            'student_id' => $student->id,
            'exam_id' => $exam->id,
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'ca1_score' => 20,
            'ca2_score' => 18,
            'ca3_score' => 16,
            'exam_score' => 60,
            'total_score' => 80,
            'score' => 80,
            'grade' => 'A',
            'remark' => 'Excellent',
            'is_published' => true,
        ]);

        return [$exam, $class, $student, $subject];
    }

    private function createAuthenticatedUser(): User
    {
        return User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
            'is_verified' => true,
        ]);
    }
}
