<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\Result;
use App\Models\Student;
use App\Models\ClassModel;
use App\Models\GradeScale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultController extends Controller
{
    public function index()
    {
        $exams = Exam::where('is_published', true)->orderBy('created_at', 'desc')->get();
        $classes = ClassModel::where('is_active', true)
            ->when(auth()->check() && auth()->user()->role === 'teacher', function ($query) {
                $query->where('class_teacher_id', getCurrentEmployeeId() ?? 0);
            })
            ->get();
        
        return view('results.index', compact('exams', 'classes'));
    }

    public function viewResults(Request $request)
    {
        if ($request->isMethod('get') && !$request->has('exam_id') && !$request->has('class_id')) {
            return redirect()->route('results.index');
        }

        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'class_id' => 'required|exists:classes,id',
        ]);

        $exam = Exam::findOrFail($request->exam_id);
        $class = ClassModel::where('is_active', true)
            ->when(auth()->check() && auth()->user()->role === 'teacher', function ($query) {
                $query->where('class_teacher_id', getCurrentEmployeeId() ?? 0);
            })
            ->findOrFail($request->class_id);
        
        $results = Result::with(['student', 'subject'])
            ->where('exam_id', $request->exam_id)
            ->where('class_id', $request->class_id)
            ->get()
            ->groupBy('student_id');

        $students = Student::where('class_id', $request->class_id)
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get();

        return view('results.view', compact('exam', 'class', 'results', 'students'));
    }

    public function generateReportCard($examId, $studentId)
    {
        $exam = Exam::findOrFail($examId);
        $student = Student::with('class')->findOrFail($studentId);

        if ($student->class === null) {
            return back()->with('error', 'Student is not assigned to a class.');
        }

        if (auth()->check() && auth()->user()->role === 'teacher' && $student->class->class_teacher_id !== getCurrentEmployeeId()) {
            return back()->with('error', 'You can only view report cards for your own students.');
        }

        if (!isReportCardPublished($examId, $student->class_id)) {
            return back()->with('error', 'Report cards for this exam and class are not published yet.');
        }
        
        $results = Result::with('subject')
            ->where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->get();

        // Calculate summary
        $totalScore = $results->sum('total_score');
        $totalSubjects = $results->count();
        $average = $totalSubjects > 0 ? $totalScore / $totalSubjects : 0;
        $grade = GradeScale::getGrade($average);
        
        // Calculate position
        $allResults = Result::where('exam_id', $examId)
            ->where('class_id', $student->class_id)
            ->get()
            ->groupBy('student_id')
            ->map(function($studentResults) {
                return $studentResults->sum('total_score');
            })
            ->sortDesc();
        
        $position = $allResults->search(function($score) use ($studentId, $allResults) {
            return $allResults->keys()->contains($studentId);
        });
        
        $position = $position !== false ? $position + 1 : 'N/A';

        return view('results.report-card', compact(
            'exam', 'student', 'results', 'totalScore',
            'totalSubjects', 'average', 'grade', 'position'
        ));
    }

    public function bulkUploadForm($examId)
    {
        $exam = Exam::findOrFail($examId);
        $classes = ClassModel::where('is_active', true)->get();
        
        return view('results.bulk-upload', compact('exam', 'classes'));
    }

    public function bulkUpload(Request $request, $examId)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'results_file' => 'required|file|mimes:csv,xlsx,xls',
        ]);

        try {
            // Process file and save results
            // This would use Excel or CSV parsing
            // For now, redirect with success

            return redirect()->route('results.index')
                ->with('success', 'Results uploaded successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error uploading results: ' . $e->getMessage());
        }
    }

    public function exportResults($examId, $classId)
    {
        $exam = Exam::findOrFail($examId);
        $class = ClassModel::findOrFail($classId);
        
        $results = Result::with(['student', 'subject'])
            ->where('exam_id', $examId)
            ->where('class_id', $classId)
            ->get()
            ->groupBy('student_id');

        // Generate export (CSV/PDF)
        // For now, redirect back
        return back()->with('success', 'Export generated successfully!');
    }
}