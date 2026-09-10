@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid py-4">
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
                    <p class="text-muted">{{ App\Models\User::getRoleLabel($user->role) }}</p>
                    <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5>Update Profile</h5>
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6"><input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" placeholder="Full Name" required></div>
                            <div class="col-md-6"><input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" placeholder="Email" required></div>
                            <div class="col-md-6"><input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}" placeholder="Phone"></div>
                            <div class="col-md-6"><input type="file" name="avatar" class="form-control"></div>
                            <div class="col-12"><textarea name="address" class="form-control" rows="3" placeholder="Address">{{ old('address', $user->address) }}</textarea></div>
                        </div>
                        <button class="btn btn-primary mt-3" type="submit">Save Profile</button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <h5>Change Password</h5>
                    <form method="POST" action="{{ route('profile.password') }}">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4"><input type="password" name="current_password" class="form-control" placeholder="Current Password" required></div>
                            <div class="col-md-4"><input type="password" name="password" class="form-control" placeholder="New Password" required></div>
                            <div class="col-md-4"><input type="password" name="password_confirmation" class="form-control" placeholder="Confirm Password" required></div>
                        </div>
                        <button class="btn btn-outline-warning mt-3" type="submit">Update Password</button>
                    </form>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h5>Security Settings</h5>
                    <form method="POST" action="{{ route('profile.security') }}">
                        @csrf
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="two_factor_enabled" value="1" {{ $user->two_factor_enabled ? 'checked' : '' }}>
                            <label class="form-check-label">Enable two-factor authentication</label>
                        </div>
                        <button class="btn btn-outline-secondary mt-3" type="submit">Save Security Settings</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
