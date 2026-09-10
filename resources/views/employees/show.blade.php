@extends('layouts.app')

@section('title', 'Employee Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employees</a></li>
    <li class="breadcrumb-item active">{{ $employee->full_name }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-user me-2"></i>Employee Details</h5>
            <div>
                <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Personal Information</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th width="35%">Employee ID</th>
                            <td>{{ $employee->employee_id }}</td>
                        </tr>
                        <tr>
                            <th>Full Name</th>
                            <td>{{ $employee->full_name }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td>{{ $employee->email }}</td>
                        </tr>
                        <tr>
                            <th>Phone</th>
                            <td>{{ $employee->phone }}</td>
                        </tr>
                        <tr>
                            <th>Address</th>
                            <td>{{ $employee->address }}</td>
                        </tr>
                        <tr>
                            <th>Joining Date</th>
                            <td>{{ date('d M Y', strtotime($employee->joining_date)) }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($employee->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Employment Information</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th width="35%">Position</th>
                            <td>{{ $employee->position }}</td>
                        </tr>
                        <tr>
                            <th>Department</th>
                            <td>{{ $employee->department }}</td>
                        </tr>
                        <tr>
                            <th>Base Salary</th>
                            <td>₦{{ number_format($employee->base_salary, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Allowances</th>
                            <td>₦{{ number_format($employee->allowances, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Gross Salary</th>
                            <td>₦{{ number_format($employee->base_salary + $employee->allowances, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Total Paid Salary</th>
                            <td>₦{{ number_format($totalPaidSalary, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Outstanding Loan</th>
                            <td>₦{{ number_format($totalLoanBalance, 2) }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <h6 class="mt-4">Bank Details</h6>
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <tr>
                            <th width="25%">Bank Name</th>
                            <td>{{ $employee->bank_name }}</td>
                            <th width="25%">Account Number</th>
                            <td>{{ $employee->account_number }}</td>
                            <th width="25%">IFSC Code</th>
                            <td>{{ $employee->ifsc_code }}</td>
                        </tr>
                        <tr>
                            <th>PAN Number</th>
                            <td>{{ $employee->pan_number ?? 'N/A' }}</td>
                            <th></th>
                            <td></td>
                            <th></th>
                            <td></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            @if($salaries->count() > 0)
            <h6 class="mt-4">Recent Salary History</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Month/Year</th>
                            <th>Gross Salary</th>
                            <th>Deductions</th>
                            <th>Net Salary</th>
                            <th>Payment Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salaries as $salary)
                        <tr>
                            <td>{{ $salary->month }} {{ $salary->year }}</td>
                            <td>₦{{ number_format($salary->basic_salary + $salary->allowances, 2) }}</td>
                            <td>₦{{ number_format($salary->deductions, 2) }}</td>
                            <td>₦{{ number_format($salary->net_salary, 2) }}</td>
                            <td>{{ $salary->payment_date->format('d M Y') }}</td>
                            <td>
                                @if($salary->status == 'paid')
                                    <span class="badge bg-success">Paid</span>
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
            
            @if($loans->count() > 0)
            <h6 class="mt-4">Loan History</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Amount</th>
                            <th>Monthly Installment</th>
                            <th>Paid Amount</th>
                            <th>Remaining</th>
                            <th>Status</th>
                            <th>Sanction Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loans as $loan)
                        <tr>
                            <td>₦{{ number_format($loan->amount, 2) }}</td>
                            <td>₦{{ number_format($loan->monthly_installment, 2) }}</td>
                            <td>₦{{ number_format($loan->paid_amount, 2) }}</td>
                            <td>₦{{ number_format($loan->remaining_amount, 2) }}</td>
                            <td>
                                @if($loan->status == 'active')
                                    <span class="badge bg-warning">Active</span>
                                @elseif($loan->status == 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @else
                                    <span class="badge bg-danger">Defaulted</span>
                                @endif
                            </td>
                            <td>{{ date('d M Y', strtotime($loan->sanction_date)) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection