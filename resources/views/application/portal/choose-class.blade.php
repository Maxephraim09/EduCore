@extends('layouts.portal-dashboard')

@section('title','Choose Class')
@section('page_title','Choose Class')

@section('styles')
<style>
    .choose-class-page {
        padding: 72px 0;
        background: linear-gradient(180deg, rgba(248,250,252,1) 0%, rgba(91,111,245,0.08) 100%);
    }

    .choose-class-card {
        border-radius: 32px;
        background: rgba(255,255,255,0.96);
        border: 1px solid rgba(226,232,240,0.95);
        box-shadow: 0 30px 70px rgba(15,23,42,0.1);
        overflow: hidden;
    }

    .choose-class-header {
        padding: 34px 32px;
        background: linear-gradient(135deg, rgba(91,111,245,0.18), rgba(118,75,162,0.12));
    }

    .choose-class-header h3 {
        margin: 0;
        color: #111827;
        font-size: clamp(2rem, 2.5vw, 2.6rem);
    }

    .choose-class-header p {
        margin-top: 14px;
        color: #475569;
        line-height: 1.75;
    }

    .choose-class-body {
        padding: 32px;
    }

    .payment-summary {
        border-radius: 20px;
        background: #f8fafc;
        padding: 24px;
        border: 1px solid rgba(226,232,240,0.9);
        margin-bottom: 24px;
    }

    .payment-summary strong {
        font-size: 1.1rem;
        color: #111827;
    }

    .btn-continue {
        border-radius: 16px;
        padding: 14px 30px;
        font-weight: 700;
    }

    .form-control,
    .form-select {
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        padding: 14px 16px;
    }

    @media (max-width: 768px) {
        .choose-class-page {
            padding: 40px 0;
        }
    }
</style>
@endsection

@section('content')
<div class="choose-class-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-7">
                <div class="choose-class-card">
                    <div class="choose-class-header">
                        <h3>Choose Your Class</h3>
                        <p>Select the category and class you want to apply for, then complete payment to unlock the admission form.</p>
                    </div>
                    <div class="choose-class-body">
                        <form action="{{ route('application.pay') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Select Class</label>
                                <select name="class_id" class="form-select" required>
                                    <option value="">Select Class</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}">{{ $class->full_class_name ?? $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="payment-summary">
                                <p class="mb-2 text-uppercase text-muted small">Application Fee</p>
                                <strong>{{ formatCurrency(getSetting('application_fee', 5000)) }}</strong>
                                <p class="mt-3 text-muted mb-0">This fee covers processing and initial review of your application.</p>
                            </div>

                            <div class="d-grid">
                                <button class="btn btn-primary btn-continue" type="submit">Pay Application Fee & Continue</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
