@extends('layouts.app')

@section('title', 'Subject Statistics')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('subjects.index') }}">Subjects</a></li>
    <li class="breadcrumb-item active">Statistics</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card bg-primary text-white h-100">
                <div class="card-body">
                    <h6 class="mb-2">Total Subjects</h6>
                    <h2 class="mb-0">{{ $subjects->total() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-success text-white h-100">
                <div class="card-body">
                    <h6 class="mb-2">Classes Assigned</h6>
                    <h2 class="mb-0">{{ $subjects->sum('classes_count') }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-info text-white h-100">
                <div class="card-body">
                    <h6 class="mb-2">Students Assigned</h6>
                    <h2 class="mb-0">{{ $subjects->sum('students_count') }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card bg-warning text-white h-100">
                <div class="card-body">
                    <h6 class="mb-2">Exams Covered</h6>
                    <h2 class="mb-0">{{ $subjects->sum('exams_count') }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Subject Statistics</h5>
            <a href="{{ route('subjects.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Back to Subjects
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Subject</th>
                            <th>Code</th>
                            <th>Department</th>
                            <th>Classes</th>
                            <th>Students</th>
                            <th>Exams</th>
                            <th>Teacher</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subjects as $subject)
                            <tr>
                                <td>{{ $subject->id }}</td>
                                <td>{{ $subject->name }}</td>
                                <td>{{ $subject->code }}</td>
                                <td>{{ $subject->department ?? 'N/A' }}</td>
                                <td><span class="badge bg-info text-dark">{{ $subject->classes_count }}</span></td>
                                <td><span class="badge bg-success">{{ $subject->students_count }}</span></td>
                                <td><span class="badge bg-warning text-dark">{{ $subject->exams_count }}</span></td>
                                <td>{{ optional($subject->teacher)->full_name ?? 'Unassigned' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">No subject statistics available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $subjects->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
