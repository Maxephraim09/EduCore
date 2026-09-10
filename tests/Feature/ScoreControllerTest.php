<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\ScoreSheet;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Term;
use App\Models\AcademicYear;

class ScoreControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_toggle_score_sheet_opens_and_closes()
    {
        $this->withoutMiddleware();

        $year = AcademicYear::create([
            'name' => '2024/2025',
            'start_date' => '2024-09-01',
            'end_date' => '2025-07-31',
            'is_current' => true,
            'status' => 'active'
        ]);

        $term = Term::create([
            'academic_year_id' => $year->id,
            'name' => 'First Term',
            'slug' => 'first-term',
            'sequence' => 1,
            'start_date' => now(),
            'end_date' => now()->addMonths(3),
            'is_current' => true,
            'status' => 'active'
        ]);

        $class = ClassModel::create(['name' => 'JSS 1', 'section' => 'A', 'full_name' => 'JSS 1 A', 'code' => 'JSS1-A', 'is_active' => true]);
        $subject = Subject::create(['name' => 'Mathematics', 'code' => 'MATH', 'is_active' => true]);

        $sheet = ScoreSheet::create([
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'term_id' => $term->id,
            'academic_year_id' => $year->id,
            'is_active' => false
        ]);

        $resp = $this->post("/score-sheets/{$sheet->id}/toggle");
        $resp->assertStatus(200)->assertJson(['is_active' => true]);
        $this->assertDatabaseHas('score_sheets', ['id' => $sheet->id, 'is_active' => 1]);

        $resp2 = $this->post("/score-sheets/{$sheet->id}/toggle");
        $resp2->assertStatus(200)->assertJson(['is_active' => false]);
        $this->assertDatabaseHas('score_sheets', ['id' => $sheet->id, 'is_active' => 0]);
    }

    public function test_enter_scores_creates_results_when_sheet_open()
    {
        $this->withoutMiddleware();

        $year = AcademicYear::create([
            'name' => '2024/2025',
            'start_date' => '2024-09-01',
            'end_date' => '2025-07-31',
            'is_current' => true,
            'status' => 'active'
        ]);

        $term = Term::create([
            'academic_year_id' => $year->id,
            'name' => 'First Term',
            'slug' => 'first-term',
            'sequence' => 1,
            'start_date' => now(),
            'end_date' => now()->addMonths(3),
            'is_current' => true,
            'status' => 'active'
        ]);

        $class = ClassModel::create(['name' => 'JSS 1', 'section' => 'A', 'full_name' => 'JSS 1 A', 'code' => 'JSS1-A', 'is_active' => true]);
        $subject = Subject::create(['name' => 'Mathematics', 'code' => 'MATH', 'is_active' => true]);
        // We skip creating a full student record to avoid many NOT NULL fields in this test environment.
        $studentId = 9999;

        $sheet = ScoreSheet::create([
            'class_id' => $class->id,
            'subject_id' => $subject->id,
            'term_id' => $term->id,
            'academic_year_id' => $year->id,
            'is_active' => true,
            'opened_at' => now()
        ]);

        $payload = [
            'scores' => [
                ['student_id' => $studentId, 'ca1' => 5, 'ca2' => 5, 'ca3' => 5, 'exam' => 40]
            ]
        ];

        $resp = $this->postJson("/score-sheets/{$sheet->id}/enter-scores", $payload);
        $resp->assertStatus(200)->assertJson(['status' => 'ok']);

        $this->assertDatabaseHas('results', ['student_id' => $student->id, 'subject_id' => $subject->id, 'score' => 55]);
    }
}
