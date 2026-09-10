@extends('layouts.portal')

@section('title', 'Check Application Status')

@section('styles')
<style>
    .status-page {
        min-height: 80vh;
        display: flex;
        align-items: center;
        padding: 80px 0;
    }
    .status-card {
        background: #ffffff;
        border-radius: 28px;
        padding: 42px;
        box-shadow: 0 30px 80px rgba(15, 23, 42, 0.12);
    }
    .status-card h2 {
        font-size: 2.4rem;
        margin-bottom: 18px;
    }
    .status-card .form-control {
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        padding: 14px 16px;
    }
    .status-card .btn-primary {
        border-radius: 14px;
        padding: 14px 22px;
    }
</style>
@endsection

@section('content')
<div class="status-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="status-card">
                    <div class="text-center mb-4">
                        <h2>Track Your Application</h2>
                        <p class="text-muted">Enter your application number and email address to view the current status.</p>
                    </div>
                    <form action="{{ route('application.status.check') }}" method="POST">
                        @csrf
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Application Number</label>
                                <input type="text" name="application_number" class="form-control" value="{{ old('application_number') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Check Status</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection