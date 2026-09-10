<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncomeCategory extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
    ];

    public function incomes()
    {
        return $this->hasMany(OtherIncome::class, 'category_id');
    }
}
