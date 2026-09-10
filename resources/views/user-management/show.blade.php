@extends('layouts.app')

@section('title', 'User Details')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">User Details</h2>
            <p class="text-muted mb-0">View profile information and recent activity.</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Back</a>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-body text-center">
                    <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mx-auto mb-3" style="width:110px;height:110px;">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="avatar" class="rounded-circle" style="width:110px;height:110px;object-fit:cover;">
                        @else
                            <i class="fas fa-user fa-2x"></i>
                        @endif
                    </div>
                    <h4>{{ $user->name }}</h4>
                    <p class="text-muted mb-3">{{ App\Models\User::getRoleLabel($user->role) }}</p>
                    <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6"><strong>Email:</strong> {{ $user->email }}</div>
                        <div class="col-md-6"><strong>Phone:</strong> {{ $user->phone ?? '—' }}</div>
                        <div class="col-md-6"><strong>Address:</strong> {{ $user->address ?? '—' }}</div>
                        <div class="col-md-6"><strong>Created:</strong> {{ $user->created_at->format('M d, Y H:i') }}</div>
                    </div>
                    @if($relatedProfile)
                        <hr>
                        <h5 class="mt-3">Linked Profile</h5>
                        <p class="mb-0">{{ class_basename($relatedProfile) }} record linked to this account.</p>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm border-0 mt-4">
                <div class="card-body">
                    <h5>Recent Activity</h5>
                    <ul class="list-group list-group-flush">
                        @forelse($activityLogs as $log)
                            <li class="list-group-item">{{ $log->description ?? $log->action }} <small class="text-muted">{{ $log->created_at->format('M d, Y H:i') }}</small></li>
                        @empty
                            <li class="list-group-item text-muted">No activity recorded yet.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
