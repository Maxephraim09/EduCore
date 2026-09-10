@extends('layouts.app')

@section('title', 'CBT Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">CBT Dashboard</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5>Available Exams</h5>
                    <h2>{{ $availableExams }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Completed</h5>
                    <h2>{{ $completedExams }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5>In Progress</h5>
                    <h2>{{ $inProgressExams }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5>Average Score</h5>
                    <h2>{{ number_format($averageScore, 1) }}%</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Available Exams -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-list me-2"></i>Available Exams</h5>
                </div>
                <div class="card-body">
                    @if($availableExamsList->isEmpty())
                        <div class="alert alert-info">No exams available at the moment.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Exam</th>
                                        <th>Subject</th>
                                        <th>Type</th>
                                        <th>Duration</th>
                                        <th>Questions</th>
                                        <th>Deadline</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($availableExamsList as $exam)
                                        <tr>
                                            <td>{{ $exam->title }}</td>
                                            <td>{{ $exam->subject->name ?? 'N/A' }}</td>
                                            <td><span class="badge bg-info">{{ ucfirst($exam->type) }}</span></td>
                                            <td>{{ $exam->duration_minutes }} min</td>
                                            <td>{{ $exam->questions->count() }}</td>
                                            <td>{{ $exam->end_date ? $exam->end_date->format('d M Y h:i A') : 'N/A' }}</td>
                                            <td>
                                                <a href="{{ route('cbt.student.take-exam', $exam->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-pencil-alt"></i> Take Exam
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection