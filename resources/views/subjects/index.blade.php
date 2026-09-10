@extends('layouts.app')

@section('title', 'Subject Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Subjects</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="mb-0">Total Subjects</h6>
                    <h3 class="mt-2 mb-0">{{ $totalSubjects ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="mb-0">Core Subjects</h6>
                    <h3 class="mt-2 mb-0">{{ $coreSubjects ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="mb-0">Active Subjects</h6>
                    <h3 class="mt-2 mb-0">{{ $activeSubjects ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="mb-0">Assigned to Classes</h6>
                    <h3 class="mt-2 mb-0">{{ $subjects->filter(function($s) { return $s->classes->count() > 0; })->count() ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-book me-2"></i>Subjects</h5>
            <a href="{{ route('subjects.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add New Subject
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="subjectsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Subject Name</th>
                            <th>Code</th>
                            <th>Department</th>
                            <th>Teacher</th>
                            <th>Credit Hours</th>
                            <th>Type</th>
                            <th>Classes</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subjects ?? [] as $subject)
                        <tr>
                            <td>{{ $subject->id }}</td>
                            <td><strong>{{ $subject->name }}</strong></td>
                            <td>{{ $subject->code }}</td>
                            <td>{{ $subject->department ?? 'N/A' }}</td>
                            <td>{{ $subject->teacher->full_name ?? 'Not Assigned' }}</td>
                            <td>{{ $subject->credit_hours }}</td>
                            <td>
                                @if($subject->is_core)
                                    <span class="badge bg-danger">Core</span>
                                @elseif($subject->is_elective)
                                    <span class="badge bg-secondary">Elective</span>
                                @else
                                    <span class="badge bg-info">Standard</span>
                                @endif
                            </td>
                            <td><span class="badge bg-info">{{ $subject->classes->count() }}</span></td>
                            <td>
                                @if($subject->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('subjects.show', $subject->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('subjects.edit', $subject->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('subjects.destroy', $subject->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">No subjects found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $subjects->links() ?? '' }}
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
        $('#subjectsTable').DataTable({
            pageLength: 10,
            responsive: true,
            order: [[0, 'desc']]
        });
    });
</script>
@endpush
@endsection