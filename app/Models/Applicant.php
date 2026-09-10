<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class Applicant extends \Illuminate\Foundation\Auth\User
{
    use HasFactory;

    protected $table = 'applicants';

    protected $fillable = ['name', 'email', 'phone', 'password'];

    protected $hidden = ['password'];
}
