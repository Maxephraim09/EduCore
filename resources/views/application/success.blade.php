@extends('layouts.portal')

@section('title', 'Application Submitted')

@section('styles')
<style>
    .success-card {
        background: #fff;
        border-radius: 16px;
        padding: 50px;
        text-align: center;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    }
    .success-icon {
        font-size: 80px;
        color: #22c55e;
        margin-bottom: 20px;
        animation: bounceIn 0.6s ease;
    }
    .success-card .application-number {
        font-size: 24px;
        font-weight: 700;
        color: #667eea;
        background: #f0f4ff;
        padding: 10px 30px;
        border-radius: 10px;
        display: inline-block;
        margin: 10px 0 20px;
    }
    .success-card .steps {
        text-align: left;
        max-width: 400px;
        margin: 30px auto;
    }
    .success-card .steps .step-item {
        display: flex;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .success-card .steps .step-item:last-child {
        border-bottom: none;
    }
    .success-card .steps .step-item .step-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        font-weight: 700;
        font-size: 14px;
    }
    .success-card .steps .step-item .step-icon.pending {
        background: #fef3c7;
        color: #d97706;
    }
    .success-card .steps .step-item .step-icon.completed {
        background: #d1fae5;
        color: #059669;
    }
    .success-card .steps .step-item .step-icon.current {
        background: #667eea;
        color: white;
    }
    @keyframes bounceIn {
        0% { transform: scale(0); opacity: 0; }
        50% { transform: scale(1.2); }
        100% { transform: scale(1); opacity: 1; }
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="success-card">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>

                <h2 class="fw-bold text-primary">Application Submitted!</h2>
                <p class="text-muted">Your application has been received successfully.</p>

                <div class="application-number">
                    <i class="fas fa-qrcode me-2"></i>
                    {{ $application->application_number }}
                </div>

                <p class="text-muted">
                    <i class="fas fa-envelope me-2"></i>
                    A confirmation email has been sent to <strong>{{ $application->email }}</strong>
                </p>

                <div class="steps">
                    <h6 class="fw-bold mb-3">Application Process</h6>
                    <div class="step-item">
                        <span class="step-icon completed"><i class="fas fa-check"></i></span>
                        <div>
                            <div class="fw-bold">Application Submitted</div>
                            <small class="text-muted">Completed</small>
                        </div>
                    </div>
                    <div class="step-item">
                        <span class="step-icon current">2</span>
                        <div>
                            <div class="fw-bold">Pay Application Fee</div>
                            <small class="text-muted">Pending - {{ formatCurrency($application->application_fee) }}</small>
                        </div>
                    </div>
                    <div class="step-item">
                        <span class="step-icon pending">3</span>
                        <div>
                            <div class="fw-bold">Application Review</div>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                    <div class="step-item">
                        <span class="step-icon pending">4</span>
                        <div>
                            <div class="fw-bold">Admission Decision</div>
                            <small class="text-muted">Pending</small>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ route('application.payment', $application->application_number) }}" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-credit-card me-2"></i>Pay Application Fee
                    </a>
                    <a href="{{ route('application.status.form') }}" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-search me-2"></i>Check Status
                    </a>
                </div>

                <div class="mt-4">
                    <small class="text-muted">Keep your application number safe. You'll need it to check your status.</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection