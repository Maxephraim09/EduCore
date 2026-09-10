@extends('layouts.app')

@section('title', 'Notifications')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Notifications</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-bell me-2"></i>Notifications</h5>
            <div class="d-flex gap-2">
                @can('send-notifications')
                    <a href="{{ route('notifications.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-paper-plane"></i> Send Notification
                    </a>
                @endcan
                @if($unreadCount > 0)
                    <button class="btn btn-success btn-sm" onclick="markAllAsRead()">
                        <i class="fas fa-check-double"></i> Mark All as Read
                    </button>
                @endif
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            @if($notifications->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No notifications</h5>
                    <p class="text-muted">You're all caught up!</p>
                </div>
            @else
                <div class="list-group">
                    @foreach($notifications as $notification)
                        <div class="list-group-item list-group-item-action {{ !$notification->is_read ? 'bg-light' : '' }}">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center">
                                        @if(!$notification->is_read)
                                            <span class="badge bg-primary me-2">New</span>
                                        @endif
                                        <h6 class="mb-1">{{ $notification->title }}</h6>
                                    </div>
                                    <p class="mb-1 text-muted small">{{ $notification->message }}</p>
                                    <small class="text-muted">
                                        <i class="fas fa-clock me-1"></i> {{ $notification->created_at->diffForHumans() }}
                                        @if($notification->type)
                                            <span class="badge bg-secondary ms-2">{{ ucfirst($notification->type) }}</span>
                                        @endif
                                    </small>
                                </div>
                                <div>
                                    @if(!$notification->is_read)
                                        <button class="btn btn-sm btn-outline-primary" onclick="markAsRead({{ $notification->id }})">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                    @if($notification->link)
                                        <a href="{{ $notification->link }}" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-arrow-right"></i>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    function markAsRead(id) {
        $.ajax({
            url: '/notifications/mark-read/' + id,
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function() {
                location.reload();
            },
            error: function() {
                alert('Error marking notification as read.');
            }
        });
    }
    
    function markAllAsRead() {
        if (confirm('Mark all notifications as read?')) {
            $.ajax({
                url: '/notifications/mark-all-read',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function() {
                    location.reload();
                },
                error: function() {
                    alert('Error marking all notifications as read.');
                }
            });
        }
    }
</script>
@endpush
@endsection