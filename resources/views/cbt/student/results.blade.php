@extends('layouts.app')

@section('title', 'Exam Results - ' . $exam->title)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('cbt.student.dashboard') }}">CBT Dashboard</a></li>
    <li class="breadcrumb-item active">{{ $exam->title }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-chart-bar me-2"></i>Results for {{ $exam->title }}</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h6>Total Marks</h6>
                                    <h2>{{ $attempt->total_marks ?? 0 }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h6>Score</h6>
                                    <h2>{{ $attempt->score ?? 0 }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6>Percentage</h6>
                                    <h2>{{ number_format($attempt->percentage ?? 0, 1) }}%</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-{{ ($attempt->percentage ?? 0) >= 70 ? 'success' : (($attempt->percentage ?? 0) >= 50 ? 'warning' : 'danger') }} text-white">
                                <div class="card-body">
                                    <h6>Grade</h6>
                                    <h2>{{ $attempt->grade ?? 'N/A' }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Result Details -->
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Status</th>
                                    <td>
                                        @if(($attempt->percentage ?? 0) >= 40)
                                            <span class="badge bg-success">Passed</span>
                                        @else
                                            <span class="badge bg-danger">Failed</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Correct Answers</th>
                                    <td>{{ $attempt->correct_answers ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th>Wrong Answers</th>
                                    <td>{{ $attempt->wrong_answers ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th>Skipped Questions</th>
                                    <td>{{ $attempt->skipped_questions ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th>Total Questions</th>
                                    <td>{{ $attempt->total_questions ?? 0 }}</td>
                                </tr>
                                <tr>
                                    <th>Time Taken</th>
                                    <td>
                                        @php
                                            $minutes = floor(($attempt->duration_used ?? 0) / 60);
                                            $seconds = ($attempt->duration_used ?? 0) % 60;
                                        @endphp
                                        {{ $minutes }} min {{ $seconds }} sec
                                    </td>
                                </tr>
                                <tr>
                                    <th>Submitted At</th>
                                    <td>{{ $attempt->submitted_at ? $attempt->submitted_at->format('d M Y h:i A') : 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($attempt->remarks)
                        <div class="alert alert-info">
                            <strong>Remarks:</strong> {{ $attempt->remarks }}
                        </div>
                    @endif

                    <div class="mt-3">
                        <a href="{{ route('cbt.student.dashboard') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Back to Dashboard
                        </a>
                        <button onclick="window.print()" class="btn btn-primary">
                            <i class="fas fa-print"></i> Print Results
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle me-2"></i>Exam Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Subject</th>
                            <td>{{ $exam->subject->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Class</th>
                            <td>{{ $exam->class->full_class_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Type</th>
                            <td><span class="badge bg-info">{{ ucfirst($exam->type) }}</span></td>
                        </tr>
                        <tr>
                            <th>Term</th>
                            <td>{{ $exam->term }}</td>
                        </tr>
                        <tr>
                            <th>Duration</th>
                            <td>{{ $exam->duration_minutes }} minutes</td>
                        </tr>
                        <tr>
                            <th>Passing Marks</th>
                            <td>{{ $exam->passing_marks }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5><i class="fas fa-trophy me-2"></i>Performance Summary</h5>
                </div>
                <div class="card-body">
                    <div class="progress mb-2" style="height: 30px;">
                        <div class="progress-bar bg-{{ ($attempt->percentage ?? 0) >= 70 ? 'success' : (($attempt->percentage ?? 0) >= 50 ? 'warning' : 'danger') }}" 
                             role="progressbar" 
                             style="width: {{ $attempt->percentage ?? 0 }}%;" 
                             aria-valuenow="{{ $attempt->percentage ?? 0 }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            {{ number_format($attempt->percentage ?? 0, 1) }}%
                        </div>
                    </div>
                    <div class="small text-muted">
                        <span class="badge bg-success">● Correct</span>
                        <span class="badge bg-danger">● Wrong</span>
                        <span class="badge bg-secondary">● Skipped</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection