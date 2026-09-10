@extends('layouts.app')

@section('title', 'Send Notification')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('notifications.index') }}">Notifications</a></li>
    <li class="breadcrumb-item active">Send</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">Send Notification</div>
        <div class="card-body">
            <form method="POST" action="{{ route('notifications.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <textarea name="message" class="form-control" rows="5" required>{{ old('message') }}</textarea>
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="sendToAll" name="send_to_all" value="1" {{ old('send_to_all') ? 'checked' : '' }}>
                    <label class="form-check-label" for="sendToAll">Send to all users</label>
                </div>

                <div class="mb-3" id="userSelectWrapper">
                    <label class="form-label">Send To User</label>
                    <select name="user_id" class="form-control">
                        <option value="">Select user</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user->name }} ({{ $user->email }})</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Send Notification</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sendToAllCheckbox = document.getElementById('sendToAll');
        const userSelectWrapper = document.getElementById('userSelectWrapper');

        function updateUserSelectVisibility() {
            userSelectWrapper.style.display = sendToAllCheckbox.checked ? 'none' : 'block';
        }

        sendToAllCheckbox.addEventListener('change', updateUserSelectVisibility);
        updateUserSelectVisibility();
    });
</script>
@endpush
