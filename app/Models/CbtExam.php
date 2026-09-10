<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CbtExam extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id', 'class_id', 'created_by', 'approved_by',
        'title', 'code', 'description', 'type', 'term', 'academic_year',
        'duration_minutes', 'total_marks', 'passing_marks', 'total_questions',
        'is_randomized', 'show_results_immediately', 'status',
        'start_date', 'end_date', 'approved_at', 'published_at', 'is_active'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'approved_at' => 'datetime',
        'published_at' => 'datetime',
        'is_randomized' => 'boolean',
        'show_results_immediately' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function questions()
    {
        return $this->hasMany(CbtQuestion::class);
    }

    public function attempts()
    {
        return $this->hasMany(CbtAttempt::class);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            'draft' => 'secondary',
            'pending' => 'warning',
            'approved' => 'info',
            'published' => 'success',
            'closed' => 'danger',
            'archived' => 'dark',
        ];
        return $badges[$this->status] ?? 'secondary';
    }
}