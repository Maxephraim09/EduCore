@extends('layouts.app')

@section('title', 'Salary Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('salaries.index') }}">Salaries</a></li>
    <li class="breadcrumb-item active">Salary Details</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Salary Details</h5>
            <div>
                <a href="{{ route('salaries.payslip', $salary->id) }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-download"></i> Download Payslip
                </a>
                <a href="{{ route('salaries.index') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Employee Information</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Employee Name</th>
                            <td>{{ $salary->employee->full_name }}</td>
                        </tr>
                        <tr>
                            <th>Employee ID</th>
                            <td>{{ $salary->employee->employee_id }}</td>
                        </tr>
                        <tr>
                            <th>Position</th>
                            <td>{{ $salary->employee->position }}</td>
                        </tr>
                        <tr>
                            <th>Department</th>
                            <td>{{ $salary->employee->department }}</td>
                        </tr>
                        <tr>
                            <th>Bank Account</th>
                            <td>{{ $salary->employee->bank_name }} - {{ $salary->employee->account_number }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Salary Information</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th width="40%">Month/Year</th>
                            <td>{{ $salary->month }} {{ $salary->year }}</td>
                        </tr>
                        <tr>
                            <th>Payment Date</th>
                            <td>{{ $salary->payment_date->format('d M Y') }}</td>
                        </tr>
                        <tr>
                            <th>Payment Method</th>
                            <td>{{ ucfirst($salary->payment_method) }}</td>
                        </tr>
                        <tr>
                            <th>Transaction ID</th>
                            <td>{{ $salary->transaction_id }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($salary->status == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <h6 class="mt-4">Salary Breakdown</h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6>Earnings</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>Basic Salary</td>
                                    <td class="text-end">₦{{ number_format($salary->basic_salary, 2) }}</td>
                                </tr>
                                <tr>
                                    <td>Allowances</td>
                                    <td class="text-end">₦{{ number_format($salary->allowances, 2) }}</td>
                                </tr>
                                <tr class="border-top">
                                    <td><strong>Gross Salary</strong></td>
                                    <td class="text-end"><strong>₦{{ number_format($salary->basic_salary + $salary->allowances, 2) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6>Deductions</h6>
                            <table class="table table-sm">
                                <tr>
                                    <td>Total Deductions</td>
                                    <td class="text-end">₦{{ number_format($salary->deductions, 2) }}</td>
                                </tr>
                                <tr class="border-top table-success">
                                    <td><strong>Net Salary</strong></td>
                                    <td class="text-end"><strong>₦{{ number_format($salary->net_salary, 2) }}</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($salary->remarks)
            <div class="alert alert-info mt-3">
                <strong>Remarks:</strong> {{ $salary->remarks }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection