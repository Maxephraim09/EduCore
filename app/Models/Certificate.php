<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'type', 'certificate_number', 'issue_date',
        'description', 'status'
    ];

    protected $casts = [
        'issue_date' => 'date',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function getTypeLabelAttribute()
    {
        $types = [
            'Academic Excellence' => '🎓 Academic Excellence',
            'Graduation' => '📜 Graduation',
            'Merit Award' => '🏆 Merit Award',
            'Participation' => '⭐ Participation',
            'Sports' => '⚽ Sports',
            'Art & Culture' => '🎨 Art & Culture',
        ];
        return $types[$this->type] ?? $this->type;
    }

    public function getStatusBadgeAttribute()
    {
        if ($this->status == 'active') {
            return '<span class="badge bg-success">Active</span>';
        } else {
            return '<span class="badge bg-danger">Inactive</span>';
        }
    }
}