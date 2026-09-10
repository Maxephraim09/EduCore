<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ScoreSheet;
use App\Models\Result;
use Illuminate\Support\Facades\Auth;

class ScoreController extends Controller
{
    public function index()
    {
        $sheets = ScoreSheet::with(['class', 'subject', 'term', 'academicYear'])->get();
        return response()->json($sheets);
    }

    public function toggle($id)
    {
        $sheet = ScoreSheet::findOrFail($id);
        $sheet->is_active = !$sheet->is_active;
        if ($sheet->is_active) {
            $sheet->is_locked = false;
            $sheet->opened_at = now();
            $sheet->opened_by = Auth::id();
        } else {
            $sheet->closed_at = now();
            $sheet->closed_by = Auth::id();
        }
        $sheet->save();
        return response()->json(['is_active' => (bool) $sheet->is_active]);
    }

    public function enterScores(Request $request, $id)
    {
        $sheet = ScoreSheet::findOrFail($id);
        if (!method_exists($sheet, 'isOpen') || !$sheet->isOpen()) {
            return response()->json(['error' => 'Score sheet is not open'], 403);
        }

        $scores = $request->input('scores', []);
        foreach ($scores as $s) {
            $studentId = $s['student_id'] ?? null;
            $subjectId = $sheet->subject_id ?? ($s['subject_id'] ?? null);
            if (!$studentId || !$subjectId) {
                continue;
            }
            $ca1 = floatval($s['ca1'] ?? 0);
            $ca2 = floatval($s['ca2'] ?? 0);
            $ca3 = floatval($s['ca3'] ?? 0);
            $exam = floatval($s['exam'] ?? 0);
            $score = $ca1 + $ca2 + $ca3 + $exam;

            Result::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'subject_id' => $subjectId,
                    'term_id' => $sheet->term_id,
                    'academic_year_id' => $sheet->academic_year_id,
                ],
                [
                    'ca1' => $ca1,
                    'ca2' => $ca2,
                    'ca3' => $ca3,
                    'exam' => $exam,
                    'score' => $score,
                ]
            );
        }
        return response()->json(['status' => 'ok']);
    }
}
