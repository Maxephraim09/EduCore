<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Term extends Model
{
    use HasFactory;

    protected $fillable = [
        'academic_year_id',
        'name',
        'slug',
        'start_date',
        'end_date',
        'is_current',
        'status',
        'sequence',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    // If you later link Result to Term, you can enable this.
    // public function results()
    // {
    //     return $this->hasMany(Result::class);
    // }

    public function getDurationAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return null;
        }

        return $this->start_date->diffInDays($this->end_date) . ' days';
    }

    public function getTermLabelAttribute()
    {
        $terms = [
            'first-term' => '1st Term',
            'second-term' => '2nd Term',
            'third-term' => '3rd Term',
        ];

        return $terms[$this->slug] ?? $this->name;
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCurrent($query)
    {
        return $query->where('is_current', true);
    }

    public static function getCurrent()
    {
        return self::where('is_current', true)->first();
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isCurrent()
    {
        return (bool) $this->is_current;
    }

    public function getTermName()
    {
        $names = [
            1 => 'First Term',
            2 => 'Second Term',
            3 => 'Third Term',
        ];
        return $names[$this->sequence] ?? $this->name;
    }

    public function isFirstTerm()
    {
        return $this->sequence === 1;
    }

    public function isSecondTerm()
    {
        return $this->sequence === 2;
    }

    public function isThirdTerm()
    {
        return $this->sequence === 3;
    }

    public static function generateSlug($name, $academicYearName)
    {
        return Str::slug($name . '-' . $academicYearName);
    }
}

