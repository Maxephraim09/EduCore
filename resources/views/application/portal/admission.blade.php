@extends('layouts.portal-dashboard')

@section('title', 'Admission | Applicant Portal')
@section('page_title', 'Admission')

@section('content')
    <div class="portal-card">
        @if($application && $application->isAdmitted())
            <div class="p-4 rounded-4 mb-4" style="background: #dcfce7; border: 1px solid #d1fae5;">
                <h3 class="mb-3" style="color: #0f5132;">Congratulations!</h3>
                <p class="mb-0" style="color: #276749; line-height: 1.7;">Your application has been admitted. Please download your admission letter and complete student registration to secure your place.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <strong>Admission Number</strong>
                    <p>{{ $application->admission_number ?? 'Not available' }}</p>
                </div>
                <div class="col-md-6">
                    <strong>Admission Date</strong>
                    <p>{{ $application->admitted_at ? $application->admitted_at->format('M d, Y') : 'Pending' }}</p>
                </div>
                <div class="col-12">
                    <strong>Admission Notes</strong>
                    <p>{{ $application->admission_notes ?? 'No notes available.' }}</p>
                </div>
            </div>

            <div class="mt-4 d-flex flex-wrap gap-3">
                @if($application->admission_number)
                    <a href="{{ route('application.admission-letter.download', $application->admission_number) }}" class="btn btn-success">Download Admission Letter</a>
                @endif
                <a href="{{ route('application.portal.registration') }}" class="btn btn-primary">Complete Student Registration</a>
            </div>
        @else
            <div class="p-4 rounded-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <h3 class="mb-3" style="color: #0f172a;">Admission Pending</h3>
                <p class="mb-0" style="color: #475569; line-height: 1.7;">Your application is still under review. Once admission is confirmed, this page will show your admission letter and registration next steps.</p>
            </div>
        @endif
    </div>
@endsection
