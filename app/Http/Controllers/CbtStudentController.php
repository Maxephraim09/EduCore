<?php

namespace App\Http\Controllers;

use App\Models\CbtExam;
use App\Models\CbtAttempt;
use App\Models\CbtQuestion;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CbtStudentController extends Controller
{
    public function dashboard()
    {
        $student = Student::where('user_id', auth()->id())->first();
        
        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Student profile not found.');
        }

        $availableExams = CbtExam::where('status', 'published')
            ->where('class_id', $student->class_id)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->count();

        $completedExams = CbtAttempt::where('student_id', $student->id)
            ->where('status', 'completed')
            ->count();

        $inProgressExams = CbtAttempt::where('student_id', $student->id)
            ->whereIn('status', ['started', 'in_progress'])
            ->count();

        $averageScore = CbtAttempt::where('student_id', $student->id)
            ->where('status', 'completed')
            ->avg('percentage') ?? 0;

        $availableExamsList = CbtExam::with(['subject', 'class'])
            ->where('status', 'published')
            ->where('class_id', $student->class_id)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->get();

        return view('cbt.student.dashboard', compact(
            'availableExams', 
            'completedExams', 
            'inProgressExams', 
            'averageScore',
            'availableExamsList'
        ));
    }

    public function exams(Request $request)
    {
        $student = Student::where('user_id', auth()->id())->first();
        
        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Student profile not found.');
        }

        $filter = $request->get('filter', 'available');
        
        $query = CbtExam::with(['subject', 'class'])
            ->where('class_id', $student->class_id);

        if ($filter === 'available') {
            $query->where('status', 'published')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now());
        } elseif ($filter === 'completed') {
            $completedIds = CbtAttempt::where('student_id', $student->id)
                ->where('status', 'completed')
                ->pluck('cbt_exam_id');
            $query->whereIn('id', $completedIds);
        }

        $exams = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('cbt.student.exams', compact('exams', 'filter'));
    }

    public function takeExam($id)
    {
        $student = Student::where('user_id', auth()->id())->first();
        
        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Student profile not found.');
        }

        $exam = CbtExam::with(['questions' => function($q) {
            $q->where('is_active', true)->orderBy('order');
        }])->findOrFail($id);

        // Check if exam is available
        if ($exam->status !== 'published') {
            return redirect()->route('cbt.student.exams')->with('error', 'This exam is not available.');
        }

        if (now() < $exam->start_date || now() > $exam->end_date) {
            return redirect()->route('cbt.student.exams')->with('error', 'This exam is not available at this time.');
        }

        // Check if already attempted
        $attempt = CbtAttempt::where('cbt_exam_id', $id)
            ->where('student_id', $student->id)
            ->first();

        if ($attempt && $attempt->status === 'completed') {
            return redirect()->route('cbt.student.results', $id)
                ->with('info', 'You have already completed this exam.');
        }

        // Create or get existing attempt
        if (!$attempt) {
            $attempt = CbtAttempt::create([
                'cbt_exam_id' => $id,
                'student_id' => $student->id,
                'status' => 'started',
                'started_at' => now(),
            ]);
        }

        $timeLeft = $exam->duration_minutes * 60;
        $totalQuestions = $exam->questions->count();

        return view('cbt.student.take-exam', compact('exam', 'attempt', 'timeLeft', 'totalQuestions'));
    }

    public function startExam($id)
    {
        $student = Student::where('user_id', auth()->id())->first();
        
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $attempt = CbtAttempt::where('cbt_exam_id', $id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        $attempt->update([
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        return response()->json(['success' => true]);
    }

    public function submitExam(Request $request, $id)
    {
        $student = Student::where('user_id', auth()->id())->first();
        
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $attempt = CbtAttempt::where('cbt_exam_id', $id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        $exam = CbtExam::with('questions')->findOrFail($id);
        
        $answers = $request->input('answers', []);
        $score = 0;
        $correct = 0;
        $wrong = 0;
        $skipped = 0;

        foreach ($exam->questions as $question) {
            $answer = $answers[$question->id] ?? null;
            
            if ($answer === null) {
                $skipped++;
            } elseif ($answer === $question->correct_answer) {
                $correct++;
                $score += $question->marks;
            } else {
                $wrong++;
            }
        }

        $totalQuestions = $exam->questions->count();
        $percentage = $totalQuestions > 0 ? ($score / $exam->total_marks) * 100 : 0;

        $attempt->update([
            'answers' => $answers,
            'score' => $score,
            'total_answered' => $correct + $wrong,
            'correct_answers' => $correct,
            'wrong_answers' => $wrong,
            'skipped_questions' => $skipped,
            'percentage' => $percentage,
            'status' => 'completed',
            'submitted_at' => now(),
            'duration_used' => now()->diffInSeconds($attempt->started_at),
        ]);

        return response()->json([
            'success' => true,
            'redirect' => route('cbt.student.results', $id)
        ]);
    }

    public function results($id)
    {
        $student = Student::where('user_id', auth()->id())->first();
        
        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Student profile not found.');
        }

        $exam = CbtExam::with(['subject', 'class'])->findOrFail($id);
        $attempt = CbtAttempt::where('cbt_exam_id', $id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        return view('cbt.student.results', compact('exam', 'attempt'));
    }

    public function saveProgress(Request $request)
    {
        $student = Student::where('user_id', auth()->id())->first();
        
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $request->validate([
            'attempt_id' => 'required|exists:cbt_attempts,id',
            'answers' => 'nullable|array',
            'current_question' => 'nullable|integer',
        ]);

        $attempt = CbtAttempt::where('id', $request->attempt_id)
            ->where('student_id', $student->id)
            ->firstOrFail();

        $attempt->update([
            'answers' => $request->answers ?? $attempt->answers,
        ]);

        return response()->json(['success' => true]);
    }

    public function getQuestion(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:cbt_exams,id',
            'question_index' => 'required|integer',
        ]);

        $exam = CbtExam::with(['questions' => function($q) {
            $q->where('is_active', true)->orderBy('order');
        }])->findOrFail($request->exam_id);

        $questions = $exam->questions->values();
        $index = $request->question_index - 1;

        if (!isset($questions[$index])) {
            return response()->json(['error' => 'Question not found'], 404);
        }

        $question = $questions[$index];
        $html = view('cbt.student.partials.question', compact('question', 'index'))->render();

        return response()->json(['html' => $html]);
    }

    public function getSavedAnswers(Request $request)
    {
        $student = Student::where('user_id', auth()->id())->first();
        
        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $attempt = CbtAttempt::where('id', $request->attempt_id)
            ->where('student_id', $student->id)
            ->first();

        if (!$attempt) {
            return response()->json(['answers' => []]);
        }

        return response()->json(['answers' => $attempt->answers ?? []]);
    }
}