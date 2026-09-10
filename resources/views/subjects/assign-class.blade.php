@extends('layouts.app')

@section('title', 'Assign Subject to Class - ' . $subject->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('subjects.index') }}">Subjects</a></li>
    <li class="breadcrumb-item"><a href="{{ route('subjects.show', $subject->id) }}">{{ $subject->name }}</a></li>
    <li class="breadcrumb-item active">Assign to Class</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-school me-2"></i>Assign {{ $subject->name }} to a Class</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('subjects.assign-class.post', $subject->id) }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="class_id" class="form-label">Select Class <span class="text-danger">*</span></label>
                                    <select name="class_id" id="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                                        <option value="">-- Select Class --</option>
                                        @foreach($availableClasses as $class)
                                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->full_class_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($availableClasses->isEmpty())
                                        <small class="text-warning">No available classes to assign. All classes already have this subject.</small>
                                    @endif
                                </div>

                                <div class="form-group mb-3">
                                    <label for="teacher_id" class="form-label">Assign Teacher</label>
                                    <select name="teacher_id" id="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror">
                                        <option value="">-- Select Teacher (Optional) --</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('teacher_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="term" class="form-label">Term <span class="text-danger">*</span></label>
                                    <select name="term" id="term" class="form-select @error('term') is-invalid @enderror" required>
                                        <option value="">-- Select Term --</option>
                                        @foreach($terms as $term)
                                            <option value="{{ $term }}" {{ old('term') == $term ? 'selected' : '' }}>
                                                {{ $term }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('term')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="academic_year" class="form-label">Academic Year</label>
                                    <input type="text" name="academic_year" id="academic_year" 
                                           class="form-control @error('academic_year') is-invalid @enderror"
                                           value="{{ old('academic_year', date('Y') . '/' . (date('Y') + 1)) }}"
                                           placeholder="e.g., 2024/2025">
                                    @error('academic_year')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="max_marks" class="form-label">Maximum Marks <span class="text-danger">*</span></label>
                                    <input type="number" name="max_marks" id="max_marks" 
                                           class="form-control @error('max_marks') is-invalid @enderror"
                                           value="{{ old('max_marks', 100) }}" min="1" required>
                                    @error('max_marks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="passing_marks" class="form-label">Passing Marks <span class="text-danger">*</span></label>
                                    <input type="number" name="passing_marks" id="passing_marks" 
                                           class="form-control @error('passing_marks') is-invalid @enderror"
                                           value="{{ old('passing_marks', 40) }}" min="0" required>
                                    @error('passing_marks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_required" id="is_required" 
                                       class="form-check-input" value="1" {{ old('is_required') ? 'checked' : '' }}>
                                <label for="is_required" class="form-check-label">
                                    This is a Core/Required Subject
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Assign Subject to Class
                            </button>
                            <a href="{{ route('subjects.show', $subject->id) }}" class="btn btn-secondary">
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
                    <h5><i class="fas fa-info-circle me-2"></i>Subject Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Subject Name</th>
                            <td><strong>{{ $subject->name }}</strong></td>
                        </tr>
                        <tr>
                            <th>Subject Code</th>
                            <td>{{ $subject->code }}</td>
                        </tr>
                        <tr>
                            <th>Department</th>
                            <td>{{ $subject->department ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Type</th>
                            <td>
                                @if($subject->is_core)
                                    <span class="badge bg-danger">Core</span>
                                @elseif($subject->is_elective)
                                    <span class="badge bg-secondary">Elective</span>
                                @else
                                    <span class="badge bg-info">Standard</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Credit Hours</th>
                            <td>{{ $subject->credit_hours }}</td>
                        </tr>
                        <tr>
                            <th>Currently Assigned To</th>
                            <td>{{ $subject->classes->count() }} Classes</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5><i class="fas fa-list me-2"></i>Currently Assigned Classes</h5>
                </div>
                <div class="card-body">
                    @if($subject->classes->isEmpty())
                        <p class="text-muted">Not assigned to any class yet.</p>
                    @else
                        <ul class="list-group">
                            @foreach($subject->classes as $class)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    {{ $class->full_class_name }}
                                    <span class="badge bg-info rounded-pill">{{ $class->pivot->term ?? 'N/A' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection