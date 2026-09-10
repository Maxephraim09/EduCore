@extends('layouts.app')

@section('title', 'Edit CBT Exam - ' . $exam->title)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('cbt.exams.index') }}">CBT Exams</a></li>
    <li class="breadcrumb-item"><a href="{{ route('cbt.exams.show', $exam->id) }}">{{ $exam->title }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-edit me-2"></i>Edit CBT Exam</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('cbt.exams.update', $exam->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="title" class="form-label">Exam Title <span class="text-danger">*</span></label>
                                    <input type="text" name="title" id="title" 
                                           class="form-control @error('title') is-invalid @enderror"
                                           value="{{ old('title', $exam->title) }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="subject_id" class="form-label">Subject <span class="text-danger">*</span></label>
                                    <select name="subject_id" id="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                                        <option value="">-- Select Subject --</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}" {{ old('subject_id', $exam->subject_id) == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subject_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="class_id" class="form-label">Class <span class="text-danger">*</span></label>
                                    <select name="class_id" id="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                                        <option value="">-- Select Class --</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ old('class_id', $exam->class_id) == $class->id ? 'selected' : '' }}>
                                                {{ $class->full_class_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="type" class="form-label">Exam Type <span class="text-danger">*</span></label>
                                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                                        <option value="">-- Select Type --</option>
                                        @foreach($types as $type)
                                            <option value="{{ $type }}" {{ old('type', $exam->type) == $type ? 'selected' : '' }}>
                                                {{ ucfirst($type) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="term" class="form-label">Term <span class="text-danger">*</span></label>
                                    <select name="term" id="term" class="form-select @error('term') is-invalid @enderror" required>
                                        <option value="">-- Select Term --</option>
                                        @foreach($terms as $term)
                                            <option value="{{ $term }}" {{ old('term', $exam->term) == $term ? 'selected' : '' }}>
                                                {{ $term }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('term')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="academic_year" class="form-label">Academic Year <span class="text-danger">*</span></label>
                                    <input type="text" name="academic_year" id="academic_year" 
                                           class="form-control @error('academic_year') is-invalid @enderror"
                                           value="{{ old('academic_year', $exam->academic_year) }}" required>
                                    @error('academic_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="duration_minutes" class="form-label">Duration (Minutes) <span class="text-danger">*</span></label>
                                    <input type="number" name="duration_minutes" id="duration_minutes" 
                                           class="form-control @error('duration_minutes') is-invalid @enderror"
                                           value="{{ old('duration_minutes', $exam->duration_minutes) }}" min="1" max="180" required>
                                    @error('duration_minutes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="total_marks" class="form-label">Total Marks <span class="text-danger">*</span></label>
                                    <input type="number" name="total_marks" id="total_marks" 
                                           class="form-control @error('total_marks') is-invalid @enderror"
                                           value="{{ old('total_marks', $exam->total_marks) }}" min="1" required>
                                    @error('total_marks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="passing_marks" class="form-label">Passing Marks <span class="text-danger">*</span></label>
                                    <input type="number" name="passing_marks" id="passing_marks" 
                                           class="form-control @error('passing_marks') is-invalid @enderror"
                                           value="{{ old('passing_marks', $exam->passing_marks) }}" min="0" required>
                                    @error('passing_marks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="start_date" class="form-label">Start Date & Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="start_date" id="start_date" 
                                           class="form-control @error('start_date') is-invalid @enderror"
                                           value="{{ old('start_date', $exam->start_date ? $exam->start_date->format('Y-m-d\TH:i') : '') }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="end_date" class="form-label">End Date & Time <span class="text-danger">*</span></label>
                                    <input type="datetime-local" name="end_date" id="end_date" 
                                           class="form-control @error('end_date') is-invalid @enderror"
                                           value="{{ old('end_date', $exam->end_date ? $exam->end_date->format('Y-m-d\TH:i') : '') }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea name="description" id="description" 
                                              class="form-control @error('description') is-invalid @enderror"
                                              rows="3">{{ old('description', $exam->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_randomized" id="is_randomized" 
                                               class="form-check-input" value="1" {{ old('is_randomized', $exam->is_randomized) ? 'checked' : '' }}>
                                        <label for="is_randomized" class="form-check-label">
                                            Randomize Questions
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="show_results_immediately" id="show_results_immediately" 
                                               class="form-check-input" value="1" {{ old('show_results_immediately', $exam->show_results_immediately) ? 'checked' : '' }}>
                                        <label for="show_results_immediately" class="form-check-label">
                                            Show Results Immediately
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Exam
                            </button>
                            <a href="{{ route('cbt.exams.show', $exam->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle me-2"></i>Exam Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Code</th>
                            <td><span class="badge bg-secondary">{{ $exam->code }}</span></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td><span class="badge bg-{{ $exam->status_badge }}">{{ ucfirst($exam->status) }}</span></td>
                        </tr>
                        <tr>
                            <th>Questions</th>
                            <td>{{ $exam->questions->count() }}</td>
                        </tr>
                        <tr>
                            <th>Created</th>
                            <td>{{ $exam->created_at->format('d M Y') }}</td>
                        </tr>
                    </table>
                    @if($exam->status != 'draft')
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            Only draft exams can be edited. Current status: <strong>{{ ucfirst($exam->status) }}</strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection