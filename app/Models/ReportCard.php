<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class ReportCard extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'exam_id',
        'is_published',
        'published_at',
        'published_by',
        'remarks',
        'teacher_comment',
        'principal_comment',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function scopeForExam($query, $examId)
    {
        $table = $query->getModel()->getTable();
        if (Schema::hasColumn($table, 'exam_id')) {
            return $query->where('exam_id', $examId);
        }

        return $query->where('term_id', $examId);
    }

    public static function firstOrCreateForExam(int $studentId, int $examId, array $attributes = [])
    {
        $model = new self();
        if (Schema::hasColumn($model->getTable(), 'exam_id')) {
            return static::firstOrCreate([
                'student_id' => $studentId,
                'exam_id' => $examId,
            ], $attributes);
        }

        return static::firstOrCreate([
            'student_id' => $studentId,
            'term_id' => $examId,
        ], array_merge($attributes, ['term_id' => $examId]));
    }

    public static function findForExam(int $studentId, int $examId)
    {
        $query = static::where('student_id', $studentId);
        $table = (new self())->getTable();

        if (Schema::hasColumn($table, 'exam_id')) {
            return $query->where('exam_id', $examId)->first();
        }

        return $query->where('term_id', $examId)->first();
    }

    public function results()
    {
        return $this->hasMany(Result::class);
    }

    public function isPublished()
    {
        return (bool) $this->is_published;
    }

    public function publish()
    {
        $this->is_published = true;
        $this->published_at = now();
        $this->published_by = auth()->id();
        $this->save();
    }

    public function unpublish()
    {
        $this->is_published = false;
        $this->published_at = null;
        $this->published_by = null;
        $this->save();
    }
}
