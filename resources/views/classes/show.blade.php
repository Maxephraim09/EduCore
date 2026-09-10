@extends('layouts.app')

@section('title', $class->full_class_name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('classes.index') }}">Classes</a></li>
    <li class="breadcrumb-item active">{{ $class->full_class_name }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Class Overview -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-school me-2"></i>{{ $class->full_class_name }}</h5>
                    <div>
                        <a href="{{ route('classes.edit', $class->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('classes.students', $class->id) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-user-graduate"></i> View Students
                        </a>
                        <a href="{{ route('classes.subjects', $class->id) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-book"></i> View Subjects
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Class Name</th>
                                    <td><strong>{{ $class->full_class_name }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Class Code</th>
                                    <td>{{ $class->code }}</td>
                                </tr>
                                <tr>
                                    <th>Section</th>
                                    <td>{{ $class->section ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Class Teacher</th>
                                    <td>{{ $class->classTeacher->full_name ?? 'Not Assigned' }}</td>
                                </tr>
                                <tr>
                                    <th>Category</th>
                                    <td>{{ optional($class->category)->name ?? 'Uncategorized' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if($class->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Total Students</th>
                                    <td><strong>{{ $stats['total_students'] }}</strong> / {{ $class->capacity }}</td>
                                </tr>
                                <tr>
                                    <th>Gender Distribution</th>
                                    <td>
                                        <span class="text-primary">♂ {{ $stats['male_students'] }}</span> | 
                                        <span class="text-danger">♀ {{ $stats['female_students'] }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Subjects</th>
                                    <td>{{ $stats['total_subjects'] }} ({{ $stats['core_subjects'] }} Core, {{ $stats['elective_subjects'] }} Elective)</td>
                                </tr>
                                <tr>
                                    <th>Capacity Used</th>
                                    <td>
                                        <div class="progress">
                                            <div class="progress-bar bg-{{ $stats['capacity_used'] > 90 ? 'danger' : ($stats['capacity_used'] > 70 ? 'warning' : 'success') }}" 
                                                 role="progressbar" 
                                                 style="width: {{ $stats['capacity_used'] }}%">
                                                {{ $stats['capacity_used'] }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Average Age</th>
                                    <td>{{ number_format($stats['average_age'], 1) }} years</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @if($class->description)
                        <div class="alert alert-info mt-2">
                            <strong>Description:</strong> {{ $class->description }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-chart-bar me-2"></i>Quick Stats</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Current Enrolment
                            <span class="badge bg-primary rounded-pill">{{ $stats['total_students'] }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Available Slots
                            <span class="badge bg-success rounded-pill">{{ $class->capacity - $stats['total_students'] }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Subjects Offered
                            <span class="badge bg-info rounded-pill">{{ $stats['total_subjects'] }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Recent Exams
                            <span class="badge bg-warning rounded-pill">{{ $recentExams->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5><i class="fas fa-user-graduate me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" onclick="window.location.href='{{ route('students.create') }}?class_id={{ $class->id }}'">
                            <i class="fas fa-user-plus"></i> Add Student
                        </button>
                        <button class="btn btn-success" onclick="window.location.href='{{ route('classes.assign-students', $class->id) }}'">
                            <i class="fas fa-users"></i> Assign Students
                        </button>
                        <button class="btn btn-info" onclick="window.location.href='{{ route('classes.assign-subject', $class->id) }}'">
                            <i class="fas fa-book"></i> Assign Subject
                        </button>
                        <button class="btn btn-warning" onclick="window.location.href='{{ route('classes.exams', $class->id) }}'">
                            <i class="fas fa-file-alt"></i> View Exams
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Students -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-trophy me-2"></i>Top Performing Students</h5>
                </div>
                <div class="card-body">
                    @if($topStudents->isEmpty())
                        <div class="alert alert-info">No results available yet.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Student</th>
                                        <th>Total Score</th>
                                        <th>Average</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topStudents as $index => $data)
                                    <tr>
                                        <td>
                                            @if($index == 0) 🥇
                                            @elseif($index == 1) 🥈
                                            @elseif($index == 2) 🥉
                                            @else #{{ $index + 1 }}
                                            @endif
                                        </td>
                                        <td>{{ $data['student']->full_name }}</td>
                                        <td>{{ $data['total_score'] }}</td>
                                        <td>{{ number_format($data['average'], 1) }}%</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Subject Performance -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-chart-line me-2"></i>Subject Performance</h5>
                </div>
                <div class="card-body">
                    @if($subjectPerformance->isEmpty())
                        <div class="alert alert-info">No results available yet.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Average</th>
                                        <th>Pass Rate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subjectPerformance as $data)
                                    <tr>
                                        <td>{{ $data['subject']->name }}</td>
                                        <td>{{ number_format($data['average'], 1) }}%</td>
                                        <td>
                                            @php
                                                $passRate = $data['pass_count'] > 0 ? ($data['pass_count'] / $data['total_students']) * 100 : 0;
                                            @endphp
                                            <div class="progress">
                                                <div class="progress-bar bg-{{ $passRate >= 70 ? 'success' : ($passRate >= 50 ? 'warning' : 'danger') }}" 
                                                     style="width: {{ $passRate }}%">
                                                    {{ number_format($passRate, 1) }}%
                                                </div>
                                            </div>
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

    <!-- Recent Exams -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-file-alt me-2"></i>Recent Exams</h5>
                    <a href="{{ route('classes.exams', $class->id) }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    @if($recentExams->isEmpty())
                        <div class="alert alert-info">No exams scheduled for this class.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Exam Name</th>
                                        <th>Term</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentExams as $exam)
                                    <tr>
                                        <td>{{ $exam->name }}</td>
                                        <td>{{ $exam->term }}</td>
                                        <td>{{ date('d M Y', strtotime($exam->start_date)) }}</td>
                                        <td>{{ date('d M Y', strtotime($exam->end_date)) }}</td>
                                        <td>
                                            @if($exam->is_published)
                                                <span class="badge bg-success">Published</span>
                                            @else
                                                <span class="badge bg-warning">Draft</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('exams.show', $exam->id) }}" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('classes.results', [$class->id, 'exam' => $exam->id]) }}" class="btn btn-sm btn-success">
                                                <i class="fas fa-file-alt"></i> Results
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