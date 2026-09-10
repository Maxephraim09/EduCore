<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CbtAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'cbt_exam_id', 'student_id', 'started_at', 'submitted_at',
        'duration_used', 'score', 'total_answered', 'correct_answers',
        'wrong_answers', 'skipped_questions', 'percentage', 'grade',
        'remarks', 'status', 'answers', 'ip_address', 'user_agent',
        'is_flagged', 'flag_reason'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'answers' => 'array',
        'is_flagged' => 'boolean',
    ];

    public function exam()
    {
        return $this->belongsTo(CbtExam::class, 'cbt_exam_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}