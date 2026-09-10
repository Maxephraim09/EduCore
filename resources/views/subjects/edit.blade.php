@extends('layouts.app')

@section('title', 'Edit Subject - ' . $subject->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('subjects.index') }}">Subjects</a></li>
    <li class="breadcrumb-item"><a href="{{ route('subjects.show', $subject->id) }}">{{ $subject->name }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-edit me-2"></i>Edit Subject</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('subjects.update', $subject->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="name" class="form-label">Subject Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" id="name" 
                                           class="form-control @error('name') is-invalid @enderror"
                                           value="{{ old('name', $subject->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="code" class="form-label">Subject Code <span class="text-danger">*</span></label>
                                    <input type="text" name="code" id="code" 
                                           class="form-control @error('code') is-invalid @enderror"
                                           value="{{ old('code', $subject->code) }}" required>
                                    @error('code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="department" class="form-label">Department</label>
                                    <select name="department" id="department" class="form-select @error('department') is-invalid @enderror">
                                        <option value="">-- Select Department --</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department }}" {{ old('department', $subject->department) == $department ? 'selected' : '' }}>
                                                {{ $department }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('department')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="teacher_id" class="form-label">Teacher</label>
                                    <select name="teacher_id" id="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror">
                                        <option value="">-- Select Teacher --</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ old('teacher_id', $subject->teacher_id) == $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('teacher_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group mb-3">
                                    <label for="credit_hours" class="form-label">Credit Hours <span class="text-danger">*</span></label>
                                    <input type="number" name="credit_hours" id="credit_hours" 
                                           class="form-control @error('credit_hours') is-invalid @enderror"
                                           value="{{ old('credit_hours', $subject->credit_hours) }}" min="1" max="10" required>
                                    @error('credit_hours')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_core" id="is_core" 
                                               class="form-check-input" value="1" {{ old('is_core', $subject->is_core) ? 'checked' : '' }}>
                                        <label for="is_core" class="form-check-label">
                                            Core Subject
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="is_elective" id="is_elective" 
                                               class="form-check-input" value="1" {{ old('is_elective', $subject->is_elective) ? 'checked' : '' }}>
                                        <label for="is_elective" class="form-check-label">
                                            Elective Subject
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea name="description" id="description" 
                                      class="form-control @error('description') is-invalid @enderror"
                                      rows="3">{{ old('description', $subject->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="is_active" id="is_active" 
                                       class="form-check-input" value="1" {{ old('is_active', $subject->is_active) ? 'checked' : '' }}>
                                <label for="is_active" class="form-check-label">
                                    Active
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Update Subject
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
                    <h5><i class="fas fa-info-circle me-2"></i>Subject Information</h5>
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
                            <th>Current Status</th>
                            <td>
                                @if($subject->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Assigned To</th>
                            <td>{{ $subject->classes->count() }} Classes</td>
                        </tr>
                        <tr>
                            <th>Created</th>
                            <td>{{ $subject->created_at ? $subject->created_at->format('d M Y') : 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5><i class="fas fa-exclamation-triangle me-2"></i>Danger Zone</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <strong>Warning!</strong> Deleting this subject will remove it from all classes.
                    </div>
                    <form action="{{ route('subjects.destroy', $subject->id) }}" method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this subject?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100" 
                                {{ $subject->classes->count() > 0 ? 'disabled' : '' }}>
                            <i class="fas fa-trash"></i> Delete Subject
                        </button>
                        @if($subject->classes->count() > 0)
                            <small class="text-muted">Cannot delete subject assigned to classes.</small>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection