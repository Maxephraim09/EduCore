@extends('layouts.app')

@section('title', 'Loan Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('staff-loans.index') }}">Staff Loans</a></li>
    <li class="breadcrumb-item active">Loan Details</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Loan Details</h5>
            <a href="{{ route('staff-loans.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Employee Information</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th width="35%">Employee Name</th>
                            <td>{{ $loan->employee->full_name }}</td>
                        </tr>
                        <tr>
                            <th>Employee ID</th>
                            <td>{{ $loan->employee->employee_id }}</td>
                        </tr>
                        <tr>
                            <th>Position</th>
                            <td>{{ $loan->employee->position }}</td>
                        </tr>
                        <tr>
                            <th>Department</th>
                            <td>{{ $loan->employee->department }}</td>
                        </tr>
                        <tr>
                            <th>Monthly Salary</th>
                            <td>₦{{ number_format($loan->employee->base_salary + $loan->employee->allowances, 2) }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Loan Information</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th width="35%">Loan Amount</th>
                            <td>₦{{ number_format($loan->amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Interest Rate</th>
                            <td>{{ $loan->interest_rate }}%</td>
                        </tr>
                        <tr>
                            <th>Tenure</th>
                            <td>{{ $loan->tenure_months }} months</td>
                        </tr>
                        <tr>
                            <th>Monthly Installment</th>
                            <td>₦{{ number_format($loan->monthly_installment, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Total Payable</th>
                            <td>₦{{ number_format($loan->total_payable, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Sanction Date</th>
                            <td>{{ date('d M Y', strtotime($loan->sanction_date)) }}</td>
                        </tr>
                        <tr>
                            <th>First Installment</th>
                            <td>{{ date('d M Y', strtotime($loan->first_installment_date)) }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($loan->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($loan->status == 'completed')
                                    <span class="badge bg-info">Completed</span>
                                @else
                                    <span class="badge bg-danger">Defaulted</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-6">
                    <h6>Payment Summary</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th width="35%">Amount Paid</th>
                            <td>₦{{ number_format($loan->paid_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Remaining Balance</th>
                            <td><strong class="text-danger">₦{{ number_format($loan->remaining_amount, 2) }}</strong></td>
                        </tr>
                        <tr>
                            <th>Payment Progress</th>
                            <td>
                                @php
                                    $percentage = ($loan->paid_amount / $loan->total_payable) * 100;
                                @endphp
                                <div class="progress">
                                    <div class="progress-bar bg-success" role="progressbar" 
                                         style="width: {{ $percentage }}%" 
                                         aria-valuenow="{{ $percentage }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ number_format($percentage, 1) }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Loan Purpose</h6>
                    <div class="card bg-light">
                        <div class="card-body">
                            {{ $loan->purpose }}
                        </div>
                    </div>
                    
                    @if($loan->remarks)
                        <h6 class="mt-3">Remarks</h6>
                        <div class="alert alert-info">
                            {{ $loan->remarks }}
                        </div>
                    @endif
                </div>
            </div>
            
            <h6 class="mt-4">Payment Schedule</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Installment #</th>
                            <th>Due Date</th>
                            <th>Amount Due (₦)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($paymentSchedule as $schedule)
                        <tr>
                            <td>{{ $schedule['installment_no'] }}</td>
                            <td>{{ date('d M Y', strtotime($schedule['due_date'])) }}</td>
                            <td>₦{{ number_format($schedule['amount_due'], 2) }}</td>
                            <td>
                                @if($schedule['status'] == 'Paid')
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
        </div>
    </div>
</div>
@endsection