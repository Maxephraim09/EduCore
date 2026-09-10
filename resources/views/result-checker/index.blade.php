@extends('layouts.app')

@section('title', 'Result Checker PINs')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item active">Result Checker PINs</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card mb-4">
        <div class="card-header"><h5 class="mb-0"><i class="fas fa-key me-2"></i>Generate Result Checker PINs</h5></div>
        <div class="card-body">
            <form method="POST" action="{{ route('result-checker-pins.generate') }}" class="row g-3">
                @csrf
                <div class="col-md-5"><label class="form-label">Published term / exam</label><select name="exam_id" class="form-control" required><option value="">Select term</option>@foreach($exams as $exam)<option value="{{ $exam->id }}">{{ $exam->term }} ({{ $exam->academic_year }})</option>@endforeach</select></div>
                <div class="col-md-5"><label class="form-label">Class</label><select name="class_id" class="form-control" required><option value="">Select class</option>@foreach($classes as $class)<option value="{{ $class->id }}">{{ $class->full_class_name }}</option>@endforeach</select></div>
                <div class="col-md-2 d-flex align-items-end"><button class="btn btn-primary w-100"><i class="fas fa-print me-2"></i>Generate & Print</button></div>
            </form>
        </div>
    </div>
    <div class="card"><div class="card-header"><h5 class="mb-0">Generated PINs</h5></div><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Student</th><th>Registration</th><th>Term</th><th>PIN</th></tr></thead><tbody>@foreach($pins as $pin)<tr><td>{{ $pin->student->full_name }}</td><td>{{ $pin->student->admission_number }}</td><td>{{ $pin->exam->term }} ({{ $pin->exam->academic_year }})</td><td><strong>{{ $pin->pin }}</strong></td></tr>@endforeach</tbody></table></div><div class="p-3">{{ $pins->links() }}</div></div>
</div>
@endsection
