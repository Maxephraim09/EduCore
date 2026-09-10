@extends('layouts.app')

@section('title', 'Add New Subject')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('subjects.index') }}">Subjects</a></li>
    <li class="breadcrumb-item active">Add Subject</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Add New Subject</h5>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('subjects.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Subject Name *</label>
                        <input type="text" name="name" class="form-control" required value="{{ old('name') }}" placeholder="e.g., Mathematics">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Subject Code *</label>
                        <input type="text" name="code" class="form-control" required value="{{ old('code') }}" placeholder="e.g., MATH101">
                        <small class="text-muted">Unique identifier for this subject</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Department</label>
                        <select name="department" class="form-control">
                            <option value="">Select Department</option>
                            @foreach($departments ?? ['Science', 'Arts', 'Commercial', 'Social Sciences', 'Languages', 'Vocational', 'ICT', 'General Studies'] as $department)
                                <option value="{{ $department }}" {{ old('department') == $department ? 'selected' : '' }}>{{ $department }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Subject Teacher</label>
                        <select name="teacher_id" class="form-control">
                            <option value="">Select Teacher</option>
                            @foreach($teachers ?? [] as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->full_name }} ({{ $teacher->position }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Credit Hours *</label>
                        <input type="number" name="credit_hours" class="form-control" required min="1" max="10" value="{{ old('credit_hours', 1) }}">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Subject Type</label>
                        <div class="form-check mt-2">
                            <input type="checkbox" name="is_core" class="form-check-input" id="isCore" value="1" {{ old('is_core') ? 'checked' : '' }}>
                            <label class="form-check-label" for="isCore">Core Subject</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="is_elective" class="form-check-input" id="isElective" value="1" {{ old('is_elective') ? 'checked' : '' }}>
                            <label class="form-check-label" for="isElective">Elective Subject</label>
                        </div>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Save Subject
                    </button>
                    <a href="{{ route('subjects.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection