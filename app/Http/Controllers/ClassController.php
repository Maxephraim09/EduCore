<?php

namespace App\Http\Controllers;

use App\Models\ClassCategory;
use App\Models\ClassModel;
use App\Models\Subject;
use App\Models\Employee;
use App\Models\Student;
use App\Models\Exam;
use App\Models\Result;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $categories = ClassCategory::where('is_active', true)->orderBy('name')->get();

        $query = ClassModel::with(['classTeacher', 'students', 'category']);

        if (auth()->user()?->role === 'teacher') {
            $teacherId = getCurrentEmployeeId();
            $query->where('class_teacher_id', $teacherId ?? 0);
        }

        if ($request->filled('category_id')) {
            $query->where('class_category_id', $request->category_id);
        }

        $classes = $query->orderBy('name')
            ->orderBy('section')
            ->paginate(15)
            ->appends($request->only('category_id'));
        
        $levels = ClassModel::getLevels();
        $totalClasses = ClassModel::count();
        $totalStudents = Student::where('is_active', true)->count();
        $activeClasses = ClassModel::where('is_active', true)->count();
        
        return view('classes.index', compact('classes', 'levels', 'totalClasses', 'totalStudents', 'activeClasses', 'categories'));
    }

    public function create()
    {
        $teachers = Employee::where('is_active', true)
            ->where(function($q) {
                $q->where('position', 'like', '%Teacher%')
                  ->orWhere('position', 'like', '%Head%');
            })
            ->orderBy('first_name')
            ->get();
        
        $levels = ['JSS 1', 'JSS 2', 'JSS 3', 'SSS 1', 'SSS 2', 'SSS 3'];
        $sections = ['A', 'B', 'C', 'D', 'E'];
        $categories = ClassCategory::where('is_active', true)->orderBy('name')->get();
        
        return view('classes.create', compact('teachers', 'levels', 'sections', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:classes',
            'section' => 'nullable|string|max:10',
            'class_category_id' => 'nullable|exists:class_categories,id',
            'class_teacher_id' => 'nullable|exists:employees,id',
            'capacity' => 'required|integer|min:1',
            'academic_year' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $fullName = $request->name . ($request->section ? ' ' . $request->section : '');

            $class = ClassModel::create([
                'name' => $request->name,
                'code' => $request->code,
                'section' => $request->section,
                'full_name' => $fullName,
                'class_category_id' => $request->class_category_id,
                'class_teacher_id' => $request->class_teacher_id,
                'capacity' => $request->capacity,
                'academic_year' => $request->academic_year,
                'description' => $request->description,
                'is_active' => true,
            ]);

            DB::commit();

            return redirect()->route('classes.index')
                ->with('success', 'Class ' . $fullName . ' created successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating class: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $class = ClassModel::with(['classTeacher', 'students', 'subjects.teacher'])
            ->findOrFail($id);

        if (auth()->user()?->role === 'teacher' && $class->class_teacher_id !== getCurrentEmployeeId()) {
            abort(403);
        }

        // Calculate statistics
        $totalStudents = $class->students()->where('is_active', true)->count();
        $maleStudents = $class->students()->where('is_active', true)->where('gender', 'Male')->count();
        $femaleStudents = $class->students()->where('is_active', true)->where('gender', 'Female')->count();
        $totalSubjects = $class->subjects()->count();
        $coreSubjects = $class->subjects()->wherePivot('is_required', true)->count();
        $electiveSubjects = $class->subjects()->wherePivot('is_required', false)->count();
        $capacityUsed = $class->capacity > 0 ? ($totalStudents / $class->capacity) * 100 : 0;
        $averageAge = $class->students()->where('is_active', true)->avg('age') ?? 0;

        $stats = [
            'total_students' => $totalStudents,
            'male_students' => $maleStudents,
            'female_students' => $femaleStudents,
            'total_subjects' => $totalSubjects,
            'core_subjects' => $coreSubjects,
            'elective_subjects' => $electiveSubjects,
            'capacity_used' => $capacityUsed,
            'average_age' => $averageAge,
        ];

        // Get top students (based on results)
        $topStudents = $this->getTopStudents($id);
        
        // Get subject performance
        $subjectPerformance = $this->getSubjectPerformance($id);
        
        // Get recent exams
        $recentExams = Exam::where('class_id', $id)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $availableSubjects = Subject::where('is_active', true)
            ->whereDoesntHave('classes', function($q) use ($id) {
                $q->where('class_id', $id);
            })
            ->get();

        $teachers = Employee::where('is_active', true)->orderBy('first_name')->get();
        $terms = ['First Term', 'Second Term', 'Third Term'];

        return view('classes.show', compact(
            'class', 
            'availableSubjects', 
            'teachers', 
            'terms',
            'stats',
            'topStudents',
            'subjectPerformance',
            'recentExams'
        ));
    }

    /**
     * Get top performing students for a class
     */
    private function getTopStudents($classId)
    {
        // Get all students in the class
        $students = Student::where('class_id', $classId)
            ->where('is_active', true)
            ->get();
        
        $topStudents = collect();
        
        foreach ($students as $student) {
            // Calculate total score from results
            $totalScore = Result::where('student_id', $student->id)
                ->whereHas('exam', function($q) use ($classId) {
                    $q->where('class_id', $classId);
                })
                ->sum('score');
            
            $resultCount = Result::where('student_id', $student->id)
                ->whereHas('exam', function($q) use ($classId) {
                    $q->where('class_id', $classId);
                })
                ->count();
            
            $average = $resultCount > 0 ? ($totalScore / $resultCount) : 0;
            
            if ($resultCount > 0) {
                $topStudents->push([
                    'student' => $student,
                    'total_score' => $totalScore,
                    'average' => $average,
                    'exam_count' => $resultCount,
                ]);
            }
        }
        
        return $topStudents->sortByDesc('average')->take(10);
    }

    /**
     * Get subject performance for a class
     */
    private function getSubjectPerformance($classId)
    {
        $class = ClassModel::findOrFail($classId);
        $subjects = $class->subjects;
        $performance = collect();
        
        foreach ($subjects as $subject) {
            $totalStudents = $class->students()->where('is_active', true)->count();
            
            // Get results for this subject
            $results = Result::whereHas('exam', function($q) use ($classId, $subject) {
                $q->where('class_id', $classId)
                  ->where('subject_id', $subject->id);
            })->get();
            
            $avgScore = $results->avg('score') ?? 0;
            $passCount = $results->where('score', '>=', $subject->pivot->passing_marks ?? 40)->count();
            
            $performance->push([
                'subject' => $subject,
                'average' => $avgScore,
                'pass_count' => $passCount,
                'total_students' => $totalStudents > 0 ? $totalStudents : 1,
            ]);
        }
        
        return $performance;
    }

    /**
     * Show the form for editing the specified class.
     */
    public function edit($id)
    {
        $class = ClassModel::findOrFail($id);
        
        $teachers = Employee::where('is_active', true)
            ->where(function($q) {
                $q->where('position', 'like', '%Teacher%')
                  ->orWhere('position', 'like', '%Head%');
            })
            ->orderBy('first_name')
            ->get();
        
        $levels = ['JSS 1', 'JSS 2', 'JSS 3', 'SSS 1', 'SSS 2', 'SSS 3'];
        $sections = ['A', 'B', 'C', 'D', 'E'];
        $categories = ClassCategory::where('is_active', true)->orderBy('name')->get();
        
        return view('classes.edit', compact('class', 'teachers', 'levels', 'sections', 'categories'));
    }

    /**
     * Update the specified class in storage.
     */
    public function update(Request $request, $id)
    {
        $class = ClassModel::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:classes,code,' . $id,
            'section' => 'nullable|string|max:10',
            'class_teacher_id' => 'nullable|exists:employees,id',
            'capacity' => 'required|integer|min:1',
            'academic_year' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        try {
            DB::beginTransaction();

            $fullName = $request->name . ($request->section ? ' ' . $request->section : '');

            $class->update([
                'name' => $request->name,
                'code' => $request->code,
                'section' => $request->section,
                'full_name' => $fullName,
                'class_category_id' => $request->class_category_id,
                'class_teacher_id' => $request->class_teacher_id,
                'capacity' => $request->capacity,
                'academic_year' => $request->academic_year,
                'description' => $request->description,
                'is_active' => $request->has('is_active'),
            ]);

            DB::commit();

            return redirect()->route('classes.show', $id)
                ->with('success', 'Class updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating class: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified class from storage.
     */
    public function destroy($id)
    {
        try {
            $class = ClassModel::findOrFail($id);
            
            // Check if class has students
            if ($class->students()->count() > 0) {
                return back()->with('error', 'Cannot delete class with enrolled students. Transfer students first.');
            }
            
            $class->delete();

            return redirect()->route('classes.index')
                ->with('success', 'Class deleted successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting class: ' . $e->getMessage());
        }
    }

    /**
     * Show students for a specific class
     */
    public function students($id)
    {
        $class = ClassModel::with('students')->findOrFail($id);
        return view('classes.students', compact('class'));
    }

    /**
     * Show subjects for a specific class
     */
    public function subjects($id)
    {
        $class = ClassModel::with('subjects.teacher')->findOrFail($id);
        return view('classes.subjects', compact('class'));
    }

    /**
     * Show form to assign students to a class
     */
    public function showAssignStudentsForm($id)
    {
        $class = ClassModel::findOrFail($id);
        $availableStudents = Student::where('is_active', true)
            ->whereNull('class_id')
            ->orWhere('class_id', $id)
            ->get();
        
        return view('classes.assign-students', compact('class', 'availableStudents'));
    }

    /**
     * Show form to assign subject to a class
     */
    public function showAssignSubjectForm($id)
    {
        $class = ClassModel::findOrFail($id);
        $subjects = Subject::where('is_active', true)->get();
        $teachers = Employee::where('is_active', true)->get();
        $terms = ['First Term', 'Second Term', 'Third Term'];
        
        return view('classes.assign-subject', compact('class', 'subjects', 'teachers', 'terms'));
    }

    /**
     * Show exams for a specific class
     */
    public function exams($id)
    {
        $class = ClassModel::findOrFail($id);
        $exams = Exam::where('class_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view('classes.exams', compact('class', 'exams'));
    }

    /**
     * Show results for a specific class
     */
    public function results($id, Request $request)
    {
        $class = ClassModel::findOrFail($id);
        $examId = $request->input('exam');
        
        $exam = null;
        $results = collect();
        
        if ($examId) {
            $exam = Exam::findOrFail($examId);
            $results = Result::with('student')
                ->whereHas('exam', function($q) use ($examId) {
                    $q->where('id', $examId);
                })
                ->get();
        }
        
        $exams = Exam::where('class_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('classes.results', compact('class', 'exam', 'results', 'exams', 'examId'));
    }

    public function assignSubject(Request $request, $id)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'teacher_id' => 'nullable|exists:employees,id',
            'max_marks' => 'required|integer|min:1',
            'passing_marks' => 'required|integer|min:0',
            'term' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $class = ClassModel::findOrFail($id);
            
            if ($class->subjects()->where('subject_id', $request->subject_id)->where('term', $request->term)->exists()) {
                return back()->with('error', 'Subject already assigned for ' . $request->term);
            }

            $class->subjects()->attach($request->subject_id, [
                'teacher_id' => $request->teacher_id,
                'max_marks' => $request->max_marks,
                'passing_marks' => $request->passing_marks,
                'is_required' => $request->has('is_required'),
                'term' => $request->term,
                'academic_year' => $request->academic_year ?? date('Y') . '/' . (date('Y') + 1),
            ]);

            DB::commit();

            return redirect()->route('classes.show', $id)
                ->with('success', 'Subject assigned successfully for ' . $request->term . '!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error assigning subject: ' . $e->getMessage());
        }
    }

    public function removeSubject($classId, $subjectId)
    {
        try {
            DB::beginTransaction();

            $class = ClassModel::findOrFail($classId);
            $class->subjects()->detach($subjectId);

            DB::commit();

            return redirect()->route('classes.show', $classId)
                ->with('success', 'Subject removed successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error removing subject: ' . $e->getMessage());
        }
    }

    public function assignStudents(Request $request, $id)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
        ]);

        try {
            DB::beginTransaction();

            $class = ClassModel::findOrFail($id);
            
            $currentCount = $class->students()->count();
            $newCount = $currentCount + count($request->student_ids);
            
            if ($newCount > $class->capacity) {
                return back()->with('error', 'Class capacity exceeded. Available: ' . ($class->capacity - $currentCount));
            }

            Student::whereIn('id', $request->student_ids)->update(['class_id' => $id]);
            $class->update(['current_students' => $class->students()->count()]);

            DB::commit();

            return redirect()->route('classes.show', $id)
                ->with('success', count($request->student_ids) . ' students assigned!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error assigning students: ' . $e->getMessage());
        }
    }

    public function removeStudent($classId, $studentId)
    {
        try {
            DB::beginTransaction();

            $student = Student::findOrFail($studentId);
            $student->update(['class_id' => null]);
            
            $class = ClassModel::findOrFail($classId);
            $class->update(['current_students' => $class->students()->count()]);

            DB::commit();

            return redirect()->route('classes.show', $classId)
                ->with('success', 'Student removed from class!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error removing student: ' . $e->getMessage());
        }
    }
}