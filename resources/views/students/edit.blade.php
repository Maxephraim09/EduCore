@extends('layouts.app')

@section('title', 'Edit Student')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
    <li class="breadcrumb-item active">Edit Student</li>
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-edit me-2"></i>Edit Student: {{ $student->full_name }}
            </h5>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form action="{{ route('students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Same tabs as create form, but with old values filled -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#personal">
                            <i class="fas fa-user"></i> Personal Info
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#parents">
                            <i class="fas fa-users"></i> Parent Info
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#academic">
                            <i class="fas fa-book"></i> Academic Info
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#medical">
                            <i class="fas fa-notes-medical"></i> Medical Info
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#other">
                            <i class="fas fa-ellipsis-h"></i> Other Info
                        </a>
                    </li>
                </ul>
                
                <div class="tab-content mt-3">
                    <!-- Personal Information Tab -->
                    <div class="tab-pane fade show active" id="personal">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>Profile Photo</label>
                                <input type="file" name="photo" class="form-control" accept="image/*">
                                @if($student->photo)
                                    <div class="mt-2">
                                        <img src="{{ Storage::url($student->photo) }}" style="width: 50px; height: 50px; object-fit: cover;">
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Admission Number *</label>
                                <input type="text" name="admission_number" class="form-control" required value="{{ old('admission_number', $student->admission_number) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Roll Number</label>
                                <input type="text" name="roll_number" class="form-control" value="{{ old('roll_number', $student->roll_number) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>First Name *</label>
                                <input type="text" name="first_name" class="form-control" required value="{{ old('first_name', $student->first_name) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Middle Name</label>
                                <input type="text" name="middle_name" class="form-control" value="{{ old('middle_name', $student->middle_name) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Last Name *</label>
                                <input type="text" name="last_name" class="form-control" required value="{{ old('last_name', $student->last_name) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Date of Birth *</label>
                                <input type="date" name="date_of_birth" class="form-control" required value="{{ old('date_of_birth', $student->date_of_birth) }}">
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Gender *</label>
                                <select name="gender" class="form-control" required>
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ old('gender', $student->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $student->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender', $student->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Blood Group</label>
                                <select name="blood_group" class="form-control">
                                    <option value="">Select Blood Group</option>
                                    @foreach(['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'] as $bg)
                                        <option value="{{ $bg }}" {{ old('blood_group', $student->blood_group) == $bg ? 'selected' : '' }}>{{ $bg }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label>Religion</label>
                                <input type="text" name="religion" class="form-control" value="{{ old('religion', $student->religion) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Nationality</label>
                                <input type="text" name="nationality" class="form-control" value="{{ old('nationality', $student->nationality ?? 'Nigerian') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>State of Origin</label>
                                <input type="text" name="state_of_origin" class="form-control" value="{{ old('state_of_origin', $student->state_of_origin) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Local Government</label>
                                <input type="text" name="local_government" class="form-control" value="{{ old('local_government', $student->local_government) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', $student->email) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Phone Number</label>
                                <input type="text" name="phone_number" class="form-control" value="{{ old('phone_number', $student->phone_number) }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label>Home Address</label>
                                <textarea name="home_address" class="form-control" rows="2">{{ old('home_address', $student->home_address) }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Parent Information Tab -->
                    <div class="tab-pane fade" id="parents">
                        <h6 class="mb-3">Father's Information</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Father's Name *</label>
                                <input type="text" name="father_name" class="form-control" required value="{{ old('father_name', $student->father_name) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Father's Phone *</label>
                                <input type="text" name="father_phone" class="form-control" required value="{{ old('father_phone', $student->father_phone) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Father's Email</label>
                                <input type="email" name="father_email" class="form-control" value="{{ old('father_email', $student->father_email) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Father's Occupation</label>
                                <input type="text" name="father_occupation" class="form-control" value="{{ old('father_occupation', $student->father_occupation) }}">
                            </div>
                        </div>
                        
                        <h6 class="mb-3 mt-3">Mother's Information</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Mother's Name *</label>
                                <input type="text" name="mother_name" class="form-control" required value="{{ old('mother_name', $student->mother_name) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Mother's Phone *</label>
                                <input type="text" name="mother_phone" class="form-control" required value="{{ old('mother_phone', $student->mother_phone) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Mother's Email</label>
                                <input type="email" name="mother_email" class="form-control" value="{{ old('mother_email', $student->mother_email) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Mother's Occupation</label>
                                <input type="text" name="mother_occupation" class="form-control" value="{{ old('mother_occupation', $student->mother_occupation) }}">
                            </div>
                        </div>
                        
                        <h6 class="mb-3 mt-3">Guardian Information</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Guardian Name</label>
                                <input type="text" name="guardian_name" class="form-control" value="{{ old('guardian_name', $student->guardian_name) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Guardian Phone</label>
                                <input type="text" name="guardian_phone" class="form-control" value="{{ old('guardian_phone', $student->guardian_phone) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Guardian Email</label>
                                <input type="email" name="guardian_email" class="form-control" value="{{ old('guardian_email', $student->guardian_email) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Relationship</label>
                                <input type="text" name="guardian_relationship" class="form-control" value="{{ old('guardian_relationship', $student->guardian_relationship) }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label>Guardian Address</label>
                                <textarea name="guardian_address" class="form-control" rows="2">{{ old('guardian_address', $student->guardian_address) }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Academic Information Tab -->
                    <div class="tab-pane fade" id="academic">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>Class *</label>
                                <select name="class" class="form-control" required>
                                    <option value="">Select Class</option>
                                    @foreach(['JSS 1', 'JSS 2', 'JSS 3', 'SSS 1', 'SSS 2', 'SSS 3'] as $class)
                                        <option value="{{ $class }}" {{ old('class', $student->class) == $class ? 'selected' : '' }}>{{ $class }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Section</label>
                                <input type="text" name="section" class="form-control" value="{{ old('section', $student->section) }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>House</label>
                                <select name="house" class="form-control">
                                    <option value="">Select House</option>
                                    @foreach(['Red', 'Blue', 'Green', 'Yellow'] as $house)
                                        <option value="{{ $house }}" {{ old('house', $student->house) == $house ? 'selected' : '' }}>{{ $house }} House</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Admission Date</label>
                                <input type="date" name="admission_date" class="form-control" value="{{ old('admission_date', $student->admission_date) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Academic Year</label>
                                <input type="text" name="academic_year" class="form-control" value="{{ old('academic_year', $student->academic_year) }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label>Previous School</label>
                                <input type="text" name="previous_school" class="form-control" value="{{ old('previous_school', $student->previous_school) }}">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Medical Information Tab -->
                    <div class="tab-pane fade" id="medical">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Emergency Contact Name</label>
                                <input type="text" name="emergency_contact_name" class="form-control" value="{{ old('emergency_contact_name', $student->emergency_contact_name) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Emergency Contact Phone</label>
                                <input type="text" name="emergency_contact_phone" class="form-control" value="{{ old('emergency_contact_phone', $student->emergency_contact_phone) }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label>Allergies</label>
                                <textarea name="allergies" class="form-control" rows="2">{{ old('allergies', $student->allergies) }}</textarea>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label>Medical Conditions</label>
                                <textarea name="medical_conditions" class="form-control" rows="2">{{ old('medical_conditions', $student->medical_conditions) }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Other Information Tab -->
                    <div class="tab-pane fade" id="other">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Total Fees (Annual)</label>
                                <input type="number" step="0.01" name="total_fees" class="form-control" value="{{ old('total_fees', $student->total_fees) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Fee Concession</label>
                                <input type="number" step="0.01" name="fee_concession" class="form-control" value="{{ old('fee_concession', $student->fee_concession) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Transport Route</label>
                                <input type="text" name="transport_route" class="form-control" value="{{ old('transport_route', $student->transport_route) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Transport Fee</label>
                                <input type="number" step="0.01" name="transport_fee" class="form-control" value="{{ old('transport_fee', $student->transport_fee) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check">
                                    <input type="checkbox" name="is_hosteller" value="1" class="form-check-input" id="is_hosteller" {{ old('is_hosteller', $student->is_hosteller) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_hosteller">Is Hosteller?</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Status</label>
                                <select name="is_active" class="form-control">
                                    <option value="1" {{ old('is_active', $student->is_active) ? 'selected' : '' }}>Active</option>
                                    <option value="0" {{ old('is_active', $student->is_active) ? '' : 'selected' }}>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label>Remarks</label>
                                <textarea name="remarks" class="form-control" rows="2">{{ old('remarks', $student->remarks) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Student
                    </button>
                    <a href="{{ route('students.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection