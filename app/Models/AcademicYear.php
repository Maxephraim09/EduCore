<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_current',
        'status',
        'description',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
    ];

    public function terms()
    {
        return $this->hasMany(Term::class);
    }

    public function getCurrentTerm()
    {
        return $this->terms()->where('is_current', true)->first();
    }

    public function getTermBySequence($sequence)
    {
        return $this->terms()->where('sequence', $sequence)->first();
    }

    public function getTermsCount()
    {
        return $this->terms()->count();
    }

    public function hasAllTerms()
    {
        return $this->terms()->count() === 3;
    }

    public function getTermsCountAttribute()
    {
        return $this->terms()->count();
    }

    public function getDurationAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return null;
        }

        return $this->start_date->diffInDays($this->end_date) . ' days';
    }

    public function getYearRangeAttribute()
    {
        if (!$this->start_date || !$this->end_date) {
            return null;
        }

        return $this->start_date->format('Y') . '/' . $this->end_date->format('Y');
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
}

