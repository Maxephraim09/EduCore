@extends('layouts.app')

@section('title', 'Print Report Card')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item active">Print Report Card</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-file-pdf me-2"></i>Print Report Card
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Report Card</strong> - Generate and print student report cards.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 offset-md-3">
                            <form>
                                <div class="form-group">
                                    <label for="studentSelect">Select Student</label>
                                    <select id="studentSelect" class="form-control">
                                        <option value="">Choose Student</option>
                                        <option value="1">John Doe - Grade 10A</option>
                                        <option value="2">Jane Smith - Grade 10A</option>
                                        <option value="3">Peter Pan - Grade 11B</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="termSelect">Select Term</label>
                                    <select id="termSelect" class="form-control">
                                        <option value="first">First Term</option>
                                        <option value="second">Second Term</option>
                                        <option value="third">Third Term</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="academicYear">Academic Year</label>
                                    <input type="text" id="academicYear" class="form-control" value="2024/2025">
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-print me-2"></i>Generate & Print
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection