<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $table = 'visitors';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'id_card',
        'person_to_see',
        'purpose',
        'check_in',
        'check_out',
        'status',
        'remarks',
        'checked_in_by',
        'checked_out_by',
    ];

    protected $casts = [
        'check_in' => 'datetime:H:i',
        'check_out' => 'datetime:H:i',
    ];

    public function checkedInBy()
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function checkedOutBy()
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }
}

