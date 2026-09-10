<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Employee;
use App\Models\ClassModel;
use App\Models\Exam;
use App\Models\Result;
use App\Models\ClassSubject;
use App\Models\Student;
use App\Models\StudentSubject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    public function index()
    {
        $subjects = Subject::with(['teacher', 'classes'])
            ->orderBy('name')
            ->paginate(15);
        
        $totalSubjects = Subject::count();
        $coreSubjects = Subject::where('is_core', true)->count();
        $activeSubjects = Subject::where('is_active', true)->count();
        
        return view('subjects.index', compact('subjects', 'totalSubjects', 'coreSubjects', 'activeSubjects'));
    }

    public function create()
    {
        $teachers = Employee::where('is_active', true)->orderBy('first_name')->get();
        $departments = ['Science', 'Arts', 'Commercial', 'Social Sciences', 'Languages', 'Vocational', 'ICT', 'General Studies'];
        
        return view('subjects.create', compact('teachers', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:subjects',
            'department' => 'nullable|string',
            'teacher_id' => 'nullable|exists:employees,id',
            'credit_hours' => 'required|integer|min:1|max:10',
            'description' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $subject = Subject::create([
                'name' => $request->name,
                'code' => $request->code,
                'department' => $request->department,
                'teacher_id' => $request->teacher_id,
                'credit_hours' => $request->credit_hours,
                'is_core' => $request->has('is_core'),
                'is_elective' => $request->has('is_elective'),
                'description' => $request->description,
                'is_active' => true,
            ]);

            DB::commit();

            return redirect()->route('subjects.index')
                ->with('success', 'Subject created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating subject: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $subject = Subject::with(['teacher', 'classes.students', 'classes.classTeacher'])
            ->findOrFail($id);
        
        // Calculate statistics
        $totalClasses = $subject->classes()->count();
        $totalStudents = $subject->classes()->withCount('students')->get()->sum('students_count');
        $totalTeachers = $subject->classes()
            ->whereNotNull('teacher_id')
            ->distinct('teacher_id')
            ->count('teacher_id');
        
        // Get exams for this subject
        $exams = Exam::where('subject_id', $id)->get();
        $totalExams = $exams->count();
        
        // Calculate average score and pass rate from results using total_score
        $results = Result::whereHas('exam', function($q) use ($id) {
            $q->where('subject_id', $id);
        })->get();
        
        $averageScore = $results->avg('total_score') ?? 0;
        
        // Calculate pass rate (assuming passing marks is 40)
        $passCount = $results->where('total_score', '>=', 40)->count();
        $passRate = $results->count() > 0 ? ($passCount / $results->count()) * 100 : 0;
        
        $stats = [
            'total_classes' => $totalClasses,
            'total_students' => $totalStudents,
            'total_teachers' => $totalTeachers,
            'total_exams' => $totalExams,
            'average_score' => round($averageScore, 1),
            'pass_rate' => round($passRate, 1),
        ];
        
        // Get class performance data using total_score
        $classPerformance = collect();
        foreach ($subject->classes as $class) {
            $studentCount = $class->students()->where('is_active', true)->count();
            
            // Get results for this class and subject
            $classResults = Result::whereHas('exam', function($q) use ($id, $class) {
                $q->where('subject_id', $id)
                  ->where('class_id', $class->id);
            })->get();
            
            $average = $classResults->avg('total_score') ?? 0;
            $highest = $classResults->max('total_score') ?? 0;
            $lowest = $classResults->min('total_score') ?? 0;
            
            $classPerformance->push([
                'class' => $class,
                'students' => $studentCount,
                'average' => $average,
                'highest' => $highest,
                'lowest' => $lowest,
            ]);
        }
        
        // Get top performing students using total_score
        $topStudents = Result::with('student')
            ->whereHas('exam', function($q) use ($id) {
                $q->where('subject_id', $id);
            })
            ->select('student_id', DB::raw('AVG(total_score) as total_score'))
            ->groupBy('student_id')
            ->orderBy('total_score', 'desc')
            ->limit(10)
            ->get();
        
        // Get recent exams
        $recentExams = Exam::where('subject_id', $id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return view('subjects.show', compact(
            'subject', 
            'stats', 
            'classPerformance',
            'topStudents',
            'recentExams'
        ));
    }

    /**
     * Display all classes for a specific subject
     */
    public function classes($id)
    {
        $subject = Subject::with(['classes.classTeacher'])->findOrFail($id);
        return view('subjects.classes', compact('subject'));
    }

    /**
     * Display all teachers for a specific subject
     */
    public function teachers($id)
    {
        $subject = Subject::findOrFail($id);
        $teachers = Employee::whereIn('id', function($query) use ($id) {
            $query->select('teacher_id')
                ->from('class_subject')
                ->where('subject_id', $id)
                ->whereNotNull('teacher_id');
        })->get();
        
        return view('subjects.teachers', compact('subject', 'teachers'));
    }

    /**
     * Show form to assign subject to a class
     */
    public function showAssignClassForm($id)
    {
        $subject = Subject::findOrFail($id);
        $availableClasses = ClassModel::where('is_active', true)
            ->whereDoesntHave('subjects', function($q) use ($id) {
                $q->where('subject_id', $id);
            })
            ->get();
        
        $teachers = Employee::where('is_active', true)->orderBy('first_name')->get();
        $terms = ['First Term', 'Second Term', 'Third Term'];
        
        return view('subjects.assign-class', compact('subject', 'availableClasses', 'teachers', 'terms'));
    }

    /**
     * Assign subject to a class
     */
    public function assignClass(Request $request, $id)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'teacher_id' => 'nullable|exists:employees,id',
            'max_marks' => 'required|integer|min:1',
            'passing_marks' => 'required|integer|min:0',
            'term' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $subject = Subject::findOrFail($id);
            $class = ClassModel::findOrFail($request->class_id);
            
            // Check if already assigned
            if ($class->subjects()->where('subject_id', $id)->where('term', $request->term)->exists()) {
                return back()->with('error', 'Subject already assigned to this class for ' . $request->term);
            }

            $class->subjects()->attach($id, [
                'teacher_id' => $request->teacher_id,
                'max_marks' => $request->max_marks,
                'passing_marks' => $request->passing_marks,
                'is_required' => $request->has('is_required'),
                'term' => $request->term,
                'academic_year' => $request->academic_year ?? date('Y') . '/' . (date('Y') + 1),
            ]);

            DB::commit();

            return redirect()->route('subjects.show', $id)
                ->with('success', 'Subject assigned to class successfully for ' . $request->term . '!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error assigning subject to class: ' . $e->getMessage());
        }
    }

    /**
     * Display exams for a specific subject
     */
    public function exams($id)
    {
        $subject = Subject::findOrFail($id);
        $exams = Exam::where('subject_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('subjects.exams', compact('subject', 'exams'));
    }

    public function edit($id)
    {
        $subject = Subject::findOrFail($id);
        $teachers = Employee::where('is_active', true)->orderBy('first_name')->get();
        $departments = ['Science', 'Arts', 'Commercial', 'Social Sciences', 'Languages', 'Vocational', 'ICT', 'General Studies'];
        
        return view('subjects.edit', compact('subject', 'teachers', 'departments'));
    }

    public function update(Request $request, $id)
    {
        $subject = Subject::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:subjects,code,' . $id,
            'department' => 'nullable|string',
            'teacher_id' => 'nullable|exists:employees,id',
            'credit_hours' => 'required|integer|min:1|max:10',
            'description' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $subject->update([
                'name' => $request->name,
                'code' => $request->code,
                'department' => $request->department,
                'teacher_id' => $request->teacher_id,
                'credit_hours' => $request->credit_hours,
                'is_core' => $request->has('is_core'),
                'is_elective' => $request->has('is_elective'),
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
            ]);

            DB::commit();

            return redirect()->route('subjects.show', $id)
                ->with('success', 'Subject updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating subject: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $subject = Subject::findOrFail($id);
            
            if ($subject->classes()->count() > 0) {
                return back()->with('error', 'Cannot delete subject assigned to classes.');
            }
            
            $subject->delete();

            return redirect()->route('subjects.index')
                ->with('success', 'Subject deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting subject: ' . $e->getMessage());
        }
    }

    // ==================== SUBJECT ASSIGNMENT METHODS ====================

    /**
     * Show the subject assignment form.
     * Admin can assign subjects to classes with teachers.
     */
    public function assignForm()
    {
        $classes = ClassModel::where('is_active', true)->get();
        $subjects = Subject::where('is_active', true)->get();
        $teachers = Employee::where('is_active', true)
            ->whereIn('position', ['Teacher', 'Head Teacher', 'Senior Teacher'])
            ->get();

        // Get existing assignments
        $assignments = ClassSubject::with(['class', 'subject', 'teacher'])
            ->get()
            ->groupBy('class_id');

        return view('subjects.assign', compact('classes', 'subjects', 'teachers', 'assignments'));
    }

    /**
     * Assign a subject to a class with a teacher.
     */
    public function assignSubject(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'required|exists:employees,id',
        ]);

        try {
            // Check if already assigned
            $exists = ClassSubject::where('class_id', $request->class_id)
                ->where('subject_id', $request->subject_id)
                ->exists();

            if ($exists) {
                return redirect()->back()
                    ->with('error', 'This subject is already assigned to this class.');
            }

            ClassSubject::create([
                'class_id' => $request->class_id,
                'subject_id' => $request->subject_id,
                'teacher_id' => $request->teacher_id,
            ]);

            return redirect()->route('subjects.assign')
                ->with('success', 'Subject assigned to class successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error assigning subject: ' . $e->getMessage());
        }
    }

    /**
     * Get assigned subjects for a class.
     */
    public function getAssignedSubjects($classId)
    {
        $assignments = ClassSubject::with(['subject', 'teacher'])
            ->where('class_id', $classId)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $assignments
        ]);
    }

    /**
     * Remove a subject assignment.
     */
    public function removeAssignment($assignmentId)
    {
        try {
            $assignment = ClassSubject::findOrFail($assignmentId);
            $assignment->delete();

            return redirect()->route('subjects.assign')
                ->with('success', 'Assignment removed successfully.');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error removing assignment: ' . $e->getMessage());
        }
    }

    // ==================== STUDENT SUBJECT ASSIGNMENT METHODS ====================

    /**
     * Show the student subject assignment page.
     * Admin can assign subjects to individual students.
     */
    public function studentSubjects()
    {
        $students = \App\Models\Student::with(['user', 'class', 'subjects'])
            ->where('is_active', true)
            ->get();

        foreach ($students as $student) {
            $student->autoAssignRequiredSubjects();
        }

        $students->load('subjects');
        $subjects = Subject::where('is_active', true)->get();
        $classes = ClassModel::where('is_active', true)->get();

        return view('subjects.student-subjects', compact('students', 'subjects', 'classes'));
    }

    /**
     * Assign a subject to a student.
     */
    public function assignStudentSubject(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        try {
            $exists = StudentSubject::where('student_id', $request->student_id)
                ->where('subject_id', $request->subject_id)
                ->exists();

            if ($exists) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'This subject is already assigned to the student.'], 422);
                }

                return redirect()->back()
                    ->with('error', 'This subject is already assigned to the student.');
            }

            StudentSubject::create([
                'student_id' => $request->student_id,
                'subject_id' => $request->subject_id,
            ]);

            if ($request->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Subject assigned to student successfully.']);
            }

            return redirect()->route('subjects.student-subjects')
                ->with('success', 'Subject assigned to student successfully.');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error assigning subject: ' . $e->getMessage()], 500);
            }

            return redirect()->back()
                ->with('error', 'Error assigning subject: ' . $e->getMessage());
        }
    }

    /**
     * Bulk assign one or more subjects to a class.
     */
    public function bulkAssign(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_ids' => 'required|array',
            'subject_ids.*' => 'exists:subjects,id',
            'teacher_id' => 'nullable|exists:employees,id',
            'term' => 'nullable|string',
            'academic_year' => 'nullable|string',
        ]);

        $created = 0;

        DB::beginTransaction();
        try {
            foreach ($request->subject_ids as $subjectId) {
                $exists = ClassSubject::where('class_id', $request->class_id)
                    ->where('subject_id', $subjectId)
                    ->where('term', $request->term)
                    ->exists();

                if ($exists) {
                    continue;
                }

                ClassSubject::create([
                    'class_id' => $request->class_id,
                    'subject_id' => $subjectId,
                    'teacher_id' => $request->teacher_id,
                    'max_marks' => $request->max_marks ?? 100,
                    'passing_marks' => $request->passing_marks ?? 40,
                    'is_required' => $request->boolean('is_required', true),
                    'term' => $request->term ?? session('current_term', 'First Term'),
                    'academic_year' => $request->academic_year ?? date('Y') . '/' . (date('Y') + 1),
                ]);

                $created++;
            }

            DB::commit();

            return redirect()->route('subjects.assign')
                ->with('success', "{$created} subject assignments created successfully.");
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Error bulk assigning subjects: ' . $e->getMessage());
        }
    }

    /**
     * Remove a subject from a student.
     */
    public function removeStudentSubject($studentId, $subjectId)
    {
        try {
            $student = Student::with('class')->findOrFail($studentId);

            if ($student->class && $student->class->subjects()
                ->where('subjects.id', $subjectId)
                ->wherePivot('is_required', true)
                ->exists()) {
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Required subjects cannot be removed.'], 403);
                }

                return redirect()->back()
                    ->with('error', 'Required subjects cannot be removed.');
            }

            $assignment = StudentSubject::where('student_id', $studentId)
                ->where('subject_id', $subjectId)
                ->firstOrFail();

            $assignment->delete();

            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Subject removed from student successfully.']);
            }

            return redirect()->route('subjects.student-subjects')
                ->with('success', 'Subject removed from student successfully.');

        } catch (\Exception $e) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Error removing subject: ' . $e->getMessage()], 500);
            }

            return redirect()->back()
                ->with('error', 'Error removing subject: ' . $e->getMessage());
        }
    }

    /**
     * Get subjects for a specific student.
     */
    public function getStudentSubjects($studentId)
    {
        $student = Student::with(['class.subjects' => function ($query) {
            $query->orderBy('name');
        }, 'subjects'])->findOrFail($studentId);

        $student->autoAssignRequiredSubjects();
        $student->load('subjects');

        $classSubjects = $student->class ? $student->class->subjects()->orderBy('name')->get() : collect();
        $assignedSubjectIds = $student->subjects->pluck('id')->toArray();

        $subjectData = $classSubjects->map(function ($subject) use ($assignedSubjectIds) {
            return [
                'id' => $subject->id,
                'name' => $subject->name,
                'code' => $subject->code,
                'is_required' => optional($subject->pivot)->is_required ?? false,
                'assigned' => in_array($subject->id, $assignedSubjectIds),
                'source' => 'class',
            ];
        });

        $extraSubjects = $student->subjects->filter(function ($subject) use ($assignedSubjectIds, $classSubjects) {
            return !$classSubjects->pluck('id')->contains($subject->id);
        });

        foreach ($extraSubjects as $subject) {
            $subjectData->push([
                'id' => $subject->id,
                'name' => $subject->name,
                'code' => $subject->code,
                'is_required' => false,
                'assigned' => true,
                'source' => 'extra',
            ]);
        }

        return response()->json([
            'success' => true,
            'student' => [
                'id' => $student->id,
                'full_name' => $student->full_name,
                'admission_number' => $student->admission_number,
                'class_name' => optional($student->class)->full_class_name,
            ],
            'subjects' => $subjectData,
            'assigned_count' => count($assignedSubjectIds),
            'class_subject_count' => $classSubjects->count(),
        ]);
    }

    /**
     * Get students for a specific class who don't have a subject assigned.
     */
    public function getAvailableStudents($classId, $subjectId)
    {
        $students = \App\Models\Student::where('class_id', $classId)
            ->where('is_active', true)
            ->whereDoesntHave('subjects', function($q) use ($subjectId) {
                $q->where('subject_id', $subjectId);
            })
            ->get();

        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }

    /**
     * Bulk assign subject to all students in a class.
     */
    public function bulkAssignSubject(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'subject_id' => 'required|exists:subjects,id',
        ]);

        try {
            $students = \App\Models\Student::where('class_id', $request->class_id)
                ->where('is_active', true)
                ->get();

            $count = 0;
            foreach ($students as $student) {
                $exists = StudentSubject::where('student_id', $student->id)
                    ->where('subject_id', $request->subject_id)
                    ->exists();

                if (!$exists) {
                    StudentSubject::create([
                        'student_id' => $student->id,
                        'subject_id' => $request->subject_id,
                    ]);
                    $count++;
                }
            }

            return redirect()->route('subjects.student-subjects')
                ->with('success', "Subject assigned to {$count} students successfully.");

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error assigning subject: ' . $e->getMessage());
        }
    }

    /**
     * Bulk remove a subject from selected students or a whole class.
     */
    public function bulkRemoveSubject(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'class_id' => 'nullable|exists:classes,id',
            'student_ids' => 'nullable|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        $query = StudentSubject::where('subject_id', $request->subject_id);

        if ($request->filled('student_ids')) {
            $query->whereIn('student_id', $request->student_ids);
        } elseif ($request->filled('class_id')) {
            $studentIds = \App\Models\Student::where('class_id', $request->class_id)->pluck('id');
            $query->whereIn('student_id', $studentIds);
        }

        $count = $query->delete();

        return redirect()->route('subjects.student-subjects')
            ->with('success', "{$count} student subject assignments removed successfully.");
    }

    /**
     * Get active students in a class who are missing one or all subject assignments.
     */
    public function getMissingStudents($classId)
    {
        $subjectId = request('subject_id');

        $students = \App\Models\Student::where('class_id', $classId)
            ->where('is_active', true)
            ->when($subjectId, function ($query) use ($subjectId) {
                $query->whereDoesntHave('subjects', function ($subjectQuery) use ($subjectId) {
                    $subjectQuery->where('subjects.id', $subjectId);
                });
            })
            ->get();

        return response()->json([
            'success' => true,
            'data' => $students,
        ]);
    }

    /**
     * Display subject statistics.
     */
    public function statistics()
    {
        $subjects = Subject::withCount(['classes', 'students', 'exams'])
            ->orderBy('name')
            ->paginate(20);

        return view('subjects.statistics', compact('subjects'));
    }

    /**
     * Display the subject report page.
     */
    public function report()
    {
        $subjects = Subject::with(['teacher'])->withCount(['classes', 'students'])->orderBy('name')->get();
        $departments = Subject::select('department')->whereNotNull('department')->distinct()->orderBy('department')->pluck('department');

        return view('subjects.report', compact('subjects', 'departments'));
    }

    /**
     * Export subjects as a CSV download.
     */
    public function export()
    {
        $subjects = Subject::with('teacher')->orderBy('name')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="subjects.csv"',
        ];

        return response()->stream(function () use ($subjects) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Name', 'Code', 'Department', 'Teacher', 'Credit Hours', 'Active']);

            foreach ($subjects as $subject) {
                fputcsv($handle, [
                    $subject->name,
                    $subject->code,
                    $subject->department,
                    optional($subject->teacher)->full_name ?? optional($subject->teacher)->name,
                    $subject->credit_hours,
                    $subject->is_active ? 'Yes' : 'No',
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    /**
     * Display performance for a subject.
     */
    public function performance($id)
    {
        $subject = Subject::findOrFail($id);
        $results = Result::with(['student', 'exam'])
            ->where('subject_id', $id)
            ->latest()
            ->paginate(20);

        $stats = [
            'average' => Result::where('subject_id', $id)->avg('total_score') ?? 0,
            'highest' => Result::where('subject_id', $id)->max('total_score') ?? 0,
            'lowest' => Result::where('subject_id', $id)->min('total_score') ?? 0,
            'entries' => Result::where('subject_id', $id)->count(),
        ];

        return view('subjects.performance', compact('subject', 'results', 'stats'));
    }

    /**
     * Display students taking a subject.
     */
    public function studentsList($id)
    {
        $subject = Subject::with(['students.class'])->findOrFail($id);
        $students = $subject->students()->with('class')->paginate(20);

        return view('subjects.students', compact('subject', 'students'));
    }
}
