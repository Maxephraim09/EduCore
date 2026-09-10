<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffLoan extends Model
{
    protected $fillable = [
        'employee_id',
        'amount',
        'interest_rate',
        'tenure_months',
        'monthly_installment',
        'total_payable',
        'paid_amount',
        'remaining_amount',
        'sanction_date',
        'first_installment_date',
        'purpose',
        'status',
        'approved_by',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'monthly_installment' => 'decimal:2',
        'total_payable' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'remaining_amount' => 'decimal:2',
        'sanction_date' => 'date',
        'first_installment_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
