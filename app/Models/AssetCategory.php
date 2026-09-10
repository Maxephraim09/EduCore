<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code', 'depreciation_rate', 'description'];

    protected $casts = [
        'depreciation_rate' => 'decimal:2',
    ];

    public function assets()
    {
        return $this->hasMany(Asset::class, 'category_id');
    }
}