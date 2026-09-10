@extends('layouts.app')

@section('title', 'Choose Exam & Class')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('scores.index') }}">Scores Entry</a></li>
    <li class="breadcrumb-item active">Choose Exam & Class</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-layer-group me-2"></i>Choose Exam & Class</h5>
        </div>
        <div class="card-body">
<form method="GET" action="{{ route('scores.choose.continue') }}" id="chooseForm">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Exam</label>
                        <select class="form-control" name="exam_id" id="examSelect" required>
                            <option value="">-- Select Exam --</option>
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}" {{ (request('exam_id') == $exam->id) ? 'selected' : '' }}>
                                    {{ $exam->name ?? ('Exam #' . $exam->id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Class</label>
                        <select class="form-control" name="class_id" id="classSelect" required>
                            <option value="">-- Select Class --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ (request('class_id') == $class->id) ? 'selected' : '' }}>
                                    {{ $class->full_class_name ?? ('Class #' . $class->id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 mt-2">
                        <input type="hidden" name="action" id="actionField" value="single-entry">
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-primary" onclick="setActionAndSubmit('single-entry')">
                                <i class="fas fa-pen me-1"></i> Single Entry
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="setActionAndSubmit('bulk-entry')">
                                <i class="fas fa-layer-group me-1"></i> Bulk Entry
                            </button>
                            <button type="button" class="btn btn-info" onclick="setActionAndSubmit('import')">
                                <i class="fas fa-file-import me-1"></i> Import Scores
                            </button>
                            <button type="button" class="btn btn-success" onclick="setActionAndSubmit('auto-fill')">
                                <i class="fas fa-magic me-1"></i> Auto Fill
                            </button>
                            <button type="button" class="btn btn-warning" onclick="setActionAndSubmit('calculate-positions')">
                                <i class="fas fa-chart-line me-1"></i> Calculate Positions
                            </button>
                            <button type="button" class="btn btn-dark" onclick="setActionAndSubmit('publish')">
                                <i class="fas fa-upload me-1"></i> Publish Results
                            </button>
                            <button type="button" class="btn btn-outline-primary" onclick="setActionAndSubmit('download-template')">
                                <i class="fas fa-download me-1"></i> Download Template
                            </button>
                            <button type="submit" class="btn btn-outline-secondary">
                                <i class="fas fa-route me-1"></i> Continue
                            </button>
                        </div>
                        <p class="text-muted mt-2 mb-0" style="font-size: 13px;">
                            Selecting an exam/class first ensures the menu links include valid <code>examId</code> and <code>classId</code>.
                        </p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function setActionAndSubmit(action) {
        document.getElementById('actionField').value = action;
        document.getElementById('chooseForm').submit();
    }
</script>
@endpush
@endsection

