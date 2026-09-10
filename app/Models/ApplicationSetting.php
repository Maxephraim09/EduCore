<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApplicationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_editable',
    ];

    protected $casts = [
        'is_editable' => 'boolean',
    ];

    public function getTypedValueAttribute(): mixed
    {
        return match ($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'integer' => (int) $this->value,
            'json' => is_null($this->value) ? null : json_decode($this->value, true),
            default => $this->value,
        };
    }

    public function scopeGroup($query, string $group)
    {
        return $query->where('group', $group);
    }

    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = static::where('key', $key)->first();
        if (! $setting) {
            return $default;
        }

        return $setting->typed_value;
    }

    public static function setValue(string $key, mixed $value): void
    {
        $setting = static::where('key', $key)->first();
        if (! $setting) {
            return;
        }

        $setting->value = is_array($value) || is_object($value)
            ? json_encode($value)
            : (string) $value;

        $setting->save();
    }
}

