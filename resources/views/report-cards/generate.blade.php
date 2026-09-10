@extends('layouts.app')

@section('title', 'Report Card Preview')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('report-cards.index') }}">Report Cards</a></li>
    <li class="breadcrumb-item active">Preview</li>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Report Card Preview</h4>
        </div>
        <div class="card-body">
            <p><strong>Term:</strong> {{ $exam->term }} ({{ $exam->academic_year }})</p>
            <p><strong>Class:</strong> {{ $class->full_class_name }}</p>

            @if($students->isEmpty())
                <div class="alert alert-warning">No active students found for this class.</div>
            @else
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th>Admission</th>
                            <th>Total Score</th>
                            <th>Average</th>
                            <th>Grade</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($students as $student)
                            @php $entry = $reportData[$student->id] ?? null; @endphp
                            <tr>
                                <td>{{ $student->full_name }}</td>
                                <td>{{ $student->admission_number }}</td>
                                <td>{{ $entry['total_score'] ?? 0 }}</td>
                                <td>{{ isset($entry['average']) ? number_format($entry['average'], 1) : '0.0' }}</td>
                                <td>{{ $entry['grade']?->grade ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('report-cards.view', [$exam->id, $student->id]) }}" class="btn btn-sm btn-info">
                                        View
                                    </a>
                                    <a href="{{ route('report-cards.download', [$exam->id, $student->id]) }}" class="btn btn-sm btn-success" download>
                                        PDF
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
