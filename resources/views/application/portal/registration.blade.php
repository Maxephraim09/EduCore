@extends('layouts.portal-dashboard')

@section('title', 'Registration | Applicant Portal')
@section('page_title', 'Registration')

@section('content')
    <div class="portal-card">
        <h3 class="mb-4">Registration</h3>
        <p class="text-muted">Once your application is admitted, registration details and next steps will appear here.</p>

        <div class="mt-3">
            <dl class="row">
                <dt class="col-sm-4">Registration status</dt>
                <dd class="col-sm-8">{{ $application->isAdmitted() ? 'Open' : 'Pending admission' }}</dd>

                <dt class="col-sm-4">Registration deadline</dt>
                <dd class="col-sm-8">{{ $application->registration_deadline ? $application->registration_deadline->format('M d, Y') : 'To be announced' }}</dd>

                <dt class="col-sm-4">Next step</dt>
                <dd class="col-sm-8">Please check with the admissions office for registration payment and class schedule.</dd>
            </dl>
        </div>
    </div>
@endsection
