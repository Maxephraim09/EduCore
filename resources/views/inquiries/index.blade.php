@extends('layouts.app')

@section('title', 'Inquiries')

@section('breadcrumb')
    <li class="breadcrumb-item active">Inquiries</li>
@endsection

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Inquiries</h2>
        <a href="{{ route('inquiries.create') }}" class="btn btn-primary">New Inquiry</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Subject</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inquiries as $inquiry)
                            <tr>
                                <td>{{ $inquiry->name }}</td>
                                <td>{{ $inquiry->subject }}</td>
                                <td>{{ $inquiry->priority }}</td>
                                <td>{{ $inquiry->status }}</td>
                                <td>{{ $inquiry->created_at?->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('inquiries.show', $inquiry) }}" class="btn btn-sm btn-secondary">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">No inquiries found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $inquiries->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

