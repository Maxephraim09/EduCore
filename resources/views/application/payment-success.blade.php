@extends('layouts.portal')

@section('title', 'Application Payment Success')

@section('styles')
<style>
    .success-card {
        background: #ffffff;
        border-radius: 28px;
        padding: 48px;
        box-shadow: 0 30px 80px rgba(15, 23, 42, 0.12);
        text-align: center;
    }
    .success-card .icon {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        background: #d1fae5;
        color: #16a34a;
        display: grid;
        place-items: center;
        margin: 0 auto 24px;
        font-size: 2.75rem;
    }
    .success-card h2 {
        font-size: 2.4rem;
        margin-bottom: 16px;
    }
    .success-card p {
        color: #475569;
        margin-bottom: 24px;
    }
    .success-card .details {
        text-align: left;
        max-width: 520px;
        margin: 0 auto 24px;
    }
    .success-card .details dt {
        font-weight: 700;
        color: #1f2937;
    }
    .success-card .details dd {
        margin: 0 0 14px;
        color: #475569;
    }
    .btn-primary {
        border-radius: 14px;
        padding: 12px 28px;
    }
</style>
@endsection

@section('content')
<div class="py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="success-card">
                <div class="icon">
                    <i class="fas fa-check"></i>
                </div>
                <h2>Payment Confirmed</h2>
                <p>Your application fee has been successfully received. Thank you for completing the payment.</p>

                <dl class="details">
                    <dt>Application Number</dt>
                    <dd>{{ $application->application_number }}</dd>
                    <dt>Amount Paid</dt>
                    <dd>{{ formatCurrency($application->amount_paid) }}</dd>
                    <dt>Payment Method</dt>
                    <dd>{{ ucfirst($application->payment_details['method'] ?? $application->payment_method ?? 'Online') }}</dd>
                </dl>

                <a href="{{ route('application.status.form') }}" class="btn btn-primary">Check Application Status</a>
            </div>
        </div>
    </div>
</div>
@endsection