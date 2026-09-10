@extends('layouts.app')

@section('title', 'Assign Subject')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">Assign Subject to {{ $class->full_class_name }}</div>
        <div class="card-body">
            <form action="{{ route('classes.assign-subject.post', $class->id) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Subject</label>
                        <select name="subject_id" class="form-control" required>
                            <option value="">-- Select Subject --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Teacher (Optional)</label>
                        <select name="teacher_id" class="form-control">
                            <option value="">-- Select Teacher --</option>
                            @foreach($teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Max Marks</label>
                        <input type="number" name="max_marks" class="form-control" value="100" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Passing Marks</label>
                        <input type="number" name="passing_marks" class="form-control" value="40" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Term</label>
                        <select name="term" class="form-control" required>
                            @foreach($terms as $t)
                                <option value="{{ $t }}">{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <button class="btn btn-primary">Assign Subject</button>
            </form>
        </div>
    </div>
</div>
@endsection
