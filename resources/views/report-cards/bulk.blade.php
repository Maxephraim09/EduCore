@extends('layouts.app')

@section('title', 'Bulk Report Card')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('report-cards.index') }}">Report Cards</a></li>
    <li class="breadcrumb-item active">Bulk Report Card</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-layer-group"></i> Bulk Report Card</h5>
            <a href="{{ route('report-cards.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-user-graduate"></i> Single Report Card
            </a>
        </div>
        <div class="card-body">
            <p class="text-muted">Generate report cards for an entire class and term in bulk.</p>
            <form id="bulkReportCardForm" class="row g-3" onsubmit="performBulkReportCard(event)">
                <div class="col-md-5">
                    <label class="form-label">Term / Exam</label>
                    <select id="bulkExamId" class="form-control" required>
                        <option value="">-- Select Term --</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->id }}">{{ $exam->term }} ({{ $exam->academic_year }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Class</label>
                    <select id="bulkClassId" class="form-control" required>
                        <option value="">-- Select Class --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->full_class_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-print"></i> Print Bulk
                    </button>
                </div>
            </form>
            <div class="alert alert-info mt-4">
                <strong>Note:</strong> This page generates a single PDF containing all active students for the selected class and term.
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function performBulkReportCard(event) {
        event.preventDefault();
        var examId = document.getElementById('bulkExamId').value;
        var classId = document.getElementById('bulkClassId').value;

        if (!examId || !classId) {
            alert('Please select both term and class for bulk report card printing');
            return;
        }

        window.location.href = @json(route('report-cards.bulk', ['examId' => '__EXAM__', 'classId' => '__CLASS__']))
            .replace('__EXAM__', examId).replace('__CLASS__', classId);
    }
</script>
@endpush
