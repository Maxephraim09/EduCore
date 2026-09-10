@extends('layouts.app')

@section('title', 'Edit Profile')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('profile.index') }}">My Profile</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('styles')
<style>
    /* Card Improvements */
    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .card-header {
        background: white;
        border-bottom: 1px solid #f1f3f5;
        padding: 20px 24px;
    }
    .card-header h5 {
        font-weight: 700;
        font-size: 18px;
        color: #1b1b18;
    }
    .card-header h5 i {
        color: #667eea;
    }
    .card-body {
        padding: 28px 32px;
    }

    /* Avatar Upload */
    .avatar-upload {
        position: relative;
        display: inline-block;
    }
    .avatar-upload .avatar-preview {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        border: 4px solid #e5e7eb;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        font-size: 52px;
        color: #6b7280;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }
    .avatar-upload .avatar-preview:hover {
        border-color: #667eea;
        transform: scale(1.02);
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.15);
    }
    .avatar-upload .avatar-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .avatar-upload .upload-overlay {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 3px solid white;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }
    .avatar-upload .upload-overlay:hover {
        transform: scale(1.15);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
    }
    .avatar-upload .upload-overlay i {
        font-size: 16px;
    }
    .avatar-upload input[type="file"] {
        display: none;
    }
    .avatar-upload .upload-hint {
        font-size: 13px;
        color: #6b7280;
        margin-top: 12px;
    }

    /* Form Sections */
    .form-section {
        background: #fafbfc;
        border: 1px solid #f1f3f5;
        border-radius: 12px;
        padding: 24px 28px;
        margin-bottom: 24px;
        transition: all 0.3s ease;
    }
    .form-section:hover {
        border-color: #d1d5db;
        background: #f9fafb;
    }
    .form-section .section-title {
        font-weight: 700;
        font-size: 16px;
        color: #1b1b18;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 2px solid #e5e7eb;
        display: flex;
        align-items: center;
    }
    .form-section .section-title i {
        color: #667eea;
        margin-right: 10px;
        font-size: 18px;
    }
    .form-section .section-title .badge {
        margin-left: 10px;
        font-size: 11px;
        padding: 3px 10px;
        border-radius: 20px;
        font-weight: 500;
    }

    /* Form Controls */
    .form-label {
        font-weight: 600;
        font-size: 14px;
        color: #374151;
        margin-bottom: 6px;
    }
    .form-label .text-danger {
        font-weight: 700;
    }
    .form-control {
        border-radius: 10px;
        border: 1.5px solid #e5e7eb;
        padding: 10px 14px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: white;
    }
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }
    .form-control:disabled {
        background: #f3f4f6;
        cursor: not-allowed;
    }
    .form-control.is-invalid {
        border-color: #ef4444;
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1);
    }
    .form-control.is-invalid:focus {
        box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15);
    }
    .form-control-lg {
        padding: 12px 16px;
        font-size: 15px;
    }
    textarea.form-control {
        resize: vertical;
        min-height: 80px;
    }

    /* Buttons */
    .btn {
        border-radius: 10px;
        padding: 10px 24px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        letter-spacing: 0.3px;
    }
    .btn-primary {
        background: linear-gradient(135deg, #667eea, #764ba2);
        border: none;
        color: white;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.25);
    }
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(102, 126, 234, 0.35);
        color: white;
    }
    .btn-primary:active {
        transform: translateY(0);
    }
    .btn-secondary {
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
        color: #4b5563;
    }
    .btn-secondary:hover {
        background: #e5e7eb;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .btn-lg {
        padding: 12px 32px;
        font-size: 15px;
        border-radius: 12px;
    }

    /* Alerts */
    .alert {
        border-radius: 12px;
        border: none;
        padding: 16px 20px;
        font-weight: 500;
        box-shadow: 0 4px 12px rgba(0,0,0,0.06);
    }
    .alert-success {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
    }
    .alert-success i {
        color: #10b981;
    }
    .alert-danger {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
    }
    .alert-danger i {
        color: #ef4444;
    }
    .alert .btn-close {
        padding: 1rem;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .card-body {
            padding: 20px;
        }
        .form-section {
            padding: 16px 20px;
        }
        .avatar-upload .avatar-preview {
            width: 120px;
            height: 120px;
            font-size: 40px;
        }
        .avatar-upload .upload-overlay {
            width: 34px;
            height: 34px;
            font-size: 14px;
        }
        .btn {
            padding: 8px 16px;
            font-size: 13px;
        }
        .btn-lg {
            padding: 10px 20px;
        }
        .form-control {
            font-size: 13px;
            padding: 8px 12px;
        }
    }

    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .alert {
        animation: fadeIn 0.5s ease;
    }
    .form-section {
        animation: fadeIn 0.6s ease;
    }

    /* Custom checkbox and radio styles */
    .form-check {
        padding-left: 0;
    }
    .form-check-input {
        width: 18px;
        height: 18px;
        margin-right: 8px;
        border-radius: 6px;
        border: 2px solid #d1d5db;
        cursor: pointer;
        transition: all 0.2s;
    }
    .form-check-input:checked {
        background-color: #667eea;
        border-color: #667eea;
    }

    /* Additional Info */
    .info-box {
        background: #f3f4f6;
        border-radius: 8px;
        padding: 8px 14px;
        font-size: 13px;
        color: #6b7280;
    }
    .info-box i {
        color: #667eea;
        margin-right: 6px;
    }

    /* Error styling */
    .invalid-feedback {
        font-size: 13px;
        font-weight: 500;
        margin-top: 4px;
        color: #ef4444;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .invalid-feedback::before {
        content: '⚠';
        font-size: 14px;
    }

    /* Required field indicator */
    .required-indicator {
        color: #ef4444;
        font-weight: 700;
        margin-left: 2px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-edit me-2"></i>Edit Profile</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Avatar Upload -->
                        <div class="text-center mb-4">
                            <div class="avatar-upload">
                                <div class="avatar-preview" onclick="document.getElementById('avatarInput').click()" title="Click to change avatar">
                                    @if($user->avatar_url)
                                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
                                    @else
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    @endif
                                </div>
                                <div class="upload-overlay" onclick="document.getElementById('avatarInput').click()" title="Upload new avatar">
                                    <i class="fas fa-camera"></i>
                                </div>
                                <input type="file" id="avatarInput" name="avatar" accept="image/*">
                            </div>
                            <div class="upload-hint">
                                <i class="fas fa-info-circle me-1"></i>
                                Click to upload new avatar (JPEG, PNG, JPG, GIF - Max 2MB)
                            </div>
                            @error('avatar')
                                <div class="invalid-feedback d-block text-center">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Personal Information -->
                        <div class="form-section">
                            <h6 class="section-title">
                                <i class="fas fa-user"></i>
                                Personal Information
                                <span class="badge bg-primary ms-2">Required</span>
                            </h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">
                                            Full Name <span class="required-indicator">*</span>
                                        </label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $user->name) }}" 
                                               placeholder="Enter your full name" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="email" class="form-label">
                                            Email Address <span class="required-indicator">*</span>
                                        </label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email', $user->email) }}" 
                                               placeholder="Enter your email address" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">
                                            <i class="fas fa-phone me-1 text-muted"></i>
                                            Phone Number
                                        </label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                               id="phone" name="phone" value="{{ old('phone', $profileData['student']->phone ?? $profileData['employee']->phone ?? '') }}" 
                                               placeholder="Enter your phone number">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="role" class="form-label">
                                            <i class="fas fa-user-tag me-1 text-muted"></i>
                                            Role
                                        </label>
                                        <input type="text" class="form-control" 
                                               value="{{ ucfirst(str_replace('_', ' ', $user->role ?? 'User')) }}" 
                                               disabled>
                                        <small class="text-muted">Role cannot be changed</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="address" class="form-label">
                                            <i class="fas fa-home me-1 text-muted"></i>
                                            Address
                                        </label>
                                        <textarea class="form-control @error('address') is-invalid @enderror" 
                                                  id="address" name="address" rows="2" 
                                                  placeholder="Enter your current address">{{ old('address', $profileData['student']->address ?? $profileData['employee']->address ?? '') }}</textarea>
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-0">
                                        <label for="bio" class="form-label">
                                            <i class="fas fa-info-circle me-1 text-muted"></i>
                                            Bio / About
                                        </label>
                                        <textarea class="form-control @error('bio') is-invalid @enderror" 
                                                  id="bio" name="bio" rows="3" 
                                                  placeholder="Tell us a little about yourself...">{{ old('bio', $user->bio ?? '') }}</textarea>
                                        <small class="text-muted">Maximum 1000 characters</small>
                                        @error('bio')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Information for Students -->
                        @if($user->role === 'student' && $profileData['student'])
                            <div class="form-section">
                                <h6 class="section-title">
                                    <i class="fas fa-graduation-cap"></i>
                                    Student Information
                                    <span class="badge bg-info ms-2">Read Only</span>
                                </h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-id-card me-1 text-muted"></i>
                                                Admission Number
                                            </label>
                                            <input type="text" class="form-control" 
                                                   value="{{ $profileData['student']->admission_number ?? 'N/A' }}" 
                                                   disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-school me-1 text-muted"></i>
                                                Class
                                            </label>
                                            <input type="text" class="form-control" 
                                                   value="{{ $profileData['student']->class->full_class_name ?? 'N/A' }}" 
                                                   disabled>
                                        </div>
                                    </div>
                                </div>
                                @if($profileData['student']->guardian_contact ?? null)
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="info-box">
                                            <i class="fas fa-info-circle"></i>
                                            Guardian Contact: {{ $profileData['student']->guardian_contact }}
                                            <span class="ms-3">|</span>
                                            <i class="fas fa-calendar-alt ms-3"></i>
                                            Enrollment: {{ $profileData['student']->enrollment_date ?? 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        @endif

                        <!-- Additional Information for Employees -->
                        @if(in_array($user->role, ['teacher', 'admin', 'super-admin', 'accountant', 'frontdesk']) && $profileData['employee'])
                            <div class="form-section">
                                <h6 class="section-title">
                                    <i class="fas fa-briefcase"></i>
                                    Employee Information
                                    <span class="badge bg-info ms-2">Read Only</span>
                                </h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-id-badge me-1 text-muted"></i>
                                                Employee ID
                                            </label>
                                            <input type="text" class="form-control" 
                                                   value="{{ $profileData['employee']->employee_id ?? 'N/A' }}" 
                                                   disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-briefcase me-1 text-muted"></i>
                                                Position
                                            </label>
                                            <input type="text" class="form-control" 
                                                   value="{{ $profileData['employee']->position ?? 'N/A' }}" 
                                                   disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label class="form-label">
                                                <i class="fas fa-building me-1 text-muted"></i>
                                                Department
                                            </label>
                                            <input type="text" class="form-control" 
                                                   value="{{ $profileData['employee']->department ?? 'N/A' }}" 
                                                   disabled>
                                        </div>
                                    </div>
                                </div>
                                @if($profileData['employee']->hire_date ?? null)
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="info-box">
                                            <i class="fas fa-calendar-check"></i>
                                            Hire Date: {{ \Carbon\Carbon::parse($profileData['employee']->hire_date)->format('d M Y') }}
                                            <span class="ms-3">|</span>
                                            <i class="fas fa-clock ms-3"></i>
                                            Working Since: {{ \Carbon\Carbon::parse($profileData['employee']->hire_date)->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        @endif

                        <!-- Form Actions -->
                        <div class="d-flex flex-wrap justify-content-between align-items-center mt-4 pt-2">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i> Save Changes
                                </button>
                                <a href="{{ route('profile.index') }}" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times me-2"></i> Cancel
                                </a>
                            </div>
                            <div class="mt-3 mt-md-0">
                                <small class="text-muted d-flex align-items-center">
                                    <i class="fas fa-info-circle me-2"></i>
                                    All fields marked with <span class="required-indicator mx-1">*</span> are required
                                </small>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Preview avatar before upload
    document.getElementById('avatarInput').addEventListener('change', function(e) {
        const preview = document.querySelector('.avatar-preview');
        const file = this.files[0];
        
        if (file) {
            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('File size exceeds 2MB limit. Please choose a smaller image.');
                this.value = '';
                return;
            }
            
            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                alert('Invalid file type. Please upload JPEG, PNG, JPG, or GIF images only.');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `<img src="${e.target.result}" alt="Avatar">`;
            }
            reader.readAsDataURL(file);
        }
    });

    // Auto-dismiss alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const closeButton = alert.querySelector('.btn-close');
                if (closeButton) {
                    closeButton.click();
                }
            });
        }, 5000);
    });

    // Character counter for bio
    document.addEventListener('DOMContentLoaded', function() {
        const bioTextarea = document.getElementById('bio');
        if (bioTextarea) {
            const maxLength = 1000;
            const counter = document.createElement('small');
            counter.className = 'text-muted float-end mt-1';
            counter.style.fontSize = '12px';
            bioTextarea.parentNode.appendChild(counter);
            
            function updateCounter() {
                const remaining = maxLength - bioTextarea.value.length;
                counter.textContent = `${remaining} characters remaining`;
                if (remaining < 0) {
                    counter.style.color = '#ef4444';
                } else {
                    counter.style.color = '#6b7280';
                }
            }
            
            bioTextarea.addEventListener('input', updateCounter);
            updateCounter();
        }
    });
</script>
@endpush