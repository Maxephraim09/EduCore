<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_paper_id',
        'question',
        'type',
        'marks',
        'options',
        'correct_answer',
        'explanation',
        'order',
    ];

    protected $casts = [
        'options' => 'array',
        'marks' => 'integer',
        'order' => 'integer',
    ];

    public function questionPaper()
    {
        return $this->belongsTo(QuestionPaper::class);
    }

    // Accessors
    public function getTypeLabelAttribute()
    {
        $labels = [
            'multiple_choice' => 'Multiple Choice',
            'true_false' => 'True/False',
            'short_answer' => 'Short Answer',
            'essay' => 'Essay',
        ];
        return $labels[$this->type] ?? $this->type;
    }

    // Helper methods
    public function isMultipleChoice()
    {
        return $this->type === 'multiple_choice';
    }

    public function isTrueFalse()
    {
        return $this->type === 'true_false';
    }

    public function isShortAnswer()
    {
        return $this->type === 'short_answer';
    }

    public function isEssay()
    {
        return $this->type === 'essay';
    }
}