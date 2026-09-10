@extends('layouts.portal-dashboard')

@section('title', 'Enquiry | Applicant Portal')
@section('page_title', 'Enquiry')

@section('content')
    <div class="portal-card">
        <h3 class="mb-4">Enquiry</h3>
        <p class="text-muted">If you have questions about your application or admission status, please contact the admissions office.</p>

        <div class="row g-4">
            <div class="col-md-6">
                <strong>Phone</strong>
                <p>{{ getSchoolPhone() ?: 'Not available' }}</p>
            </div>
            <div class="col-md-6">
                <strong>Email</strong>
                <p>{{ getSchoolEmail() ?: 'Not available' }}</p>
            </div>
        </div>

        <div class="mt-4">
            <p class="text-muted">You can also submit your enquiry using the contact form below.</p>
            <form action="{{ route('application.portal.enquiry.send') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="subject" class="form-label">Subject</label>
                    <input type="text" class="form-control" id="subject" name="subject" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Message</label>
                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Enquiry</button>
            </form>
        </div>
    </div>
@endsection
