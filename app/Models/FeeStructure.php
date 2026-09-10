<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'fee_name', 'fee_code', 'fee_type', 'class', 'class_id', 'term', 'academic_year',
        'amount', 'description', 'is_compulsory', 'is_active'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_compulsory' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function payments()
    {
        return $this->hasMany(FeePayment::class);
    }

    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function getFormattedAmountAttribute()
    {
        return '₦' . number_format($this->amount, 2);
    }
}
