@extends('layouts.app')

@section('title', 'Promotions')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0">Student Promotion Review</h4>
                <small class="text-muted">Review students for the selected class in the current academic year.</small>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('promotions.settings') }}" class="btn btn-outline-primary btn-sm">Promotion settings</a>
                <span class="badge bg-{{ $promotionEnabled ? 'success' : 'secondary' }}">{{ $promotionEnabled ? 'Enabled' : 'Disabled' }}</span>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <strong>Criteria:</strong>
                Minimum average score {{ $minAverageScore }},
                maximum absenteeism {{ $maxAbsenteeism }} day(s),
                minimum passing subjects {{ $minPassingSubjects }},
                core subjects: {{ (is_array($coreSubjects) ? implode(', ', array_filter($coreSubjects)) : (string) ($coreSubjects ?: '')) ?: 'None' }}.
            </div>

            @if(!$promotionWindowOpen)
                <div class="alert alert-warning">
                    Promotion review is only available at the end of Third Term. Current term: {{ $currentTerm }}.
                </div>
            @endif

            <form method="GET" action="{{ route('promotions.index') }}" class="row g-3 align-items-end mb-4">
                <div class="col-md-4">
                    <label class="form-label">Academic year</label>
                    <input type="text" name="academic_year" class="form-control" value="{{ $currentAcademicYear }}">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Class</label>
                    <select name="class_id" class="form-select">
                        <option value="">Select class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}" {{ $selectedClass && $selectedClass->id == $class->id ? 'selected' : '' }}>{{ $class->full_class_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Load class</button>
                </div>
            </form>

            @if($selectedClass)
                <div class="alert alert-secondary">
                    Showing students in <strong>{{ $selectedClass->full_class_name }}</strong> for academic year <strong>{{ $currentAcademicYear }}</strong>.
                </div>

                <form method="POST" action="{{ route('promotions.promote') }}">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="select-all"></th>
                                    <th>Student</th>
                                    <th>Current Class</th>
                                    <th>Target Class</th>
                                    <th>Seasonal Total</th>
                                    <th>Seasonal Average</th>
                                    <th>Class Position</th>
                                    <th>Eligibility</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reviewStudents as $entry)
                                    <tr>
                                        <td><input type="checkbox" name="student_ids[]" value="{{ $entry['student']->id }}"></td>
                                        <td>{{ $entry['student']->full_name }}</td>
                                        <td>{{ $entry['current_class'] }}</td>
                                        <td>{{ $entry['target_class_name'] }}</td>
                                        <td>{{ $entry['seasonal_total'] }}</td>
                                        <td>{{ $entry['seasonal_average'] }}</td>
                                        <td>{{ $entry['class_position'] ?? 'N/A' }}</td>
                                        <td>{{ $entry['eligible'] ? 'Yes' : 'No' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $entry['eligible'] ? 'success' : 'warning' }}">{{ $entry['status'] }}</span>
                                            @if(!$entry['eligible'] && !empty($entry['reasons']))
                                                <div class="text-muted small mt-1">{{ implode(', ', $entry['reasons']) }}</div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="9" class="text-center">No students available for review.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Promote Selected</button>
                    </div>
                </form>
            @else
                <div class="alert alert-light border">Select a class to review students.</div>
            @endif
        </div>
    </div>
</div>

<script>
    document.getElementById('select-all')?.addEventListener('change', function () {
        document.querySelectorAll('input[name="student_ids[]"]').forEach(function (checkbox) {
            checkbox.checked = this.checked;
        }.bind(this));
    });
</script>
@endsection
