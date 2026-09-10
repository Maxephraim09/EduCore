@extends('layouts.app')

@section('title', 'Student Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('students.index') }}">Students</a></li>
    <li class="breadcrumb-item active">{{ $student->full_name }}</li>
@endsection

@section('content')
<div class="fade-in">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-user-graduate me-2"></i>Student Details
                </h5>
                <div>
                    <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('students.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Back
                    </a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <!-- Student Header -->
            <div class="row mb-4">
                <div class="col-md-2 text-center">
                    @if($student->photo)
                        <img src="{{ Storage::url($student->photo) }}" alt="{{ $student->full_name }}" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px; font-size: 3rem;">
                            {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div class="col-md-10">
                    <div class="row">
                        <div class="col-md-4">
                            <strong>Admission Number:</strong><br>
                            {{ $student->admission_number }}
                        </div>
                        <div class="col-md-4">
                            <strong>Roll Number:</strong><br>
                            {{ $student->roll_number ?? 'N/A' }}
                        </div>
                        <div class="col-md-4">
                            <strong>Class:</strong><br>
                            {{ $student->class }} {{ $student->section }}
                        </div>
                        <div class="col-md-4 mt-2">
                            <strong>House:</strong><br>
                            {{ $student->house ?? 'N/A' }}
                        </div>
                        <div class="col-md-4 mt-2">
                            <strong>Status:</strong><br>
                            @if($student->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </div>
                        <div class="col-md-4 mt-2">
                            <strong>Admission Date:</strong><br>
                            {{ date('d M Y', strtotime($student->admission_date)) }}
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Tabs for different sections -->
            <ul class="nav nav-tabs" id="studentTab" role="tablist">
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
                    <a class="nav-link" data-bs-toggle="tab" href="#fees">
                        <i class="fas fa-money-bill"></i> Fee Details
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#medical">
                        <i class="fas fa-notes-medical"></i> Medical Info
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#transport">
                        <i class="fas fa-bus"></i> Transport
                    </a>
                </li>
            </ul>
            
            <div class="tab-content mt-3">
                <!-- Personal Information -->
                <div class="tab-pane fade show active" id="personal">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Full Name</th>
                                    <td>{{ $student->full_name }}</td>
                                </tr>
                                <tr>
                                    <th>Date of Birth</th>
                                    <td>{{ date('d M Y', strtotime($student->date_of_birth)) }} (Age: {{ \Carbon\Carbon::parse($student->date_of_birth)->age }} years)</td>
                                </tr>
                                <tr>
                                    <th>Gender</th>
                                    <td>{{ $student->gender ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Blood Group</th>
                                    <td>{{ $student->blood_group ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Religion</th>
                                    <td>{{ $student->religion ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Nationality</th>
                                    <td>{{ $student->nationality ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>State of Origin</th>
                                    <td>{{ $student->state_of_origin ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Local Government</th>
                                    <td>{{ $student->local_government ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Email</th>
                                    <td>{{ $student->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Phone Number</th>
                                    <td>{{ $student->phone_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Home Address</th>
                                    <td>{{ $student->home_address ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Parent Information -->
                <div class="tab-pane fade" id="parents">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Father's Information</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Name</th>
                                    <td>{{ $student->father_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $student->father_phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $student->father_email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Occupation</th>
                                    <td>{{ $student->father_occupation ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Mother's Information</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Name</th>
                                    <td>{{ $student->mother_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $student->mother_phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $student->mother_email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Occupation</th>
                                    <td>{{ $student->mother_occupation ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @if($student->guardian_name)
                    <div class="row mt-3">
                        <div class="col-12">
                            <h6>Guardian Information</h6>
                            <table class="table table-bordered">
                                <tr>
                                    <th width="20%">Name</th>
                                    <td>{{ $student->guardian_name }}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td>{{ $student->guardian_phone }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $student->guardian_email }}</td>
                                </tr>
                                <tr>
                                    <th>Relationship</th>
                                    <td>{{ $student->guardian_relationship }}</td>
                                </tr>
                                <tr>
                                    <th>Address</th>
                                    <td>{{ $student->guardian_address }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Academic Information -->
                <div class="tab-pane fade" id="academic">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Class</th>
                                    <td>{{ $student->class }}</td>
                                </tr>
                                <tr>
                                    <th>Section</th>
                                    <td>{{ $student->section ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Roll Number</th>
                                    <td>{{ $student->roll_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>House</th>
                                    <td>{{ $student->house ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Admission Date</th>
                                    <td>{{ date('d M Y', strtotime($student->admission_date)) }}</td>
                                </tr>
                                <tr>
                                    <th>Academic Year</th>
                                    <td>{{ $student->academic_year ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Previous School</th>
                                    <td>{{ $student->previous_school ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Fee Details -->
                <div class="tab-pane fade" id="fees">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Total Fees (Annual)</th>
                                    <td>₦{{ number_format($student->total_fees, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Fee Concession</th>
                                    <td>₦{{ number_format($student->fee_concession ?? 0, 2) }}</td>
                                </tr>
                                <tr class="table-success">
                                    <th>Net Fees</th>
                                    <td>₦{{ number_format($student->total_fees - ($student->fee_concession ?? 0), 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Paid Amount</th>
                                    <td>₦{{ number_format($student->paid_fees, 2) }}</td>
                                </tr>
                                <tr class="table-danger">
                                    <th>Due Amount</th>
                                    <td>₦{{ number_format($student->due_fees, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($feePayments->count() > 0)
                    <h6 class="mt-3">Payment History</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Receipt No</th>
                                    <th>Date</th>
                                    <th>Fee Type</th>
                                    <th>Term</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($feePayments as $payment)
                                <tr>
                                    <td>{{ $payment->receipt_number }}</td>
                                    <td>{{ date('d M Y', strtotime($payment->payment_date)) }}</td>
                                    <td>{{ $payment->fee_type }}</td>
                                    <td>{{ $payment->term }}</td>
                                    <td>₦{{ number_format($payment->amount, 2) }}</td>
                                    <td>{{ $payment->payment_method }}</td>
                                    <td>
                                        @if($payment->status == 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
                
                <!-- Medical Information -->
                <div class="tab-pane fade" id="medical">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Blood Group</th>
                                    <td>{{ $student->blood_group ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Emergency Contact</th>
                                    <td>{{ $student->emergency_contact_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Emergency Phone</th>
                                    <td>{{ $student->emergency_contact_phone ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Allergies</th>
                                    <td>{{ $student->allergies ?? 'None' }}</td>
                                </tr>
                                <tr>
                                    <th>Medical Conditions</th>
                                    <td>{{ $student->medical_conditions ?? 'None' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <!-- Transport Information -->
                <div class="tab-pane fade" id="transport">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Transport Route</th>
                                    <td>{{ $student->transport_route ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Transport Fee</th>
                                    <td>₦{{ number_format($student->transport_fee ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Is Hosteller?</th>
                                    <td>{{ $student->is_hosteller ? 'Yes' : 'No' }}</td>
                                </tr>
                                @if($student->is_hosteller)
                                <tr>
                                    <th>Hostel Name</th>
                                    <td>{{ $student->hostel_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Room Number</th>
                                    <td>{{ $student->room_number ?? 'N/A' }}</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Remarks -->
            @if($student->remarks)
            <div class="alert alert-info mt-3">
                <strong>Remarks:</strong> {{ $student->remarks }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection