@extends('layouts.app')

@section('title', 'Edit Employee')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employees</a></li>
    <li class="breadcrumb-item active">Edit Employee</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Edit Employee</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('employees.update', $employee->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-4"><label class="form-label">Employee ID</label><input name="employee_id" class="form-control" value="{{ old('employee_id', $employee->employee_id) }}" required></div>
                <div class="col-md-4"><label class="form-label">First Name</label><input name="first_name" class="form-control" value="{{ old('first_name', $employee->first_name) }}" required></div>
                <div class="col-md-4"><label class="form-label">Last Name</label><input name="last_name" class="form-control" value="{{ old('last_name', $employee->last_name) }}" required></div>
                <div class="col-md-4"><label class="form-label">Email</label><input type="email" name="email" class="form-control" value="{{ old('email', $employee->email) }}" required></div>
                <div class="col-md-4"><label class="form-label">Phone</label><input name="phone" class="form-control" value="{{ old('phone', $employee->phone) }}" required></div>
                <div class="col-md-4"><label class="form-label">Joining Date</label><input type="date" name="joining_date" class="form-control" value="{{ old('joining_date', $employee->joining_date ? \Illuminate\Support\Carbon::parse($employee->joining_date)->format('Y-m-d') : '') }}" required></div>
                <div class="col-md-4">
                    <label class="form-label">Department</label>
                    <select name="department" class="form-select" required>
                        @foreach($departments as $department)
                            <option value="{{ $department }}" {{ old('department', $employee->department) == $department ? 'selected' : '' }}>{{ $department }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Position</label>
                    <select name="position" class="form-select" required>
                        @foreach($positions as $position)
                            <option value="{{ $position }}" {{ old('position', $employee->position) == $position ? 'selected' : '' }}>{{ $position }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4"><label class="form-label">Base Salary</label><input type="number" step="0.01" name="base_salary" class="form-control" value="{{ old('base_salary', $employee->base_salary) }}" required></div>
                <div class="col-md-4"><label class="form-label">Allowances</label><input type="number" step="0.01" name="allowances" class="form-control" value="{{ old('allowances', $employee->allowances) }}"></div>
                <div class="col-md-4"><label class="form-label">Bank Name</label><input name="bank_name" class="form-control" value="{{ old('bank_name', $employee->bank_name) }}" required></div>
                <div class="col-md-4"><label class="form-label">Account Number</label><input name="account_number" class="form-control" value="{{ old('account_number', $employee->account_number) }}" required></div>
                <div class="col-md-4"><label class="form-label">IFSC Code</label><input name="ifsc_code" class="form-control" value="{{ old('ifsc_code', $employee->ifsc_code) }}" required></div>
                <div class="col-md-4"><label class="form-label">PAN Number</label><input name="pan_number" class="form-control" value="{{ old('pan_number', $employee->pan_number) }}"></div>
                <div class="col-12"><label class="form-label">Address</label><textarea name="address" class="form-control" required>{{ old('address', $employee->address) }}</textarea></div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Employee</button>
                <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
