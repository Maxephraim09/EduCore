<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'fee_structure_id', 'receipt_number', 'transaction_ref',
        'amount', 'amount_paid', 'balance', 'payment_date', 'payment_method',
        'payment_status', 'term', 'academic_year', 'payment_details', 'remarks', 'collected_by', 'received_by', 'fee_type'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'balance' => 'decimal:2',
        'payment_date' => 'date',
        'payment_details' => 'array'
    ];

    protected static function booted(): void
    {
        static::creating(function (self $payment): void {
            if (empty($payment->received_by)) {
                $payment->received_by = null;
            } elseif ($payment->received_by && !User::whereKey($payment->received_by)->exists()) {
                $payment->received_by = null;
            }
            if (empty($payment->payment_date)) {
                $payment->payment_date = now()->toDateString();
            }
            if ($payment->amount_paid === null && $payment->amount !== null) {
                $payment->amount_paid = $payment->amount;
            }
            if ($payment->balance === null && $payment->amount_paid !== null && $payment->amount !== null) {
                $payment->balance = max(0, (float) $payment->amount - (float) $payment->amount_paid);
            }
            if (empty($payment->status)) {
                $payment->status = 'completed';
            }
            if (empty($payment->payment_status)) {
                $payment->payment_status = 'success';
            }
        });
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class);
    }

    public function collector()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function paystackTransaction()
    {
        return $this->hasOne(PaystackTransaction::class, 'payment_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function getReceiptUrlAttribute()
    {
        return route('fee-payments.receipt', $this->id);
    }
}
