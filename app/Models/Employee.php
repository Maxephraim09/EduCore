<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'employee_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'position',
        'department',
        'base_salary',
        'allowances',
        'joining_date',
        'bank_name',
        'account_number',
        'ifsc_code',
        'pan_number',
        'is_active',
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
        'joining_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }

    public function loans()
    {
        return $this->hasMany(StaffLoan::class);
    }

    public function overdrafts()
    {
        return $this->hasMany(StaffOverdraft::class);
    }
}
