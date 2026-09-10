@extends('layouts.app')

@section('title', 'Payment Receipt')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-white text-center">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="logo">
                            @php $logo = getSchoolLogo() ?: asset('favicon.ico'); @endphp
                            <img src="{{ $logo }}" alt="logo" style="max-height:70px; max-width:140px; object-fit:contain">
                        </div>
                        <div class="text-center">
                            <h4 class="mb-0">{{ getSchoolName() }}</h4>
                            <small class="text-muted">{{ getSchoolTagline() }}</small>
                            <div class="d-block mt-1">
                                <small>{{ getSchoolAddress() }}</small><br>
                                <small>{{ getSchoolPhone() }} | {{ getSchoolEmail() }} | {{ getSchoolWebsite() }}</small>
                            </div>
                        </div>
                        <div class="qr">
                            @php $verifyUrl = route('fee-payments.verify', $payment->id); $qrSrc = 'https://chart.googleapis.com/chart?cht=qr&chs=200x200&chl=' . urlencode($verifyUrl) . '&chld=L|1'; @endphp
                            <img src="{{ $qrSrc }}" alt="QR" style="height:80px;width:80px">
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h5>{{ getSchoolName() }}</h5>
                        @if(getSchoolTagline())
                            <p class="mb-1">{{ getSchoolTagline() }}</p>
                        @endif
                        <p>Official Payment Receipt</p>
                        <hr>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <strong>Receipt Number:</strong><br>
                            <span class="h6">{{ $payment->receipt_number }}</span>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <strong>Date:</strong><br>
                            {{ optional($payment->payment_date)->format('d M Y h:i A') }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <strong>Transaction Reference:</strong><br>
                            {{ $payment->transaction_ref }}
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <strong>Payment Status:</strong><br>
                            @php
                                $status = strtolower($payment->payment_status ?? 'pending');
                                $badge = 'secondary';
                                if (in_array($status, ['success', 'completed', 'paid'])) $badge = 'success';
                                if (in_array($status, ['failed', 'error'])) $badge = 'danger';
                                if ($status === 'pending') $badge = 'warning';
                            @endphp
                            <span class="badge bg-{{ $badge }} text-white">{{ ucfirst($payment->payment_status ?? 'Pending') }}</span>
                        </div>
                    </div>

                    <hr>

                    <h6>Student Details</h6>
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <strong>Name:</strong><br>
                            {{ $payment->student->full_name }}
                        </div>
                        <div class="col-sm-6">
                            <strong>Admission Number:</strong><br>
                            {{ $payment->student->admission_number }}
                        </div>
                        <div class="col-sm-6 mt-2">
                            <strong>Class:</strong><br>
                            {{ $payment->student->class_name ?? (is_object($payment->student->class) ? ($payment->student->class->full_name ?? $payment->student->class->name ?? 'N/A') : $payment->student->class) }}
                        </div>
                        <div class="col-sm-6 mt-2">
                            <strong>Parent/Guardian:</strong><br>
                            {{ $payment->student->father_name }}
                        </div>
                    </div>

                    <hr>

                    <h6>Payment Details</h6>
                    <div class="row mb-3">
                        <div class="col-sm-6">
                            <strong>Fee Type:</strong><br>
                            {{ $payment->payment_details['fee_type'] ?? 'N/A' }}
                        </div>
                        <div class="col-sm-6">
                            <strong>Term:</strong><br>
                            {{ $payment->term }}
                        </div>
                        <div class="col-sm-6 mt-2">
                            <strong>Academic Year:</strong><br>
                            {{ $payment->academic_year }}
                        </div>
                        <div class="col-sm-6 mt-2">
                            <strong>Payment Method:</strong><br>
                            {{ ucfirst($payment->payment_method ?? 'Online') }}
                        </div>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-sm-6">
                            <strong>Amount Paid:</strong><br>
                            <h3 class="text-success">₦{{ number_format($payment->amount_paid, 2) }}</h3>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <strong>Balance:</strong><br>
                            <h4 class="text-danger">₦{{ number_format($payment->balance, 2) }}</h4>
                        </div>
                    </div>

                    @if($payment->paystackTransaction)
                        <hr>
                        <h6>Transaction Details</h6>
                        <div class="row">
                            <div class="col-sm-6">
                                <strong>Transaction ID:</strong><br>
                                {{ $payment->paystackTransaction->transaction_id }}
                            </div>
                            <div class="col-sm-6">
                                <strong>Channel:</strong><br>
                                {{ $payment->paystackTransaction->channel }}
                            </div>
                        </div>
                    @endif

                    <div class="alert alert-info mt-4">
                        <i class="fas fa-check-circle"></i> This is a computer-generated receipt and does not require a signature.
                    </div>

                    <div class="text-center mt-4">
                        <button onclick="window.print()" class="btn btn-primary">
                            <i class="fas fa-print"></i> Print Receipt
                        </button>
                        <a href="{{ route('fee-payments.history') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to History
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .sidebar, .top-header, .footer, .btn, .alert, .card-header {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
        }
        .card {
            border: none !important;
        }
    }
</style>
@endsection