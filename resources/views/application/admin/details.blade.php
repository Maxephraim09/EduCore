@extends('layouts.app')

@section('title', 'Application Details - ' . $application->application_number)

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('application.admin.index') }}">Applications</a></li>
<li class="breadcrumb-item active">{{ $application->application_number }}</li>
@endsection

@section('styles')
<style>
    .detail-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        transition: all 0.3s ease;
        background: #fff;
    }
    .detail-card:hover {
        border-color: #667eea;
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.08);
    }
    .detail-card .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        border-radius: 12px 12px 0 0 !important;
        padding: 15px 20px;
    }
    .detail-card .card-body {
        padding: 20px;
    }
    .info-row {
        display: flex;
        padding: 8px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .info-row .label {
        width: 140px;
        font-weight: 600;
        color: #475569;
        font-size: 14px;
    }
    .info-row .value {
        flex: 1;
        color: #1e293b;
    }
    .status-badge {
        font-size: 14px;
        padding: 6px 20px;
        border-radius: 20px;
    }
    .document-preview {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 10px;
        text-align: center;
        transition: all 0.3s ease;
    }
    .document-preview:hover {
        border-color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .document-preview .doc-icon {
        font-size: 48px;
        color: #94a3b8;
        margin-bottom: 8px;
    }
    .document-preview .doc-name {
        font-size: 12px;
        color: #64748b;
        word-break: break-all;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Status Banner -->
            <div class="alert alert-{{ $application->status == 'pending' ? 'warning' : ($application->status == 'admitted' ? 'success' : ($application->status == 'rejected' ? 'danger' : 'info')) }} mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-{{ $application->status == 'pending' ? 'clock' : ($application->status == 'admitted' ? 'check-circle' : ($application->status == 'rejected' ? 'times-circle' : 'search')) }} me-2"></i>
                        <strong>Application Status:</strong> 
                        {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                        @if($application->status == 'admitted')
                            <span class="ms-2">| Admission Number: <strong>{{ $application->admission_number }}</strong></span>
                        @endif
                    </div>
                    <div>
                        @if($application->payment_status == 'paid')
                            <span class="badge badge-success">Payment: Paid</span>
                        @else
                            <span class="badge badge-warning">Payment: Pending</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Personal Information -->
                <div class="col-md-6">
                    <div class="detail-card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-user me-2" style="color: #667eea;"></i>Personal Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="info-row">
                                <span class="label">Full Name</span>
                                <span class="value">{{ $application->full_name }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Application No.</span>
                                <span class="value"><strong>{{ $application->application_number }}</strong></span>
                            </div>
                            <div class="info-row">
                                <span class="label">Email</span>
                                <span class="value">{{ $application->email }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Phone</span>
                                <span class="value">{{ $application->phone }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Date of Birth</span>
                                <span class="value">{{ $application->date_of_birth->format('d/m/Y') }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Gender</span>
                                <span class="value">{{ ucfirst($application->gender) }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Blood Group</span>
                                <span class="value">{{ $application->blood_group ?? 'N/A' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Address</span>
                                <span class="value">{{ $application->address }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Parent Information -->
                    <div class="detail-card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-users me-2" style="color: #667eea;"></i>Parent/Guardian Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="info-row">
                                <span class="label">Full Name</span>
                                <span class="value">{{ $application->parent_name }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Phone</span>
                                <span class="value">{{ $application->parent_phone }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Email</span>
                                <span class="value">{{ $application->parent_email ?? 'N/A' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Occupation</span>
                                <span class="value">{{ $application->parent_occupation ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Academic Information -->
                    <div class="detail-card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-graduation-cap me-2" style="color: #667eea;"></i>Academic Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="info-row">
                                <span class="label">Applied Class</span>
                                <span class="value">{{ $application->class->full_class_name ?? 'N/A' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Application Type</span>
                                <span class="value">{{ ucfirst($application->application_type) }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Previous School</span>
                                <span class="value">{{ $application->previous_school ?? 'N/A' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Previous Class</span>
                                <span class="value">{{ $application->previous_class ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="col-md-6">
                    <!-- Payment Information -->
                    <div class="detail-card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-credit-card me-2" style="color: #667eea;"></i>Payment Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="info-row">
                                <span class="label">Application Fee</span>
                                <span class="value">{{ formatCurrency($application->application_fee) }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Amount Paid</span>
                                <span class="value">{{ formatCurrency($application->amount_paid) }}</span>
                            </div>
                            <div class="info-row">
                                <span class="label">Payment Status</span>
                                <span class="value">
                                    @if($application->payment_status == 'paid')
                                        <span class="badge badge-success">Paid</span>
                                    @else
                                        <span class="badge badge-warning">Pending</span>
                                    @endif
                                </span>
                            </div>
                            @if($application->payment_reference)
                            <div class="info-row">
                                <span class="label">Reference</span>
                                <span class="value">{{ $application->payment_reference }}</span>
                            </div>
                            @endif
                            @if($application->payment_date)
                            <div class="info-row">
                                <span class="label">Payment Date</span>
                                <span class="value">{{ $application->payment_date->format('d/m/Y h:i A') }}</span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Documents -->
                    <div class="detail-card mb-4">
                        <div class="card-header">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-file-upload me-2" style="color: #667eea;"></i>Uploaded Documents
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if($application->documents->count() > 0)
                                    @foreach($application->documents as $doc)
                                        <div class="col-md-4 mb-3">
                                            <div class="document-preview">
                                                <div class="doc-icon">
                                                    <i class="fas fa-{{ str_contains($doc->mime_type, 'pdf') ? 'file-pdf' : (str_contains($doc->mime_type, 'image') ? 'image' : 'file') }}"></i>
                                                </div>
                                                <div class="doc-name">{{ $doc->document_name }}</div>
                                                <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="btn btn-sm btn-primary mt-2">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12 text-center py-3">
                                        <p class="text-muted">No documents uploaded</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Update Status -->
                    <div class="detail-card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-edit me-2"></i>Update Application Status
                            </h6>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('application.admin.update', $application->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                            <select name="status" id="status" class="form-select" required>
                                                <option value="pending" {{ $application->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="under_review" {{ $application->status == 'under_review' ? 'selected' : '' }}>Under Review</option>
                                                <option value="approved" {{ $application->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                                <option value="admitted" {{ $application->status == 'admitted' ? 'selected' : '' }}>Admitted</option>
                                                <option value="rejected" {{ $application->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="class_id" class="form-label">Change Class</label>
                                            <select name="class_id" id="class_id" class="form-select">
                                                <option value="">Keep Current Class</option>
                                                @foreach($classes as $class)
                                                    <option value="{{ $class->id }}" {{ $application->class_id == $class->id ? 'selected' : '' }}>
                                                        {{ $class->full_class_name ?? $class->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="remarks" class="form-label">Remarks</label>
                                            <textarea name="remarks" id="remarks" class="form-control" rows="2">{{ $application->remarks }}</textarea>
                                        </div>
                                    </div>

                                    <div class="col-12" id="rejectionReasonField" style="display: {{ $application->status == 'rejected' ? 'block' : 'none' }};">
                                        <div class="form-group">
                                            <label for="rejection_reason" class="form-label">Rejection Reason <span class="text-danger">*</span></label>
                                            <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="2">{{ $application->rejection_reason }}</textarea>
                                            <small class="text-muted">Required when rejecting an application</small>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary w-100">
                                            <i class="fas fa-save me-2"></i>Update Status
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    @if($application->status == 'admitted')
                    <div class="detail-card mb-4">
                        <div class="card-header bg-success text-white">
                            <h6 class="mb-0 fw-bold">
                                <i class="fas fa-bolt me-2"></i>Quick Actions
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('application.admission-letter', $application->admission_number) }}" target="_blank" class="btn btn-info">
                                    <i class="fas fa-file-pdf me-2"></i>View Admission Letter
                                </a>
                                <a href="{{ route('application.admission-letter.download', $application->admission_number) }}" class="btn btn-success">
                                    <i class="fas fa-download me-2"></i>Download Admission Letter
                                </a>
                                <a href="#" class="btn btn-primary" onclick="sendAdmissionEmail({{ $application->id }})">
                                    <i class="fas fa-envelope me-2"></i>Resend Admission Email
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Show/hide rejection reason based on status selection
    document.getElementById('status').addEventListener('change', function() {
        const rejectionField = document.getElementById('rejectionReasonField');
        if (this.value === 'rejected') {
            rejectionField.style.display = 'block';
        } else {
            rejectionField.style.display = 'none';
        }
    });

    function sendAdmissionEmail(applicationId) {
        if (confirm('Are you sure you want to resend the admission email to the applicant?')) {
            // AJAX call to resend email
            fetch(`/admin/applications/${applicationId}/resend-admission`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Admission email sent successfully!');
                } else {
                    alert('Failed to send email. Please try again.');
                }
            })
            .catch(error => {
                alert('An error occurred. Please try again.');
            });
        }
    }
</script>
@endpush
@endsection