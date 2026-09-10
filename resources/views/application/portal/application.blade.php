@extends('layouts.portal-dashboard')

@section('title', 'Application | Applicant Portal')
@section('page_title', 'Application Details')

@section('content')
    <div class="portal-card">
        <h3 class="mb-4">Your Application</h3>

        @if($application)
            <div class="row g-4">
                <div class="col-md-6">
                    <strong>Application ID</strong>
                    <p>{{ $application->id }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Status</strong>
                    <p>{{ ucfirst($application->status) }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Class</strong>
                    <p>{{ optional($application->class)->name ?? 'Not selected' }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Submitted</strong>
                    <p>{{ $application->created_at->format('M d, Y') }}</p>
                </div>
                <div class="col-12">
                    <strong>Notes</strong>
                    <p>{{ $application->remarks ?? 'No additional notes yet.' }}</p>
                </div>
            </div>
        @else
            <p class="text-muted">You have not submitted an application yet.</p>
            <a href="{{ route('application.select-category') }}" class="btn btn-primary">Start Application</a>
        @endif
    </div>
@endsection
