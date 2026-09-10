@extends('layouts.app')

@section('title', 'Assign Students')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">Assign Students to {{ $class->full_class_name }}</div>
        <div class="card-body">
            <form action="{{ route('classes.assign-students.post', $class->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label>Select Students</label>
                    <select name="student_ids[]" class="form-control" multiple size="10">
                        @foreach($availableStudents as $student)
                            <option value="{{ $student->id }}">{{ $student->admission_number }} - {{ $student->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-primary">Assign Selected</button>
            </form>
        </div>
    </div>
</div>
@endsection
