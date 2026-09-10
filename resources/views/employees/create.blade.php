@extends('layouts.app')

@section('title', 'Add New Employee')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employees</a></li>
    <li class="breadcrumb-item active">Add Employee</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-user-plus me-2"></i>Add New Employee</h5>
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

            <form action="{{ route('employees.store') }}" method="POST">
                @csrf
                
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#personal">
                            <i class="fas fa-user"></i> Personal Info
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#employment">
                            <i class="fas fa-briefcase"></i> Employment Info
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#bank">
                            <i class="fas fa-university"></i> Bank Details
                        </a>
                    </li>
                </ul>
                
                <div class="tab-content mt-3">
                    <!-- Personal Information Tab -->
                    <div class="tab-pane fade show active" id="personal">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label>Employee ID *</label>
                                <input type="text" name="employee_id" class="form-control" required value="{{ old('employee_id') }}" placeholder="EMP001">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>First Name *</label>
                                <input type="text" name="first_name" class="form-control" required value="{{ old('first_name') }}">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label>Last Name *</label>
                                <input type="text" name="last_name" class="form-control" required value="{{ old('last_name') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Email *</label>
                                <input type="email" name="email" class="form-control" required value="{{ old('email') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Phone *</label>
                                <input type="text" name="phone" class="form-control" required value="{{ old('phone') }}">
                            </div>
                            <div class="col-md-12 mb-3">
                                <label>Address *</label>
                                <textarea name="address" class="form-control" rows="2" required>{{ old('address') }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>PAN Number (Optional)</label>
                                <input type="text" name="pan_number" class="form-control" value="{{ old('pan_number') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Joining Date *</label>
                                <input type="date" name="joining_date" class="form-control" required value="{{ old('joining_date', date('Y-m-d')) }}">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Employment Information Tab -->
                    <div class="tab-pane fade" id="employment">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Position *</label>
                                <select name="position" class="form-control" required>
                                    <option value="">Select Position</option>
                                    @foreach($positions as $position)
                                        <option value="{{ $position }}" {{ old('position') == $position ? 'selected' : '' }}>{{ $position }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Department *</label>
                                <select name="department" class="form-control" required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department }}" {{ old('department') == $department ? 'selected' : '' }}>{{ $department }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Base Salary (₦) *</label>
                                <input type="number" step="0.01" name="base_salary" class="form-control" required value="{{ old('base_salary') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Allowances (₦)</label>
                                <input type="number" step="0.01" name="allowances" class="form-control" value="{{ old('allowances', 0) }}">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bank Details Tab -->
                    <div class="tab-pane fade" id="bank">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Bank Name *</label>
                                <input type="text" name="bank_name" class="form-control" required value="{{ old('bank_name') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Account Number *</label>
                                <input type="text" name="account_number" class="form-control" required value="{{ old('account_number') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>IFSC Code *</label>
                                <input type="text" name="ifsc_code" class="form-control" required value="{{ old('ifsc_code') }}">
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Bank details are required for salary processing.
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Employee
                    </button>
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection