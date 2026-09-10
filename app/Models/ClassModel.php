<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    use HasFactory;

    protected $table = 'classes';

    protected $fillable = [
        'name', 'code', 'section', 'full_name', 'class_teacher_id',
        'capacity', 'current_students', 'academic_year', 'description', 'is_active',
        'class_category_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
        'current_students' => 'integer',
    ];

    // Relationships
    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function classTeacher()
    {
        return $this->belongsTo(Employee::class, 'class_teacher_id');
    }

    // Alias for older naming conventions (class_master)
    public function classMaster()
    {
        return $this->classTeacher();
    }

    public function category()
    {
        return $this->belongsTo(ClassCategory::class, 'class_category_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subject', 'class_id', 'subject_id')
                    ->withPivot('id', 'teacher_id', 'max_marks', 'passing_marks', 'is_required', 'term', 'academic_year')
                    ->withTimestamps();
    }

    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class);
    }

    // Accessors
    public function getFullClassNameAttribute()
    {
        return $this->full_name ?? $this->name . ($this->section ? ' ' . $this->section : '');
    }

    public function getStudentCountAttribute()
    {
        return $this->students()->where('is_active', true)->count();
    }

    public function getAvailableSpotsAttribute()
    {
        return max(0, $this->capacity - $this->student_count);
    }

    public function getIsFullAttribute()
    {
        return $this->student_count >= $this->capacity;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function getLevels()
    {
        return self::select('name')->distinct()->orderBy('name')->pluck('name');
    }
}