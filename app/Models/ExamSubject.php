<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamSubject extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id', 'class_id', 'subject_id', 'teacher_id',
        'max_marks', 'passing_marks', 'exam_date'
    ];

    protected $casts = [
        'exam_date' => 'datetime',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Employee::class, 'teacher_id');
    }
}