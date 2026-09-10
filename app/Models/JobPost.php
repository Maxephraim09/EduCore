<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'department',
        'description',
        'requirements',
        'salary_range',
        'application_deadline',
        'is_active',
        'is_featured',
        'job_type',
        'experience_level',
        'created_by',
        'academic_year_id',
    ];

    protected $casts = [
        'application_deadline' => 'date',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class, 'job_post_id');
    }

    public function scopeActive($query)
    {
        return $query
            ->where('is_active', true)
            ->whereDate('application_deadline', '>=', now()->toDateString());
    }

    public function canApply(): bool
    {
        return $this->is_active && $this->application_deadline >= now()->toDateString();
    }
}

