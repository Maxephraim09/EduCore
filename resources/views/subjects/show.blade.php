@extends('layouts.app')

@section('title', $subject->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('subjects.index') }}">Subjects</a></li>
    <li class="breadcrumb-item active">{{ $subject->name }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Subject Overview -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-book me-2"></i>{{ $subject->name }}</h5>
                    <div>
                        <a href="{{ route('subjects.edit', $subject->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> Edit
                        </a>
                        <a href="{{ route('subjects.classes', $subject->id) }}" class="btn btn-info btn-sm">
                            <i class="fas fa-school"></i> View Classes
                        </a>
                        <a href="{{ route('subjects.teachers', $subject->id) }}" class="btn btn-success btn-sm">
                            <i class="fas fa-chalkboard-teacher"></i> View Teachers
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Subject Name</th>
                                    <td><strong>{{ $subject->name }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Subject Code</th>
                                    <td>{{ $subject->code }}</td>
                                </tr>
                                <tr>
                                    <th>Department</th>
                                    <td>{{ $subject->department ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Subject Type</th>
                                    <td>
                                        @if($subject->is_core)
                                            <span class="badge bg-danger">Core</span>
                                        @elseif($subject->is_elective)
                                            <span class="badge bg-secondary">Elective</span>
                                        @else
                                            <span class="badge bg-info">Standard</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Credit Hours</th>
                                    <td>{{ $subject->credit_hours }}</td>
                                </tr>
                                <tr>
                                    <th>Teacher</th>
                                    <td>{{ $subject->teacher->full_name ?? 'Not Assigned' }}</td>
                                </tr>
                                <tr>
                                    <th>Status</th>
                                    <td>
                                        @if($subject->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Assigned To</th>
                                    <td>{{ $subject->classes->count() }} Classes</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    @if($subject->description)
                        <div class="alert alert-info mt-2">
                            <strong>Description:</strong> {{ $subject->description }}
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
                            Classes Offering
                            <span class="badge bg-primary rounded-pill">{{ $stats['total_classes'] ?? 0 }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Students Enrolled
                            <span class="badge bg-success rounded-pill">{{ $stats['total_students'] ?? 0 }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Teachers Assigned
                            <span class="badge bg-info rounded-pill">{{ $stats['total_teachers'] ?? 0 }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Exams Conducted
                            <span class="badge bg-warning rounded-pill">{{ $stats['total_exams'] ?? 0 }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Average Score
                            <span class="badge bg-primary rounded-pill">{{ $stats['average_score'] ?? 0 }}%</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Pass Rate
                            <span class="badge bg-{{ isset($stats['pass_rate']) && $stats['pass_rate'] >= 70 ? 'success' : (isset($stats['pass_rate']) && $stats['pass_rate'] >= 50 ? 'warning' : 'danger') }} rounded-pill">
                                {{ $stats['pass_rate'] ?? 0 }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5><i class="fas fa-actions me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" onclick="window.location.href='{{ route('subjects.edit', $subject->id) }}'">
                            <i class="fas fa-edit"></i> Edit Subject
                        </button>
                        <button class="btn btn-info" onclick="window.location.href='{{ route('subjects.assign-class', $subject->id) }}'">
                            <i class="fas fa-school"></i> Assign to Class
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
                    @if(isset($topStudents) && $topStudents->isEmpty())
                        <div class="alert alert-info">No results available yet.</div>
                    @elseif(isset($topStudents))
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Rank</th>
                                        <th>Student</th>
                                        <th>Score</th>
                                        <th>Grade</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topStudents as $index => $result)
                                    <tr>
                                        <td>
                                            @if($index == 0) 🥇
                                            @elseif($index == 1) 🥈
                                            @elseif($index == 2) 🥉
                                            @else #{{ $index + 1 }}
                                            @endif
                                        </td>
                                        <td>{{ $result->student->full_name ?? 'Unknown' }}</td>
                                        <td>{{ number_format($result->total_score, 1) }}%</td>
                                        <td>
                                            @php
                                                $grade = $result->total_score >= 70 ? 'A' : ($result->total_score >= 60 ? 'B' : ($result->total_score >= 50 ? 'C' : ($result->total_score >= 45 ? 'D' : 'F')));
                                            @endphp
                                            <span class="badge bg-{{ $grade == 'A' ? 'success' : ($grade == 'B' ? 'info' : ($grade == 'C' ? 'warning' : 'danger')) }}">
                                                {{ $grade }}
                                            </span>
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

        <!-- Class Performance -->
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-chart-line me-2"></i>Class Performance</h5>
                </div>
                <div class="card-body">
                    @if(isset($classPerformance) && $classPerformance->isEmpty())
                        <div class="alert alert-info">No results available yet.</div>
                    @elseif(isset($classPerformance))
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Class</th>
                                        <th>Students</th>
                                        <th>Average</th>
                                        <th>Highest</th>
                                        <th>Lowest</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($classPerformance as $data)
                                    <tr>
                                        <td>{{ $data['class']->full_class_name }}</td>
                                        <td>{{ $data['students'] }}</td>
                                        <td>{{ number_format($data['average'], 1) }}%</td>
                                        <td>{{ number_format($data['highest'], 1) }}%</td>
                                        <td>{{ number_format($data['lowest'], 1) }}%</td>
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
                    <a href="{{ route('subjects.exams', $subject->id) }}" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    @if(isset($recentExams) && $recentExams->isEmpty())
                        <div class="alert alert-info">No exams scheduled for this subject.</div>
                    @elseif(isset($recentExams))
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Exam Name</th>
                                        <th>Term</th>
                                        <th>Date</th>
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