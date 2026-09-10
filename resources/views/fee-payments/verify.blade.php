@extends('layouts.app')

@section('title', 'Verify Receipt')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header text-center">
                    <h4>Receipt Verification</h4>
                </div>
                <div class="card-body text-center">
                    @if($status === 'not_found' || !$payment)
                        <div class="alert alert-danger">Receipt not found or invalid.</div>
                    @else
                        <div class="mb-3">
                            <h5>{{ $payment->school_name ?? getSchoolName() }}</h5>
                            <small class="text-muted">{{ $payment->school_address ?? getSchoolAddress() }}</small>
                        </div>

                        <p><strong>Receipt:</strong> {{ $payment->receipt_number }}</p>
                        <p><strong>Student:</strong> {{ $payment->student->full_name ?? 'N/A' }}</p>
                        <p><strong>Amount Paid:</strong> {{ formatCurrency($payment->amount_paid) }}</p>
                        <p><strong>Date:</strong> {{ optional($payment->payment_date)->format('d M Y h:i A') }}</p>
                        <p><strong>Status:</strong> <span class="badge bg-success text-white">{{ ucfirst($payment->payment_status) }}</span></p>

                        <div class="mt-3">
                            <a href="{{ route('fee-payments.receipt', $payment->id) }}" class="btn btn-primary" target="_blank">View Full Receipt</a>
                        </div>

                        <div class="text-muted mt-3 small">Verified via Financial Management System</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
