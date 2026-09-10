<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'excerpt', 'content',
        'featured_image', 'status', 'rejection_reason', 'views', 'approved_at', 'approved_by'
    ];

    protected $casts = ['approved_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(function (self $post): void {
            $post->slug = $post->slug ?: Str::slug($post->title) . '-' . Str::lower(Str::random(5));
        });
    }

    public function author() { return $this->belongsTo(User::class, 'user_id'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
    public function category() { return $this->belongsTo(BlogCategory::class, 'category_id'); }
    public function tags() { return $this->belongsToMany(BlogTag::class, 'blog_post_tag'); }
    public function comments() { return $this->hasMany(BlogComment::class); }
    public function approvedComments() { return $this->comments()->where('is_approved', true); }
    public function scopeApproved($query) { return $query->where('status', 'approved'); }
}