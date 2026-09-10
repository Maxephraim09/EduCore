@extends('layouts.app')

@section('title', 'Add Certificate')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('certificates.index') }}">Certificates</a></li>
    <li class="breadcrumb-item active">Add Certificate</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5><i class="fas fa-plus-circle me-2"></i>Add New Certificate</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('certificates.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Select Student</label>
                        <select name="student_id" class="form-control" required>
                            <option value="">-- Select Student --</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}">{{ $student->admission_number }} - {{ $student->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Certificate Type</label>
                        <select name="type" class="form-control" required>
                            <option value="">-- Select Type --</option>
                            @foreach($types as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Certificate Number</label>
                        <input type="text" name="certificate_number" class="form-control" required placeholder="CERT-2025-001">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Issue Date</label>
                        <input type="date" name="issue_date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Create Certificate
                    </button>
                    <a href="{{ route('certificates.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection