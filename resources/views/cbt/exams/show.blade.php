@extends('layouts.app')

@section('title', $exam->title)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('cbt.exams.index') }}">CBT Exams</a></li>
    <li class="breadcrumb-item active">{{ $exam->title }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Exam Header -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-laptop me-2"></i>{{ $exam->title }}</h5>
                    <span class="badge bg-{{ $exam->status_badge }} badge-lg">
                        {{ ucfirst($exam->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Exam Code</th>
                                    <td><strong>{{ $exam->code }}</strong></td>
                                </tr>
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
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="40%">Duration</th>
                                    <td>{{ $exam->duration_minutes }} minutes</td>
                                </tr>
                                <tr>
                                    <th>Total Marks</th>
                                    <td>{{ $exam->total_marks }}</td>
                                </tr>
                                <tr>
                                    <th>Passing Marks</th>
                                    <td>{{ $exam->passing_marks }}</td>
                                </tr>
                                <tr>
                                    <th>Questions</th>
                                    <td>{{ $exam->questions->count() }}</td>
                                </tr>
                                <tr>
                                    <th>Randomized</th>
                                    <td>{{ $exam->is_randomized ? 'Yes' : 'No' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($exam->description)
                        <div class="alert alert-info mt-2">
                            <strong>Description:</strong> {{ $exam->description }}
                        </div>
                    @endif

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="alert alert-success">
                                <strong>Start:</strong> {{ $exam->start_date ? $exam->start_date->format('d M Y, h:i A') : 'N/A' }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-danger">
                                <strong>End:</strong> {{ $exam->end_date ? $exam->end_date->format('d M Y, h:i A') : 'N/A' }}
                            </div>
                        </div>
                    </div>

                    @if($exam->created_by)
                        <div class="small text-muted">
                            Created by: {{ $exam->createdBy->name ?? 'Unknown' }} | 
                            {{ $exam->created_at->format('d M Y h:i A') }}
                        </div>
                    @endif
                    @if($exam->approved_by)
                        <div class="small text-muted">
                            Approved by: {{ $exam->approvedBy->name ?? 'Unknown' }} | 
                            {{ $exam->approved_at ? $exam->approved_at->format('d M Y h:i A') : 'N/A' }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-chart-bar me-2"></i>Exam Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Total Questions
                            <span class="badge bg-primary rounded-pill">{{ $stats['total_questions'] ?? 0 }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Total Attempts
                            <span class="badge bg-success rounded-pill">{{ $stats['total_attempts'] ?? 0 }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Average Score
                            <span class="badge bg-info rounded-pill">{{ number_format($stats['average_score'] ?? 0, 1) }}%</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            Pass Rate
                            <span class="badge bg-{{ ($stats['pass_rate'] ?? 0) >= 70 ? 'success' : (($stats['pass_rate'] ?? 0) >= 50 ? 'warning' : 'danger') }} rounded-pill">
                                {{ number_format($stats['pass_rate'] ?? 0, 1) }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Workflow Actions -->
            <div class="card mt-3">
                <div class="card-header">
                    <h5><i class="fas fa-tasks me-2"></i>Workflow Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($exam->status == 'draft')
                            <a href="{{ route('cbt.questions.create', $exam->id) }}" class="btn btn-info">
                                <i class="fas fa-plus-circle"></i> Add Questions
                            </a>
                            <button onclick="submitForApproval({{ $exam->id }})" class="btn btn-warning">
                                <i class="fas fa-paper-plane"></i> Submit for Approval
                            </button>
                            <a href="{{ route('cbt.exams.edit', $exam->id) }}" class="btn btn-secondary">
                                <i class="fas fa-edit"></i> Edit Exam
                            </a>
                        @endif

                        @if($exam->status == 'pending' && auth()->user()->hasRole('super-admin'))
                            <button onclick="approveExam({{ $exam->id }})" class="btn btn-success">
                                <i class="fas fa-check-circle"></i> Approve Exam
                            </button>
                            <button onclick="rejectExam({{ $exam->id }})" class="btn btn-danger">
                                <i class="fas fa-times-circle"></i> Reject Exam
                            </button>
                        @endif

                        @if($exam->status == 'approved')
                            <button onclick="publishExam({{ $exam->id }})" class="btn btn-primary">
                                <i class="fas fa-globe"></i> Publish Exam
                            </button>
                        @endif

                        @if($exam->status == 'published')
                            <button onclick="closeExam({{ $exam->id }})" class="btn btn-warning">
                                <i class="fas fa-lock"></i> Close Exam
                            </button>
                            <button onclick="autoConvert({{ $exam->id }})" class="btn btn-success">
                                <i class="fas fa-exchange-alt"></i> Auto-Convert to Term Exam
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Questions List -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-list me-2"></i>Questions</h5>
                    @if($exam->status == 'draft')
                        <a href="{{ route('cbt.questions.create', $exam->id) }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus"></i> Add Question
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if($exam->questions->isEmpty())
                        <div class="alert alert-info">No questions added yet.</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Question</th>
                                        <th>Type</th>
                                        <th>Marks</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($exam->questions as $index => $question)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ Str::limit($question->question, 60) }}</td>
                                            <td>
                                                <span class="badge bg-secondary">
                                                    {{ str_replace('_', ' ', ucfirst($question->type)) }}
                                                </span>
                                            </td>
                                            <td>{{ $question->marks }}</td>
                                            <td>
                                                @if($exam->status == 'draft')
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('cbt.questions.edit', $question->id) }}" class="btn btn-warning">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button onclick="deleteQuestion({{ $question->id }})" class="btn btn-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Locked</span>
                                                @endif
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

@push('scripts')
<script>
    function submitForApproval(id) {
        if (confirm('Are you sure you want to submit this exam for approval?')) {
            $.ajax({
                url: '/cbt/exams/' + id + '/submit-for-approval',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseJSON.message);
                }
            });
        }
    }

    function approveExam(id) {
        if (confirm('Are you sure you want to approve this exam?')) {
            $.ajax({
                url: '/cbt/exams/' + id + '/approve',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseJSON.message);
                }
            });
        }
    }

    function publishExam(id) {
        if (confirm('Are you sure you want to publish this exam?')) {
            $.ajax({
                url: '/cbt/exams/' + id + '/publish',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseJSON.message);
                }
            });
        }
    }

    function closeExam(id) {
        if (confirm('Are you sure you want to close this exam?')) {
            $.ajax({
                url: '/cbt/exams/' + id + '/close',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseJSON.message);
                }
            });
        }
    }

    function autoConvert(id) {
        if (confirm('This will convert this CBT exam to a regular term exam. Continue?')) {
            $.ajax({
                url: '/cbt/exams/' + id + '/auto-convert',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    window.location.href = response.redirect;
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseJSON.message);
                }
            });
        }
    }

    function deleteQuestion(id) {
        if (confirm('Are you sure you want to delete this question?')) {
            $.ajax({
                url: '/cbt/questions/' + id,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.responseJSON.message);
                }
            });
        }
    }
</script>
@endpush
@endsection