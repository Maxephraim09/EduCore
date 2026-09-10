<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffOverdraft extends Model
{
    protected $fillable = [
        'employee_id',
        'limit_amount',
        'used_amount',
        'available_amount',
        'interest_rate',
        'sanction_date',
        'expiry_date',
        'status',
        'approved_by',
        'remarks',
    ];

    protected $casts = [
        'limit_amount' => 'decimal:2',
        'used_amount' => 'decimal:2',
        'available_amount' => 'decimal:2',
        'interest_rate' => 'decimal:2',
        'sanction_date' => 'date',
        'expiry_date' => 'date',
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
