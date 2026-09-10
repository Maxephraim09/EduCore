<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\Employee;
use App\Models\Result;
use App\Models\Student;
use App\Models\GradeScale;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ExamController extends Controller
{
    // ==================== INDEX ====================
    public function index()
    {
        $exams = Exam::with(['subject', 'class', 'teacher', 'creator'])
            ->withCount(['questions', 'results'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // Get statistics for the dashboard
        $totalExams = Exam::count();
        $publishedExams = Exam::where('is_published', true)->count();
        $draftExams = Exam::where('is_published', false)->count();
        $upcomingExams = Exam::where('start_date', '>', now())->count();
        $totalStudents = Student::where('is_active', true)->count();
        
        return view('exams.index', compact(
            'exams', 
            'totalExams', 
            'publishedExams', 
            'draftExams', 
            'upcomingExams',
            'totalStudents'
        ));
    }

    // ==================== MANAGE QUESTIONS (New) ====================
    public function manageQuestions(Request $request)
    {
        $academicYears = $this->getAcademicYears();
        $classes = ClassModel::where('is_active', true)->orderBy('name')->get();
        $subjects = Subject::with('teacher')->where('is_active', true)->orderBy('name')->get();
        
        $selectedAcademicYear = $request->query('academic_year');
        $selectedTerm = $request->query('term');
        $selectedClassId = $request->query('class_id');
        $selectedSubjectId = $request->query('subject_id');
        
        $selectedClass = null;
        $selectedSubject = null;
        $questions = collect();
        $totalMarks = 0;
        $exam = null;
        
        if ($selectedClassId && $selectedSubjectId && $selectedTerm && $selectedAcademicYear) {
            $selectedClass = ClassModel::find($selectedClassId);
            $selectedSubject = Subject::with('teacher')->find($selectedSubjectId);
            
            // Get or create exam for this combination
            $exam = Exam::firstOrCreate(
                [
                    'academic_year' => $selectedAcademicYear,
                    'term' => $selectedTerm,
                    'class_id' => $selectedClassId,
                    'subject_id' => $selectedSubjectId,
                ],
                [
                    'name' => ($selectedSubject ? $selectedSubject->name : 'Subject') . ' - ' . $selectedTerm . ' ' . $selectedAcademicYear,
                    'code' => 'EXAM-' . Str::upper(Str::random(6)),
                    'teacher_id' => $selectedSubject ? $selectedSubject->teacher_id : null,
                    'created_by' => auth()->id(),
                    'is_published' => false,
                    'total_marks' => 0,
                    'passing_marks' => 40,
                    'start_date' => now(),
                    'end_date' => now()->addDays(7),
                ]
            );
            
            $questions = $exam->questions;
            $totalMarks = $questions->sum('marks');
        }
        
        return view('exams.create', compact(
            'academicYears',
            'classes',
            'subjects',
            'selectedAcademicYear',
            'selectedTerm',
            'selectedClassId',
            'selectedSubjectId',
            'selectedClass',
            'selectedSubject',
            'questions',
            'totalMarks',
            'exam'
        ));
    }

    // ==================== STORE QUESTIONS (New) ====================
    public function storeQuestions(Request $request)
    {
        $request->validate([
            'academic_year' => 'required|string',
            'term' => 'required|string',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.type' => 'required|string',
            'questions.*.marks' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $subject = Subject::find($request->subject_id);
            
            // Get or create exam
            $exam = Exam::firstOrCreate(
                [
                    'academic_year' => $request->academic_year,
                    'term' => $request->term,
                    'class_id' => $request->class_id,
                    'subject_id' => $request->subject_id,
                ],
                [
                    'name' => $subject->name . ' - ' . $request->term . ' ' . $request->academic_year,
                    'code' => 'EXAM-' . Str::upper(Str::random(6)),
                    'teacher_id' => $subject->teacher_id,
                    'created_by' => auth()->id(),
                    'is_published' => false,
                    'passing_marks' => 40,
                    'start_date' => now(),
                    'end_date' => now()->addDays(7),
                ]
            );

            // Get existing question IDs
            $existingIds = $exam->questions->pluck('id')->toArray();
            $submittedIds = [];

            foreach ($request->questions as $questionData) {
                if (isset($questionData['id']) && !empty($questionData['id'])) {
                    $submittedIds[] = $questionData['id'];
                    
                    // Update existing question
                    $question = Question::find($questionData['id']);
                    if ($question && $question->exam_id == $exam->id) {
                        $question->update([
                            'question' => $questionData['question'],
                            'type' => $questionData['type'],
                            'marks' => $questionData['marks'],
                            'options' => $questionData['options'] ?? null,
                            'correct_answer' => $questionData['correct_answer'] ?? null,
                            'explanation' => $questionData['explanation'] ?? null,
                        ]);
                    }
                } else {
                    // Create new question
                    Question::create([
                        'exam_id' => $exam->id,
                        'question' => $questionData['question'],
                        'type' => $questionData['type'],
                        'marks' => $questionData['marks'],
                        'options' => $questionData['options'] ?? null,
                        'correct_answer' => $questionData['correct_answer'] ?? null,
                        'explanation' => $questionData['explanation'] ?? null,
                    ]);
                }
            }

            // Delete questions that were removed
            $toDelete = array_diff($existingIds, $submittedIds);
            if (!empty($toDelete)) {
                Question::whereIn('id', $toDelete)->delete();
            }

            // Update exam total marks
            $totalMarks = $exam->questions()->sum('marks');
            $exam->update(['total_marks' => $totalMarks]);

            DB::commit();

            return redirect()->route('exams.manage-questions', [
                'academic_year' => $request->academic_year,
                'term' => $request->term,
                'class_id' => $request->class_id,
                'subject_id' => $request->subject_id,
            ])->with('success', 'Questions saved successfully! Total marks: ' . $totalMarks);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error saving questions: ' . $e->getMessage());
        }
    }

    // ==================== CREATE (Redirect to manage-questions) ====================
    public function create()
    {
        return redirect()->route('exams.manage-questions');
    }

    // ==================== STORE (Deprecated - Use storeQuestions instead) ====================
    public function store(Request $request)
    {
        // This is kept for backward compatibility but redirects to manage-questions
        return redirect()->route('exams.manage-questions')
            ->with('info', 'Please use the Manage Questions page to create exams with questions.');
    }

    // ==================== SHOW ====================
    public function show($id)
    {
        $exam = Exam::with(['subject', 'class', 'teacher', 'creator', 'questions'])
            ->findOrFail($id);
        
        // Get results for this exam
        $results = Result::with(['student', 'subject'])
            ->where('exam_id', $id)
            ->get();
        
        $stats = $this->getExamStats($id);
        
        return view('exams.show', compact('exam', 'results', 'stats'));
    }

    // ==================== EDIT ====================
    public function edit($id)
    {
        $exam = Exam::with(['questions'])->findOrFail($id);
        
        // Redirect to manage-questions with the exam parameters
        return redirect()->route('exams.manage-questions', [
            'academic_year' => $exam->academic_year,
            'term' => $exam->term,
            'class_id' => $exam->class_id,
            'subject_id' => $exam->subject_id,
        ]);
    }

    // ==================== UPDATE ====================
    public function update(Request $request, $id)
    {
        // This is kept for backward compatibility
        return redirect()->route('exams.manage-questions')
            ->with('info', 'Please use the Manage Questions page to update exams.');
    }

    // ==================== PUBLISH ====================
    public function publish($id)
    {
        try {
            $exam = Exam::findOrFail($id);
            
            if ($exam->questions()->count() === 0) {
                return back()->with('error', 'Cannot publish exam with no questions. Please add questions first.');
            }
            
            $exam->update([
                'is_published' => true,
                'published_at' => now(),
            ]);
            
            // Publish all results for this exam
            Result::where('exam_id', $id)->update(['is_published' => true]);

            return redirect()->route('exams.show', $id)
                ->with('success', 'Exam published successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error publishing exam: ' . $e->getMessage());
        }
    }

    // ==================== UNPUBLISH ====================
    public function unpublish($id)
    {
        try {
            $exam = Exam::findOrFail($id);
            $exam->update([
                'is_published' => false,
            ]);
            
            Result::where('exam_id', $id)->update(['is_published' => false]);

            return redirect()->route('exams.show', $id)
                ->with('success', 'Exam unpublished successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error unpublishing exam: ' . $e->getMessage());
        }
    }

    // ==================== DESTROY ====================
    public function destroy($id)
    {
        try {
            $exam = Exam::findOrFail($id);
            
            // Delete associated questions, results and exam subjects
            Question::where('exam_id', $id)->delete();
            Result::where('exam_id', $id)->delete();
            ExamSubject::where('exam_id', $id)->delete();
            $exam->delete();

            return redirect()->route('exams.index')
                ->with('success', 'Exam deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting exam: ' . $e->getMessage());
        }
    }

    // ==================== ENTER RESULTS ====================
    public function enterResults($examId)
    {
        $exam = Exam::with(['subject', 'class'])
            ->findOrFail($examId);
        
        $students = Student::where('is_active', true)
            ->where('class_id', $exam->class_id)
            ->orderBy('first_name')
            ->get();
        
        // Get subjects for this exam
        $subjects = collect([$exam->subject]);
        
        return view('exams.enter-results', compact('exam', 'students', 'subjects'));
    }

    // ==================== STORE RESULTS ====================
    public function storeResults(Request $request, $examId)
    {
        $request->validate([
            'results' => 'required|array',
            'results.*.student_id' => 'required|exists:students,id',
            'results.*.subject_id' => 'required|exists:subjects,id',
            'results.*.ca1_score' => 'nullable|numeric|min:0|max:30',
            'results.*.ca2_score' => 'nullable|numeric|min:0|max:30',
            'results.*.ca3_score' => 'nullable|numeric|min:0|max:30',
            'results.*.exam_score' => 'nullable|numeric|min:0|max:70',
        ]);

        try {
            DB::beginTransaction();

            $exam = Exam::findOrFail($examId);
            $gradeScales = GradeScale::where('is_active', true)->get();

            foreach ($request->results as $resultData) {
                // Calculate total score
                $ca1 = $resultData['ca1_score'] ?? 0;
                $ca2 = $resultData['ca2_score'] ?? 0;
                $ca3 = $resultData['ca3_score'] ?? 0;
                $examScore = $resultData['exam_score'] ?? 0;
                $totalScore = $ca1 + $ca2 + $ca3 + $examScore;

                // Find grade
                $grade = $gradeScales->filter(function($g) use ($totalScore) {
                    return $totalScore >= $g->min_score && $totalScore <= $g->max_score;
                })->first();

                // Get or create result
                Result::updateOrCreate(
                    [
                        'student_id' => $resultData['student_id'],
                        'exam_id' => $examId,
                        'subject_id' => $resultData['subject_id'],
                    ],
                    [
                        'class_id' => $exam->class_id,
                        'term' => $exam->term,
                        'academic_year' => $exam->academic_year,
                        'ca1_score' => $ca1,
                        'ca2_score' => $ca2,
                        'ca3_score' => $ca3,
                        'exam_score' => $examScore,
                        'total_score' => $totalScore,
                        'grade' => $grade ? $grade->grade : null,
                        'remark' => $grade ? $grade->remark : null,
                        'is_published' => $exam->is_published,
                    ]
                );
            }

            DB::commit();

            return redirect()->route('exams.show', $examId)
                ->with('success', 'Results entered successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error saving results: ' . $e->getMessage());
        }
    }

    // ==================== BULK RESULTS ====================
    public function bulkResults($examId)
    {
        $exam = Exam::with(['subject', 'class'])
            ->findOrFail($examId);
        
        $students = Student::where('is_active', true)
            ->where('class_id', $exam->class_id)
            ->orderBy('first_name')
            ->get();
        
        return view('exams.bulk-results', compact('exam', 'students'));
    }

    // ==================== UPLOAD BULK RESULTS ====================
    public function uploadBulkResults(Request $request, $examId)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls',
        ]);

        try {
            // Process the uploaded file
            // This will be implemented based on your file format
            return redirect()->route('exams.show', $examId)
                ->with('success', 'Results uploaded successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error uploading results: ' . $e->getMessage());
        }
    }

    // ==================== DOWNLOAD TEMPLATE ====================
    public function downloadTemplate($examId)
    {
        $exam = Exam::findOrFail($examId);
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=exam_{$examId}_results_template.csv",
        ];
        
        $callback = function() use ($exam) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Student ID', 'Admission Number', 'Student Name', 'CA1', 'CA2', 'CA3', 'Exam Score']);
            
            // Add sample student data
            $students = Student::where('class_id', $exam->class_id)
                ->where('is_active', true)
                ->limit(5)
                ->get();
            
            foreach ($students as $student) {
                fputcsv($file, [
                    $student->id,
                    $student->admission_number,
                    $student->full_name,
                    '', '', '', ''
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    // ==================== HELPER METHODS ====================
    
    private function getAcademicYears()
    {
        $years = [];
        $currentYear = date('Y');
        for ($i = -2; $i <= 2; $i++) {
            $year = $currentYear + $i;
            $years[] = $year . '/' . ($year + 1);
        }
        return $years;
    }

    private function getExamStats($examId)
    {
        $results = Result::where('exam_id', $examId)->get();
        
        $totalStudents = $results->groupBy('student_id')->count();
        $totalResults = $results->count();
        
        return [
            'total_students' => $totalStudents,
            'total_subjects' => $results->groupBy('subject_id')->count(),
            'results_entered' => $totalResults,
            'average_score' => $totalResults > 0 ? round($results->avg('total_score'), 2) : 0,
            'pass_rate' => $totalResults > 0 ? round(($results->filter(function($r) {
                return $r->total_score >= 40;
            })->count() / $totalResults) * 100, 2) : 0,
            'highest_score' => $results->max('total_score') ?? 0,
            'lowest_score' => $results->min('total_score') ?? 0,
        ];
    }
}