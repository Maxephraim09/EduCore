@extends('layouts.app')

@section('title', 'Question Papers')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Question Papers</li>
@endsection

@section('styles')
<style>
    .stats-card {
        background: white;
        border-radius: 12px;
        padding: 16px 20px;
        border: 1px solid #f1f3f5;
        transition: all 0.3s;
        height: 100%;
    }
    .stats-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.06);
    }
    .stats-card .number {
        font-size: 24px;
        font-weight: 700;
        color: #1b1b18;
    }
    .stats-card .label {
        font-size: 13px;
        color: #6b7280;
        font-weight: 500;
    }
    .stats-card .icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    .stats-card .icon.blue { background: #dbeafe; color: #1e40af; }
    .stats-card .icon.green { background: #d1fae5; color: #065f46; }
    .stats-card .icon.orange { background: #fef3c7; color: #92400e; }
    .stats-card .icon.purple { background: #ede9fe; color: #6d28d9; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-md-6">
            <h4><i class="fas fa-file-alt me-2" style="color: #667eea;"></i>Question Papers</h4>
            <p class="text-muted mb-0">Manage all subject question papers</p>
        </div>
        <div class="col-md-6 text-end mt-3 mt-md-0">
            <a href="{{ route('question-papers.manage-questions') }}" class="btn btn-primary">
                <i class="fas fa-plus-circle me-2"></i> Create New Question Paper
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="number">{{ $totalPapers ?? 0 }}</div>
                        <div class="label">Total Papers</div>
                    </div>
                    <div class="icon blue">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="number">{{ $publishedPapers ?? 0 }}</div>
                        <div class="label">Published</div>
                    </div>
                    <div class="icon green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="number">{{ $draftPapers ?? 0 }}</div>
                        <div class="label">Drafts</div>
                    </div>
                    <div class="icon orange">
                        <i class="fas fa-pen"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="number">{{ $totalQuestions ?? 0 }}</div>
                        <div class="label">Total Questions</div>
                    </div>
                    <div class="icon purple">
                        <i class="fas fa-question-circle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Question Papers Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="8%">Code</th>
                            <th width="20%">Title</th>
                            <th width="15%">Subject</th>
                            <th width="12%">Class</th>
                            <th width="10%">Term</th>
                            <th width="8%">Questions</th>
                            <th width="10%">Status</th>
                            <th width="10%">Date</th>
                            <th width="12%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($questionPapers as $paper)
                        <tr>
                            <td><span class="badge bg-dark">{{ $paper->code }}</span></td>
                            <td>{{ Str::limit($paper->title, 30) }}</td>
                            <td>{{ $paper->subject->name ?? 'N/A' }}</td>
                            <td>{{ $paper->class->full_class_name ?? 'N/A' }}</td>
                            <td><span class="badge bg-info">{{ $paper->term }}</span></td>
                            <td><span class="badge bg-primary">{{ $paper->questions_count ?? 0 }}</span></td>
                            <td>
                                <span class="badge bg-{{ $paper->is_published ? 'success' : 'warning' }}">
                                    {{ $paper->is_published ? 'Published' : 'Draft' }}
                                </span>
                            </td>
                            <td>{{ $paper->created_at ? $paper->created_at->format('d M Y') : 'N/A' }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('question-papers.show', $paper->id) }}" class="btn btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('question-papers.manage-questions', ['academic_year' => $paper->academic_year, 'term' => $paper->term, 'class_id' => $paper->class_id, 'subject_id' => $paper->subject_id]) }}" class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($paper->is_published)
                                        <button onclick="unpublishPaper({{ $paper->id }})" class="btn btn-secondary" title="Unpublish">
                                            <i class="fas fa-times-circle"></i>
                                        </button>
                                    @else
                                        <button onclick="publishPaper({{ $paper->id }})" class="btn btn-success" title="Publish">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    @endif
                                    <button onclick="deletePaper({{ $paper->id }})" class="btn btn-danger" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted">No question papers found.</p>
                                <a href="{{ route('question-papers.manage-questions') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus-circle"></i> Create Your First Question Paper
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-between align-items-center p-3 border-top">
                <div>
                    <small class="text-muted">
                        Showing {{ $questionPapers->firstItem() ?? 0 }} to {{ $questionPapers->lastItem() ?? 0 }} of {{ $questionPapers->total() }} papers
                    </small>
                </div>
                <div>
                    {{ $questionPapers->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function publishPaper(id) {
        if (confirm('Are you sure you want to publish this question paper?')) {
            $.ajax({
                url: '/question-papers/' + id + '/publish',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function() { location.reload(); },
                error: function(xhr) {
                    alert('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));
                }
            });
        }
    }

    function unpublishPaper(id) {
        if (confirm('Are you sure you want to unpublish this question paper?')) {
            $.ajax({
                url: '/question-papers/' + id + '/unpublish',
                method: 'POST',
                data: { _token: '{{ csrf_token() }}' },
                success: function() { location.reload(); },
                error: function(xhr) {
                    alert('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));
                }
            });
        }
    }

    function deletePaper(id) {
        if (confirm('Are you sure you want to delete this question paper? This action cannot be undone.')) {
            $.ajax({
                url: '/question-papers/' + id,
                method: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function() { location.reload(); },
                error: function(xhr) {
                    alert('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));
                }
            });
        }
    }
</script>
@endpush
@endsection