@extends('layouts.app')

@section('title', 'Visitors')

@section('breadcrumb')
    <li class="breadcrumb-item active">Visitors</li>
@endsection

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Visitors</h2>
        <a href="{{ route('visitors.create') }}" class="btn btn-primary">Check In Visitor</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Person To See</th>
                            <th>Purpose</th>
                            <th>Status</th>
                            <th>Check In</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($visitors as $visitor)
                            <tr>
                                <td>{{ $visitor->name }}</td>
                                <td>{{ $visitor->phone }}</td>
                                <td>{{ $visitor->person_to_see }}</td>
                                <td>{{ $visitor->purpose }}</td>
                                <td>{{ $visitor->status }}</td>
                                <td>{{ $visitor->check_in }}</td>
                                <td>
                                    <a href="{{ route('visitors.show', $visitor) }}" class="btn btn-sm btn-secondary">View</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">No visitors found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $visitors->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

