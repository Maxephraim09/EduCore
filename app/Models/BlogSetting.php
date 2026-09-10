<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogSetting extends Model
{
    protected $fillable = ['hero_title', 'hero_text', 'hero_image', 'facebook_url', 'instagram_url', 'youtube_url', 'x_url'];
}