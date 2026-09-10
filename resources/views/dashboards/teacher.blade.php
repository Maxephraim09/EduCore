@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Teacher Dashboard</li>
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
                            <div class="text-uppercase small mb-1">My Classes</div>
                            <div class="h2 mb-0">{{ $myClasses ?? 0 }}</div>
                            <div class="small mt-2">
                                <i class="fas fa-chalkboard"></i> Assigned Classes
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-chalkboard fa-2x"></i>
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
                            <div class="text-uppercase small mb-1">My Students</div>
                            <div class="h2 mb-0">{{ $myStudents ?? 0 }}</div>
                            <div class="small mt-2">
                                <i class="fas fa-user-graduate"></i> Total Students
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
            <div class="card bg-warning text-white h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small mb-1">Pending Grading</div>
                            <div class="h2 mb-0">{{ $pendingGrading ?? 0 }}</div>
                            <div class="small mt-2">
                                <i class="fas fa-pen"></i> Needs Grading
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-pen fa-2x"></i>
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
    </div>
    
    <!-- My Classes List -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-chalkboard text-primary me-2"></i>My Classes
                    </h5>
                    <span class="small text-muted">Assigned only</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Class</th>
                                    <th>Students</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($myClassesList ?? [] as $class)
                                <tr>
                                    <td>{{ $class->full_class_name }}</td>
                                    <td>{{ $class->students_count ?? 0 }}</td>
                                    <td>
                                        <a href="{{ route('classes.show', $class->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('scores.single-entry') }}?class_id={{ $class->id }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">No classes assigned</td>
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
                            <a href="{{ route('scores.single-entry') }}" class="btn btn-primary w-100">
                                <i class="fas fa-pen me-2"></i>Enter Scores
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('scores.bulk-entry') }}" class="btn btn-success w-100">
                                <i class="fas fa-table me-2"></i>Bulk Entry
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('scores.index') }}" class="btn btn-info w-100">
                                <i class="fas fa-chart-line me-2"></i>View Scores
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('results.index') }}" class="btn btn-warning w-100">
                                <i class="fas fa-file-alt me-2"></i>View Results
                            </a>
                        </div>
                        @if(app('router')->has('fee-payments.history'))
                        <div class="col-6">
                            <a href="{{ route('fee-payments.history') }}" class="btn btn-secondary w-100">
                                <i class="fas fa-wallet me-2"></i>Fee Status
                            </a>
                        </div>
                        @endif
                        @if(app('router')->has('expenses.create'))
                        <div class="col-6">
                            <a href="{{ route('expenses.create') }}" class="btn btn-dark w-100">
                                <i class="fas fa-file-invoice-dollar me-2"></i>Expense Request
                            </a>
                        </div>
                        @endif
                        @if(app('router')->has('salaries.index'))
                        <div class="col-6">
                            <a href="{{ route('salaries.index') }}" class="btn btn-secondary w-100">
                                <i class="fas fa-money-bill-wave me-2"></i>My Salary
                            </a>
                        </div>
                        @endif
                        @if(app('router')->has('staff-loans.index'))
                        <div class="col-6">
                            <a href="{{ route('staff-loans.index') }}" class="btn btn-warning w-100">
                                <i class="fas fa-hand-holding-usd me-2"></i>Loan Status
                            </a>
                        </div>
                        @endif
                        @if(app('router')->has('staff-overdrafts.index'))
                        <div class="col-6">
                            <a href="{{ route('staff-overdrafts.index') }}" class="btn btn-info w-100">
                                <i class="fas fa-university me-2"></i>Overdraft Status
                            </a>
                        </div>
                        @endif
                        @if(app('router')->has('admin.blog.create'))
                        <div class="col-6">
                            <a href="{{ route('admin.blog.create') }}" class="btn btn-dark w-100">
                                <i class="fas fa-blog me-2"></i>Submit Blog Post
                            </a>
                        </div>
                        @endif
                        @if(app('router')->has('notifications.index'))
                        <div class="col-6">
                            <a href="{{ route('notifications.index') }}" class="btn btn-primary w-100">
                                <i class="fas fa-bullhorn me-2"></i>Announcements
                            </a>
                        </div>
                        @endif
                        @if(app('router')->has('academic-calendar.index'))
                        <div class="col-6">
                            <a href="{{ route('academic-calendar.index') }}" class="btn btn-success w-100">
                                <i class="fas fa-calendar-alt me-2"></i>Academic Calendar
                            </a>
                        </div>
                        @endif
                        @if(app('router')->has('exam-timetable.index'))
                        <div class="col-6">
                            <a href="{{ route('exam-timetable.index') }}" class="btn btn-primary w-100">
                                <i class="fas fa-book-open me-2"></i>Exam Timetable
                            </a>
                        </div>
                        @endif
                        @if(app('router')->has('timetables.index'))
                        <div class="col-6">
                            <a href="{{ route('timetables.index') }}" class="btn btn-success w-100">
                                <i class="fas fa-clock me-2"></i>Academic Timetable
                            </a>
                        </div>
                        @endif
                        @if(app('router')->has('lesson-timetables.index'))
                        <div class="col-6">
                            <a href="{{ route('lesson-timetables.index') }}" class="btn btn-dark w-100">
                                <i class="fas fa-chalkboard-teacher me-2"></i>Lesson Timetable
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection