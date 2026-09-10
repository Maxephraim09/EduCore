@extends('layouts.app')

@section('title', 'Add New Class')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('classes.index') }}">Classes</a></li>
    <li class="breadcrumb-item active">Add Class</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Add New Class</h5>
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

            <form action="{{ route('classes.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Class Name *</label>
                        <select name="name" class="form-control" required>
                            <option value="">Select Class Level</option>
                            @foreach($levels as $level)
                                <option value="{{ $level }}" {{ old('name') == $level ? 'selected' : '' }}>{{ $level }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Section *</label>
                        <select name="section" class="form-control" required>
                            <option value="">Select Section</option>
                            @foreach($sections as $section)
                                <option value="{{ $section }}" {{ old('section') == $section ? 'selected' : '' }}>{{ $section }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Class Code *</label>
                        <input type="text" name="code" class="form-control" required value="{{ old('code') }}" placeholder="e.g., JSS1A">
                        <small class="text-muted">Unique identifier for this class</small>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Category</label>
                        <select name="class_category_id" class="form-control">
                            <option value="">Select Category</option>
                            @foreach($categories ?? [] as $category)
                                <option value="{{ $category->id }}" {{ old('class_category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Class Teacher</label>
                        <select name="class_teacher_id" class="form-control">
                            <option value="">Select Teacher</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}" {{ old('class_teacher_id') == $teacher->id ? 'selected' : '' }}>
                                    {{ $teacher->full_name }} ({{ $teacher->position }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Capacity *</label>
                        <input type="number" name="capacity" class="form-control" required min="1" value="{{ old('capacity', 50) }}">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Academic Year</label>
                        <input type="text" name="academic_year" class="form-control" value="{{ old('academic_year', date('Y') . '/' . (date('Y') + 1)) }}" placeholder="e.g., 2024/2025">
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Save Class
                    </button>
                    <a href="{{ route('classes.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection