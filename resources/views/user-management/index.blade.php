@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">User Management</h2>
            <p class="text-muted mb-0">Create, manage, and monitor all platform users.</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Create User</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><h6 class="text-muted">Total Users</h6><h3>{{ $stats['total'] }}</h3></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><h6 class="text-muted">Active</h6><h3>{{ $stats['active'] }}</h3></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><h6 class="text-muted">Inactive</h6><h3>{{ $stats['inactive'] }}</h3></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><h6 class="text-muted">Roles</h6><h3>{{ count($stats['by_role']) }}</h3></div></div></div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Search</label>
                    <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Name, email, phone">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-select">
                        <option value="">All</option>
                        @foreach($roles as $role)
                            <option value="{{ $role }}" {{ request('role') === $role ? 'selected' : '' }}>{{ App\Models\User::getRoleLabel($role) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All</option>
                        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Sort</label>
                    <select name="sort" class="form-select">
                        <option value="created_at" {{ request('sort') === 'created_at' ? 'selected' : '' }}>Created</option>
                        <option value="name" {{ request('sort') === 'name' ? 'selected' : '' }}>Name</option>
                        <option value="email" {{ request('sort') === 'email' ? 'selected' : '' }}>Email</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-outline-secondary w-100" type="submit">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0 mt-4">
        <div class="card-body">
            <form method="POST" action="{{ route('users.bulk-action') }}">
                @csrf
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Users</h5>
                    <div class="d-flex gap-2">
                        <select name="action" class="form-select form-select-sm" required>
                            <option value="">Bulk Action</option>
                            <option value="activate">Activate</option>
                            <option value="deactivate">Deactivate</option>
                            <option value="delete">Delete</option>
                        </select>
                        <button class="btn btn-sm btn-outline-primary">Apply</button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                        <tr>
                            <th><input type="checkbox" id="select-all"></th>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td><input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="user-checkbox"></td>
                                <td>{{ $user->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-light d-flex align-items-center justify-content-center" style="width:36px;height:36px;">
                                            @if($user->avatar)
                                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="avatar" class="rounded-circle" style="width:36px;height:36px;object-fit:cover;">
                                            @else
                                                <i class="fas fa-user"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $user->name }}</div>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone ?? '—' }}</td>
                                <td>{{ App\Models\User::getRoleLabel($user->role) }}</td>
                                <td><span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $user->is_active ? 'Active' : 'Inactive' }}</span></td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('users.show', $user) }}" class="btn btn-outline-info"><i class="fas fa-eye"></i></a>
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary"><i class="fas fa-edit"></i></a>
                                        @if($user->is_active)
                                            <form action="{{ route('users.deactivate', $user) }}" method="POST" class="d-inline">@csrf<button class="btn btn-outline-warning"><i class="fas fa-user-slash"></i></button></form>
                                        @else
                                            <form action="{{ route('users.activate', $user) }}" method="POST" class="d-inline">@csrf<button class="btn btn-outline-success"><i class="fas fa-user-check"></i></button></form>
                                        @endif
                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this user?');">@csrf @method('DELETE')<button class="btn btn-outline-danger"><i class="fas fa-trash"></i></button></form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-muted py-4">No users found.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </form>
            <div class="mt-3">{{ $users->links() }}</div>
        </div>
    </div>
</div>

<script>
    document.getElementById('select-all')?.addEventListener('change', function () {
        document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = this.checked);
    });
</script>
@endsection
