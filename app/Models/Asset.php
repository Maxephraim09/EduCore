<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_code', 'name', 'category_id', 'purchase_price', 'current_value',
        'purchase_date', 'supplier', 'serial_number', 'location', 'status',
        'assigned_to', 'remarks'
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'current_value' => 'decimal:2',
        'purchase_date' => 'date',
    ];

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'category_id');
    }

    public function calculateDepreciation()
    {
        $category = $this->category;
        $purchaseDate = Carbon::parse($this->purchase_date);
        $monthsOld = $purchaseDate->diffInMonths(Carbon::now());
        $annualDepreciation = $this->purchase_price * ($category->depreciation_rate / 100);
        $monthlyDepreciation = $annualDepreciation / 12;
        $totalDepreciation = $monthlyDepreciation * $monthsOld;
        
        return max(0, $this->purchase_price - $totalDepreciation);
    }

    public function getDepreciationPercentageAttribute()
    {
        if ($this->purchase_price == 0) return 0;
        return (($this->purchase_price - $this->current_value) / $this->purchase_price) * 100;
    }

    public function getAgeInYearsAttribute()
    {
        return Carbon::parse($this->purchase_date)->diffInYears(Carbon::now());
    }

    public function isFullyDepreciated()
    {
        return $this->current_value <= 0 || $this->status == 'depreciated';
    }
}