<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GradeScale extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'grade', 'min_score', 'max_score', 'remark', 'color', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function getGrade($score)
    {
        $grade = self::where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->where('is_active', true)
            ->first();
        
        return $grade;
    }

    public static function getGradeRemark($score)
    {
        $grade = self::getGrade($score);
        return $grade ? $grade->remark : 'N/A';
    }
}