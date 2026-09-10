@extends('layouts.app')

@section('title', 'Results')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Results</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h5 class="mb-2"><i class="fas fa-file-alt"></i> Results Overview</h5>
                <p class="mb-0 text-muted">Select an exam and class to view official student results.</p>
            </div>
            <a href="{{ route('results.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-redo"></i> Refresh
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-search"></i> Find Results</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('results.view') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Exam</label>
                        <select name="exam_id" class="form-control" required>
                            <option value="">-- Select Exam --</option>
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}">{{ $exam->term }} ({{ $exam->academic_year }})</option>
                            @endforeach
                        </select>
                        @error('exam_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Class</label>
                        <select name="class_id" class="form-control" required>
                            <option value="">-- Select Class --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->full_class_name }}</option>
                            @endforeach
                        </select>
                        @error('class_id')<div class="text-danger small">{{ $message }}</div>@enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-eye"></i> View Results
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-cogs"></i> Quick Actions</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @foreach($exams as $exam)
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h6 class="card-title mb-2">{{ $exam->term }} ({{ $exam->academic_year }})</h6>
                                <p class="card-text text-muted">Manage result uploads and exports for this exam.</p>
                                <a href="{{ route('results.bulk-upload', $exam->id) }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-file-upload"></i> Bulk Upload
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection
