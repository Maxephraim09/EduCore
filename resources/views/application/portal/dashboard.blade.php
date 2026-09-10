@extends('layouts.portal-dashboard')

@section('title','Applicant Dashboard')
@section('page_title','Dashboard')

@section('styles')
<style>
    .dashboard-intro {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        gap: 18px;
        align-items: center;
        margin-bottom: 28px;
    }

    .dashboard-intro h2 {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .dashboard-intro p {
        margin: 0;
        color: #6b7280;
        font-size: 0.95rem;
        line-height: 1.7;
    }

    .stat-card {
        background: #ffffff;
        border: 1px solid rgba(226,232,240,0.95);
        border-radius: 24px;
        padding: 24px;
        min-height: 140px;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 22px 50px rgba(15,23,42,0.08);
    }

    .stat-card h5 {
        font-size: 0.9rem;
        font-weight: 700;
        color: #475569;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .stat-card p {
        margin: 0;
        font-size: 1.6rem;
        font-weight: 700;
        color: #0f172a;
    }

    .stat-card small {
        color: #64748b;
        display: block;
        margin-top: 8px;
    }

    .summary-text {
        color: #475569;
        line-height: 1.75;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="portal-card">
        <div class="dashboard-intro">
            <div>
                <h2>Welcome, {{ $applicant->name ?? 'Applicant' }}</h2>
                <p>Your application status and recent activity are available below. Use the sidebar to continue the application, view payment history, or access admission details.</p>
            </div>
            @php
                $status = $application ? $application->status : 'no_application';
                $statusClass = $application ? 'status-' . $application->status : 'status-no-application';
                if ($application && method_exists($application, 'isAdmitted') && $application->isAdmitted()) {
                    $statusClass = 'status-admitted';
                }
                $displayStatus = ucfirst(str_replace('_', ' ', $status));
            @endphp
            <span class="badge-status {{ $statusClass }}">{{ $displayStatus }}</span>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="stat-card">
                    <h5><i class="fas fa-file-alt me-2" style="color: #5B6FF5;"></i>Application</h5>
                    <p>{{ $application ? $application->created_at->format('M d, Y') : 'Not started' }}</p>
                    <small>Latest submission date</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <h5><i class="fas fa-credit-card me-2" style="color: #22C55E;"></i>Payments</h5>
                    <p>{{ $transactionCount ?? 0 }}</p>
                    <small>Transactions recorded</small>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <h5><i class="fas fa-graduation-cap me-2" style="color: #F59E0B;"></i>Admission</h5>
                    <p>{{ isset($hasAdmittedApplication) && $hasAdmittedApplication ? 'Admitted' : 'Pending Review' }}</p>
                    <small>Admission eligibility</small>
                </div>
            </div>
        </div>

        <div class="mt-4" style="padding-top: 24px; border-top: 2px solid rgba(226,232,240,0.6);">
            @if($application)
                <h5 style="font-size: 1.1rem; font-weight: 700; color: #0F172A; margin-bottom: 8px; display: flex; align-items: center; gap: 8px;">
                    <i class="fas fa-clipboard-list" style="color: #5B6FF5;"></i>
                    Application Summary
                </h5>
                <p class="summary-text">
                    {{ Str::limit($application->personal_statement ?? 'No summary available.', 220) }}
                </p>
            @else
                <h5 style="font-size: 1.1rem; font-weight: 700; color: #0F172A; margin-bottom: 8px;">Next step</h5>
                <p class="summary-text">Visit the Application section in the sidebar to start or continue your application.</p>
            @endif
        </div>
    </div>
</div>
@endsection
