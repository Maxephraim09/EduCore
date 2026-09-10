@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Student Dashboard</li>
@endsection

@section('content')
<div class="fade-in">
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-primary text-white h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small mb-1">Average Score</div>
                            <div class="h2 mb-0">{{ number_format($averageScore ?? 0, 1) }}%</div>
                            <div class="small mt-2">
                                <i class="fas fa-chart-line"></i> Overall Performance
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-success text-white h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small mb-1">Subjects</div>
                            <div class="h2 mb-0">{{ $subjectCount ?? 0 }}</div>
                            <div class="small mt-2">
                                <i class="fas fa-book"></i> Registered Subjects
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-book fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-warning text-white h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small mb-1">Upcoming Exams</div>
                            <div class="h2 mb-0">{{ $upcomingExams ?? 0 }}</div>
                            <div class="small mt-2">
                                <i class="fas fa-calendar-alt"></i> Scheduled
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-calendar-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-info text-white h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small mb-1">Fee Balance</div>
                            <div class="h3 mb-0">₦{{ number_format($balance ?? 0, 2) }}</div>
                            <div class="small mt-2">
                                <i class="fas fa-money-bill-wave"></i> Outstanding
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Results -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt text-primary me-2"></i>Recent Results
                    </h5>
                    <a href="{{ route('results.index') }}" class="btn btn-sm btn-outline-primary">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>CA1</th>
                                    <th>CA2</th>
                                    <th>CA3</th>
                                    <th>Exam</th>
                                    <th>Total</th>
                                    <th>Grade</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentResults ?? [] as $result)
                                <tr>
                                    <td>{{ $result->subject->name ?? 'N/A' }}</td>
                                    <td>{{ $result->ca1_score ?? '-' }}</td>
                                    <td>{{ $result->ca2_score ?? '-' }}</td>
                                    <td>{{ $result->ca3_score ?? '-' }}</td>
                                    <td>{{ $result->exam_score ?? '-' }}</td>
                                    <td><strong>{{ $result->total_score ?? 0 }}</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $result->grade == 'A' ? 'success' : ($result->grade == 'B' ? 'info' : ($result->grade == 'C' ? 'warning' : 'danger')) }}">
                                            {{ $result->grade ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No results available</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection