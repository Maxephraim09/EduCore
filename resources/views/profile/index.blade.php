@extends('layouts.app')

@section('title', 'My Profile')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">My Profile</li>
@endsection

@section('styles')
<style>
    /* Profile Header */
    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 35px 40px;
        color: white;
        position: relative;
        overflow: hidden;
    }
    .profile-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: rgba(255,255,255,0.05);
        border-radius: 50%;
    }
    .profile-header::after {
        content: '';
        position: absolute;
        bottom: -40%;
        left: 20%;
        width: 200px;
        height: 200px;
        background: rgba(255,255,255,0.03);
        border-radius: 50%;
    }
    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 4px solid rgba(255,255,255,0.4);
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        color: white;
        font-weight: 700;
        overflow: hidden;
        flex-shrink: 0;
        position: relative;
        z-index: 1;
        backdrop-filter: blur(10px);
        box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    }
    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .profile-header .user-info {
        position: relative;
        z-index: 1;
    }
    .profile-header .user-info h3 {
        font-weight: 700;
        font-size: 28px;
        margin-bottom: 6px;
    }
    .profile-header .user-info p {
        opacity: 0.9;
        margin-bottom: 4px;
        font-size: 15px;
    }

    /* Stat Cards */
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid #f1f3f5;
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        border-radius: 16px 16px 0 0;
    }
    .stat-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.08);
    }
    .stat-card .stat-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: relative;
        z-index: 1;
    }
    .stat-card .stat-number {
        font-size: 32px;
        font-weight: 800;
        color: #1b1b18;
        line-height: 1;
        margin-bottom: 4px;
    }
    .stat-card .stat-label {
        font-size: 14px;
        color: #6b7280;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stat-card .stat-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    .stat-card .stat-icon.blue { 
        background: linear-gradient(135deg, #dbeafe, #bfdbfe); 
        color: #1e40af; 
    }
    .stat-card .stat-icon.green { 
        background: linear-gradient(135deg, #d1fae5, #a7f3d0); 
        color: #065f46; 
    }
    .stat-card .stat-icon.orange { 
        background: linear-gradient(135deg, #fef3c7, #fde68a); 
        color: #92400e; 
    }
    .stat-card .stat-icon.pink { 
        background: linear-gradient(135deg, #fce7f3, #f9a8d4); 
        color: #9d174d; 
    }
    .stat-card .stat-icon.purple { 
        background: linear-gradient(135deg, #ede9fe, #c4b5fd); 
        color: #6d28d9; 
    }
    /* Individual card accent colors */
    .stat-card.accent-blue::before { background: #3b82f6; }
    .stat-card.accent-green::before { background: #10b981; }
    .stat-card.accent-orange::before { background: #f59e0b; }
    .stat-card.accent-pink::before { background: #ec4899; }
    .stat-card.accent-purple::before { background: #8b5cf6; }

    /* Info Items */
    .info-item {
        display: flex;
        padding: 14px 0;
        border-bottom: 1px solid #f1f3f5;
        transition: background 0.2s;
    }
    .info-item:last-child {
        border-bottom: none;
    }
    .info-item:hover {
        background: #fafbfc;
        margin: 0 -20px;
        padding-left: 20px;
        padding-right: 20px;
        border-radius: 8px;
    }
    .info-item .label {
        width: 150px;
        font-weight: 600;
        color: #6b7280;
        flex-shrink: 0;
        font-size: 14px;
    }
    .info-item .value {
        flex: 1;
        color: #1b1b18;
        font-weight: 500;
        font-size: 14px;
    }
    .info-item .value .badge {
        font-size: 12px;
        padding: 5px 12px;
        font-weight: 500;
    }

    /* Activity Items */
    .activity-item {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        padding: 14px 0;
        border-bottom: 1px solid #f1f3f5;
        transition: background 0.2s;
    }
    .activity-item:last-child {
        border-bottom: none;
    }
    .activity-item:hover {
        background: #fafbfc;
        margin: 0 -20px;
        padding-left: 20px;
        padding-right: 20px;
        border-radius: 8px;
    }
    .activity-item .activity-icon {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
        margin-top: 2px;
    }
    .activity-item .activity-icon.blue { background: #dbeafe; color: #1e40af; }
    .activity-item .activity-icon.green { background: #d1fae5; color: #065f46; }
    .activity-item .activity-icon.orange { background: #fef3c7; color: #92400e; }
    .activity-item .activity-icon.pink { background: #fce7f3; color: #9d174d; }
    .activity-item .activity-icon.purple { background: #ede9fe; color: #6d28d9; }
    .activity-item .activity-content {
        flex: 1;
    }
    .activity-item .activity-content .title {
        font-weight: 600;
        font-size: 14px;
        color: #1b1b18;
        margin-bottom: 2px;
    }
    .activity-item .activity-content .time {
        font-size: 12px;
        color: #6b7280;
    }

    /* Cards */
    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        transition: box-shadow 0.3s;
        overflow: hidden;
    }
    .card:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    }
    .card-header {
        background: white;
        border-bottom: 1px solid #f1f3f5;
        padding: 18px 24px;
        font-weight: 600;
    }
    .card-header h5 {
        font-weight: 700;
        font-size: 16px;
        color: #1b1b18;
    }
    .card-header h5 i {
        color: #667eea;
    }
    .card-body {
        padding: 24px;
    }

    /* Quick Actions */
    .btn-action {
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        text-align: left;
        transition: all 0.3s;
        position: relative;
        overflow: hidden;
    }
    .btn-action i {
        width: 20px;
        text-align: center;
    }
    .btn-action:hover {
        transform: translateX(4px);
    }
    .btn-action.primary { 
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border: none;
    }
    .btn-action.primary:hover { 
        background: linear-gradient(135deg, #5a6fd6, #6a4292);
        color: white;
    }
    .btn-action.info { 
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        color: white;
        border: none;
    }
    .btn-action.info:hover { 
        background: linear-gradient(135deg, #2563eb, #1d4ed8);
        color: white;
    }
    .btn-action.secondary { 
        background: #f1f3f5;
        color: #1b1b18;
        border: none;
    }
    .btn-action.secondary:hover { 
        background: #e5e7eb;
        color: #1b1b18;
    }
    .btn-action.danger { 
        background: #fee2e2;
        color: #dc2626;
        border: none;
    }
    .btn-action.danger:hover { 
        background: #fecaca;
        color: #b91c1c;
    }

    /* Account Status */
    .status-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f1f3f5;
    }
    .status-item:last-child {
        border-bottom: none;
    }
    .status-item .label {
        font-weight: 500;
        color: #6b7280;
        font-size: 14px;
    }
    .status-item .badge {
        font-size: 12px;
        padding: 5px 12px;
        font-weight: 500;
        border-radius: 20px;
    }

    /* Modal */
    .modal-content {
        border-radius: 16px;
        border: none;
        box-shadow: 0 20px 60px rgba(0,0,0,0.15);
    }
    .modal-header {
        border-bottom: 1px solid #f1f3f5;
        padding: 20px 24px;
    }
    .modal-header .modal-title {
        font-weight: 700;
        color: #1b1b18;
    }
    .modal-body {
        padding: 24px;
    }
    .modal-footer {
        border-top: 1px solid #f1f3f5;
        padding: 16px 24px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .profile-header {
            padding: 24px 20px;
            border-radius: 16px;
        }
        .profile-header .user-info h3 {
            font-size: 22px;
        }
        .profile-avatar {
            width: 80px;
            height: 80px;
            font-size: 32px;
        }
        .info-item {
            flex-direction: column;
            gap: 4px;
            padding: 12px 0;
        }
        .info-item .label {
            width: 100%;
            font-size: 13px;
        }
        .info-item .value {
            font-size: 14px;
        }
        .stat-card .stat-number {
            font-size: 24px;
        }
        .stat-card .stat-icon {
            width: 44px;
            height: 44px;
            font-size: 18px;
        }
        .card-body {
            padding: 16px;
        }
    }

    /* Empty State */
    .empty-state {
        padding: 30px 20px;
        text-align: center;
    }
    .empty-state i {
        color: #d1d5db;
        margin-bottom: 12px;
    }
    .empty-state p {
        color: #6b7280;
        margin-bottom: 0;
        font-size: 14px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Profile Header -->
    <div class="profile-header mb-4">
        <div class="row align-items-center">
            <div class="col-md-8 d-flex align-items-center gap-4">
                <div class="profile-avatar">
                    @if($user->avatar_url)
                        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
                    @else
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    @endif
                </div>
                <div class="user-info">
                    <h3>{{ $user->name }}</h3>
                    <p><i class="fas fa-envelope me-2"></i>{{ $user->email }}</p>
                    <p><i class="fas fa-user-tag me-2"></i>{{ ucfirst(str_replace('_', ' ', $user->role ?? 'User')) }}</p>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('profile.edit') }}" class="btn btn-light btn-lg px-4" style="border-radius: 12px; font-weight: 600; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <i class="fas fa-edit me-2"></i> Edit Profile
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card accent-blue">
                <div class="stat-content">
                    <div>
                        <div class="stat-number">{{ number_format($profileData['stats']['total_students'] ?? 0) }}</div>
                        <div class="stat-label">Total Students</div>
                    </div>
                    <div class="stat-icon blue">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card accent-green">
                <div class="stat-content">
                    <div>
                        <div class="stat-number">{{ number_format($profileData['stats']['total_employees'] ?? 0) }}</div>
                        <div class="stat-label">Total Employees</div>
                    </div>
                    <div class="stat-icon green">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card accent-orange">
                <div class="stat-content">
                    <div>
                        <div class="stat-number">{{ number_format($profileData['stats']['total_classes'] ?? 0) }}</div>
                        <div class="stat-label">Total Classes</div>
                    </div>
                    <div class="stat-icon orange">
                        <i class="fas fa-school"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
            <div class="stat-card accent-pink">
                <div class="stat-content">
                    <div>
                        <div class="stat-number">{{ number_format($profileData['stats']['total_subjects'] ?? 0) }}</div>
                        <div class="stat-label">Total Subjects</div>
                    </div>
                    <div class="stat-icon pink">
                        <i class="fas fa-book"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Profile Information -->
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-user me-2"></i>Profile Information</h5>
                </div>
                <div class="card-body">
                    <div class="info-item">
                        <div class="label">Full Name</div>
                        <div class="value">{{ $user->name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Email Address</div>
                        <div class="value">{{ $user->email }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Role</div>
                        <div class="value">
                            <span class="badge bg-primary">
                                {{ ucfirst(str_replace('_', ' ', $user->role ?? 'User')) }}
                            </span>
                        </div>
                    </div>
                    @if($profileData['student'] ?? null)
                    <div class="info-item">
                        <div class="label">Student ID</div>
                        <div class="value">{{ $profileData['student']->admission_number ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Class</div>
                        <div class="value">{{ $profileData['student']->class->full_class_name ?? 'N/A' }}</div>
                    </div>
                    @endif
                    @if($profileData['employee'] ?? null)
                    <div class="info-item">
                        <div class="label">Employee ID</div>
                        <div class="value">{{ $profileData['employee']->employee_id ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Position</div>
                        <div class="value">{{ $profileData['employee']->position ?? 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Department</div>
                        <div class="value">{{ $profileData['employee']->department ?? 'N/A' }}</div>
                    </div>
                    @endif
                    <div class="info-item">
                        <div class="label">Member Since</div>
                        <div class="value">{{ $user->created_at ? $user->created_at->format('d M Y') : 'N/A' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">Last Updated</div>
                        <div class="value">{{ $user->updated_at ? $user->updated_at->diffForHumans() : 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5><i class="fas fa-clock me-2"></i>Recent Activities</h5>
                </div>
                <div class="card-body">
                    @forelse($profileData['recent_activities'] ?? [] as $activity)
                        <div class="activity-item">
                            <div class="activity-icon {{ $activity['icon_color'] ?? 'blue' }}">
                                <i class="fas {{ $activity['icon'] ?? 'fa-circle' }}"></i>
                            </div>
                            <div class="activity-content">
                                <div class="title">{{ $activity['title'] ?? 'Activity' }}</div>
                                <div class="time">
                                    <i class="far fa-clock me-1"></i>
                                    {{ $activity['time'] ?? 'Just now' }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-inbox fa-3x"></i>
                            <p>No recent activities found.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-bolt me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                        <a href="{{ route('profile.edit') }}" class="btn-action primary">
                            <i class="fas fa-edit me-2"></i> Edit Profile
                        </a>
                        <a href="#" class="btn-action info" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                            <i class="fas fa-key me-2"></i> Change Password
                        </a>
                        <a href="#" class="btn-action secondary">
                            <i class="fas fa-bell me-2"></i> Notification Settings
                        </a>
                        <a href="#" class="btn-action danger" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="fas fa-sign-out-alt me-2"></i> Logout
                        </a>
                    </div>
                </div>
            </div>

            <!-- Account Status -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5><i class="fas fa-shield-alt me-2"></i>Account Status</h5>
                </div>
                <div class="card-body">
                    <div class="status-item">
                        <span class="label">Account Status</span>
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle me-1"></i> Active
                        </span>
                    </div>
                    <div class="status-item">
                        <span class="label">Email Verified</span>
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle me-1"></i> Verified
                        </span>
                    </div>
                    <div class="status-item">
                        <span class="label">Two-Factor Auth</span>
                        <span class="badge bg-secondary">
                            <i class="fas fa-times-circle me-1"></i> Disabled
                        </span>
                    </div>
                </div>
            </div>

            <!-- Additional Info -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle me-2"></i>Did You Know?</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-0" style="font-size: 14px; line-height: 1.6;">
                        <i class="fas fa-lightbulb text-warning me-2"></i>
                        You can update your profile information anytime. 
                        Keep your contact details up to date to receive important notifications.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('profile.update-password') }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordModalLabel">
                        <i class="fas fa-key me-2" style="color: #667eea;"></i>Change Password
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label fw-semibold">
                            Current Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" class="form-control form-control-lg @error('current_password') is-invalid @enderror" 
                               id="current_password" name="current_password" placeholder="Enter current password" required>
                        @error('current_password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">
                            New Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror" 
                               id="password" name="password" placeholder="Enter new password" required>
                        <small class="text-muted">Minimum 8 characters</small>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-0">
                        <label for="password_confirmation" class="form-label fw-semibold">
                            Confirm New Password <span class="text-danger">*</span>
                        </label>
                        <input type="password" class="form-control form-control-lg" 
                               id="password_confirmation" name="password_confirmation" placeholder="Confirm new password" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-lg" data-bs-dismiss="modal" style="border-radius: 12px; font-weight: 600;">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg px-4" style="border-radius: 12px; font-weight: 600;">
                        <i class="fas fa-save me-2"></i> Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show modal if there are password errors
        @if($errors->has('current_password') || $errors->has('password'))
            var passwordModal = new bootstrap.Modal(document.getElementById('changePasswordModal'));
            passwordModal.show();
        @endif
    });
</script>
@endpush