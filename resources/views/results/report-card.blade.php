@extends('layouts.app')

@section('title', 'Report Card')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('results.index') }}">Results</a></li>
    <li class="breadcrumb-item active">Report Card</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h5 class="mb-2"><i class="fas fa-user-graduate"></i> Report Card for {{ $student->full_name }}</h5>
                <p class="mb-0 text-muted">{{ $exam->term }} ({{ $exam->academic_year }}) — {{ $student->class->full_class_name ?? 'N/A' }}</p>
            </div>
            <a href="{{ url()->previous() ?? route('results.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title">Summary</h6>
                    <p class="mb-1"><strong>Total Subjects:</strong> {{ $totalSubjects }}</p>
                    <p class="mb-1"><strong>Total Score:</strong> {{ number_format($totalScore, 2) }}</p>
                    <p class="mb-1"><strong>Average:</strong> {{ number_format($average, 2) }}%</p>
                    <p class="mb-1"><strong>Grade:</strong> {{ $grade->grade ?? ($grade ?? 'N/A') }}</p>
                    <p class="mb-0"><strong>Position:</strong> {{ $position }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-8 mb-3">
            <div class="card h-100">
                <div class="card-body">
                    <h6 class="card-title">Student Info</h6>
                    <p class="mb-1"><strong>Name:</strong> {{ $student->full_name }}</p>
                    <p class="mb-1"><strong>Admission No:</strong> {{ $student->admission_number ?? 'N/A' }}</p>
                    <p class="mb-1"><strong>Class:</strong> {{ $student->class->full_class_name ?? 'N/A' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-list"></i> Subject Scores</h5>
        </div>
        <div class="card-body">
            @if($results->isEmpty())
                <div class="alert alert-warning">No subject results available.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Score</th>
                                <th>Maximum</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $result)
                                <tr>
                                    <td>{{ $result->subject->name ?? 'N/A' }}</td>
                                    <td>{{ number_format($result->total_score, 2) }}</td>
                                    <td>{{ number_format($result->max_score ?? 100, 2) }}</td>
                                    <td>{{ \App\Models\GradeScale::getGrade($result->total_score)->grade ?? 'N/A' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
