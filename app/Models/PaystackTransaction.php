<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaystackTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'gateway_response',
        'channel',
        'ip_address',
        'applicant_id',
        'paystack_response',
        'payment_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paystack_response' => 'array',
    ];

    public function payment()
    {
        return $this->belongsTo(FeePayment::class, 'payment_id');
    }

    public function applicant()
    {
        return $this->belongsTo(\App\Models\Applicant::class, 'applicant_id');
    }
}
