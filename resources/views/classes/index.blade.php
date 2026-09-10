@extends('layouts.app')

@section('title', 'Class Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Classes</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="mb-0">Total Classes</h6>
                    <h3 class="mt-2 mb-0">{{ $totalClasses ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="mb-0">Total Students</h6>
                    <h3 class="mt-2 mb-0">{{ $totalStudents ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="mb-0">Active Classes</h6>
                    <h3 class="mt-2 mb-0">{{ $activeClasses ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-school me-2"></i>Classes</h5>
            <a href="{{ route('classes.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add New Class
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('classes.index') }}">
                            <div class="input-group">
                                <label class="input-group-text" for="categoryFilter">Category</label>
                                <select id="categoryFilter" name="category_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Categories</option>
                                    @foreach($categories ?? [] as $category)
                                        <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6 text-end">
                        <a href="{{ route('class-categories.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-layer-group"></i> Manage Categories
                        </a>
                    </div>
                </div>
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="classesTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Class Name</th>
                            <th>Code</th>
                            <th>Category</th>
                            <th>Class Teacher</th>
                            <th>Students</th>
                            <th>Capacity</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($classes ?? [] as $class)
                        <tr>
                            <td>{{ $class->id }}</td>
                            <td><strong>{{ $class->full_class_name ?? $class->name . ' ' . $class->section }}</strong></td>
                            <td>{{ $class->code }}</td>
                            <td>{{ optional($class->category)->name ?? 'Uncategorized' }}</td>
                            <td>
                                @if($class->classTeacher)
                                    {{ $class->classTeacher->first_name }} {{ $class->classTeacher->last_name }}
                                @else
                                    <span class="text-muted">Not Assigned</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ ($class->student_count ?? 0) >= $class->capacity ? 'danger' : 'info' }}">
                                    {{ $class->student_count ?? 0 }} / {{ $class->capacity }}
                                    @if(($class->student_count ?? 0) >= $class->capacity)
                                        (FULL)
                                    @endif
                                </span>
                            </td>
                            <td>{{ $class->capacity }}</td>
                            <td>
                                @if($class->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('classes.show', $class->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('classes.edit', $class->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('classes.destroy', $class->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this class?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">No classes found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $classes->links() ?? '' }}
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#classesTable').DataTable({
            pageLength: 10,
            responsive: true,
            order: [[0, 'desc']]
        });
    });
</script>
@endpush
@endsection