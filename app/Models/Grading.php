<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grading extends Model
{
    use HasFactory;

    protected $fillable = [
        'ca1_max',
        'ca2_max',
        'ca3_max',
        'exam_max',
        'practical_max',
        'passing_marks',
        'has_practical',
        'total_max',
        'created_by',
    ];

    protected $casts = [
        'has_practical' => 'boolean',
        'ca1_max' => 'integer',
        'ca2_max' => 'integer',
        'ca3_max' => 'integer',
        'exam_max' => 'integer',
        'practical_max' => 'integer',
        'passing_marks' => 'integer',
        'total_max' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the grade for a given score.
     */
    public function getGrade($score)
    {
        $gradeScale = GradeScale::where('is_active', true)
            ->where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->first();
            
        return $gradeScale;
    }

    /**
     * Check if a score is passing.
     */
    public function isPassing($score)
    {
        return $score >= $this->passing_marks;
    }

    /**
     * Get the total CA marks.
     */
    public function getTotalCaAttribute()
    {
        return $this->ca1_max + $this->ca2_max + $this->ca3_max;
    }

    /**
     * Get the total marks including practical.
     */
    public function getTotalWithPracticalAttribute()
    {
        $total = $this->getTotalCaAttribute() + $this->exam_max;
        if ($this->has_practical) {
            $total += $this->practical_max;
        }
        return $total;
    }
}