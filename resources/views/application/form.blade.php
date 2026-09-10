@extends('layouts.portal-dashboard')

@section('page_title', 'Application Form')

@section('title', 'Apply for Admission')

@push('styles')
<style>
    :root {
        --primary: #5b6ff5;
        --primary-strong: #3f51f5;
        --surface: #f8fbff;
        --surface-alt: #ffffff;
        --border: #e2e8f0;
        --text: #111827;
        --muted: #64748b;
        --success: #22c55e;
        --danger: #ef4444;
    }

    body {
        background: linear-gradient(180deg, rgba(91, 111, 245, 0.12) 0%, rgba(248, 250, 252, 1) 100%);
    }

    .application-form-page {
        min-height: 100vh;
        padding: 60px 0;
    }

    .application-form-card {
        background: rgba(255, 255, 255, 0.98);
        border-radius: 32px;
        box-shadow: 0 32px 80px rgba(15, 23, 42, 0.12);
        overflow: hidden;
    }

    .application-form-hero {
        padding: 34px 30px;
        background: linear-gradient(135deg, rgba(91, 111, 245, 0.14), rgba(118, 75, 162, 0.11));
        border-bottom: 1px solid rgba(99, 102, 241, 0.16);
        text-align: center;
    }

    .application-form-hero h1 {
        font-size: clamp(2.4rem, 3vw, 3.4rem);
        font-weight: 800;
        color: var(--text);
        letter-spacing: -0.04em;
        margin-bottom: 0.9rem;
    }

    .application-form-hero p {
        max-width: 760px;
        margin: 0 auto;
        color: var(--muted);
        line-height: 1.9;
    }

    .application-form {
        padding: 28px;
        background: #ffffff;
        border-radius: 32px;
        border: 1px solid rgba(226, 232, 240, 0.95);
        box-shadow: 0 28px 80px rgba(15, 23, 42, 0.12);
    }

    .form-section {
        margin-bottom: 24px;
        padding: 24px;
        border-radius: 28px;
        background: #f8fafc;
        border: 1px solid rgba(226, 232, 240, 0.95);
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.04);
    }

    .section-title {
        display: inline-flex;
        align-items: center;
        gap: 0.9rem;
        margin-bottom: 24px;
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text);
    }

    .section-icon {
        width: 42px;
        height: 42px;
        display: grid;
        place-items: center;
        border-radius: 14px;
        background: rgba(91, 111, 245, 0.16);
        color: var(--primary);
    }

    .form-label {
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.5rem;
    }

    .form-control,
    .form-select {
        border-radius: 14px;
        border: 1px solid var(--border);
        padding: 12px 14px;
        min-height: 44px;
        font-size: 0.95rem;
        background: var(--surface-alt);
        transition: border-color 0.25s ease, box-shadow 0.25s ease;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 8px rgba(91, 111, 245, 0.12);
    }

    .form-control::placeholder {
        color: #94a3b8;
    }

    .input-group-text {
        border-radius: 16px 0 0 16px;
        border: 1px solid var(--border);
        background: #f8fafc;
        color: #475569;
    }

    .form-check-input {
        width: 1.25rem;
        height: 1.25rem;
        border-radius: 0.35rem;
    }

    .form-check-label {
        color: #475569;
        margin-left: 0.5rem;
    }

    .file-upload-wrapper {
        border: 2px dashed rgba(99, 102, 241, 0.28);
        border-radius: 20px;
        padding: 28px 22px;
        text-align: center;
        cursor: pointer;
        transition: all 0.25s ease;
        background: #ffffff;
    }

    .file-upload-wrapper:hover {
        border-color: var(--primary);
        background: rgba(91, 111, 245, 0.06);
        transform: translateY(-1px);
    }

    .file-icon {
        font-size: 42px;
        color: var(--primary);
        margin-bottom: 12px;
    }

    .file-text {
        color: #475569;
        line-height: 1.7;
    }

    .file-text strong {
        color: var(--text);
    }

    .form-hint {
        font-size: 0.9rem;
        color: var(--muted);
        margin-top: 0.35rem;
    }

    .btn-submit {
        border-radius: 16px;
        padding: 14px 36px;
        font-weight: 700;
        background: #ffffff;
        color: var(--primary-strong);
        border: 2px solid rgba(91, 111, 245, 0.95);
        box-shadow: 0 16px 30px rgba(91, 111, 245, 0.14);
        transition: transform 0.25s ease, background-color 0.25s ease, color 0.25s ease;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        background: rgba(91, 111, 245, 0.08);
        color: var(--primary);
        box-shadow: 0 20px 36px rgba(91, 111, 245, 0.18);
    }

    .btn-submit:disabled {
        opacity: 0.78;
        cursor: not-allowed;
        transform: none;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 24px 48px rgba(91, 111, 245, 0.28);
    }

    .btn-submit:disabled {
        opacity: 0.78;
        cursor: not-allowed;
        transform: none;
    }

    .step-indicator {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 32px;
    }

    .step-pill {
        display: flex;
        align-items: center;
        gap: 0.8rem;
        padding: 14px 16px;
        border-radius: 18px;
        border: 1px solid rgba(226, 232, 240, 0.95);
        background: #f8fafc;
        color: #64748b;
        font-weight: 700;
        box-shadow: inset 0 1px 3px rgba(15, 23, 42, 0.04);
        min-height: 56px;
    }

    .step-pill.active {
        background: #ffffff;
        color: var(--text);
        border-color: rgba(91, 111, 245, 0.2);
        box-shadow: 0 8px 24px rgba(91, 111, 245, 0.1);
    }

    .step-number {
        width: 38px;
        height: 38px;
        display: grid;
        place-items: center;
        border-radius: 50%;
        background: #eef2ff;
        color: #4338ca;
        font-weight: 800;
        box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.08);
    }

    .step-pill.completed .step-number {
        background: rgba(34, 197, 94, 0.16);
        color: #16a34a;
    }

    .step-pill span {
        white-space: nowrap;
    }

    .modal-content {
        border-radius: 24px;
        overflow: hidden;
    }

    .modal-header {
        border-bottom: none;
    }

    .modal-body h6 {
        margin-top: 1.25rem;
        font-weight: 700;
        color: var(--text);
    }

    .modal-body p {
        color: #475569;
        line-height: 1.8;
    }

    @media (max-width: 992px) {
        .application-form {
            padding: 30px 22px;
        }

        .step-indicator {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="application-form-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10">
                <div class="application-form-card">
                    <div class="application-form-hero">
                        <p class="text-uppercase text-primary fw-bold mb-3" style="letter-spacing:0.18em;">Admission Portal</p>
                        <h1>Submit Your Admission Application</h1>
                        <p>Fill in your details, upload your documents, and complete the registration process with a modern, intuitive application experience.</p>
                    </div>

                    <div class="application-form">
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="step-indicator">
                            <div class="step-pill active">
                                <span class="step-number">1</span>
                                <span>Personal Info</span>
                            </div>
                            <div class="step-pill">
                                <span class="step-number">2</span>
                                <span>Academic Info</span>
                            </div>
                            <div class="step-pill">
                                <span class="step-number">3</span>
                                <span>Documents</span>
                            </div>
                            <div class="step-pill">
                                <span class="step-number">4</span>
                                <span>Finalize</span>
                            </div>
                        </div>

                        <form action="{{ route('application.submit') }}" method="POST" enctype="multipart/form-data" id="applicationForm">
                            @csrf

                            @php
                                $nameParts = [];
                                if (!empty($applicant->name)) {
                                    $nameParts = explode(' ', trim($applicant->name), 2);
                                }
                                $prefillFirstName = old('first_name', $nameParts[0] ?? '');
                                $prefillLastName = old('last_name', $nameParts[1] ?? '');
                                $prefillEmail = old('email', optional($applicant)->email);
                                $prefillPhone = old('phone', optional($applicant)->phone);
                                $prefillCategoryId = old('category_id', $selectedCategoryId ?? '');
                                $prefillClassId = old('class_id', $selectedClassId ?? '');
                            @endphp

                            <div class="form-section">
                                <div class="section-title"><span class="section-icon"><i class="fas fa-user"></i></span>Personal Information</div>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" id="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ $prefillFirstName }}" required>
                                        @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="middle_name" class="form-label">Middle Name</label>
                                        <input type="text" name="middle_name" id="middle_name" class="form-control @error('middle_name') is-invalid @enderror" value="{{ old('middle_name') }}">
                                        @error('middle_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" id="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ $prefillLastName }}" required>
                                        @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ $prefillEmail }}" required>
                                        </div>
                                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone Number <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                            <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ $prefillPhone }}" required>
                                        </div>
                                        @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-md-4">
                                        <label for="date_of_birth" class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                        <input type="date" name="date_of_birth" id="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ old('date_of_birth') }}" required>
                                        @error('date_of_birth') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                        <select name="gender" id="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                            <option value="">Choose gender</option>
                                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                                        </select>
                                        @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="blood_group" class="form-label">Blood Group</label>
                                        <select name="blood_group" id="blood_group" class="form-select @error('blood_group') is-invalid @enderror">
                                            <option value="">Select blood group</option>
                                            <option value="A+" {{ old('blood_group') == 'A+' ? 'selected' : '' }}>A+</option>
                                            <option value="A-" {{ old('blood_group') == 'A-' ? 'selected' : '' }}>A-</option>
                                            <option value="B+" {{ old('blood_group') == 'B+' ? 'selected' : '' }}>B+</option>
                                            <option value="B-" {{ old('blood_group') == 'B-' ? 'selected' : '' }}>B-</option>
                                            <option value="AB+" {{ old('blood_group') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                            <option value="AB-" {{ old('blood_group') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                            <option value="O+" {{ old('blood_group') == 'O+' ? 'selected' : '' }}>O+</option>
                                            <option value="O-" {{ old('blood_group') == 'O-' ? 'selected' : '' }}>O-</option>
                                        </select>
                                        @error('blood_group') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>

                                    <div class="col-12">
                                        <label for="address" class="form-label">Home Address <span class="text-danger">*</span></label>
                                        <textarea name="address" id="address" rows="2" class="form-control @error('address') is-invalid @enderror" required>{{ old('address') }}</textarea>
                                        @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="section-title"><span class="section-icon"><i class="fas fa-users"></i></span>Parent / Guardian Information</div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="parent_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                        <input type="text" name="parent_name" id="parent_name" class="form-control @error('parent_name') is-invalid @enderror" value="{{ old('parent_name') }}" required>
                                        @error('parent_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="parent_phone" class="form-label">Phone <span class="text-danger">*</span></label>
                                        <input type="text" name="parent_phone" id="parent_phone" class="form-control @error('parent_phone') is-invalid @enderror" value="{{ old('parent_phone') }}" required>
                                        @error('parent_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="parent_email" class="form-label">Email Address</label>
                                        <input type="email" name="parent_email" id="parent_email" class="form-control @error('parent_email') is-invalid @enderror" value="{{ old('parent_email') }}">
                                        @error('parent_email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="parent_occupation" class="form-label">Occupation</label>
                                        <input type="text" name="parent_occupation" id="parent_occupation" class="form-control @error('parent_occupation') is-invalid @enderror" value="{{ old('parent_occupation') }}">
                                        @error('parent_occupation') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="section-title"><span class="section-icon"><i class="fas fa-graduation-cap"></i></span>Academic Information</div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="category_id" class="form-label">Class Category</label>
                                        <select name="category_id" id="category_id" class="form-select">
                                            <option value="">All categories</option>
                                            @if(!empty($categories))
                                                @foreach($categories as $cat)
                                                    <option value="{{ $cat->id }}" {{ $prefillCategoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="class_id" class="form-label">Applying for Class <span class="text-danger">*</span></label>
                                        <select name="class_id" id="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                                            <option value="">Select class</option>
                                            @foreach($classes as $class)
                                                <option value="{{ $class->id }}" data-category-id="{{ $class->class_category_id }}" {{ $prefillClassId == $class->id ? 'selected' : '' }}>{{ $class->full_class_name ?? $class->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('class_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Registration Fee</label>
                                        <div id="registrationFeeDisplay" class="p-3 rounded-3 border bg-white text-dark">Select a class to view the registration fee</div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="application_type" class="form-label">Application Type</label>
                                        <select name="application_type" id="application_type" class="form-select">
                                            <option value="new" {{ old('application_type') == 'new' ? 'selected' : '' }}>New Admission</option>
                                            <option value="transfer" {{ old('application_type') == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                            <option value="re-admission" {{ old('application_type') == 're-admission' ? 'selected' : '' }}>Re-admission</option>
                                        </select>
                                        @error('application_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="previous_school" class="form-label">Previous School</label>
                                        <input type="text" name="previous_school" id="previous_school" class="form-control @error('previous_school') is-invalid @enderror" value="{{ old('previous_school') }}">
                                        @error('previous_school') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label for="previous_class" class="form-label">Previous Class</label>
                                        <input type="text" name="previous_class" id="previous_class" class="form-control @error('previous_class') is-invalid @enderror" value="{{ old('previous_class') }}">
                                        @error('previous_class') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="section-title"><span class="section-icon"><i class="fas fa-file-upload"></i></span>Required Documents</div>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Birth Certificate <span class="text-danger">*</span></label>
                                        <div class="file-upload-wrapper" onclick="document.getElementById('birth_certificate').click()">
                                            <div class="file-icon"><i class="fas fa-file-pdf"></i></div>
                                            <div class="file-text"><strong>Click to upload</strong> or drag and drop<br><span class="text-muted">PDF, JPG, PNG (Max 2MB)</span></div>
                                        </div>
                                        <input type="file" name="birth_certificate" id="birth_certificate" accept=".pdf,.jpg,.jpeg,.png" class="d-none @error('birth_certificate') is-invalid @enderror" onchange="updateFileName(this, 'birth_certificate_label')">
                                        <div class="form-hint" id="birth_certificate_label">No file selected</div>
                                        @error('birth_certificate') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Passport Photo <span class="text-danger">*</span></label>
                                        <div class="file-upload-wrapper" onclick="document.getElementById('passport_photo').click()">
                                            <div class="file-icon"><i class="fas fa-image"></i></div>
                                            <div class="file-text"><strong>Click to upload</strong> or drag and drop<br><span class="text-muted">JPG, JPEG, PNG (Max 2MB)</span></div>
                                        </div>
                                        <input type="file" name="passport_photo" id="passport_photo" accept=".jpg,.jpeg,.png" class="d-none @error('passport_photo') is-invalid @enderror" onchange="updateFileName(this, 'passport_photo_label')">
                                        <div class="form-hint" id="passport_photo_label">No file selected</div>
                                        @error('passport_photo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">Last Report Card</label>
                                        <div class="file-upload-wrapper" onclick="document.getElementById('report_card').click()">
                                            <div class="file-icon"><i class="fas fa-file-alt"></i></div>
                                            <div class="file-text"><strong>Click to upload</strong> or drag and drop<br><span class="text-muted">PDF, JPG, PNG (Max 2MB)</span></div>
                                        </div>
                                        <input type="file" name="report_card" id="report_card" accept=".pdf,.jpg,.jpeg,.png" class="d-none @error('report_card') is-invalid @enderror" onchange="updateFileName(this, 'report_card_label')">
                                        <div class="form-hint" id="report_card_label">No file selected</div>
                                        @error('report_card') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-section">
                                <div class="row g-3 align-items-center">
                                    <div class="col-md-8">
                                        <div class="form-check">
                                            <input type="checkbox" name="terms" id="terms" class="form-check-input @error('terms') is-invalid @enderror" required>
                                            <label class="form-check-label" for="terms">I confirm that all information provided is accurate and I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms & Conditions</a>.</label>
                                        </div>
                                        @error('terms') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                                    </div>
                                    <div class="col-md-4 text-md-end">
                                        <button type="submit" class="btn btn-submit" id="submitBtn"><i class="fas fa-paper-plane me-2"></i>Submit Application</button>
                                    </div>
                                    <div class="col-12">
                                        <p class="text-muted mb-0">After submission, you will be redirected to payment. Application fee of â‚¦{{ number_format($applicationFee) }} applies.</p>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="termsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Terms & Conditions</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>1. Application Process</h6>
                <p>Provide accurate information. Incomplete or false details may delay your admission process.</p>
                <h6>2. Application Fee</h6>
                <p>A non-refundable application fee of â‚¦{{ number_format($applicationFee) }} is required to complete the application.</p>
                <h6>3. Document Verification</h6>
                <p>All uploaded documents will be verified before admission is confirmed.</p>
                <h6>4. Admission Decision</h6>
                <p>Admission is subject to school capacity and eligibility requirements.</p>
                <h6>5. Data Privacy</h6>
                <p>Your personal information is securely stored and used only for admission purposes.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">I Understand</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function updateFileName(input, labelId) {
        const label = document.getElementById(labelId);
        if (!label) return;
        if (input.files && input.files[0]) {
            label.textContent = input.files[0].name;
            label.style.color = '#16a34a';
        } else {
            label.textContent = 'No file selected';
            label.style.color = '#64748b';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('applicationForm');
        if (form) {
            form.addEventListener('submit', function() {
                const submitBtn = document.getElementById('submitBtn');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
                }
            });
        }

        const categorySelect = document.getElementById('category_id');
        const classSelect = document.getElementById('class_id');
        const regDisplay = document.getElementById('registrationFeeDisplay');

        const initialCategoryId = '{{ $prefillCategoryId ?? '' }}';
        const initialClassId = '{{ $prefillClassId ?? '' }}';

        if (categorySelect && classSelect) {
            const filterClassesByCategory = function(categoryId) {
                Array.from(classSelect.options).forEach(function(option) {
                    const catId = option.dataset.categoryId;
                    option.style.display = !categoryId || categoryId === '' || (catId && catId.toString() === categoryId.toString()) ? '' : 'none';
                });
            };

            categorySelect.addEventListener('change', function() {
                const selected = this.value;
                filterClassesByCategory(selected);
                if (classSelect.selectedOptions.length && classSelect.selectedOptions[0].style.display === 'none') {
                    classSelect.value = '';
                    updateRegistrationFee();
                }
            });

            if (initialCategoryId) {
                categorySelect.value = initialCategoryId;
                filterClassesByCategory(initialCategoryId);
            }
        }

        if (classSelect && regDisplay) {
            const regFees = @json($registrationFees ?? []);
            const updateRegistrationFee = function() {
                const val = classSelect.value;
                if (val && regFees[val]) {
                    regDisplay.innerHTML = '<strong>â‚¦' + Number(regFees[val]).toLocaleString() + '</strong>';
                } else {
                    regDisplay.innerHTML = 'Select a class to view the registration fee';
                }
            };
            classSelect.addEventListener('change', updateRegistrationFee);
            if (initialClassId) {
                classSelect.value = initialClassId;
            }
            updateRegistrationFee();
        }
    });
</script>
@endpush

