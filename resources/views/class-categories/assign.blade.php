@extends('layouts.app')

@section('title', 'Assign Class to Category')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('class-categories.index') }}">Class Categories</a></li>
    <li class="breadcrumb-item active">Assign Class</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-chalkboard-teacher me-2"></i>Assign Class to {{ $category->name }}</h5>
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

            <form action="{{ route('class-categories.assign', $category->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label>Category</label>
                        <input type="text" class="form-control" value="{{ $category->name }}" disabled>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label>Class *</label>
                        <select name="class_id" class="form-control" required>
                            <option value="">Select a class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ $class->class_category_id == $category->id ? 'selected' : '' }}>
                                    {{ $class->full_class_name }} ({{ $class->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Assign Class</button>
                    <a href="{{ route('class-categories.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
