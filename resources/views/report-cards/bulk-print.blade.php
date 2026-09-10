@extends('layouts.app')

@section('title', 'Bulk Print Report Cards')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item active">Bulk Print Report Cards</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-print me-2"></i>Bulk Print Report Cards
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Bulk Print</strong> - Generate and print multiple report cards at once.
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8 offset-md-2">
                            <form>
                                <div class="form-group">
                                    <label for="classSelect">Select Class</label>
                                    <select id="classSelect" class="form-control">
                                        <option value="">Choose Class</option>
                                        <option value="10A">Grade 10A</option>
                                        <option value="10B">Grade 10B</option>
                                        <option value="11A">Grade 11A</option>
                                        <option value="11B">Grade 11B</option>
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
                                    <label>Select Students</label>
                                    <div class="border rounded p-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                            <label class="form-check-label" for="selectAll">
                                                <strong>Select All</strong>
                                            </label>
                                        </div>
                                        <hr>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="student1">
                                            <label class="form-check-label" for="student1">John Doe</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="student2">
                                            <label class="form-check-label" for="student2">Jane Smith</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="student3">
                                            <label class="form-check-label" for="student3">Peter Pan</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="student4">
                                            <label class="form-check-label" for="student4">Mary Johnson</label>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-print me-2"></i>Generate & Print All
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.form-check-input');
        checkboxes.forEach(cb => {
            if (cb.id !== 'selectAll') {
                cb.checked = this.checked;
            }
        });
    });
</script>
@endpush
@endsection