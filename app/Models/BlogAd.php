<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogAd extends Model
{
    protected $fillable = ['title', 'description', 'image', 'url', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
}