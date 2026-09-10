@extends('layouts.app')

@section('title', 'Report Card Settings')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('report-cards.index') }}">Report Cards</a></li>
    <li class="breadcrumb-item active">Settings</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-header">
            <h5><i class="fas fa-cog"></i> Report Card Visibility & Publishing</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('report-cards.settings') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Term / Exam</label>
                        <select name="exam_id" class="form-control" required>
                            <option value="">-- Select Term --</option>
                            @foreach($exams as $exam)
                                <option value="{{ $exam->id }}" {{ optional($selectedExam)->id == $exam->id ? 'selected' : '' }}>
                                    {{ $exam->term }} • {{ $exam->academic_year }} • {{ $exam->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Class</label>
                        <select name="class_id" class="form-control" required>
                            <option value="">-- Select Class --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ optional($selectedClass)->id == $class->id ? 'selected' : '' }}>
                                    {{ $class->full_class_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 align-self-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i> Load Report Card Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($selectedExam && $selectedClass)
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <strong>{{ $selectedExam->term }} ({{ $selectedExam->academic_year }})</strong>
                    for <strong>{{ $selectedClass->full_class_name }}</strong>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <strong>{{ $students->count() }}</strong> active students found.
                    </div>
                    <div class="btn-group">
                        <form action="{{ route('report-cards.publish') }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="exam_id" value="{{ $selectedExam->id }}">
                            <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">
                            <button type="submit" class="btn btn-success btn-sm" {{ $isPublished ? 'disabled' : '' }}>
                                <i class="fas fa-check-circle me-1"></i> Publish
                            </button>
                        </form>
                        <form action="{{ route('report-cards.unpublish') }}" method="POST" class="d-inline ms-2">
                            @csrf
                            <input type="hidden" name="exam_id" value="{{ $selectedExam->id }}">
                            <input type="hidden" name="class_id" value="{{ $selectedClass->id }}">
                            <button type="submit" class="btn btn-outline-warning btn-sm" {{ !$isPublished ? 'disabled' : '' }}>
                                <i class="fas fa-ban me-1"></i> Unpublish
                            </button>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student</th>
                                <th>Admission</th>
                                <th>Report Card</th>
                                <th>Visibility</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $index => $student)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $student->full_name }}</td>
                                    <td>{{ $student->admission_number }}</td>
                                    <td>
                                        <span class="badge bg-{{ $isPublished ? 'success' : 'secondary' }}">
                                            {{ $isPublished ? 'Published' : 'Hidden' }}
                                        </span>
                                    </td>
                                    <td>
                                        {{ $isPublished ? 'Visible to students' : 'Hidden from students' }}
                                    </td>
                                    <td class="text-nowrap">
                                        <a href="{{ route('report-cards.view', [$selectedExam->id, $student->id]) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('report-cards.download', [$selectedExam->id, $student->id]) }}" class="btn btn-sm btn-success ms-1">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
