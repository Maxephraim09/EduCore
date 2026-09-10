@extends('layouts.app')

@section('title', 'Results')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('results.index') }}">Results</a></li>
    <li class="breadcrumb-item active">View Results</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h5 class="mb-2"><i class="fas fa-chart-bar"></i> {{ $exam->term }} Results for {{ $class->full_class_name }}</h5>
                <p class="mb-0 text-muted">Review student performance and generate report cards.</p>
            </div>
            <div class="btn-group">
                <a href="{{ route('results.index') }}" class="btn btn-outline-secondary">Back to Search</a>
                <a href="{{ route('results.bulk-upload', $exam->id) }}" class="btn btn-success">Bulk Upload</a>
            </div>
        </div>
    </div>

    @if($results->isEmpty())
        <div class="alert alert-warning">No results found for the selected exam and class.</div>
    @else
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Total Score</th>
                                <th>Average</th>
                                <th>Grade</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($results as $studentId => $studentResults)
                                @php
                                    $student = $studentResults->first()->student;
                                    $totalScore = $studentResults->sum('total_score');
                                    $subjectCount = $studentResults->count();
                                    $average = $subjectCount > 0 ? $totalScore / $subjectCount : 0;
                                    $grade = \App\Models\GradeScale::getGrade($average);
                                @endphp
                                <tr>
                                    <td>{{ $student->full_name }}</td>
                                    <td>{{ number_format($totalScore, 2) }}</td>
                                    <td>{{ number_format($average, 2) }}%</td>
                                    <td>{{ $grade->grade ?? ($grade ?? 'N/A') }}</td>
                                    <td>
                                        <a href="{{ route('results.report-card', ['examId' => $exam->id, 'studentId' => $studentId]) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-file-alt"></i> Report Card
                                        </a>
                                        <a href="{{ route('results.export', ['examId' => $exam->id, 'classId' => $class->id]) }}" class="btn btn-sm btn-secondary">
                                            <i class="fas fa-file-export"></i> Export
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
