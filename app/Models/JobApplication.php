<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_post_id',
        'applicant_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'qualifications',
        'experience',
        'cover_letter',
        'skills',
        'cv_path',
        'certificates_path',
        'portfolio_path',
        'status',
        'admin_notes',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
        'interview_date',
        'interview_notes',
        'interview_link',
        'interview_location',
        'hired_date',
        'employee_id',
        'academic_year_id',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'reviewed_at' => 'datetime',
        'interview_date' => 'datetime',
        'hired_date' => 'date',
    ];

    public function jobPost()
    {
        return $this->belongsTo(JobPost::class, 'job_post_id');
    }

    public function applicant()
    {
        return $this->belongsTo(User::class, 'applicant_id');
    }

    public function reviewedBy()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class, 'academic_year_id');
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'under_review' => 'Under Review',
            'shortlisted' => 'Shortlisted',
            'interviewed' => 'Interviewed',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            default => (string) $this->status,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        $labels = [
            'pending' => '<span class="badge badge-warning">Pending</span>',
            'under_review' => '<span class="badge badge-info">Under Review</span>',
            'shortlisted' => '<span class="badge badge-primary">Shortlisted</span>',
            'interviewed' => '<span class="badge badge-secondary">Interviewed</span>',
            'accepted' => '<span class="badge badge-success">Accepted</span>',
            'rejected' => '<span class="badge badge-danger">Rejected</span>',
        ];

        return $labels[$this->status] ?? (string) $this->status;
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', 'under_review');
    }

    public function scopeShortlisted($query)
    {
        return $query->where('status', 'shortlisted');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function canUpdateStatus(): bool
    {
        return ! in_array($this->status, ['accepted', 'rejected'], true);
    }

    public function markAsReviewed(int $userId): void
    {
        $this->update([
            'reviewed_by' => $userId,
            'reviewed_at' => now(),
        ]);
    }
}

