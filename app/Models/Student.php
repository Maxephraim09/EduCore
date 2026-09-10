<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';
    
    protected $fillable = [
        'admission_number',
        'first_name',
        'last_name',
        'middle_name',
        'date_of_birth',
        'gender',
        'blood_group',
        'religion',
        'nationality',
        'state_of_origin',
        'local_government',
        'home_address',
        'phone_number',
        'phone',
        'email',
        'address',
        'photo',
        
        // Parent/Guardian Information
        'father_name',
        'father_phone',
        'father_email',
        'father_occupation',
        'parent_name',
        'parent_phone',
        'parent_email',
        'mother_name',
        'mother_phone',
        'mother_email',
        'mother_occupation',
        'guardian_name',
        'guardian_phone',
        'guardian_email',
        'guardian_address',
        'guardian_relationship',
        
        // Academic Information
        'class', // This is the class name (string), but we also need class_id for relationship
        'class_id', // Add this for the relationship
        'section',
        'roll_number',
        'house',
        'previous_school',
        'admission_date',
        'academic_year',
        
        // Medical Information
        'allergies',
        'medical_conditions',
        'emergency_contact_name',
        'emergency_contact_phone',
        
        // Fee Information
        'total_fees',
        'paid_fees',
        'due_fees',
        'fee_concession',
        
        // Transport Information
        'transport_route',
        'transport_fee',
        
        // Hostel Information
        'is_hosteller',
        'hostel_name',
        'room_number',
        
        // Status
        'is_active',
        'is_alumni',
        'remarks'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'admission_date' => 'date',
        'total_fees' => 'decimal:2',
        'paid_fees' => 'decimal:2',
        'due_fees' => 'decimal:2',
        'transport_fee' => 'decimal:2',
        'fee_concession' => 'decimal:2',
        'is_active' => 'boolean',
        'is_alumni' => 'boolean',
        'is_hosteller' => 'boolean',
    ];

    protected static function booted()
    {
        // Ensure legacy `class` string column is populated when `class_id` is set
        static::creating(function ($student) {
            if (empty($student->class) && !empty($student->class_id)) {
                try {
                    $cls = ClassModel::find($student->class_id);
                    $student->class = $cls ? ($cls->full_class_name ?? $cls->name ?? null) : null;
                } catch (\Throwable $e) {
                    // swallow - leave class as-is to allow validation elsewhere to catch
                }
            }
        });

        static::updating(function ($student) {
            if (empty($student->class) && !empty($student->class_id)) {
                try {
                    $cls = ClassModel::find($student->class_id);
                    $student->class = $cls ? ($cls->full_class_name ?? $cls->name ?? null) : $student->class;
                } catch (\Throwable $e) {
                }
            }
        });
    }

    // ==================== RELATIONSHIPS ====================
    
    /**
     * Get the class that the student belongs to
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get the class model for this student.
     */
    public function getClassModel()
    {
        return $this->class()->first();
    }

    /**
     * Resolve the student class attribute safely.
     *
     * This handles the legacy string `class` attribute while preserving
     * access to the `class()` relationship.
     */
    public function getClassAttribute($value)
    {
        if ($this->relationLoaded('class')) {
            return $this->getRelation('class');
        }

        if ($this->class_id) {
            return $this->class()->getResults();
        }

        return $value;
    }

    /**
     * Get the user account associated with the student
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get all results for this student
     */
    public function results()
    {
        return $this->hasMany(Result::class);
    }

    /**
     * Get all attendances for this student
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get all fee payments for this student
     */
    public function feePayments()
    {
        return $this->hasMany(FeePayment::class);
    }

    /**
     * Get all fees for this student
     */
    public function fees()
    {
        return $this->hasMany(Fee::class);
    }

    /**
     * Get all payments for this student
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // ==================== SUBJECT RELATIONSHIPS ====================

    /**
     * Get all subjects assigned to this student.
     */
    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'student_subjects', 'student_id', 'subject_id')
                    ->withTimestamps();
    }

    /**
     * Alias for registered subjects (matches spec)
     */
    public function registeredSubjects()
    {
        return $this->subjects();
    }

    /**
     * Get the student-subject pivot records.
     */
    public function studentSubjects()
    {
        return $this->hasMany(StudentSubject::class);
    }

    /**
     * Get all class-subject assignments for this student's class.
     */
    public function classSubjects()
    {
        return $this->hasManyThrough(
            ClassSubject::class,
            ClassModel::class,
            'id', // Foreign key on classes table
            'class_id', // Foreign key on class_subject table
            'class_id', // Local key on students table
            'id' // Local key on classes table
        );
    }

    /**
     * Get subjects assigned to this student's class.
     */
    public function classSubjectsList()
    {
        $classModel = $this->getClassModel();
        return $classModel ? $classModel->subjects()->get() : collect();
    }

    // ==================== ACCESSORS ====================
    
    /**
     * Get the full name of the student
     */
    public function getFullNameAttribute()
    {
        if ($this->first_name && $this->last_name) {
            $middle = $this->middle_name ? ' ' . $this->middle_name : '';
            return $this->first_name . $middle . ' ' . $this->last_name;
        }
        return $this->first_name ?? 'N/A';
    }

    /**
     * Get the student's age
     */
    public function getAgeAttribute()
    {
        if ($this->date_of_birth) {
            return $this->date_of_birth->age;
        }
        return null;
    }

    /**
     * Get the class name
     */
    public function getClassNameAttribute()
    {
        if ($this->relationLoaded('class') && $this->getRelation('class') instanceof ClassModel) {
            return $this->getRelation('class')->full_class_name;
        }

        $relatedClass = $this->class()->getResults();
        if ($relatedClass instanceof ClassModel) {
            return $relatedClass->full_class_name;
        }

        return $this->attributes['class'] ?? 'N/A';
    }

    /**
     * Get the student's total marks across all subjects
     */
    public function getTotalMarksAttribute()
    {
        return $this->results()->sum('total_score');
    }

    /**
     * Get the student's average marks
     */
    public function getAverageMarksAttribute()
    {
        $count = $this->results()->count();
        if ($count === 0) return 0;
        return round($this->results()->avg('total_score'), 2);
    }

    /**
     * Get the student's grade (overall)
     */
    public function getOverallGradeAttribute()
    {
        $average = $this->average_marks;
        if ($average >= 70) return 'A';
        if ($average >= 60) return 'B';
        if ($average >= 50) return 'C';
        if ($average >= 45) return 'D';
        return 'F';
    }

    /**
     * Get fee balance
     */
    public function getFeeBalanceAttribute()
    {
        return $this->total_fees - $this->paid_fees;
    }

    /**
     * Get fee status
     */
    public function getFeeStatusAttribute()
    {
        $balance = $this->fee_balance;
        if ($balance <= 0) return 'Paid';
        if ($balance > 0 && $balance <= ($this->total_fees * 0.5)) return 'Partial';
        return 'Pending';
    }

    /**
     * Get the number of subjects this student is taking.
     */
    public function getSubjectsCountAttribute()
    {
        return $this->subjects()->count();
    }

    /**
     * Get the list of subject names this student is taking.
     */
    public function getSubjectNamesAttribute()
    {
        return $this->subjects()->pluck('name')->toArray();
    }

    /**
     * Check if the student has any subjects assigned.
     */
    public function getHasSubjectsAttribute()
    {
        return $this->subjects()->count() > 0;
    }

    // ==================== SCOPES ====================
    
    /**
     * Scope a query to only include active students
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include inactive students
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope a query to only include alumni
     */
    public function scopeAlumni($query)
    {
        return $query->where('is_alumni', true);
    }

    /**
     * Scope a query to only include hostellers
     */
    public function scopeHostellers($query)
    {
        return $query->where('is_hosteller', true);
    }

    /**
     * Scope a query to search students by name or admission number
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('first_name', 'LIKE', "%{$search}%")
              ->orWhere('last_name', 'LIKE', "%{$search}%")
              ->orWhere('full_name', 'LIKE', "%{$search}%")
              ->orWhere('admission_number', 'LIKE', "%{$search}%")
              ->orWhere('email', 'LIKE', "%{$search}%")
              ->orWhere('phone', 'LIKE', "%{$search}%");
        });
    }

    /**
     * Scope a query to get students by class
     */
    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Scope a query to get students by gender
     */
    public function scopeByGender($query, $gender)
    {
        return $query->where('gender', $gender);
    }

    /**
     * Scope a query to get students by academic year
     */
    public function scopeByAcademicYear($query, $year)
    {
        return $query->where('academic_year', $year);
    }

    /**
     * Scope to get students taking a specific subject.
     */
    public function scopeTakingSubject($query, $subjectId)
    {
        return $query->whereHas('subjects', function($q) use ($subjectId) {
            $q->where('subject_id', $subjectId);
        });
    }

    /**
     * Scope to get students NOT taking a specific subject.
     */
    public function scopeNotTakingSubject($query, $subjectId)
    {
        return $query->whereDoesntHave('subjects', function($q) use ($subjectId) {
            $q->where('subject_id', $subjectId);
        });
    }

    // ==================== HELPER METHODS ====================
    
    /**
     * Check if the student is in a specific class
     */
    public function isInClass($classId)
    {
        return $this->class_id == $classId;
    }

    /**
     * Get the student's results for a specific term
     */
    public function getResultsForTerm($term)
    {
        return $this->results()->where('term', $term)->get();
    }

    /**
     * Get the student's results for a specific subject
     */
    public function getResultsForSubject($subjectId)
    {
        return $this->results()->where('subject_id', $subjectId)->first();
    }

    /**
     * Get the student's results for a specific class and term
     */
    public function getResultsForClassAndTerm($classId, $term)
    {
        return $this->results()
            ->where('class_id', $classId)
            ->where('term', $term)
            ->get();
    }

    /**
     * Check if student has results for a subject
     */
    public function hasResultForSubject($subjectId, $term = null)
    {
        $query = $this->results()->where('subject_id', $subjectId);
        if ($term) {
            $query->where('term', $term);
        }
        return $query->exists();
    }

    /**
     * Get the student's position in class
     */
    public function getPositionInClass($term)
    {
        $classId = $this->class_id;
        
        if (!$classId) return null;
        
        // Get all students in the same class with their total scores
        $students = self::where('class_id', $classId)
            ->where('is_active', true)
            ->with(['results' => function($query) use ($term) {
                $query->where('term', $term);
            }])
            ->get()
            ->map(function($student) {
                return [
                    'id' => $student->id,
                    'total' => $student->results->sum('total_score')
                ];
            })
            ->sortByDesc('total')
            ->values();
            
        $position = $students->search(function($item) {
            return $item['id'] == $this->id;
        });
        
        return $position !== false ? $position + 1 : null;
    }

    /**
     * Get attendance statistics for the student
     */
    public function getAttendanceStats($term = null)
    {
        $query = $this->attendances();
        
        if ($term) {
            $query->where('term', $term);
        }
        
        $total = $query->count();
        $present = $query->where('status', 'present')->count();
        $absent = $query->where('status', 'absent')->count();
        $excused = $query->where('status', 'excused')->count();
        $late = $query->where('status', 'late')->count();
        
        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'excused' => $excused,
            'late' => $late,
            'attendance_rate' => $total > 0 ? round(($present / $total) * 100, 2) : 0
        ];
    }

    /**
     * Update fee status
     */
    public function updateFeeStatus()
    {
        $this->paid_fees = $this->feePayments()->where('status', 'completed')->sum('amount');
        $this->due_fees = $this->total_fees - $this->paid_fees;
        $this->save();
    }

    /**
     * Get fee summary for the student
     */
    public function getFeeSummary()
    {
        $totalFees = $this->total_fees ?? 0;
        $totalPaid = $this->paid_fees ?? 0;
        $balance = $totalFees - $totalPaid;
        
        return [
            'total_fees' => $totalFees,
            'total_paid' => $totalPaid,
            'balance' => $balance,
            'status' => $balance <= 0 ? 'Paid' : ($balance <= ($totalFees * 0.5) ? 'Partial' : 'Pending')
        ];
    }

    // ==================== SUBJECT HELPER METHODS ====================

    /**
     * Check if the student is taking a specific subject.
     */
    public function isTakingSubject($subjectId)
    {
        return $this->subjects()->where('subject_id', $subjectId)->exists();
    }

    /**
     * Assign a subject to this student.
     */
    public function assignSubject($subjectId)
    {
        if ($this->isTakingSubject($subjectId)) {
            return false;
        }

        StudentSubject::create([
            'student_id' => $this->id,
            'subject_id' => $subjectId,
        ]);
        return true;
    }

    /**
     * Register subject (wrapper)
     */
    public function registerSubject($subjectId)
    {
        return $this->assignSubject($subjectId);
    }

    /**
     * Unregister a subject if not required
     */
    public function unregisterSubject($subjectId)
    {
        // Check pivot on class subjects
        $classModel = $this->getClassModel();
        if ($classModel && $classModel->subjects()->where('subjects.id', $subjectId)->wherePivot('is_required', true)->exists()) {
            return false;
        }
        return $this->removeSubject($subjectId);
    }

    /**
     * Auto assign required subjects from class
     */
    public function autoAssignRequiredSubjects()
    {
        $classModel = $this->getClassModel();
        if (!$classModel) return 0;

        $required = $classModel->subjects()->wherePivot('is_required', true)->get();
        $count = 0;
        foreach ($required as $s) {
            if (!$this->isTakingSubject($s->id)) {
                $this->assignSubject($s->id);
                $count++;
            }
        }
        return $count;
    }

    /**
     * Remove a subject from this student.
     */
    public function removeSubject($subjectId)
    {
        $assignment = StudentSubject::where('student_id', $this->id)
            ->where('subject_id', $subjectId)
            ->first();

        if (!$assignment) {
            return false;
        }

        $assignment->delete();
        return true;
    }

    /**
     * Assign multiple subjects to this student.
     */
    public function assignSubjects(array $subjectIds)
    {
        $count = 0;
        foreach ($subjectIds as $subjectId) {
            if ($this->assignSubject($subjectId)) {
                $count++;
            }
        }
        return $count;
    }

    /**
     * Remove all subjects from this student.
     */
    public function removeAllSubjects()
    {
        return StudentSubject::where('student_id', $this->id)->delete();
    }

    /**
     * Get subjects that are available for this student (not yet assigned).
     */
    public function getAvailableSubjects()
    {
        $assignedIds = $this->subjects()->pluck('subjects.id')->toArray();
        return Subject::where('is_active', true)
            ->whereNotIn('id', $assignedIds)
            ->get();
    }

    /**
     * Get subjects for this student's class that are not yet assigned.
     */
    public function getAvailableClassSubjects()
    {
        $classModel = $this->getClassModel();
        if (!$classModel) {
            return collect();
        }

        $assignedIds = $this->subjects()->pluck('subjects.id')->toArray();
        return $classModel->subjects()
            ->whereNotIn('subjects.id', $assignedIds)
            ->get();
    }

    /**
     * Assign all subjects from the student's class to this student.
     */
    public function assignAllClassSubjects()
    {
        $classModel = $this->getClassModel();
        if (!$classModel) {
            return 0;
        }

        $subjectIds = $classModel->subjects()->pluck('subjects.id')->toArray();
        return $this->assignSubjects($subjectIds);
    }

    /**
     * Get the student's subject performance summary.
     */
    public function getSubjectPerformanceSummary()
    {
        $subjects = $this->subjects;
        $summary = [];

        foreach ($subjects as $subject) {
            $result = $this->getResultsForSubject($subject->id);
            $summary[] = [
                'subject_id' => $subject->id,
                'subject_name' => $subject->name,
                'subject_code' => $subject->code,
                'score' => $result ? $result->total_score : null,
                'grade' => $result ? $result->grade : null,
                'remark' => $result ? $result->remark : null,
            ];
        }

        return $summary;
    }

    /**
     * Get the student's complete academic profile.
     */
    public function getAcademicProfile()
    {
        return [
            'student' => [
                'id' => $this->id,
                'admission_number' => $this->admission_number,
                'full_name' => $this->full_name,
                'class' => $this->class_name,
                'gender' => $this->gender,
                'age' => $this->age,
            ],
            'subjects' => $this->subjects->map(function($subject) {
                return [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'code' => $subject->code,
                ];
            }),
            'subject_count' => $this->subjects_count,
            'performance' => $this->getSubjectPerformanceSummary(),
            'overall_average' => $this->average_marks,
            'overall_grade' => $this->overall_grade,
        ];
    }

    // ==================== STATIC METHODS ====================
    
    /**
     * Get students with their statistics
     */
    public static function getWithStatistics($filters = [])
    {
        $query = self::with(['class', 'user', 'subjects']);
        
        if (isset($filters['class_id'])) {
            $query->where('class_id', $filters['class_id']);
        }
        
        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }
        
        if (isset($filters['search'])) {
            $query->search($filters['search']);
        }
        
        if (isset($filters['gender'])) {
            $query->byGender($filters['gender']);
        }
        
        return $query->get()->map(function($student) {
            return [
                'id' => $student->id,
                'admission_number' => $student->admission_number,
                'full_name' => $student->full_name,
                'class' => $student->class_name,
                'gender' => $student->gender,
                'email' => $student->email,
                'phone' => $student->phone,
                'subjects_count' => $student->subjects_count,
                'total_marks' => $student->total_marks,
                'average' => $student->average_marks,
                'grade' => $student->overall_grade,
                'fee_status' => $student->fee_status,
                'fee_balance' => $student->fee_balance,
                'is_active' => $student->is_active,
            ];
        });
    }

    /**
     * Get students for dropdown
     */
    public static function getDropdown($classId = null)
    {
        $query = self::where('is_active', true)->orderBy('first_name');
        
        if ($classId) {
            $query->where('class_id', $classId);
        }
        
        return $query->get()->pluck('full_name', 'id');
    }

    /**
     * Get students by class with their results
     */
    public static function getByClassWithResults($classId, $term)
    {
        return self::where('class_id', $classId)
            ->where('is_active', true)
            ->with(['results' => function($query) use ($term) {
                $query->where('term', $term);
            }])
            ->get();
    }

    /**
     * Get student count by class
     */
    public static function getCountByClass($classId)
    {
        return self::where('class_id', $classId)
            ->where('is_active', true)
            ->count();
    }

    /**
     * Get student count by gender
     */
    public static function getCountByGender($gender)
    {
        return self::where('gender', $gender)
            ->where('is_active', true)
            ->count();
    }

    /**
     * Get student statistics
     */
    public static function getStatistics()
    {
        return [
            'total' => self::where('is_active', true)->count(),
            'male' => self::where('gender', 'male')->where('is_active', true)->count(),
            'female' => self::where('gender', 'female')->where('is_active', true)->count(),
            'active' => self::where('is_active', true)->count(),
            'inactive' => self::where('is_active', false)->count(),
            'alumni' => self::where('is_alumni', true)->count(),
            'hostellers' => self::where('is_hosteller', true)->where('is_active', true)->count(),
        ];
    }

    /**
     * Get students without subjects assigned.
     */
    public static function getWithoutSubjects()
    {
        return self::where('is_active', true)
            ->whereDoesntHave('subjects')
            ->get();
    }

    /**
     * Get students who are missing subjects from their class.
     */
    public static function getMissingClassSubjects()
    {
        $students = self::where('is_active', true)->get();
        $missing = [];

        foreach ($students as $student) {
            $classModel = $student->class()->first();
            if ($classModel) {
                $classSubjectIds = $classModel->subjects()->pluck('subjects.id')->toArray();
                $studentSubjectIds = $student->subjects()->pluck('subjects.id')->toArray();
                $missingIds = array_diff($classSubjectIds, $studentSubjectIds);
                
                if (!empty($missingIds)) {
                    $missing[] = [
                        'student' => $student,
                        'missing_subjects' => Subject::whereIn('id', $missingIds)->get(),
                    ];
                }
            }
        }

        return $missing;
    }
}