@extends('layouts.app')

@section('title', 'Academic Timetable')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item active">Academic Timetable</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-table me-2"></i>Academic Timetable
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Academic Timetable</strong> - Manage school-wide timetable.
                    </div>
                    <div class="text-center py-5">
                        <i class="fas fa-table fa-4x text-muted mb-3 d-block"></i>
                        <p class="text-muted">Academic Timetable module is under development.</p>
                        <button class="btn btn-primary" onclick="alert('Timetable management coming soon!')">
                            <i class="fas fa-plus-circle me-2"></i>Create Timetable
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection