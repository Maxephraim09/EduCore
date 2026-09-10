<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoreSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'class_id',
        'subject_id',
        'term_id',
        'academic_year_id',
        'is_active',
        'is_locked',
        'opened_at',
        'closed_at',
        'opened_by',
        'closed_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_locked' => 'boolean',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function term()
    {
        return $this->belongsTo(Term::class, 'term_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function isOpen()
    {
        return (bool) $this->is_active;
    }

    public function canEnterScores()
    {
        return $this->is_active && !$this->is_locked;
    }
}
