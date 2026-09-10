<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'academic_year', 'term', 'type', 'exam_type',
        'subject_id', 'class_id', 'teacher_id', 'created_by',
        'time_allowed', 'total_marks', 'passing_marks',
        'start_date', 'end_date', 'instructions', 'questions_data',
        'is_published', 'shuffle_questions', 'show_results_immediately',
        'allow_review', 'status', 'published_at'
    ];

    protected static function booted(): void
    {
        static::creating(function (self $exam): void {
            if (empty($exam->code)) {
                $exam->code = 'EX-' . strtoupper(substr(str_replace(' ', '', $exam->name ?: 'EXAM'), 0, 6)) . '-' . str_pad((string) (self::count() + 1), 3, '0', STR_PAD_LEFT);
            }
            if (empty($exam->exam_type) && !empty($exam->type)) {
                $exam->exam_type = $exam->type;
            }
            if (empty($exam->exam_type)) {
                $exam->exam_type = 'promotion';
            }
            if (empty($exam->start_date)) {
                $exam->start_date = now()->startOfDay();
            }
            if (empty($exam->end_date)) {
                $exam->end_date = now()->endOfDay();
            }
        });
    }

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'published_at' => 'datetime',
        'is_published' => 'boolean',
        'shuffle_questions' => 'boolean',
        'show_results_immediately' => 'boolean',
        'allow_review' => 'boolean',
        'questions_data' => 'array',
    ];

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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function examSubjects()
    {
        return $this->hasMany(ExamSubject::class);
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'results', 'exam_id', 'student_id')
            ->withPivot(['total_score', 'grade', 'remark'])
            ->withTimestamps();
    }

    public function getStatusBadgeAttribute()
    {
        if ($this->is_published) {
            return '<span class="badge bg-success">Published</span>';
        }
        return '<span class="badge bg-warning">Draft</span>';
    }

    public function getExamTypeLabelAttribute()
    {
        $types = [
            'continuous_assessment' => 'Continuous Assessment',
            'mid_term' => 'Mid Term',
            'end_term' => 'End Term',
            'promotion' => 'Promotion',
            'end_of_term' => 'End of Term',
            'first_ca' => 'First CA',
            'second_ca' => 'Second CA',
            'third_ca' => 'Third CA',
            'mock' => 'Mock',
        ];

        $key = $this->exam_type ?: $this->type;
        return $types[$key] ?? $key;
    }

    public function getTimeAllowedFormattedAttribute()
    {
        if ($this->time_allowed >= 60) {
            $hours = floor($this->time_allowed / 60);
            $minutes = $this->time_allowed % 60;
            return $hours . 'h ' . ($minutes ? $minutes . 'min' : '');
        }
        return $this->time_allowed . ' minutes';
    }

    public function getQuestionsCountAttribute()
    {
        return $this->questions_data ? count($this->questions_data) : 0;
    }
}