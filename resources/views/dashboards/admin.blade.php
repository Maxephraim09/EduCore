@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Admin Dashboard</li>
@endsection

@section('content')
<div class="fade-in">
    <!-- Stats Cards Row 1 -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-primary text-white h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small mb-1">Total Students</div>
                            <div class="h2 mb-0">{{ $totalStudents ?? 0 }}</div>
                            <div class="small mt-2">
                                <i class="fas fa-user-graduate"></i> Enrolled Students
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-user-graduate fa-2x"></i>
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
                            <div class="text-uppercase small mb-1">Total Teachers</div>
                            <div class="h2 mb-0">{{ $totalTeachers ?? 0 }}</div>
                            <div class="small mt-2">
                                <i class="fas fa-chalkboard-teacher"></i> Active Staff
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-chalkboard-teacher fa-2x"></i>
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
                            <div class="text-uppercase small mb-1">Total Revenue</div>
                            <div class="h2 mb-0">₦{{ number_format($totalRevenue ?? 0, 2) }}</div>
                            <div class="small mt-2">
                                <i class="fas fa-money-bill-wave"></i> All Time
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
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
                            <div class="text-uppercase small mb-1">Total Classes</div>
                            <div class="h2 mb-0">{{ $totalClasses ?? 0 }}</div>
                            <div class="small mt-2">
                                <i class="fas fa-school"></i> Active Classes
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-school fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards Row 2 -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-secondary text-white h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small mb-1">Question Papers</div>
                            <div class="h3 mb-0">{{ $totalQuestionPapers ?? 0 }}</div>
                            <div class="small mt-2">
                                <i class="fas fa-file-alt"></i> Total Created
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-teal text-white h-100 shadow-sm" style="background: linear-gradient(135deg, #20c997 0%, #20c997 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small mb-1">Published</div>
                            <div class="h3 mb-0">{{ $publishedPapers ?? 0 }}</div>
                            <div class="small mt-2">
                                <i class="fas fa-check-circle"></i> Ready for Use
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-dark text-white h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small mb-1">Total Subjects</div>
                            <div class="h3 mb-0">{{ $totalSubjects ?? 0 }}</div>
                            <div class="small mt-2">
                                <i class="fas fa-book"></i> Active Subjects
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
            <div class="card bg-danger text-white h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small mb-1">Total Assets</div>
                            <div class="h3 mb-0">₦{{ number_format($totalAssetValue ?? 0, 2) }}</div>
                            <div class="small mt-2">
                                <i class="fas fa-building"></i> Asset Value
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-building fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Students & Quick Actions -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-plus text-primary me-2"></i>Recent Students
                    </h5>
                    <a href="{{ route('students.index') }}" class="btn btn-sm btn-outline-primary">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Admission No</th>
                                    <th>Name</th>
                                    <th>Class</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentStudents ?? [] as $student)
                                <tr>
                                    <td>{{ $student->admission_number ?? 'N/A' }}</td>
                                    <td>{{ $student->full_name ?? 'N/A' }}</td>
                                    <td>{{ $student->class->full_class_name ?? 'N/A' }}</td>
                                    <td>{{ $student->created_at ? $student->created_at->format('d M Y') : 'N/A' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">
                                        <i class="fas fa-inbox me-2"></i>No recent students found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-tasks text-success me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('students.create') }}" class="btn btn-primary w-100">
                                <i class="fas fa-user-plus me-2"></i>Add Student
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('employees.create') }}" class="btn btn-success w-100">
                                <i class="fas fa-user-tie me-2"></i>Add Employee
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('classes.create') }}" class="btn btn-info w-100">
                                <i class="fas fa-chalkboard me-2"></i>Add Class
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('question-papers.manage-questions') }}" class="btn btn-warning w-100">
                                <i class="fas fa-file-alt me-2"></i>Manage Questions
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('question-papers.index') }}" class="btn btn-secondary w-100">
                                <i class="fas fa-list me-2"></i>Question Papers
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('reports.index') }}" class="btn btn-dark w-100">
                                <i class="fas fa-chart-line me-2"></i>View Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Question Papers -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt text-warning me-2"></i>Recent Question Papers
                    </h5>
                    <a href="{{ route('question-papers.index') }}" class="btn btn-sm btn-outline-primary">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Title</th>
                                    <th>Subject</th>
                                    <th>Class</th>
                                    <th>Term</th>
                                    <th>Questions</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentQuestionPapers ?? [] as $paper)
                                <tr>
                                    <td><span class="badge bg-dark">{{ $paper->code }}</span></td>
                                    <td>{{ Str::limit($paper->title, 25) }}</td>
                                    <td>{{ $paper->subject->name ?? 'N/A' }}</td>
                                    <td>{{ $paper->class->full_class_name ?? 'N/A' }}</td>
                                    <td><span class="badge bg-info">{{ $paper->term }}</span></td>
                                    <td>{{ $paper->questions_count ?? 0 }}</td>
                                    <td>
                                        <span class="badge bg-{{ $paper->is_published ? 'success' : 'warning' }}">
                                            {{ $paper->is_published ? 'Published' : 'Draft' }}
                                        </span>
                                    </td>
                                    <td>{{ $paper->created_at ? $paper->created_at->format('d M Y') : 'N/A' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center text-muted py-3">
                                        <i class="fas fa-inbox me-2"></i>No question papers found
                                    </td>
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