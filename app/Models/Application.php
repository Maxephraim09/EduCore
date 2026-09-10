<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        // Personal Information
        'first_name', 'last_name', 'middle_name', 'date_of_birth',
        'gender', 'nationality', 'state_of_origin', 'local_government',
        'religion', 'blood_group',
        
        // Contact Information
        'email', 'phone', 'address',
        
        // Parent/Guardian
        'parent_name', 'parent_phone', 'parent_email', 'parent_occupation',
        
        // Academic Information
        'applied_class', 'class_id', 'previous_school', 'previous_class',
        'previous_year',
        
        // Application Details
        'application_number', 'application_type', 'status', 'remarks',
        'rejection_reason',
        
        // Payment Information
        'payment_reference', 'application_fee', 'amount_paid',
        'payment_status', 'payment_date', 'payment_details',
        
        // Documents
        'birth_certificate', 'passport_photo', 'report_card',
        'other_documents',
        
        // Admission Details
        'admitted_by', 'admission_number', 'admitted_at',
        'admission_letter_sent_at',
        
        // Student Account
        'student_id', 'user_id',
        
        // Fee Payment Status
        'total_fees', 'fees_paid', 'fee_status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'payment_date' => 'datetime',
        'admitted_at' => 'datetime',
        'admission_letter_sent_at' => 'datetime',
        'application_fee' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'total_fees' => 'decimal:2',
        'fees_paid' => 'decimal:2',
    ];

    // Relationships
    public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function admittedBy()
    {
        return $this->belongsTo(User::class, 'admitted_by');
    }

    public function documents()
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    // Accessors
    public function getFullNameAttribute()
    {
        $middle = $this->middle_name ? ' ' . $this->middle_name : '';
        return $this->first_name . $middle . ' ' . $this->last_name;
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => '<span class="badge badge-warning">Pending</span>',
            'under_review' => '<span class="badge badge-info">Under Review</span>',
            'approved' => '<span class="badge badge-success">Approved</span>',
            'admitted' => '<span class="badge badge-primary">Admitted</span>',
            'rejected' => '<span class="badge badge-danger">Rejected</span>',
        ];
        return $labels[$this->status] ?? $this->status;
    }

    public function getPaymentStatusLabelAttribute()
    {
        $labels = [
            'pending' => '<span class="badge badge-warning">Pending</span>',
            'paid' => '<span class="badge badge-success">Paid</span>',
            'failed' => '<span class="badge badge-danger">Failed</span>',
        ];
        return $labels[$this->payment_status] ?? $this->payment_status;
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeUnderReview($query)
    {
        return $query->where('status', 'under_review');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeAdmitted($query)
    {
        return $query->where('status', 'admitted');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    // Helper Methods
    public function generateApplicationNumber()
    {
        $year = date('Y');
        $count = self::whereYear('created_at', $year)->count() + 1;
        return 'APP-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    public function generateAdmissionNumber()
    {
        $year = date('Y');
        $count = self::whereYear('admitted_at', $year)->count() + 1;
        return 'ADM-' . $year . '-' . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isUnderReview()
    {
        return $this->status === 'under_review';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isAdmitted()
    {
        return $this->status === 'admitted';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isPaid()
    {
        return $this->payment_status === 'paid';
    }

    public function getFeeBalance()
    {
        return $this->total_fees - $this->fees_paid;
    }

    public function getFeeStatusText()
    {
        $balance = $this->getFeeBalance();
        if ($balance <= 0) return 'Paid';
        if ($balance < $this->total_fees) return 'Partial';
        return 'Unpaid';
    }
}