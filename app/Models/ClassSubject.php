<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassSubject extends Model
{
    use HasFactory;

    protected $table = 'class_subject';

    protected $fillable = [
        'class_id', 
        'subject_id', 
        'teacher_id', 
        'max_marks',
        'passing_marks', 
        'is_required', 
        'term', 
        'academic_year'
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'max_marks' => 'integer',
        'passing_marks' => 'integer',
    ];

    // ==================== RELATIONSHIPS ====================

    /**
     * Get the class for this assignment.
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get the subject for this assignment.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the teacher for this assignment.
     */
    public function teacher()
    {
        return $this->belongsTo(Employee::class, 'teacher_id');
    }

    /**
     * Get results for this class-subject assignment.
     */
    public function results()
    {
        return $this->hasMany(Result::class, 'class_subject_id');
    }

    /**
     * Get students in this class who are taking this subject.
     */
    public function students()
    {
        return $this->hasManyThrough(
            Student::class,
            StudentSubject::class,
            'subject_id', // Foreign key on student_subjects table
            'id', // Foreign key on students table
            'subject_id', // Local key on class_subject table
            'student_id' // Local key on student_subjects table
        );
    }

    // ==================== ACCESSORS ====================

    /**
     * Get the term name formatted.
     */
    public function getTermNameAttribute()
    {
        $terms = [
            'First Term' => '1st Term',
            'Second Term' => '2nd Term',
            'Third Term' => '3rd Term',
        ];
        return $terms[$this->term] ?? $this->term;
    }

    /**
     * Get the full assignment name.
     */
    public function getFullNameAttribute()
    {
        return $this->subject->name . ' - ' . $this->class->full_class_name . ' (' . $this->term . ')';
    }

    /**
     * Check if teacher is assigned.
     */
    public function getHasTeacherAttribute()
    {
        return !is_null($this->teacher_id);
    }

    /**
     * Get the teacher's full name.
     */
    public function getTeacherNameAttribute()
    {
        if ($this->teacher) {
            return $this->teacher->first_name . ' ' . $this->teacher->last_name;
        }
        return 'Not Assigned';
    }

    /**
     * Get the pass rate for this class-subject.
     */
    public function getPassRateAttribute()
    {
        $total = $this->results()->count();
        if ($total == 0) return 0;
        
        $passed = $this->results()->where('total_score', '>=', $this->passing_marks)->count();
        return round(($passed / $total) * 100, 1);
    }

    /**
     * Get the average score for this class-subject.
     */
    public function getAverageScoreAttribute()
    {
        return round($this->results()->avg('total_score') ?? 0, 1);
    }

    /**
     * Get the highest score for this class-subject.
     */
    public function getHighestScoreAttribute()
    {
        return $this->results()->max('total_score') ?? 0;
    }

    /**
     * Get the lowest score for this class-subject.
     */
    public function getLowestScoreAttribute()
    {
        return $this->results()->min('total_score') ?? 0;
    }

    /**
     * Get the number of students taking this subject.
     */
    public function getStudentCountAttribute()
    {
        return $this->class->students()->where('is_active', true)->count();
    }

    /**
     * Get the number of students who passed.
     */
    public function getPassedCountAttribute()
    {
        return $this->results()->where('total_score', '>=', $this->passing_marks)->count();
    }

    /**
     * Get the number of students who failed.
     */
    public function getFailedCountAttribute()
    {
        return $this->results()->where('total_score', '<', $this->passing_marks)->count();
    }

    /**
     * Get the academic year display.
     */
    public function getAcademicYearDisplayAttribute()
    {
        return $this->academic_year ?? date('Y') . '/' . (date('Y') + 1);
    }

    // ==================== SCOPES ====================

    /**
     * Scope to filter by class.
     */
    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Scope to filter by subject.
     */
    public function scopeForSubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    /**
     * Scope to filter by teacher.
     */
    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    /**
     * Scope to filter by term.
     */
    public function scopeForTerm($query, $term)
    {
        return $query->where('term', $term);
    }

    /**
     * Scope to filter by academic year.
     */
    public function scopeForAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    /**
     * Scope to only include required subjects.
     */
    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    /**
     * Scope to only include elective subjects.
     */
    public function scopeElective($query)
    {
        return $query->where('is_required', false);
    }

    /**
     * Scope to only include assignments with a teacher assigned.
     */
    public function scopeWithTeacher($query)
    {
        return $query->whereNotNull('teacher_id');
    }

    /**
     * Scope to only include assignments without a teacher.
     */
    public function scopeWithoutTeacher($query)
    {
        return $query->whereNull('teacher_id');
    }

    // ==================== HELPER METHODS ====================

    /**
     * Check if a student is taking this subject.
     */
    public function isStudentTaking($studentId)
    {
        return StudentSubject::where('student_id', $studentId)
            ->where('subject_id', $this->subject_id)
            ->exists();
    }

    /**
     * Get students in this class who are NOT taking this subject.
     */
    public function getStudentsNotTaking()
    {
        $takingIds = StudentSubject::where('subject_id', $this->subject_id)
            ->pluck('student_id')
            ->toArray();
        
        return $this->class->students()
            ->where('is_active', true)
            ->whereNotIn('id', $takingIds)
            ->get();
    }

    /**
     * Assign this subject to all students in the class.
     */
    public function assignToAllStudents()
    {
        $students = $this->class->students()->where('is_active', true)->get();
        $count = 0;
        
        foreach ($students as $student) {
            $exists = StudentSubject::where('student_id', $student->id)
                ->where('subject_id', $this->subject_id)
                ->exists();
            
            if (!$exists) {
                StudentSubject::create([
                    'student_id' => $student->id,
                    'subject_id' => $this->subject_id,
                ]);
                $count++;
            }
        }
        
        return $count;
    }

    /**
     * Remove this subject from all students in the class.
     */
    public function removeFromAllStudents()
    {
        $students = $this->class->students()->where('is_active', true)->get();
        $count = 0;
        
        foreach ($students as $student) {
            $assignment = StudentSubject::where('student_id', $student->id)
                ->where('subject_id', $this->subject_id)
                ->first();
            
            if ($assignment) {
                $assignment->delete();
                $count++;
            }
        }
        
        return $count;
    }

    /**
     * Get the performance distribution for this class-subject.
     */
    public function getPerformanceDistribution()
    {
        $results = $this->results;
        $total = $results->count();
        
        if ($total == 0) return [
            'A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0
        ];
        
        $distribution = [
            'A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'F' => 0
        ];
        
        foreach ($results as $result) {
            $score = $result->total_score;
            if ($score >= 70) $distribution['A']++;
            elseif ($score >= 60) $distribution['B']++;
            elseif ($score >= 50) $distribution['C']++;
            elseif ($score >= 40) $distribution['D']++;
            else $distribution['F']++;
        }
        
        // Convert to percentages
        foreach ($distribution as $key => $value) {
            $distribution[$key] = round(($value / $total) * 100, 1);
        }
        
        return $distribution;
    }

    /**
     * Get the top performing students for this class-subject.
     */
    public function getTopStudents($limit = 5)
    {
        return $this->results()
            ->with('student')
            ->orderBy('total_score', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get the lowest performing students for this class-subject.
     */
    public function getLowestStudents($limit = 5)
    {
        return $this->results()
            ->with('student')
            ->orderBy('total_score', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get complete statistics for this class-subject.
     */
    public function getStatistics()
    {
        $results = $this->results;
        $total = $results->count();
        $passed = $results->where('total_score', '>=', $this->passing_marks)->count();
        $failed = $total - $passed;
        
        return [
            'total_students' => $this->student_count,
            'total_results' => $total,
            'passed' => $passed,
            'failed' => $failed,
            'pass_rate' => $this->pass_rate,
            'average_score' => $this->average_score,
            'highest_score' => $this->highest_score,
            'lowest_score' => $this->lowest_score,
            'distribution' => $this->getPerformanceDistribution(),
        ];
    }

    /**
     * Get the term sequence number (1, 2, 3).
     */
    public function getTermSequence()
    {
        $map = [
            'First Term' => 1,
            'Second Term' => 2,
            'Third Term' => 3,
        ];
        return $map[$this->term] ?? 0;
    }

    /**
     * Check if this assignment is for the current term.
     */
    public function getIsCurrentTermAttribute()
    {
        // Get current term from settings or default
        $currentTerm = session('current_term', 'First Term');
        return $this->term === $currentTerm;
    }

    /**
     * Get the display name for the assignment.
     */
    public function getDisplayNameAttribute()
    {
        return $this->subject->name . ' (' . $this->class->full_class_name . ')';
    }
}