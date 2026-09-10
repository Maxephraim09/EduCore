<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogCategory extends Model
{
    protected $fillable = ['name', 'slug', 'description'];

    protected static function booted(): void
    {
        static::creating(function (self $category): void {
            $category->slug = $category->slug ?: Str::slug($category->name);
        });
    }

    public function posts()
    {
        return $this->hasMany(BlogPost::class, 'category_id');
    }
}