@extends('layouts.app')

@section('title', 'CBT Exams Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">CBT Exams</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h4><i class="fas fa-laptop me-2"></i>CBT Exams Management</h4>
        </div>
        <div class="col-md-6 text-end">
            <a href="{{ route('cbt.exams.create') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Create New CBT Exam
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form method="GET" action="{{ route('cbt.exams.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search exams..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                        <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="test" {{ request('type') == 'test' ? 'selected' : '' }}>Test</option>
                        <option value="exam" {{ request('type') == 'exam' ? 'selected' : '' }}>Exam</option>
                        <option value="quiz" {{ request('type') == 'quiz' ? 'selected' : '' }}>Quiz</option>
                        <option value="assignment" {{ request('type') == 'assignment' ? 'selected' : '' }}>Assignment</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filter
                    </button>
                    <a href="{{ route('cbt.exams.index') }}" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Exams Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Title</th>
                            <th>Subject</th>
                            <th>Class</th>
                            <th>Type</th>
                            <th>Questions</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($exams as $exam)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td><span class="badge bg-secondary">{{ $exam->code }}</span></td>
                                <td>{{ Str::limit($exam->title, 30) }}</td>
                                <td>{{ $exam->subject->name ?? 'N/A' }}</td>
                                <td>{{ $exam->class->full_class_name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-info">
                                        {{ ucfirst($exam->type) }}
                                    </span>
                                </td>
                                <td>{{ $exam->questions->count() }}</td>
                                <td>
                                    <span class="badge bg-{{ $exam->status_badge }}">
                                        {{ ucfirst($exam->status) }}
                                    </span>
                                </td>
                                <td>{{ $exam->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('cbt.exams.show', $exam->id) }}" class="btn btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($exam->status == 'draft')
                                            <a href="{{ route('cbt.exams.edit', $exam->id) }}" class="btn btn-warning">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn btn-danger" onclick="deleteExam({{ $exam->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">No CBT exams found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $exams->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    function deleteExam(id) {
        if (confirm('Are you sure you want to delete this exam?')) {
            $.ajax({
                url: '/cbt/exams/' + id,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error deleting exam: ' + xhr.responseJSON.message);
                }
            });
        }
    }
</script>
@endpush
@endsection