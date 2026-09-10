<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'department', 'teacher_id', 'credit_hours',
        'is_core', 'is_elective', 'description', 'is_active'
    ];

    protected static function booted(): void
    {
        static::creating(function (self $subject): void {
            if (empty($subject->code)) {
                $subject->code = strtoupper(substr(str_replace(' ', '', $subject->name ?: 'SUBJ'), 0, 6));
            }
        });
    }

    protected $casts = [
        'is_core' => 'boolean',
        'is_elective' => 'boolean',
        'is_active' => 'boolean',
        'credit_hours' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the teacher (head of department) for this subject.
     */
    public function teacher()
    {
        return $this->belongsTo(Employee::class, 'teacher_id');
    }

    /**
     * Get all classes that have this subject assigned.
     */
    public function classes()
    {
        return $this->belongsToMany(ClassModel::class, 'class_subject', 'subject_id', 'class_id')
                    ->withPivot('id', 'teacher_id', 'max_marks', 'passing_marks', 'is_required', 'term', 'academic_year')
                    ->withTimestamps();
    }

    /**
     * Get the class-subject pivot records.
     */
    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class);
    }

    /**
     * Get all students assigned to this subject.
     */
    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_subjects', 'subject_id', 'student_id')
                    ->withTimestamps();
    }

    /**
     * Get all student-subject assignments.
     */
    public function studentSubjects()
    {
        return $this->hasMany(StudentSubject::class);
    }

    /**
     * Get exams for this subject.
     */
    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    /**
     * Get exam-subject pivot records for this subject.
     */
    public function examSubjects()
    {
        return $this->hasMany(ExamSubject::class, 'subject_id');
    }

    /**
     * Get results for this subject.
     */
    public function results()
    {
        return $this->hasMany(Result::class);
    }

    // ==================== ACCESSORS ====================

    /**
     * Get the subject type label.
     */
    public function getTypeAttribute()
    {
        if ($this->is_core) return 'Core';
        if ($this->is_elective) return 'Elective';
        return 'Standard';
    }

    /**
     * Check if subject is assigned to any class.
     */
    public function getIsAssignedAttribute()
    {
        return $this->classes()->count() > 0;
    }

    /**
     * Get the total number of classes this subject is assigned to.
     */
    public function getTotalClassesAttribute()
    {
        return $this->classes()->count();
    }

    /**
     * Get the total number of students taking this subject.
     */
    public function getTotalStudentsAttribute()
    {
        return $this->students()->count();
    }

    /**
     * Get the subject code with name.
     */
    public function getCodeNameAttribute()
    {
        return $this->code . ' - ' . $this->name;
    }

    /**
     * Get the subject name with code.
     */
    public function getNameCodeAttribute()
    {
        return $this->name . ' (' . $this->code . ')';
    }

    /**
     * Get the subject's current assignment status.
     */
    public function getAssignmentStatusAttribute()
    {
        $count = $this->classes()->count();
        if ($count == 0) return 'Not Assigned';
        if ($count == 1) return 'Assigned to 1 Class';
        return "Assigned to {$count} Classes";
    }

    /**
     * Check if subject has any student assigned.
     */
    public function getHasStudentsAttribute()
    {
        return $this->students()->count() > 0;
    }

    // ==================== SCOPES ====================

    /**
     * Scope to only include active subjects.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to only include core subjects.
     */
    public function scopeCore($query)
    {
        return $query->where('is_core', true);
    }

    /**
     * Scope to only include elective subjects.
     */
    public function scopeElective($query)
    {
        return $query->where('is_elective', true);
    }

    /**
     * Scope to only include subjects in a specific department.
     */
    public function scopeDepartment($query, $department)
    {
        return $query->where('department', $department);
    }

    /**
     * Scope to only include subjects assigned to a specific class.
     */
    public function scopeAssignedToClass($query, $classId)
    {
        return $query->whereHas('classes', function($q) use ($classId) {
            $q->where('class_id', $classId);
        });
    }

    /**
     * Scope to only include subjects taught by a specific teacher.
     */
    public function scopeTaughtBy($query, $teacherId)
    {
        return $query->whereHas('classSubjects', function($q) use ($teacherId) {
            $q->where('teacher_id', $teacherId);
        });
    }

    /**
     * Scope to only include subjects not assigned to a specific class.
     */
    public function scopeNotAssignedToClass($query, $classId)
    {
        return $query->whereDoesntHave('classes', function($q) use ($classId) {
            $q->where('class_id', $classId);
        });
    }

    /**
     * Scope to search subjects by name or code.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'LIKE', "%{$search}%")
                     ->orWhere('code', 'LIKE', "%{$search}%");
    }

    // ==================== HELPER METHODS ====================

    /**
     * Check if the subject is assigned to a specific class.
     */
    public function isAssignedToClass($classId)
    {
        return $this->classes()->where('class_id', $classId)->exists();
    }

    /**
     * Check if a specific student is taking this subject.
     */
    public function isTakenByStudent($studentId)
    {
        return $this->students()->where('student_id', $studentId)->exists();
    }

    /**
     * Get the average score for this subject across all students.
     */
    public function getAverageScore()
    {
        return $this->results()->avg('total_score') ?? 0;
    }

    /**
     * Get the pass rate for this subject.
     */
    public function getPassRate()
    {
        $total = $this->results()->count();
        if ($total == 0) return 0;
        
        $passed = $this->results()->where('total_score', '>=', 40)->count();
        return round(($passed / $total) * 100, 1);
    }

    /**
     * Get the highest score in this subject.
     */
    public function getHighestScore()
    {
        return $this->results()->max('total_score') ?? 0;
    }

    /**
     * Get the lowest score in this subject.
     */
    public function getLowestScore()
    {
        return $this->results()->min('total_score') ?? 0;
    }

    /**
     * Get the teacher for a specific class assignment.
     */
    public function getTeacherForClass($classId)
    {
        $pivot = $this->classes()->where('class_id', $classId)->first();
        if ($pivot && $pivot->pivot->teacher_id) {
            return Employee::find($pivot->pivot->teacher_id);
        }
        return null;
    }

    /**
     * Get all teachers teaching this subject.
     */
    public function getTeachersAttribute()
    {
        $teacherIds = $this->classSubjects()
            ->whereNotNull('teacher_id')
            ->distinct('teacher_id')
            ->pluck('teacher_id');
        
        return Employee::whereIn('id', $teacherIds)->get();
    }

    /**
     * Get all terms this subject is offered in.
     */
    public function getTermsAttribute()
    {
        return $this->classSubjects()
            ->distinct('term')
            ->whereNotNull('term')
            ->pluck('term');
    }

    /**
     * Check if subject has exams.
     */
    public function getHasExamsAttribute()
    {
        return $this->exams()->count() > 0;
    }

    /**
     * Get the most recent exam for this subject.
     */
    public function getRecentExamAttribute()
    {
        return $this->exams()->orderBy('created_at', 'desc')->first();
    }

    /**
     * Get subject statistics.
     */
    public function getStatisticsAttribute()
    {
        $totalStudents = $this->students()->count();
        $totalClasses = $this->classes()->count();
        $totalTeachers = $this->classSubjects()
            ->whereNotNull('teacher_id')
            ->distinct('teacher_id')
            ->count('teacher_id');
        
        $results = $this->results;
        $totalResults = $results->count();
        $averageScore = $results->avg('total_score') ?? 0;
        
        $passed = $results->where('total_score', '>=', 40)->count();
        $passRate = $totalResults > 0 ? round(($passed / $totalResults) * 100, 1) : 0;
        
        return [
            'total_students' => $totalStudents,
            'total_classes' => $totalClasses,
            'total_teachers' => $totalTeachers,
            'total_results' => $totalResults,
            'average_score' => round($averageScore, 1),
            'pass_rate' => $passRate,
            'highest_score' => $results->max('total_score') ?? 0,
            'lowest_score' => $results->min('total_score') ?? 0,
        ];
    }

    // ==================== ASSIGNMENT METHODS ====================

    /**
     * Assign this subject to a class with a teacher.
     */
    public function assignToClass($classId, $teacherId = null, $data = [])
    {
        // Check if already assigned
        if ($this->isAssignedToClass($classId)) {
            return false;
        }

        $pivotData = [
            'teacher_id' => $teacherId,
            'max_marks' => $data['max_marks'] ?? 100,
            'passing_marks' => $data['passing_marks'] ?? 40,
            'is_required' => $data['is_required'] ?? false,
            'term' => $data['term'] ?? 'First Term',
            'academic_year' => $data['academic_year'] ?? date('Y') . '/' . (date('Y') + 1),
        ];

        $this->classes()->attach($classId, $pivotData);
        return true;
    }

    /**
     * Remove this subject from a class.
     */
    public function removeFromClass($classId)
    {
        if (!$this->isAssignedToClass($classId)) {
            return false;
        }

        $this->classes()->detach($classId);
        return true;
    }

    /**
     * Assign this subject to a student.
     */
    public function assignToStudent($studentId)
    {
        if ($this->isTakenByStudent($studentId)) {
            return false;
        }

        StudentSubject::create([
            'student_id' => $studentId,
            'subject_id' => $this->id,
        ]);
        return true;
    }

    /**
     * Remove this subject from a student.
     */
    public function removeFromStudent($studentId)
    {
        $assignment = StudentSubject::where('student_id', $studentId)
            ->where('subject_id', $this->id)
            ->first();

        if (!$assignment) {
            return false;
        }

        $assignment->delete();
        return true;
    }

    /**
     * Assign this subject to all students in a class.
     */
    public function assignToAllStudentsInClass($classId)
    {
        $class = ClassModel::find($classId);
        if (!$class) {
            return 0;
        }

        $students = $class->students()->where('is_active', true)->get();
        $count = 0;

        foreach ($students as $student) {
            if (!$this->isTakenByStudent($student->id)) {
                StudentSubject::create([
                    'student_id' => $student->id,
                    'subject_id' => $this->id,
                ]);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Get all classes this subject is NOT assigned to.
     */
    public function getAvailableClasses()
    {
        $assignedIds = $this->classes()->pluck('classes.id')->toArray();
        return ClassModel::where('is_active', true)
            ->whereNotIn('id', $assignedIds)
            ->get();
    }

    /**
     * Get all students NOT taking this subject.
     */
    public function getAvailableStudents()
    {
        $assignedIds = $this->students()->pluck('students.id')->toArray();
        return Student::where('is_active', true)
            ->whereNotIn('id', $assignedIds)
            ->get();
    }

    /**
     * Get students in a specific class NOT taking this subject.
     */
    public function getAvailableStudentsInClass($classId)
    {
        $assignedIds = $this->students()->pluck('students.id')->toArray();
        return Student::where('class_id', $classId)
            ->where('is_active', true)
            ->whereNotIn('id', $assignedIds)
            ->get();
    }

    /**
     * Get the assignment details for a specific class.
     */
    public function getAssignmentForClass($classId)
    {
        return $this->classes()->where('class_id', $classId)->first();
    }

    /**
     * Update the teacher for a specific class assignment.
     */
    public function updateTeacherForClass($classId, $teacherId)
    {
        $class = $this->classes()->where('class_id', $classId)->first();
        if (!$class) {
            return false;
        }

        $class->pivot->teacher_id = $teacherId;
        $class->pivot->save();
        return true;
    }

    /**
     * Get the count of students taking this subject by class.
     */
    public function getStudentCountByClass($classId)
    {
        return $this->students()
            ->whereHas('class', function($q) use ($classId) {
                $q->where('id', $classId);
            })
            ->count();
    }

    /**
     * Check if this subject can be deleted (no assignments).
     */
    public function canBeDeleted()
    {
        return $this->classes()->count() == 0 && $this->students()->count() == 0;
    }

    /**
     * Get the deletion warning message if any.
     */
    public function getDeletionWarning()
    {
        $classCount = $this->classes()->count();
        $studentCount = $this->students()->count();
        
        if ($classCount == 0 && $studentCount == 0) {
            return null;
        }

        $messages = [];
        if ($classCount > 0) {
            $messages[] = "Assigned to {$classCount} class(es)";
        }
        if ($studentCount > 0) {
            $messages[] = "Taken by {$studentCount} student(s)";
        }

        return "Cannot delete: " . implode(', ', $messages);
    }

    /**
     * Get the performance summary for this subject.
     */
    public function getPerformanceSummary()
    {
        $results = $this->results;
        $total = $results->count();
        
        if ($total == 0) {
            return [
                'total' => 0,
                'passed' => 0,
                'failed' => 0,
                'pass_rate' => 0,
                'average' => 0,
                'highest' => 0,
                'lowest' => 0,
                'grades' => ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0]
            ];
        }

        $passed = $results->where('total_score', '>=', 40)->count();
        
        $grades = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0];
        foreach ($results as $result) {
            $score = $result->total_score;
            if ($score >= 70) $grades['A']++;
            elseif ($score >= 60) $grades['B']++;
            elseif ($score >= 50) $grades['C']++;
            elseif ($score >= 40) $grades['D']++;
            else $grades['F']++;
        }

        return [
            'total' => $total,
            'passed' => $passed,
            'failed' => $total - $passed,
            'pass_rate' => round(($passed / $total) * 100, 1),
            'average' => round($results->avg('total_score') ?? 0, 1),
            'highest' => $results->max('total_score') ?? 0,
            'lowest' => $results->min('total_score') ?? 0,
            'grades' => $grades,
        ];
    }
}