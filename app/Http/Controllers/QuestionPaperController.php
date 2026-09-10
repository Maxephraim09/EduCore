<?php

namespace App\Http\Controllers;

use App\Models\QuestionPaper;
use App\Models\Question;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class QuestionPaperController extends Controller
{
    /**
     * Display a listing of question papers.
     */
    public function index()
    {
        $questionPapers = QuestionPaper::with(['subject', 'class', 'teacher', 'creator'])
            ->withCount(['questions']) // Only count questions, not results
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $totalPapers = QuestionPaper::count();
        $publishedPapers = QuestionPaper::where('is_published', true)->count();
        $draftPapers = QuestionPaper::where('is_published', false)->count();
        $totalQuestions = Question::count();
        
        return view('question-papers.index', compact(
            'questionPapers',
            'totalPapers',
            'publishedPapers',
            'draftPapers',
            'totalQuestions'
        ));
    }

    /**
     * Manage questions for a specific subject/class/term combination.
     */
    public function manageQuestions(Request $request)
    {
        $academicYears = $this->getAcademicYears();
        $classes = ClassModel::where('is_active', true)->orderBy('name')->get();
        $subjects = Subject::with('teacher')->where('is_active', true)
            ->when(auth()->user()?->role === 'teacher', fn ($query) => $query->where('teacher_id', getCurrentEmployeeId() ?? 0))
            ->orderBy('name')->get();
        
        $selectedAcademicYear = $request->query('academic_year');
        $selectedTerm = $request->query('term');
        $selectedClassId = $request->query('class_id');
        $selectedSubjectId = $request->query('subject_id');
        
        $selectedClass = null;
        $selectedSubject = null;
        $questions = collect();
        $totalMarks = 0;
        $questionPaper = null;
        
        if ($selectedClassId && $selectedSubjectId && $selectedTerm && $selectedAcademicYear) {
            $selectedClass = ClassModel::find($selectedClassId);
            $selectedSubject = Subject::with('teacher')->find($selectedSubjectId);

            abort_unless(auth()->user()?->role !== 'teacher' || $selectedSubject?->teacher_id === getCurrentEmployeeId(), 403);
            
            // Get or create question paper for this combination
            $questionPaper = QuestionPaper::firstOrCreate(
                [
                    'academic_year' => $selectedAcademicYear,
                    'term' => $selectedTerm,
                    'class_id' => $selectedClassId,
                    'subject_id' => $selectedSubjectId,
                ],
                [
                    'title' => ($selectedSubject ? $selectedSubject->name : 'Subject') . ' - ' . $selectedTerm . ' ' . $selectedAcademicYear,
                    'code' => 'QP-' . Str::upper(Str::random(6)),
                    'teacher_id' => $selectedSubject ? $selectedSubject->teacher_id : null,
                    'created_by' => auth()->id(),
                    'is_published' => false,
                    'passing_marks' => 40,
                    'start_date' => now(),
                    'end_date' => now()->addDays(7),
                ]
            );
            
            $questions = $questionPaper->questions;
            $totalMarks = $questions->sum('marks');
        }
        
        return view('question-papers.create', compact(
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
            'questionPaper'
        ));
    }

    /**
     * Store questions for a question paper.
     */
    public function storeQuestions(Request $request)
    {
        $request->validate([
            'academic_year' => 'required|string',
            'term' => 'required|string',
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.type' => 'required|string|in:multiple_choice,true_false,short_answer,essay',
            'questions.*.marks' => 'required|integer|min:1',
            'questions.*.options' => 'nullable|array',
            'questions.*.correct_answer' => 'nullable|string',
            'questions.*.explanation' => 'nullable|string',
        ]);

        if (auth()->user()?->role === 'teacher') {
            abort_unless(Subject::whereKey($request->subject_id)->where('teacher_id', getCurrentEmployeeId())->exists(), 403);
        }

        try {
            DB::beginTransaction();

            $subject = Subject::find($request->subject_id);
            
            // Get or create question paper
            $questionPaper = QuestionPaper::firstOrCreate(
                [
                    'academic_year' => $request->academic_year,
                    'term' => $request->term,
                    'class_id' => $request->class_id,
                    'subject_id' => $request->subject_id,
                ],
                [
                    'title' => $subject->name . ' - ' . $request->term . ' ' . $request->academic_year,
                    'code' => 'QP-' . Str::upper(Str::random(6)),
                    'teacher_id' => $subject->teacher_id,
                    'created_by' => auth()->id(),
                    'is_published' => false,
                    'passing_marks' => 40,
                    'start_date' => now(),
                    'end_date' => now()->addDays(7),
                ]
            );

            // Get existing question IDs
            $existingIds = $questionPaper->questions->pluck('id')->toArray();
            $submittedIds = [];

            $order = 0;
            foreach ($request->questions as $questionData) {
                $order++;
                
                if (isset($questionData['id']) && !empty($questionData['id'])) {
                    $submittedIds[] = $questionData['id'];
                    
                    // Update existing question
                    $question = Question::find($questionData['id']);
                    if ($question && $question->question_paper_id == $questionPaper->id) {
                        $question->update([
                            'question' => $questionData['question'],
                            'type' => $questionData['type'],
                            'marks' => $questionData['marks'],
                            'options' => $questionData['options'] ?? null,
                            'correct_answer' => $questionData['correct_answer'] ?? null,
                            'explanation' => $questionData['explanation'] ?? null,
                            'order' => $order,
                        ]);
                    }
                } else {
                    // Create new question
                    Question::create([
                        'question_paper_id' => $questionPaper->id,
                        'question' => $questionData['question'],
                        'type' => $questionData['type'],
                        'marks' => $questionData['marks'],
                        'options' => $questionData['options'] ?? null,
                        'correct_answer' => $questionData['correct_answer'] ?? null,
                        'explanation' => $questionData['explanation'] ?? null,
                        'order' => $order,
                    ]);
                }
            }

            // Delete questions that were removed
            $toDelete = array_diff($existingIds, $submittedIds);
            if (!empty($toDelete)) {
                Question::whereIn('id', $toDelete)->delete();
            }

            // Update question paper total marks
            $totalMarks = $questionPaper->questions()->sum('marks');
            $questionPaper->update(['total_marks' => $totalMarks]);

            DB::commit();

            return redirect()->route('question-papers.manage-questions', [
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

    /**
     * Display the specified question paper.
     */
    public function show($id)
    {
        $questionPaper = QuestionPaper::with(['subject', 'class', 'teacher', 'creator', 'questions'])
            ->findOrFail($id);
        
        return view('question-papers.show', compact('questionPaper'));
    }

    /**
     * Publish the specified question paper.
     */
    public function publish($id)
    {
        try {
            $questionPaper = QuestionPaper::findOrFail($id);
            
            if ($questionPaper->questions()->count() === 0) {
                return back()->with('error', 'Cannot publish question paper with no questions. Please add questions first.');
            }
            
            $questionPaper->update([
                'is_published' => true,
                'published_at' => now(),
            ]);

            return redirect()->route('question-papers.show', $id)
                ->with('success', 'Question paper published successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error publishing question paper: ' . $e->getMessage());
        }
    }

    /**
     * Unpublish the specified question paper.
     */
    public function unpublish($id)
    {
        try {
            $questionPaper = QuestionPaper::findOrFail($id);
            $questionPaper->update([
                'is_published' => false,
            ]);

            return redirect()->route('question-papers.show', $id)
                ->with('success', 'Question paper unpublished successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error unpublishing question paper: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified question paper.
     */
    public function destroy($id)
    {
        try {
            $questionPaper = QuestionPaper::findOrFail($id);
            
            // Delete associated questions only (not results)
            Question::where('question_paper_id', $id)->delete();
            $questionPaper->delete();

            return redirect()->route('question-papers.index')
                ->with('success', 'Question paper deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting question paper: ' . $e->getMessage());
        }
    }

    /**
     * Get academic years for dropdown.
     */
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

    /**
     * Duplicate a question paper.
     */
    public function duplicate($id)
    {
        try {
            $questionPaper = QuestionPaper::findOrFail($id);
            $newPaper = $questionPaper->duplicate();

            return redirect()->route('question-papers.show', $newPaper->id)
                ->with('success', 'Question paper duplicated successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error duplicating question paper: ' . $e->getMessage());
        }
    }

    /**
     * Export question paper to PDF.
     */
    public function export($id)
    {
        try {
            $questionPaper = QuestionPaper::with(['subject', 'class', 'questions'])
                ->findOrFail($id);
            
            // This will be implemented with a PDF library like DomPDF or TCPDF
            // For now, return a simple view
            return view('question-papers.export', compact('questionPaper'));

        } catch (\Exception $e) {
            return back()->with('error', 'Error exporting question paper: ' . $e->getMessage());
        }
    }

    /**
     * Get question papers by filters (AJAX).
     */
    public function getByFilters(Request $request)
    {
        $request->validate([
            'class_id' => 'nullable|exists:classes,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'term' => 'nullable|string',
            'academic_year' => 'nullable|string',
        ]);

        $papers = QuestionPaper::with(['subject', 'class'])
            ->when($request->class_id, function($query) use ($request) {
                return $query->byClass($request->class_id);
            })
            ->when($request->subject_id, function($query) use ($request) {
                return $query->bySubject($request->subject_id);
            })
            ->when($request->term, function($query) use ($request) {
                return $query->byTerm($request->term);
            })
            ->when($request->academic_year, function($query) use ($request) {
                return $query->byAcademicYear($request->academic_year);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($papers);
    }

    /**
     * Get questions for a specific paper (AJAX).
     */
    public function getQuestions($id)
    {
        try {
            $questionPaper = QuestionPaper::with('questions')
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'paper' => $questionPaper,
                'questions' => $questionPaper->questions,
                'total_marks' => $questionPaper->questions->sum('marks'),
                'question_count' => $questionPaper->questions->count(),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading questions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get question statistics (AJAX).
     */
    public function getStats($id)
    {
        try {
            $questionPaper = QuestionPaper::with('questions')
                ->findOrFail($id);
            
            $stats = $questionPaper->getQuestionSummary();
            
            return response()->json([
                'success' => true,
                'stats' => $stats,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}