<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CbtQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'cbt_exam_id', 'type', 'question', 'explanation',
        'marks', 'correct_answer', 'options', 'order', 'is_active'
    ];

    protected $casts = [
        'options' => 'array',
        'is_active' => 'boolean',
    ];

    public function exam()
    {
        return $this->belongsTo(CbtExam::class, 'cbt_exam_id');
    }

    public function getOptionsArrayAttribute()
    {
        return is_array($this->options) ? $this->options : json_decode($this->options, true) ?? [];
    }
}