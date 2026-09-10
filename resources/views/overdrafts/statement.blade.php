@extends('layouts.app')

@section('title', 'Overdraft Statement')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('staff-overdrafts.index') }}">Overdrafts</a></li>
    <li class="breadcrumb-item"><a href="{{ route('overdrafts.show', $overdraft->id) }}">Overdraft Details</a></li>
    <li class="breadcrumb-item active">Statement</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Overdraft Statement</h5>
            <div>
                <button onclick="window.print()" class="btn btn-secondary btn-sm">
                    <i class="fas fa-print"></i> Print
                </button>
                <a href="{{ route('overdrafts.show', $overdraft->id) }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <!-- Header -->
            <div class="text-center mb-4">
                <h3>{{ getSchoolName() }}</h3>
                @if(getSchoolTagline())
                    <p class="mb-1">{{ getSchoolTagline() }}</p>
                @endif
                @if(getSchoolAddress())
                    <p class="mb-1">{{ getSchoolAddress() }}</p>
                @endif
                <h5>Overdraft Account Statement</h5>
                <p>Statement Period: {{ date('d M Y', strtotime($overdraft->sanction_date)) }} - {{ date('d M Y') }}</p>
            </div>
            
            <!-- Account Summary -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6>Account Holder Information</h6>
                            <table class="table table-sm">
                                <tr>
                                    <th width="35%">Name:</th>
                                    <td>{{ $overdraft->employee->full_name }}</td>
                                </tr>
                                <tr>
                                    <th>Employee ID:</th>
                                    <td>{{ $overdraft->employee->employee_id }}</td>
                                </tr>
                                <tr>
                                    <th>Department:</th>
                                    <td>{{ $overdraft->employee->department }}</td>
                                </tr>
                                <tr>
                                    <th>Position:</th>
                                    <td>{{ $overdraft->employee->position }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h6>Overdraft Summary</h6>
                            <table class="table table-sm text-white">
                                <tr>
                                    <th width="50%">Overdraft Limit:</th>
                                    <td class="text-end">₦{{ number_format($overdraft->limit_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Total Withdrawn:</th>
                                    <td class="text-end">₦{{ number_format($overdraft->used_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Available Limit:</th>
                                    <td class="text-end">₦{{ number_format($overdraft->available_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Interest Rate:</th>
                                    <td class="text-end">{{ $overdraft->interest_rate }}% p.a.</td>
                                </tr>
                                <tr>
                                    <th>Expiry Date:</th>
                                    <td class="text-end">{{ date('d M Y', strtotime($overdraft->expiry_date)) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Transaction History -->
            <h6>Transaction History</h6>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Transaction ID</th>
                            <th>Description</th>
                            <th>Type</th>
                            <th>Amount (₦)</th>
                            <th>Balance (₦)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $runningBalance = 0;
                        @endphp
                        
                        <!-- Opening Balance -->
                        <tr class="table-info">
                            <td>{{ date('d M Y', strtotime($overdraft->sanction_date)) }}</td>
                            <td>-</td>
                            <td>Overdraft Facility Opened</td>
                            <td>Credit</td>
                            <td>{{ number_format($overdraft->limit_amount, 2) }}</td>
                            <td>{{ number_format($overdraft->limit_amount, 2) }}</td>
                        </tr>
                        
                        @foreach($transactions->sortBy('transaction_date') as $transaction)
                            @php
                                if ($transaction->type == 'withdrawal') {
                                    $runningBalance = $runningBalance - $transaction->amount;
                                    $typeLabel = 'Debit';
                                    $typeClass = 'text-danger';
                                } else {
                                    $runningBalance = $runningBalance + $transaction->amount;
                                    $typeLabel = 'Credit';
                                    $typeClass = 'text-success';
                                }
                            @endphp
                            <tr>
                                <td>{{ date('d M Y', strtotime($transaction->transaction_date)) }}</td>
                                <td>{{ $transaction->reference_number ?? 'TXN-' . $loop->iteration }}</td>
                                <td>{{ $transaction->description }}</td>
                                <td><span class="{{ $typeClass }}">{{ $typeLabel }}</span></td>
                                <td><span class="{{ $typeClass }}">₦{{ number_format($transaction->amount, 2) }}</span></td>
                                <td>₦{{ number_format($overdraft->limit_amount + $runningBalance, 2) }}</td>
                            </tr>
                        @endforeach
                        
                        <!-- Closing Balance -->
                        <tr class="table-success">
                            <td><strong>{{ date('d M Y') }}</strong></td>
                            <td>-</td>
                            <td><strong>Closing Balance</strong></td>
                            <td>-</td>
                            <td>-</td>
                            <td><strong>₦{{ number_format($overdraft->available_amount, 2) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Summary -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <strong>Summary:</strong><br>
                        Total Withdrawals: ₦{{ number_format($transactions->where('type', 'withdrawal')->sum('amount'), 2) }}<br>
                        Total Repayments: ₦{{ number_format($transactions->where('type', 'repayment')->sum('amount'), 2) }}<br>
                        Outstanding Balance: ₦{{ number_format($overdraft->used_amount, 2) }}
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="text-center mt-4">
                <small class="text-muted">
                    This is a computer-generated statement. No signature required.<br>
                    Generated on: {{ now()->format('d M Y h:i A') }}
                </small>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .sidebar, .top-header, .footer, .btn, .card-header .btn {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
        }
        .card {
            border: none !important;
        }
        .btn {
            display: none !important;
        }
    }
</style>
@endsection
