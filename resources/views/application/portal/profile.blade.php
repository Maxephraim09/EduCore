@extends('layouts.portal-dashboard')

@section('title', 'Profile | Applicant Portal')
@section('page_title', 'Profile')

@section('content')
    <div class="portal-card">
        <h3 class="mb-4">Applicant Profile</h3>
        <div class="row gy-3">
            <div class="col-md-6">
                <strong>Name</strong>
                <p>{{ $applicant->name }}</p>
            </div>
            <div class="col-md-6">
                <strong>Email</strong>
                <p>{{ $applicant->email }}</p>
            </div>
            <div class="col-md-6">
                <strong>Phone</strong>
                <p>{{ $applicant->phone ?? 'Not provided' }}</p>
            </div>
            <div class="col-md-6">
                <strong>Registered</strong>
                <p>{{ $applicant->created_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>
@endsection
