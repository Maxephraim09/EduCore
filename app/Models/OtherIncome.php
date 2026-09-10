<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtherIncome extends Model
{
    protected $fillable = [
        'income_number',
        'category_id',
        'source',
        'amount',
        'income_date',
        'payment_method',
        'reference_number',
        'description',
        'receipt_path',
        'received_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'income_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(IncomeCategory::class, 'category_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }
}
