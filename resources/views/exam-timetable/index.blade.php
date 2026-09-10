@extends('layouts.app')

@section('title', 'Exam Timetable')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item active">Exam Timetable</li>
@endsection

@section('styles')
<style>
    .timetable-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        transition: all 0.3s ease;
        background: #fff;
    }
    .timetable-card:hover {
        border-color: #667eea;
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.08);
    }
    .timetable-card .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        border-radius: 12px 12px 0 0 !important;
        padding: 15px 20px;
    }
    .timetable-card .card-body {
        padding: 20px;
    }
    .exam-slot {
        background: #f8fafc;
        border-radius: 8px;
        padding: 12px 15px;
        margin-bottom: 8px;
        border-left: 4px solid #667eea;
        transition: all 0.3s ease;
    }
    .exam-slot:hover {
        background: #e9ecef;
        transform: translateX(5px);
    }
    .exam-slot .exam-time {
        font-weight: 600;
        color: #667eea;
        font-size: 13px;
    }
    .exam-slot .exam-subject {
        font-weight: 600;
        color: #1e293b;
    }
    .exam-slot .exam-class {
        color: #64748b;
        font-size: 13px;
    }
    .exam-slot .exam-venue {
        color: #64748b;
        font-size: 13px;
    }
    .exam-slot .exam-badge {
        font-size: 11px;
        padding: 3px 12px;
        border-radius: 12px;
    }
    .exam-slot .exam-badge.morning { background: #e0e7ff; color: #4f46e5; }
    .exam-slot .exam-badge.afternoon { background: #fef3c7; color: #d97706; }
    .exam-slot .exam-badge.evening { background: #d1fae5; color: #059669; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Exam Timetable
                    </h5>
                    <div>
                        <button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#addExamModal">
                            <i class="fas fa-plus-circle me-1"></i> Add Exam
                        </button>
                        <button type="button" class="btn btn-light btn-sm" onclick="window.print()">
                            <i class="fas fa-print me-1"></i> Print
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filterClass">Class</label>
                                <select id="filterClass" class="form-control">
                                    <option value="">All Classes</option>
                                    <option value="10">Grade 10</option>
                                    <option value="11">Grade 11</option>
                                    <option value="12">Grade 12</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filterDate">Date</label>
                                <input type="date" id="filterDate" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filterTerm">Term</label>
                                <select id="filterTerm" class="form-control">
                                    <option value="">All Terms</option>
                                    <option value="first">First Term</option>
                                    <option value="second">Second Term</option>
                                    <option value="third">Third Term</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-primary btn-block" onclick="filterTimetable()">
                                    <i class="fas fa-search me-1"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Timetable Display -->
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Time</th>
                                    <th>Subject</th>
                                    <th>Class</th>
                                    <th>Venue</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="timetableBody">
                                <!-- Sample Data -->
                                <tr>
                                    <td><span class="badge exam-badge morning">8:00 AM</span></td>
                                    <td><strong>Mathematics</strong></td>
                                    <td>Grade 10A</td>
                                    <td>Hall A</td>
                                    <td>2024-12-15</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="badge exam-badge morning">10:00 AM</span></td>
                                    <td><strong>English Language</strong></td>
                                    <td>Grade 10A</td>
                                    <td>Hall B</td>
                                    <td>2024-12-15</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <tr>
                                    <td><span class="badge exam-badge afternoon">1:00 PM</span></td>
                                    <td><strong>Physics</strong></td>
                                    <td>Grade 11A</td>
                                    <td>Lab 1</td>
                                    <td>2024-12-15</td>
                                    <td>
                                        <button class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Statistics Summary -->
                    <div class="row mt-4">
                        <div class="col-md-3">
                            <div class="stat-box text-center p-3 border rounded">
                                <div class="stat-label text-muted small">Total Exams</div>
                                <div class="stat-value h4 mb-0">24</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box text-center p-3 border rounded">
                                <div class="stat-label text-muted small">Classes</div>
                                <div class="stat-value h4 mb-0">6</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box text-center p-3 border rounded">
                                <div class="stat-label text-muted small">Venues</div>
                                <div class="stat-value h4 mb-0">4</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stat-box text-center p-3 border rounded bg-success bg-opacity-10">
                                <div class="stat-label text-muted small">Days Left</div>
                                <div class="stat-value h4 mb-0 text-success">5</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Exam Modal -->
<div class="modal fade" id="addExamModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>Add Exam to Timetable
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="#" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="exam_subject">Subject <span class="text-danger">*</span></label>
                        <select id="exam_subject" class="form-control" required>
                            <option value="">Select Subject</option>
                            <option value="Mathematics">Mathematics</option>
                            <option value="English Language">English Language</option>
                            <option value="Physics">Physics</option>
                            <option value="Chemistry">Chemistry</option>
                            <option value="Biology">Biology</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="exam_class">Class <span class="text-danger">*</span></label>
                        <select id="exam_class" class="form-control" required>
                            <option value="">Select Class</option>
                            <option value="10A">Grade 10A</option>
                            <option value="10B">Grade 10B</option>
                            <option value="11A">Grade 11A</option>
                            <option value="11B">Grade 11B</option>
                            <option value="12A">Grade 12A</option>
                            <option value="12B">Grade 12B</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exam_date">Date <span class="text-danger">*</span></label>
                                <input type="date" id="exam_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="exam_time">Time <span class="text-danger">*</span></label>
                                <input type="time" id="exam_time" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="exam_venue">Venue <span class="text-danger">*</span></label>
                        <input type="text" id="exam_venue" class="form-control" placeholder="e.g., Hall A, Lab 1" required>
                    </div>
                    <div class="form-group">
                        <label for="exam_duration">Duration (minutes)</label>
                        <input type="number" id="exam_duration" class="form-control" value="120" min="30" max="180">
                    </div>
                    <div class="form-group">
                        <label for="exam_term">Term</label>
                        <select id="exam_term" class="form-control">
                            <option value="first">First Term</option>
                            <option value="second">Second Term</option>
                            <option value="third">Third Term</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Exam</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function filterTimetable() {
        const classFilter = document.getElementById('filterClass').value;
        const dateFilter = document.getElementById('filterDate').value;
        const termFilter = document.getElementById('filterTerm').value;
        
        // AJAX call to filter timetable
        alert('Filtering: Class=' + classFilter + ', Date=' + dateFilter + ', Term=' + termFilter);
    }
</script>
@endpush
@endsection