<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'exam_id',
        'subject_id',
        'class_id',
        'ca1_score',
        'ca2_score',
        'ca3_score',
        'exam_score',
        'total_score',
        'score',
        'grade',
        'remark',
        'remarks',
        'position',
        'is_published',
        'teacher_comment',
        'principal_comment',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'is_published' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $result): void {
            if ($result->score === null && $result->total_score !== null) {
                $result->score = $result->total_score;
            }
            if ($result->total_score === null && $result->score !== null) {
                $result->total_score = $result->score;
            }
        });
    }

    /**
     * Get the student that owns the result.
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the exam that owns the result.
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the subject that owns the result.
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the class that owns the result.
     */
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    /**
     * Get the grade based on score
     */
    public function getGradeAttribute()
    {
        if ($this->score >= 70) return 'A';
        if ($this->score >= 60) return 'B';
        if ($this->score >= 50) return 'C';
        if ($this->score >= 45) return 'D';
        return 'F';
    }

    /**
     * Get the status based on score
     */
    public function getStatusAttribute()
    {
        return $this->score >= 40 ? 'Pass' : 'Fail';
    }

    /**
     * Calculate the total of the score components
     */
    public function calculateTotal()
    {
        return ($this->ca1_score ?? 0) + ($this->ca2_score ?? 0) + ($this->ca3_score ?? 0) + ($this->exam_score ?? 0);
    }

    /**
     * Calculate grade based on subject max marks and GradeScale
     */
    public function calculateGrade()
    {
        $total = $this->calculateTotal();
        $maxMarks = 100;
        if ($this->subject && isset($this->subject->max_marks)) {
            $maxMarks = $this->subject->max_marks ?: 100;
        }
        $percentage = $maxMarks > 0 ? ($total / $maxMarks) * 100 : 0;
        $gradeScale = GradeScale::where('min_score', '<=', $percentage)
            ->where('max_score', '>=', $percentage)
            ->where('is_active', true)
            ->first();
        return $gradeScale ? $gradeScale->grade : null;
    }
}