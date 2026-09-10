@extends('layouts.app')

@section('title', 'Process Salary')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('salaries.index') }}">Salaries</a></li>
    <li class="breadcrumb-item active">Process Salary</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-coins me-2"></i>Process Salary</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('salaries.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Employee</label>
                    <select name="employee_id" class="form-select" required>
                        <option value="">Select employee</option>
                        @foreach($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->first_name }} {{ $employee->last_name }} ({{ $employee->employee_id }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Month</label>
                    <select name="month" class="form-select" required>
                        @foreach($months as $month)
                            <option value="{{ $month }}">{{ $month }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Year</label>
                    <select name="year" class="form-select" required>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ $year == date('Y') ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Basic Salary</label>
                    <input type="number" step="0.01" name="basic_salary" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Allowances</label>
                    <input type="number" step="0.01" name="allowances" class="form-control" value="0" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Deductions</label>
                    <input type="number" step="0.01" name="deductions" class="form-control" value="0" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Net Salary</label>
                    <input type="number" step="0.01" name="net_salary" class="form-control" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Payment Method</label>
                    <select name="payment_method" class="form-select" required>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="cash">Cash</option>
                        <option value="cheque">Cheque</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Remarks</label>
                    <input type="text" name="remarks" class="form-control">
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Process Salary</button>
                <a href="{{ route('salaries.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
