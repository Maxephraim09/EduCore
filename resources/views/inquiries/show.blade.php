@extends('layouts.app')

@section('title', 'Inquiry Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('inquiries.index') }}">Inquiries</a></li>
    <li class="breadcrumb-item active">Inquiry #{{ $inquiry->id }}</li>
@endsection

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">Inquiry #{{ $inquiry->id }}</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3"><strong>Name:</strong> {{ $inquiry->name }}</div>
                <div class="col-md-6 mb-3"><strong>Email:</strong> {{ $inquiry->email }}</div>
                <div class="col-md-6 mb-3"><strong>Phone:</strong> {{ $inquiry->phone ?? '—' }}</div>
                <div class="col-md-6 mb-3"><strong>Priority:</strong> {{ $inquiry->priority }}</div>

                <div class="col-md-12 mb-3"><strong>Subject:</strong> {{ $inquiry->subject }}</div>
                <div class="col-md-12 mb-3"><strong>Message:</strong> <div class="mt-2">{{ $inquiry->message }}</div></div>

                <div class="col-md-6 mb-3"><strong>Status:</strong> {{ $inquiry->status }}</div>
                <div class="col-md-6 mb-3"><strong>Created At:</strong> {{ $inquiry->created_at?->format('d M Y H:i') }}</div>
            </div>

            <hr>

            @if($inquiry->response)
                <h5>Response</h5>
                <div class="alert alert-success">{{ $inquiry->response }}</div>
                <p class="text-muted">Responded by: {{ $inquiry->respondedBy?->name ?? 'System' }} on {{ $inquiry->responded_at?->format('d M Y H:i') }}</p>
            @elseif(auth()->user()->hasPermission('respond-inquiries'))
                <h5>Respond to Inquiry</h5>
                <form method="POST" action="{{ route('inquiries.respond', $inquiry) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Response</label>
                        <textarea name="response" class="form-control" rows="5" required>{{ old('response') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Response</button>
                </form>
            @else
                <div class="alert alert-info">
                    This inquiry has not yet been responded to. A staff member will reply shortly.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

