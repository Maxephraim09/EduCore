@extends('layouts.app')

@section('title', 'Report Cards')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Report Cards</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card mb-3">
        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h5 class="mb-2"><i class="fas fa-file-alt"></i> Report Card Views</h5>
                <p class="mb-0 text-muted">Choose between single report card generation, bulk report card printing, and report card settings.</p>
            </div>
            <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="reportCardViewDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    View Options
                </button>
                <ul class="dropdown-menu" aria-labelledby="reportCardViewDropdown">
                    <li><a class="dropdown-item" href="{{ route('report-cards.index') }}">Single Report Card</a></li>
                    <li><a class="dropdown-item" href="{{ route('report-cards.bulk.index') }}">Bulk Report Card</a></li>
                    <li><a class="dropdown-item" href="{{ route('report-cards.settings') }}">Report Card Settings</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card" id="single-report-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-user-graduate"></i> Single Report Card</h5>
            <a href="{{ route('report-cards.settings') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-cog"></i> Report Card Settings
            </a>
        </div>
        <div class="card-body">
            <p class="text-muted">Generate a report card for one student. Choose term, class, and a single student.</p>
            <form action="{{ route('report-cards.generate') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Term / Exam</label>
                        <select name="exam_id" class="form-control" required>
                            <option value="">-- Select Term --</option>
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}">{{ $exam->term }} ({{ $exam->academic_year }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Class</label>
                        <select name="class_id" class="form-control" required>
                            <option value="">-- Select Class --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->full_class_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <input type="hidden" name="report_type" value="single">
                    <div class="col-md-4 mb-3">
                        <label>Student</label>
                        <select name="student_id" class="form-control" required>
                            <option value="">-- Select Student --</option>
                        </select>
                    </div>
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-file-pdf"></i> Generate Single Report Card
                        </button>
                        <a href="{{ route('report-cards.bulk.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-layer-group"></i> Go to Bulk Report Card
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Recent Report Cards -->
    <div class="card mt-4">
        <div class="card-header">
            <h5><i class="fas fa-history"></i> Recent Report Cards</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="reportCardsTable">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Term (Session)</th>
                            <th>Class</th>
                            <th>Term</th>
                            <th>Average</th>
                            <th>Grade</th>
                            <th>Position</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $latestResults = \App\Models\Result::with(['student', 'exam', 'class'])
                                ->where('is_published', true)
                                ->orderBy('created_at', 'desc')
                                ->limit(20)
                                ->get()
                                ->groupBy('student_id')
                                ->map(function($items) {
                                    return $items->first();
                                });
                        @endphp
                        @foreach($latestResults as $result)
                        <tr>
                            <td>{{ $result->student->full_name }}</td>
                            <td>{{ $result->exam->term }} ({{ $result->exam->academic_year }})</td>
                            <td>{{ $result->class->full_class_name ?? 'N/A' }}</td>
                            <td>{{ $result->exam->term }}</td>
                            <td>
                                @php
                                    $avg = \App\Models\Result::where('student_id', $result->student_id)
                                        ->where('exam_id', $result->exam_id)
                                        ->avg('total_score');
                                @endphp
                                {{ number_format($avg, 1) }}%
                            </td>
                            <td>
                                @php
                                    $grade = \App\Models\GradeScale::getGrade($avg);
                                @endphp
                                <span class="badge bg-{{ $grade ? 'success' : 'secondary' }}">
                                    {{ $grade->grade ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $position = \App\Models\Result::where('exam_id', $result->exam_id)
                                        ->where('class_id', $result->class_id)
                                        ->get()
                                        ->groupBy('student_id')
                                        ->map(function($items) {
                                            return $items->sum('total_score');
                                        })
                                        ->sortDesc()
                                        ->search(function($score) use ($result) {
                                            return $score == \App\Models\Result::where('student_id', $result->student_id)
                                                ->where('exam_id', $result->exam_id)
                                                ->sum('total_score');
                                        });
                                @endphp
                                {{ $position !== false ? $position + 1 : 'N/A' }}
                            </td>
                            <td>
                                <a href="{{ route('report-cards.view', [$result->exam_id, $result->student_id]) }}" 
                                   class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('report-cards.download', [$result->exam_id, $result->student_id]) }}" 
                                   class="btn btn-sm btn-success">
                                    <i class="fas fa-download"></i> PDF
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#reportCardsTable').DataTable({
            pageLength: 10,
            responsive: true
        });

        // Load students when class is selected
        $('select[name="class_id"]').change(function() {
            var classId = $(this).val();
            var studentSelect = $('select[name="student_id"]');
            studentSelect.html('<option value="">Loading...</option>');
            
            if (classId) {
                $.ajax({
                    url: '/api/students-by-class/' + classId,
                    method: 'GET',
                    success: function(data) {
                        studentSelect.html('<option value="">-- All Students --</option>');
                        data.forEach(function(student) {
                            studentSelect.append(
                                '<option value="' + student.id + '">' + 
                                student.admission_number + ' - ' + student.full_name + 
                                '</option>'
                            );
                        });
                    }
                });
            } else {
                studentSelect.html('<option value="">-- All Students --</option>');
            }
        });
    });

    function bulkGenerate() {
        var examId = $('select[name="exam_id"]').val();
        var classId = $('select[name="class_id"]').val();
        
        if (!examId || !classId) {
            alert('Please select both term and class');
            return;
        }
        
        window.location.href = '/report-cards/bulk/' + examId + '/' + classId;
    }
</script>
@endpush
@endsection