<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Result;
use App\Models\GradeScale;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ScoresImport;

class ScoresEntryController extends Controller
{
    public function index()
    {
        $exams = Exam::where('is_active', true)->orderBy('created_at', 'desc')->get();
        $classes = $this->availableClasses();
        $subjects = $this->availableSubjects();
        
        // Get statistics
        $totalExams = Exam::count();
        $publishedExams = Exam::where('is_published', true)->count();
        $pendingExams = Exam::where('is_published', false)->count();
        $totalStudents = Student::where('is_active', true)->count();
        
        // Get recent scores
        $recentScores = Result::with(['student', 'subject', 'class'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('scores.index', compact(
            'exams', 'classes', 'subjects', 
            'totalExams', 'publishedExams', 'pendingExams', 'totalStudents',
            'recentScores'
        ));
    }

    // ==================== SINGLE ENTRY ====================
    
    // Single Entry View
    public function singleEntry(Request $request)
    {
        $classId = $request->query('class_id');
        $term = $request->query('term');
        $studentId = $request->query('student_id');
        
        $classes = $this->availableClasses();
        $subjects = $this->availableSubjects();
        
        $class = null;
        $student = null;
        $studentScores = collect();
        $subjectsWithScores = collect();
        
        if ($classId) {
            $class = ClassModel::findOrFail($classId);
        }
        
        if ($studentId) {
            $student = Student::with(['class', 'subjects'])->findOrFail($studentId);
            
            $subjectsWithScores = $student->subjects()->where('is_active', true)->orderBy('name')->get();
            
            $normalizedTerm = $this->normalizeExamTerm($term);
            $examId = Exam::where('class_id', $classId)
                ->where('term', $normalizedTerm)
                ->value('id');
            if (!$examId) {
                $examId = Exam::where('term', $normalizedTerm)
                    ->where('is_published', true)
                    ->orderBy('created_at', 'desc')
                    ->value('id');
            }
            
            $studentScores = collect();
            if ($examId) {
                $studentScores = Result::where('student_id', $studentId)
                    ->where('class_id', $classId)
                    ->where('exam_id', $examId)
                    ->get()
                    ->keyBy('subject_id');
            }
        }
        
        return view('scores.single-entry', compact(
            'classes', 'subjects', 'class', 'student', 
            'subjectsWithScores', 'studentScores', 'term', 'classId', 'studentId'
        ));
    }

    // Get Student Scores (AJAX)
    public function getStudentScores(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'term' => 'required|string',
        ]);

        try {
            $student = Student::with('class')->findOrFail($request->student_id);
            $class = ClassModel::findOrFail($request->class_id);
            
            $student = Student::with('subjects')->findOrFail($request->student_id);
            $subjects = $student->subjects()->where('is_active', true)->orderBy('name')->get();
            
            $normalizedTerm = $this->normalizeExamTerm($request->term);
            
            $examId = Exam::where('class_id', $request->class_id)
                ->where('term', $normalizedTerm)
                ->value('id');
            if (!$examId) {
                $examId = Exam::where('term', $normalizedTerm)
                    ->where('is_published', true)
                    ->orderBy('created_at', 'desc')
                    ->value('id');
            }
            
            $scores = collect();
            if ($examId) {
                $scores = Result::where('student_id', $request->student_id)
                    ->where('class_id', $request->class_id)
                    ->where('exam_id', $examId)
                    ->get()
                    ->keyBy('subject_id');
            }
            if ($scores->isNotEmpty()) {
                $examId = $scores->first()->exam_id;
            } else {
                $normalizedTerm = $this->normalizeExamTerm($request->term);
                $examId = Exam::where('class_id', $request->class_id)
                    ->where('term', $normalizedTerm)
                    ->value('id');
                if (!$examId) {
                    $examId = Exam::where('term', $normalizedTerm)
                        ->where('is_published', true)
                        ->orderBy('created_at', 'desc')
                        ->value('id');
                }
            }

            return response()->json([
                'success' => true,
                'student' => $student,
                'class_name' => $class->full_class_name,
                'subjects' => $subjects,
                'scores' => $scores,
                'exam_id' => $examId,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading scores: ' . $e->getMessage()
            ], 500);
        }
    }

    // Save Single Entry (AJAX)
    public function saveSingleEntry(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:classes,id',
            'term' => 'required|string',
            'exam_id' => 'nullable|exists:exams,id',
            'scores' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $savedCount = 0;
            foreach ($request->scores as $subjectId => $scoreData) {
                $student = Student::findOrFail($request->student_id);
                
                $ca1 = $scoreData['ca1_score'] ?? 0;
                $ca2 = $scoreData['ca2_score'] ?? 0;
                $ca3 = $scoreData['ca3_score'] ?? 0;
                $examScore = $scoreData['exam_score'] ?? 0;
                $totalScore = $ca1 + $ca2 + $ca3 + $examScore;
                
                // Get grade
                $grade = GradeScale::where('min_score', '<=', $totalScore)
                    ->where('max_score', '>=', $totalScore)
                    ->first();

                // Resolve exam_id (if provided or infer from class+term). Fallback to normalized term exam if class-specific not found.
                $normalizedTerm = $this->normalizeExamTerm($request->term);
                $examId = $request->exam_id ?? Exam::where('class_id', $request->class_id)
                                ->where('term', $normalizedTerm)
                                ->value('id');
                if (!$examId) {
                    $examId = Exam::where('term', $normalizedTerm)
                                ->where('is_published', true)
                                ->orderBy('created_at', 'desc')
                                ->value('id');
                }

                if (!$examId) {
                    throw new \Exception("No exam found for class {$request->class_id} and term {$request->term}. Create or select an exam first.");
                }

                // Create or update result
                Result::updateOrCreate(
                    [
                        'student_id' => $request->student_id,
                        'subject_id' => $subjectId,
                        'exam_id' => $examId,
                    ],
                    [
                        'term' => $normalizedTerm,
                        'exam_id' => $examId,
                        'class_id' => $request->class_id,
                        'ca1_score' => $ca1,
                        'ca2_score' => $ca2,
                        'ca3_score' => $ca3,
                        'exam_score' => $examScore,
                        'total_score' => $totalScore,
                        'score' => $totalScore,
                        'grade' => $grade ? $grade->grade : null,
                        'remark' => $grade ? $grade->remark : null,
                    ]
                );

                $savedCount++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $savedCount . ' scores saved successfully!',
                'count' => $savedCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error saving scores: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== BULK ENTRY ====================
    
    // Bulk Entry View
    public function bulkEntry(Request $request)
    {
        $classId = $request->query('class_id');
        $term = $request->query('term');
        $subjectId = $request->query('subject_id');
        
        $classes = $this->availableClasses();
        $subjects = $this->availableSubjects();
        
        $class = null;
        $subject = null;
        $students = collect();
        $existingScores = collect();
        
        if ($classId) {
            $class = ClassModel::findOrFail($classId);
        }
        
        if ($subjectId) {
            $subject = Subject::findOrFail($subjectId);
        }
        
        if ($classId && $subjectId && $term) {
            $students = Student::where('class_id', $classId)
                ->where('is_active', true)
                ->whereHas('subjects', function ($query) use ($subjectId) {
                    $query->where('subjects.id', $subjectId);
                })
                ->orderBy('first_name')
                ->get();
            
            $existingScores = Result::where('class_id', $classId)
                ->where('subject_id', $subjectId)
                ->where('term', $term)
                ->get()
                ->keyBy('student_id');
        }
        
        return view('scores.bulk-entry', compact(
            'classes', 'subjects', 'class', 'subject', 
            'students', 'existingScores', 'term', 'classId', 'subjectId'
        ));
    }

    // Get Bulk Scores (AJAX)
    public function getBulkScores(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'term' => 'required|string',
        ]);

        try {
            $class = ClassModel::findOrFail($request->class_id);
            $subject = Subject::findOrFail($request->subject_id);
            
            $students = Student::where('class_id', $request->class_id)
                ->where('is_active', true)
                ->whereHas('subjects', function ($query) use ($request) {
                    $query->where('subjects.id', $request->subject_id);
                })
                ->orderBy('first_name')
                ->get();
            
            $scores = Result::where('class_id', $request->class_id)
                ->where('subject_id', $request->subject_id)
                ->where('term', $request->term)
                ->get()
                ->keyBy('student_id');
            
            return response()->json([
                'success' => true,
                'class_name' => $class->full_class_name,
                'subject' => $subject,
                'students' => $students,
                'scores' => $scores
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading scores: ' . $e->getMessage()
            ], 500);
        }
    }

    // Save Bulk Entry (AJAX)
    public function saveBulkEntry(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'term' => 'required|string',
            'exam_id' => 'nullable|exists:exams,id',
            'scores' => 'required|array',
        ]);

        try {
            DB::beginTransaction();

            $savedCount = 0;
            foreach ($request->scores as $studentId => $scoreData) {
                $student = Student::findOrFail($studentId);
                
                $ca1 = $scoreData['ca1_score'] ?? 0;
                $ca2 = $scoreData['ca2_score'] ?? 0;
                $ca3 = $scoreData['ca3_score'] ?? 0;
                $examScore = $scoreData['exam_score'] ?? 0;
                $totalScore = $ca1 + $ca2 + $ca3 + $examScore;
                
                // Get grade
                $grade = GradeScale::where('min_score', '<=', $totalScore)
                    ->where('max_score', '>=', $totalScore)
                    ->first();

                // Resolve exam_id (fallback to term-only published exam)
                $normalizedTerm = $this->normalizeExamTerm($request->term);
                $examId = $request->exam_id ?? Exam::where('class_id', $request->class_id)
                                ->where('term', $normalizedTerm)
                                ->value('id');
                if (!$examId) {
                    $examId = Exam::where('term', $normalizedTerm)
                                ->where('is_published', true)
                                ->orderBy('created_at', 'desc')
                                ->value('id');
                }

                if (!$examId) {
                    throw new \Exception("No exam found for class {$request->class_id} and term {$request->term}. Create or select an exam first.");
                }

                // Create or update result
                Result::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'subject_id' => $request->subject_id,
                        'exam_id' => $examId,
                    ],
                    [
                        'term' => $request->term,
                        'exam_id' => $examId,
                        'class_id' => $request->class_id,
                        'ca1_score' => $ca1,
                        'ca2_score' => $ca2,
                        'ca3_score' => $ca3,
                        'exam_score' => $examScore,
                        'total_score' => $totalScore,
                        'score' => $totalScore,
                        'grade' => $grade ? $grade->grade : null,
                        'remark' => $grade ? $grade->remark : null,
                    ]
                );
                $savedCount++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $savedCount . ' scores saved successfully!',
                'count' => $savedCount
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error saving scores: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==================== IMPORT SCORES ====================
    
    // Import Scores View
    public function importScores(Request $request)
    {
        $classId = $request->query('class_id');
        $term = $request->query('term');
        $subjectId = $request->query('subject_id');
        
        $classes = $this->availableClasses();
        $subjects = $this->availableSubjects();
        
        $class = $classId ? ClassModel::findOrFail($classId) : null;
        $subject = $subjectId ? Subject::findOrFail($subjectId) : null;
        
        return view('scores.import', compact('classes', 'subjects', 'class', 'subject', 'term', 'classId', 'subjectId'));
    }

    // Process Import
    public function processImport(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'term' => 'required|string',
            'file' => 'required|file|mimes:csv,xlsx,xls',
        ]);

        try {
            DB::beginTransaction();

            $classId = $request->class_id;
            $subjectId = $request->subject_id;
            $term = $request->term;
            
            // Parse file
            $file = $request->file('file');
            $data = Excel::toArray(new ScoresImport, $file);
            
            $scores = [];
            $row = 0;
            
            foreach ($data[0] as $rowData) {
                $row++;
                if ($row == 1) continue; // Skip header
                
                // Find student by admission number
                $student = Student::where('admission_number', $rowData[0])
                    ->where('class_id', $classId)
                    ->first();
                
                if (!$student) {
                    continue;
                }
                
                $ca1 = $rowData[1] ?? 0;
                $ca2 = $rowData[2] ?? 0;
                $ca3 = $rowData[3] ?? 0;
                $examScore = $rowData[4] ?? 0;
                $totalScore = $ca1 + $ca2 + $ca3 + $examScore;
                
                $grade = GradeScale::where('min_score', '<=', $totalScore)
                    ->where('max_score', '>=', $totalScore)
                    ->first();
                
                $scores[] = [
                    'student_id' => $student->id,
                    'subject_id' => $subjectId,
                    'class_id' => $classId,
                    'ca1_score' => $ca1,
                    'ca2_score' => $ca2,
                    'ca3_score' => $ca3,
                    'exam_score' => $examScore,
                    'total_score' => $totalScore,
                    'grade' => $grade ? $grade->grade : null,
                    'remark' => $grade ? $grade->remark : null,
                    'term' => $term,
                ];
            }
            
            // Bulk insert
            foreach ($scores as $score) {
                // Resolve exam id for this class+term (fallback to term-only published exam)
                $normalizedTerm = $this->normalizeExamTerm($score['term']);
                $examId = Exam::where('class_id', $score['class_id'])
                    ->where('term', $normalizedTerm)
                    ->value('id');
                if (!$examId) {
                    $examId = Exam::where('term', $normalizedTerm)
                        ->where('is_published', true)
                        ->orderBy('created_at', 'desc')
                        ->value('id');
                }
                if (!$examId) {
                    throw new \Exception("No exam found for class {$score['class_id']} and term {$score['term']}. Create or select an exam first.");
                }

                $score['exam_id'] = $examId;
                $score['score'] = $score['total_score'];

                Result::updateOrCreate(
                    [
                        'student_id' => $score['student_id'],
                        'subject_id' => $score['subject_id'],
                        'exam_id' => $score['exam_id'],
                    ],
                    $score
                );
            }
            
            DB::commit();

            return redirect()->route('scores.index')
                ->with('success', count($scores) . ' scores imported successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error importing scores: ' . $e->getMessage());
        }
    }

    // Download Template
    public function downloadTemplate(Request $request)
    {
        $classId = $request->query('class_id');
        $subjectId = $request->query('subject_id');
        $term = $request->query('term');
        
        $class = ClassModel::findOrFail($classId);
        $subject = Subject::findOrFail($subjectId);
        
        $filename = 'scores_template_' . $class->code . '_' . $subject->code . '.csv';
        
        $handle = fopen('php://temp', 'w+');
        fputcsv($handle, ['Admission Number', 'Student Name', 'CA1', 'CA2', 'CA3', 'Exam Score']);
        
        // Add sample data
        $students = Student::where('class_id', $classId)->limit(3)->get();
        foreach ($students as $student) {
            $row = [$student->admission_number, $student->full_name, '', '', '', ''];
            fputcsv($handle, $row);
        }
        
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);
        
        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    // ==================== ADDITIONAL FEATURES ====================
    
    // Auto-calculate student position
    public function calculatePositions(Request $request)
    {
        $classId = $request->query('class_id');
        $subjectId = $request->query('subject_id');
        $term = $request->query('term');
        
        try {
            DB::beginTransaction();

            $results = Result::where('class_id', $classId)
                ->where('subject_id', $subjectId)
                ->where('term', $term)
                ->get()
                ->groupBy('student_id')
                ->map(function($studentResults) {
                    return $studentResults->sum('total_score');
                })
                ->sortDesc();

            $position = 1;
            $previousScore = null;
            $positionMap = [];
            
            foreach ($results as $studentId => $score) {
                if ($previousScore !== null && $score < $previousScore) {
                    $position++;
                }
                $positionMap[$studentId] = $position;
                $previousScore = $score;
            }

            foreach ($positionMap as $studentId => $position) {
                Result::where('class_id', $classId)
                    ->where('subject_id', $subjectId)
                    ->where('term', $term)
                    ->where('student_id', $studentId)
                    ->update(['position' => $position]);
            }

            DB::commit();

            return redirect()->route('scores.index')->with('success', 'Positions calculated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error calculating positions: ' . $e->getMessage());
        }
    }

    // Publish Results
    public function publishResults(Request $request)
    {
        $classId = $request->input('class_id');
        $subjectId = $request->input('subject_id');
        $term = $request->input('term');
        
        try {
            DB::beginTransaction();

            Result::where('class_id', $classId)
                ->where('subject_id', $subjectId)
                ->where('term', $term)
                ->update([
                    'is_published' => true,
                    'published_at' => now(),
                ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Results published successfully!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error publishing results: ' . $e->getMessage()
            ], 500);
        }
    }

    // Auto-fill Scores
    public function autoFillScores(Request $request)
    {
        $classId = $request->query('class_id');
        $subjectId = $request->query('subject_id');
        $term = $request->query('term');
        
        try {
            DB::beginTransaction();

            $students = Student::where('class_id', $classId)->where('is_active', true)->get();

            foreach ($students as $student) {
                // Generate random but realistic scores
                $ca1 = rand(20, 28);
                $ca2 = rand(22, 29);
                $ca3 = rand(20, 27);
                $examScore = rand(40, 65);
                $totalScore = $ca1 + $ca2 + $ca3 + $examScore;

                $grade = GradeScale::where('min_score', '<=', $totalScore)
                    ->where('max_score', '>=', $totalScore)
                    ->first();

                Result::updateOrCreate(
                    [
                        'student_id' => $student->id,
                        'subject_id' => $subjectId,
                        'term' => $term,
                    ],
                    [
                        'exam_id' => Exam::where('class_id', $classId)->where('term', $term)->value('id'),
                        'class_id' => $classId,
                        'ca1_score' => $ca1,
                        'ca2_score' => $ca2,
                        'ca3_score' => $ca3,
                        'exam_score' => $examScore,
                        'total_score' => $totalScore,
                        'grade' => $grade ? $grade->grade : null,
                        'remark' => $grade ? $grade->remark : null,
                    ]
                );
            }

            DB::commit();

            return redirect()->route('scores.index')->with('success', 'Auto-fill scores completed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error auto-filling scores: ' . $e->getMessage());
        }
    }

    // ==================== PROGRESS & MANAGEMENT ====================
    
    /**
     * Get progress data for score submission
     */
    public function getProgress(Request $request)
    {
        $term = $request->query('term');
        $classId = $request->query('class_id');
        
        $query = ClassModel::where('is_active', true);
        
        if ($classId) {
            $query->where('id', $classId);
        }
        
        $classes = $query->orderBy('name')->get();
        $progressData = [];
        
        foreach ($classes as $class) {
            $subjects = Subject::whereHas('classes', function($q) use ($class) {
                $q->where('class_id', $class->id);
            })->where('is_active', true)->get();
            
            $subjectData = [];
            foreach ($subjects as $subject) {
                // Get students in this class
                $totalStudents = Student::where('class_id', $class->id)
                    ->where('is_active', true)
                    ->count();
                
                // Get submitted scores for this subject
                $scoresQuery = Result::where('class_id', $class->id)
                    ->where('subject_id', $subject->id);
                
                if ($term) {
                    $scoresQuery->where('term', $term);
                }
                
                $submitted = $scoresQuery->distinct('student_id')->count('student_id');
                
                // Calculate average and pass rate
                $results = $scoresQuery->get();
                $totalScore = 0;
                $passCount = 0;
                
                foreach ($results as $result) {
                    $totalScore += $result->total_score;
                    if ($result->total_score >= 40) {
                        $passCount++;
                    }
                }
                
                $averageScore = $submitted > 0 ? round($totalScore / $submitted, 1) : 0;
                $passRate = $submitted > 0 ? round(($passCount / $submitted) * 100, 1) : 0;
                $percentage = $totalStudents > 0 ? round(($submitted / $totalStudents) * 100, 1) : 0;
                
                // Get teacher for this subject
                $teacher = Employee::whereHas('subjects', function($q) use ($subject) {
                    $q->where('subject_id', $subject->id);
                })->first();
                
                $subjectData[] = [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'teacher' => $teacher ? $teacher->full_name : 'Not Assigned',
                    'total_students' => $totalStudents,
                    'submitted' => $submitted,
                    'percentage' => $percentage,
                    'average_score' => $averageScore,
                    'pass_rate' => $passRate,
                ];
            }
            
            $progressData[] = [
                'class_id' => $class->id,
                'class_name' => $class->full_class_name,
                'subject_count' => count($subjectData),
                'subjects' => $subjectData,
            ];
        }
        
        return response()->json($progressData);
    }

    /**
     * Get students with scores for a specific subject
     */
    public function getSubjectStudents(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'term' => 'nullable|string',
        ]);
        
        $classId = $request->class_id;
        $subjectId = $request->subject_id;
        $term = $request->term;
        
        // Get students in this class
        $students = Student::where('class_id', $classId)
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get();
        
        // Get scores for this subject
        $scores = Result::where('class_id', $classId)
            ->where('subject_id', $subjectId);
        
        if ($term) {
            $scores->where('term', $term);
        }
        
        $scores = $scores->get()->keyBy('student_id');
        
        $result = [];
        foreach ($students as $student) {
            $score = $scores->get($student->id);
            $result[] = [
                'id' => $student->id,
                'admission_number' => $student->admission_number,
                'full_name' => $student->full_name,
                'score' => $score ? [
                    'id' => $score->id,
                    'ca1_score' => $score->ca1_score,
                    'ca2_score' => $score->ca2_score,
                    'ca3_score' => $score->ca3_score,
                    'exam_score' => $score->exam_score,
                    'total_score' => $score->total_score,
                    'grade' => $score->grade,
                ] : null,
            ];
        }
        
        return response()->json($result);
    }

    /**
     * Lock scores for a subject
     */
    public function lockScores(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'term' => 'required|string',
        ]);
        
        try {
            DB::beginTransaction();
            
            Result::where('class_id', $request->class_id)
                ->where('subject_id', $request->subject_id)
                ->where('term', $request->term)
                ->update(['is_locked' => true]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Scores locked successfully!'
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error locking scores: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export scores
     */
    public function exportScores(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'term' => 'required|string',
        ]);
        
        $class = ClassModel::findOrFail($request->class_id);
        $subject = Subject::findOrFail($request->subject_id);
        
        $students = Student::where('class_id', $request->class_id)
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get();
        
        $scores = Result::where('class_id', $request->class_id)
            ->where('subject_id', $request->subject_id)
            ->where('term', $request->term)
            ->get()
            ->keyBy('student_id');
        
        $filename = 'scores_' . $class->code . '_' . $subject->code . '_' . $request->term . '.csv';
        
        $handle = fopen('php://temp', 'w+');
        
        // Headers
        fputcsv($handle, ['Admission Number', 'Student Name', 'CA1', 'CA2', 'CA3', 'Exam', 'Total', 'Grade']);
        
        // Data
        foreach ($students as $student) {
            $score = $scores->get($student->id);
            fputcsv($handle, [
                $student->admission_number,
                $student->full_name,
                $score ? $score->ca1_score : '',
                $score ? $score->ca2_score : '',
                $score ? $score->ca3_score : '',
                $score ? $score->exam_score : '',
                $score ? $score->total_score : '',
                $score ? $score->grade : '',
            ]);
        }
        
        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);
        
        return response($content)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
    private function availableClasses()
    {
        $query = ClassModel::where('is_active', true);

        if (auth()->user()?->role === 'teacher') {
            $query->where('class_teacher_id', getCurrentEmployeeId() ?? 0);
        }

        return $query->orderBy('name')->get();
    }

    private function availableSubjects()
    {
        $query = Subject::where('is_active', true);

        if (auth()->user()?->role === 'teacher') {
            $query->where('teacher_id', getCurrentEmployeeId() ?? 0);
        }

        return $query->orderBy('name')->get();
    }

    /**
     * Normalize term labels from UI to stored exam term values.
     */
    private function normalizeExamTerm($term)
    {
        $map = [
            'First Term' => 'Term 1',
            '1st Term' => 'Term 1',
            'Second Term' => 'Term 2',
            '2nd Term' => 'Term 2',
            'Third Term' => 'Term 3',
            '3rd Term' => 'Term 3',
            'Term 1' => 'Term 1',
            'Term 2' => 'Term 2',
            'Term 3' => 'Term 3',
        ];
        return $map[$term] ?? $term;
    }
}
