@extends('layouts.app')

@section('title', 'Scores Entry')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Scores Entry</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Exams</h6>
                            <h3 class="mt-2 mb-0">{{ $totalExams ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-file-alt fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Published</h6>
                            <h3 class="mt-2 mb-0">{{ $publishedExams ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Pending</h6>
                            <h3 class="mt-2 mb-0">{{ $pendingExams ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-clock fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Students</h6>
                            <h3 class="mt-2 mb-0">{{ $totalStudents ?? 0 }}</h3>
                        </div>
                        <i class="fas fa-user-graduate fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Overview -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Score Submission Progress</h5>
            <div>
                <select id="termFilter" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                    <option value="">All Terms</option>
                    <option value="First Term">First Term</option>
                    <option value="Second Term">Second Term</option>
                    <option value="Third Term">Third Term</option>
                </select>
                <select id="classFilter" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                    <option value="">All Classes</option>
                    @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->full_class_name }}</option>
                    @endforeach
                </select>
                <button class="btn btn-primary btn-sm" onclick="filterProgress()">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <button class="btn btn-secondary btn-sm" onclick="resetFilters()">
                    <i class="fas fa-undo"></i> Reset
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="row" id="progressContainer">
                <!-- Progress cards will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Scores Management Cards -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-pen me-2"></i>Scores Management</h5>
            <div>
                <a href="{{ route('scores.single-entry') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-user-edit"></i> Single Entry
                </a>
                <a href="{{ route('scores.bulk-entry') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-table"></i> Bulk Entry
                </a>
                <a href="{{ route('scores.import') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-file-import"></i> Import
                </a>
                <a href="{{ route('scores.export') }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-file-export"></i> Export
                </a>
                <a href="{{ route('scores.calculate-positions') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-trophy"></i> Calculate Positions
                </a>
                <button class="btn btn-danger btn-sm" onclick="openModal('publish')">
                    <i class="fas fa-check-circle"></i> Publish Results
                </button>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Entry Cards -->
            <div class="row g-4 mb-4">
                <!-- Single Entry -->
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <div class="icon-wrapper bg-primary bg-opacity-10 rounded-circle mx-auto mb-3" style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user-edit fa-2x text-primary"></i>
                            </div>
                            <h5 class="card-title">Single Entry</h5>
                            <p class="card-text text-muted small">Enter scores for individual students one at a time</p>
                            <a href="{{ route('scores.single-entry') }}" class="btn btn-primary btn-sm">
                                <i class="fas fa-arrow-right"></i> Start Entry
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Bulk Entry -->
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <div class="icon-wrapper bg-success bg-opacity-10 rounded-circle mx-auto mb-3" style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-table fa-2x text-success"></i>
                            </div>
                            <h5 class="card-title">Bulk Entry</h5>
                            <p class="card-text text-muted small">Enter scores for multiple students at once</p>
                            <a href="{{ route('scores.bulk-entry') }}" class="btn btn-success btn-sm">
                                <i class="fas fa-arrow-right"></i> Start Entry
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Import Scores -->
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <div class="icon-wrapper bg-info bg-opacity-10 rounded-circle mx-auto mb-3" style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-file-import fa-2x text-info"></i>
                            </div>
                            <h5 class="card-title">Import Scores</h5>
                            <p class="card-text text-muted small">Import scores from CSV or Excel files</p>
                            <a href="{{ route('scores.import') }}" class="btn btn-info btn-sm">
                                <i class="fas fa-arrow-right"></i> Import Now
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Auto Fill Scores -->
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <div class="icon-wrapper bg-secondary bg-opacity-10 rounded-circle mx-auto mb-3" style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-magic fa-2x text-secondary"></i>
                            </div>
                            <h5 class="card-title">Auto Fill Scores</h5>
                            <p class="card-text text-muted small">Automatically generate realistic scores</p>
                            <a href="{{ route('scores.auto-fill') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-arrow-right"></i> Auto Fill
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Calculate Positions -->
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <div class="icon-wrapper bg-warning bg-opacity-10 rounded-circle mx-auto mb-3" style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-trophy fa-2x text-warning"></i>
                            </div>
                            <h5 class="card-title">Calculate Positions</h5>
                            <p class="card-text text-muted small">Calculate student rankings and positions</p>
                            <a href="{{ route('scores.calculate-positions') }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-arrow-right"></i> Calculate
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Publish Results -->
                <div class="col-lg-4 col-md-6">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            <div class="icon-wrapper bg-danger bg-opacity-10 rounded-circle mx-auto mb-3" style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-check-circle fa-2x text-danger"></i>
                            </div>
                            <h5 class="card-title">Publish Results</h5>
                            <p class="card-text text-muted small">Publish results for students to view</p>
                            <button class="btn btn-danger btn-sm" onclick="openModal('publish')">
                                <i class="fas fa-arrow-right"></i> Publish Now
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="mt-4">
                <h6 class="border-bottom pb-2"><i class="fas fa-history me-2"></i>Recent Score Entries</h6>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="scoresTable">
                        <thead>
                            <tr>
                                <th>Student</th>
                                <th>Subject</th>
                                <th>Class</th>
                                <th>Term</th>
                                <th>Total Score</th>
                                <th>Grade</th>
                                <th>Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentScores ?? [] as $score)
                                <tr>
                                    <td>{{ $score->student->full_name ?? 'N/A' }}</td>
                                    <td>{{ $score->subject->name ?? 'N/A' }}</td>
                                    <td>{{ $score->class->full_class_name ?? 'N/A' }}</td>
                                    <td>{{ $score->term }}</td>
                                    <td><strong>{{ $score->total_score }}</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $score->grade == 'A' ? 'success' : ($score->grade == 'B' ? 'info' : ($score->grade == 'C' ? 'warning' : 'danger')) }}">
                                            {{ $score->grade }}
                                        </span>
                                    </td>
                                    <td>{{ $score->updated_at ? $score->updated_at->diffForHumans() : 'N/A' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-inbox fa-2x text-muted mb-2 d-block"></i>
                                        <p class="text-muted mb-0">No recent score entries found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Student Scores Modal -->
<div class="modal fade" id="studentScoresModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-user-graduate me-2"></i>
                    <span id="modalStudentName">Student Scores</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="studentScoresBody">
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Loading scores...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="editStudentScores()">
                    <i class="fas fa-edit"></i> Edit Scores
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Publish Results Modal -->
<div class="modal fade" id="publishModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2 text-success"></i>Publish Results
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="publishForm">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Class <span class="text-danger">*</span></label>
                        <select name="class_id" id="publishClassSelect" class="form-select" required>
                            <option value="">-- Select Class --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->full_class_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Term <span class="text-danger">*</span></label>
                        <select name="term" id="publishTermSelect" class="form-select" required>
                            <option value="">-- Select Term --</option>
                            <option value="First Term">First Term</option>
                            <option value="Second Term">Second Term</option>
                            <option value="Third Term">Third Term</option>
                        </select>
                    </div>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> Publishing results will make them visible to all students and parents. This action cannot be undone.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="submitPublish()">
                    <i class="fas fa-check-circle me-1"></i> Publish Results
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Select Options Modal -->
<div class="modal fade" id="selectExamModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center">
                    <div class="modal-icon-wrapper" id="modalIconWrapper" style="border: 2px solid #667eea;">
                        <i class="fas fa-user-edit" id="modalIcon" style="color: #667eea;"></i>
                    </div>
                    <h5 class="modal-title" id="modalTitle">Single Score Entry</h5>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="selectExamForm">
                    @csrf
                    <input type="hidden" id="entryType" name="entry_type">
                    
                    <!-- Academic Year Info -->
                    <div class="alert alert-info-custom mb-3">
                        <i class="fas fa-calendar-alt me-2"></i>
                        <strong>Academic Year:</strong> 
                        {{ \App\Models\SystemSetting::getValue('academic_year', date('Y') . '/' . (date('Y') + 1)) }}
                    </div>

                    <!-- Dynamic Fields Container -->
                    <div class="row g-3" id="fieldsContainer">
                        <!-- Fields will be dynamically loaded based on entry type -->
                    </div>
                </form>

                <!-- Info Alert -->
                <div class="alert alert-info-custom mt-3 mb-0" id="actionInfoAlert">
                    <i class="fas fa-info-circle me-2"></i>
                    <span id="actionInfo">Select the required fields to proceed with score entry.</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-proceed" onclick="proceedToEntry()" id="proceedBtn" disabled>
                    <i class="fas fa-arrow-right me-1"></i> Proceed
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
<style>
    .alert-info-custom {
        background: #e8f0fe;
        border: none;
        border-radius: 10px;
        padding: 12px 18px;
        color: #1a3c6e;
        font-size: 14px;
    }
    .alert-info-custom i {
        color: #667eea;
    }
    .modal-icon-wrapper {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 14px;
        background: rgba(102, 126, 234, 0.1);
    }
    .modal-icon-wrapper i {
        font-size: 20px;
    }
    .progress-card {
        border-radius: 12px;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
        cursor: pointer;
        background: white;
    }
    .progress-card:hover {
        border-color: #667eea;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    .progress-card .subject-item {
        padding: 10px 14px;
        border-radius: 8px;
        background: #f8f9fa;
        margin-bottom: 8px;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .progress-card .subject-item:hover {
        background: #e8f0fe;
    }
    .badge-complete {
        background: #d4edda;
        color: #155724;
    }
    .badge-partial {
        background: #fff3cd;
        color: #856404;
    }
    .badge-pending {
        background: #e2e3e5;
        color: #383d41;
    }
    .form-select-sm {
        font-size: 13px;
        padding: 5px 10px;
    }
    .btn-proceed {
        padding: 10px 32px;
        border-radius: 50px;
        font-weight: 600;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        transition: all 0.3s ease;
    }
    .btn-proceed:hover {
        transform: scale(1.03);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        color: white;
    }
    .btn-proceed:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }
    .icon-wrapper {
        transition: all 0.3s ease;
    }
    .card:hover .icon-wrapper {
        transform: scale(1.1);
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    let entryType = 'single';
    let currentSubjectId = null;

    // DataTable
    $(document).ready(function() {
        $('#scoresTable').DataTable({
            pageLength: 10,
            responsive: true,
            order: [[6, 'desc']]
        });
        loadProgress();
    });

    // Load progress data
    function loadProgress() {
        const term = $('#termFilter').val();
        const classId = $('#classFilter').val();
        
        $.ajax({
            url: '{{ route("scores.get-progress") }}',
            method: 'GET',
            data: { term: term, class_id: classId },
            success: function(response) {
                renderProgress(response);
            },
            error: function() {
                // Silent fail - progress will show as empty
            }
        });
    }

    // Render progress cards
    function renderProgress(data) {
        const container = document.getElementById('progressContainer');
        container.innerHTML = '';

        if (!data || data.length === 0) {
            container.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info text-center py-4">
                        <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                        <p class="mb-0">No progress data available for the selected filters.</p>
                    </div>
                </div>
            `;
            return;
        }

        data.forEach(classData => {
            const card = document.createElement('div');
            card.className = 'col-md-6 col-lg-4 mb-3';
            card.innerHTML = `
                <div class="progress-card p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="class-name mb-0 fw-bold">${classData.class_name}</h6>
                        <span class="badge bg-primary">${classData.subject_count} Subjects</span>
                    </div>
                    <div class="subject-list">
                        ${classData.subjects.map(subject => `
                            <div class="subject-item" onclick="viewSubjectProgress(${subject.id}, '${subject.name}')">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="subject-name fw-semibold">${subject.name}</span>
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-user"></i> ${subject.teacher || 'Not Assigned'}
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <span class="score-count">${subject.submitted}/${subject.total_students}</span>
                                        <br>
                                        <span class="badge ${subject.submitted === subject.total_students ? 'badge-complete' : subject.submitted > 0 ? 'badge-partial' : 'badge-pending'}">
                                            ${subject.submitted === subject.total_students ? 'Complete' : subject.submitted > 0 ? 'Partial' : 'Pending'}
                                        </span>
                                    </div>
                                </div>
                                <div class="progress mt-1" style="height: 6px;">
                                    <div class="progress-bar bg-${subject.percentage >= 80 ? 'success' : subject.percentage >= 50 ? 'warning' : 'danger'}" 
                                         role="progressbar" 
                                         style="width: ${subject.percentage}%"
                                         aria-valuenow="${subject.percentage}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                    </div>
                                </div>
                                ${subject.submitted > 0 ? `
                                    <div class="mt-2">
                                        <small class="text-muted">Avg Score: ${subject.average_score || 0}% | Pass Rate: ${subject.pass_rate || 0}%</small>
                                    </div>
                                ` : ''}
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
            container.appendChild(card);
        });
    }

    // View subject progress
    function viewSubjectProgress(subjectId, subjectName) {
        const classId = $('#classFilter').val();
        const term = $('#termFilter').val();
        
        if (!classId || !term) {
            alert('Please select a class and term first');
            return;
        }

        currentSubjectId = subjectId;

        $.ajax({
            url: '{{ route("scores.get-subject-students") }}',
            method: 'GET',
            data: {
                class_id: classId,
                subject_id: subjectId,
                term: term
            },
            success: function(response) {
                showStudentScores(response, subjectName);
            },
            error: function() {
                alert('Error loading student scores');
            }
        });
    }

    // Show student scores modal
    function showStudentScores(data, subjectName) {
        const modal = new bootstrap.Modal(document.getElementById('studentScoresModal'));
        document.getElementById('modalStudentName').textContent = subjectName + ' - Scores';
        
        const body = document.getElementById('studentScoresBody');
        
        if (!data || data.length === 0) {
            body.innerHTML = `
                <div class="alert alert-info text-center py-4">
                    <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                    <p class="mb-0">No students found for this subject.</p>
                </div>
            `;
            modal.show();
            return;
        }

        let html = `
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Admission No</th>
                            <th>CA1</th>
                            <th>CA2</th>
                            <th>CA3</th>
                            <th>Exam</th>
                            <th>Total</th>
                            <th>Grade</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

        data.forEach((student, index) => {
            const score = student.score || {};
            const total = (parseInt(score.ca1_score) || 0) + (parseInt(score.ca2_score) || 0) + 
                         (parseInt(score.ca3_score) || 0) + (parseInt(score.exam_score) || 0);
            const grade = getGrade(total);
            
            html += `
                <tr onclick="viewStudentScores(${student.id})" style="cursor: pointer;">
                    <td>${index + 1}</td>
                    <td><strong>${student.full_name}</strong></td>
                    <td><code>${student.admission_number}</code></td>
                    <td>${score.ca1_score || '-'}</td>
                    <td>${score.ca2_score || '-'}</td>
                    <td>${score.ca3_score || '-'}</td>
                    <td>${score.exam_score || '-'}</td>
                    <td><strong>${total > 0 ? total : '-'}</strong></td>
                    <td>
                        <span class="badge bg-${getGradeColor(grade)}">${total > 0 ? grade : '-'}</span>
                    </td>
                    <td>
                        <span class="badge bg-${score.id ? 'success' : 'secondary'}">
                            ${score.id ? 'Submitted' : 'Pending'}
                        </span>
                    </td>
                </tr>
            `;
        });

        html += `
                    </tbody>
                </table>
            </div>
            <div class="mt-3 d-flex flex-wrap gap-2">
                <button class="btn btn-success btn-sm" onclick="publishSubjectScores()">
                    <i class="fas fa-check-circle me-1"></i> Publish All
                </button>
                <button class="btn btn-danger btn-sm" onclick="lockSubjectScores()">
                    <i class="fas fa-lock me-1"></i> Lock Scores
                </button>
                <button class="btn btn-info btn-sm" onclick="exportSubjectScores()">
                    <i class="fas fa-file-export me-1"></i> Export Scores
                </button>
            </div>
        `;

        body.innerHTML = html;
        modal.show();
    }

    // View individual student scores
    function viewStudentScores(studentId) {
        window.location.href = '{{ route("scores.single-entry") }}?student_id=' + studentId;
    }

    // Edit student scores
    function editStudentScores() {
        alert('Edit scores functionality will open the score entry page for this student.');
    }

    // Publish subject scores
    function publishSubjectScores() {
        if (confirm('Are you sure you want to publish all scores for this subject?')) {
            $.ajax({
                url: '{{ route("scores.publish") }}',
                method: 'POST',
                data: {
                    class_id: $('#classFilter').val(),
                    subject_id: currentSubjectId,
                    term: $('#termFilter').val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert('Scores published successfully!');
                        loadProgress();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Error publishing scores: ' + (xhr.responseJSON?.message || 'Unknown error'));
                }
            });
        }
    }

    // Lock subject scores
    function lockSubjectScores() {
        if (confirm('Are you sure you want to lock scores for this subject? Teachers will not be able to edit.')) {
            $.ajax({
                url: '{{ route("scores.lock") }}',
                method: 'POST',
                data: {
                    class_id: $('#classFilter').val(),
                    subject_id: currentSubjectId,
                    term: $('#termFilter').val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert('Scores locked successfully!');
                        loadProgress();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Error locking scores: ' + (xhr.responseJSON?.message || 'Unknown error'));
                }
            });
        }
    }

    // Export subject scores
    function exportSubjectScores() {
        const classId = $('#classFilter').val();
        const term = $('#termFilter').val();
        
        window.location.href = '{{ route("scores.export") }}?class_id=' + classId + '&subject_id=' + currentSubjectId + '&term=' + term;
    }

    // Filter progress
    function filterProgress() {
        loadProgress();
    }

    // Reset filters
    function resetFilters() {
        $('#termFilter').val('');
        $('#classFilter').val('');
        loadProgress();
    }

    // Get grade
    function getGrade(total) {
        if (total >= 70) return 'A';
        if (total >= 60) return 'B';
        if (total >= 50) return 'C';
        if (total >= 45) return 'D';
        return 'F';
    }

    // Get grade color
    function getGradeColor(grade) {
        const colors = {
            'A': 'success',
            'B': 'info',
            'C': 'warning',
            'D': 'primary',
            'F': 'danger'
        };
        return colors[grade] || 'secondary';
    }

    // Open publish modal
    function openModal(type) {
        if (type === 'publish') {
            const modal = new bootstrap.Modal(document.getElementById('publishModal'));
            modal.show();
            return;
        }
        
        entryType = type;
        document.getElementById('entryType').value = type;
        
        const config = fieldConfigs[type];
        if (!config) return;
        
        document.getElementById('modalIcon').className = 'fas ' + config.icon;
        document.getElementById('modalIcon').style.color = config.color;
        document.getElementById('modalIconWrapper').style.border = '2px solid ' + config.color;
        document.getElementById('modalTitle').textContent = config.title;
        document.getElementById('actionInfo').textContent = config.info;
        
        const container = document.getElementById('fieldsContainer');
        container.innerHTML = '';
        
        const colSize = config.fields.length === 3 ? 'col-md-4' : 'col-md-6';
        
        config.fields.forEach(field => {
            const col = document.createElement('div');
            col.className = colSize;
            
            let html = `
                <div class="mb-3">
                    <label class="form-label fw-bold">${field.label} <span class="text-danger">*</span></label>
                    <select id="${field.id}" class="form-select" ${field.required ? 'required' : ''}>
                        <option value="">-- Select ${field.label.replace('Select ', '')} --</option>
            `;
            
            if (field.options && field.options.length > 0) {
                field.options.forEach(opt => {
                    html += `<option value="${opt.value}">${opt.label}</option>`;
                });
            }
            
            html += `
                    </select>
                    <div class="text-muted small mt-1">Select the ${field.label.replace('Select ', '').toLowerCase()}</div>
                </div>
            `;
            
            col.innerHTML = html;
            container.appendChild(col);
            
            if (field.id === 'classSelect' && type === 'single') {
                document.getElementById('classSelect').addEventListener('change', function() {
                    loadStudents(this.value);
                });
            }
        });
        
        document.querySelectorAll('#fieldsContainer select').forEach(sel => {
            sel.addEventListener('change', validateFields);
        });
        
        document.getElementById('proceedBtn').disabled = true;
        
        const modal = new bootstrap.Modal(document.getElementById('selectExamModal'));
        modal.show();
    }

    // Submit publish
    function submitPublish() {
        const classId = document.getElementById('publishClassSelect').value;
        const term = document.getElementById('publishTermSelect').value;

        if (!classId || !term) {
            alert('Please select both class and term');
            return;
        }

        if (confirm('Are you sure you want to publish all results for this class and term?')) {
            $.ajax({
                url: '{{ route("scores.publish") }}',
                method: 'POST',
                data: {
                    class_id: classId,
                    term: term,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert('Results published successfully!');
                        const modal = bootstrap.Modal.getInstance(document.getElementById('publishModal'));
                        modal.hide();
                        loadProgress();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Error publishing results: ' + (xhr.responseJSON?.message || 'Unknown error'));
                }
            });
        }
    }

    // Field configurations for each entry type
    const fieldConfigs = {
        'single': {
            title: 'Single Score Entry',
            icon: 'fa-user-edit',
            color: '#667eea',
            info: 'Select class, term, and student to enter scores.',
            fields: [
                { id: 'classSelect', label: 'Select Class', type: 'select', options: @json($classes->map(fn($c) => ['value' => $c->id, 'label' => $c->full_class_name])), required: true },
                { id: 'termSelect', label: 'Select Term', type: 'select', options: [
                    { value: 'First Term', label: 'First Term' },
                    { value: 'Second Term', label: 'Second Term' },
                    { value: 'Third Term', label: 'Third Term' }
                ], required: true },
                { id: 'studentSelect', label: 'Select Student', type: 'select', options: [], required: true, dependsOn: 'classSelect' }
            ]
        },
        'bulk': {
            title: 'Bulk Score Entry',
            icon: 'fa-table',
            color: '#11998e',
            info: 'Select class, term, and subject to enter scores for multiple students.',
            fields: [
                { id: 'classSelect', label: 'Select Class', type: 'select', options: @json($classes->map(fn($c) => ['value' => $c->id, 'label' => $c->full_class_name])), required: true },
                { id: 'termSelect', label: 'Select Term', type: 'select', options: [
                    { value: 'First Term', label: 'First Term' },
                    { value: 'Second Term', label: 'Second Term' },
                    { value: 'Third Term', label: 'Third Term' }
                ], required: true },
                { id: 'subjectSelect', label: 'Select Subject', type: 'select', options: @json($subjects->map(fn($s) => ['value' => $s->id, 'label' => $s->name])), required: true }
            ]
        },
        'import': {
            title: 'Import Scores',
            icon: 'fa-file-import',
            color: '#f5576c',
            info: 'Select class, term, and subject to import scores from a file.',
            fields: [
                { id: 'classSelect', label: 'Select Class', type: 'select', options: @json($classes->map(fn($c) => ['value' => $c->id, 'label' => $c->full_class_name])), required: true },
                { id: 'termSelect', label: 'Select Term', type: 'select', options: [
                    { value: 'First Term', label: 'First Term' },
                    { value: 'Second Term', label: 'Second Term' },
                    { value: 'Third Term', label: 'Third Term' }
                ], required: true },
                { id: 'subjectSelect', label: 'Select Subject', type: 'select', options: @json($subjects->map(fn($s) => ['value' => $s->id, 'label' => $s->name])), required: true }
            ]
        },
        'auto': {
            title: 'Auto Fill Scores',
            icon: 'fa-magic',
            color: '#4facfe',
            info: 'Select class, term, and subject to auto-generate realistic scores.',
            fields: [
                { id: 'classSelect', label: 'Select Class', type: 'select', options: @json($classes->map(fn($c) => ['value' => $c->id, 'label' => $c->full_class_name])), required: true },
                { id: 'termSelect', label: 'Select Term', type: 'select', options: [
                    { value: 'First Term', label: 'First Term' },
                    { value: 'Second Term', label: 'Second Term' },
                    { value: 'Third Term', label: 'Third Term' }
                ], required: true },
                { id: 'subjectSelect', label: 'Select Subject', type: 'select', options: @json($subjects->map(fn($s) => ['value' => $s->id, 'label' => $s->name])), required: true }
            ]
        },
        'positions': {
            title: 'Calculate Positions',
            icon: 'fa-trophy',
            color: '#fa709a',
            info: 'Select class, term, and subject to calculate student positions.',
            fields: [
                { id: 'classSelect', label: 'Select Class', type: 'select', options: @json($classes->map(fn($c) => ['value' => $c->id, 'label' => $c->full_class_name])), required: true },
                { id: 'termSelect', label: 'Select Term', type: 'select', options: [
                    { value: 'First Term', label: 'First Term' },
                    { value: 'Second Term', label: 'Second Term' },
                    { value: 'Third Term', label: 'Third Term' }
                ], required: true },
                { id: 'subjectSelect', label: 'Select Subject', type: 'select', options: @json($subjects->map(fn($s) => ['value' => $s->id, 'label' => $s->name])), required: true }
            ]
        }
    };

    function loadStudents(classId) {
        if (!classId) {
            document.getElementById('studentSelect').innerHTML = '<option value="">-- Select Student --</option>';
            return;
        }
        
        $.ajax({
            url: '/api/students-by-class/' + classId,
            method: 'GET',
            success: function(students) {
                let options = '<option value="">-- Select Student --</option>';
                students.forEach(student => {
                    options += `<option value="${student.id}">${student.admission_number} - ${student.full_name}</option>`;
                });
                document.getElementById('studentSelect').innerHTML = options;
                validateFields();
            },
            error: function() {
                alert('Error loading students');
            }
        });
    }

    function validateFields() {
        const fields = document.querySelectorAll('#fieldsContainer select[required]');
        let allValid = true;
        
        fields.forEach(field => {
            if (!field.value) {
                allValid = false;
            }
        });
        
        document.getElementById('proceedBtn').disabled = !allValid;
    }

    function proceedToEntry() {
        const config = fieldConfigs[entryType];
        const fields = {};
        
        config.fields.forEach(field => {
            fields[field.id] = document.getElementById(field.id).value;
        });
        
        for (const [key, value] of Object.entries(fields)) {
            if (!value) {
                alert(`Please select ${key.replace('Select', '').replace('Select', '')}`);
                document.getElementById(key).focus();
                return;
            }
        }
        
        let url = '';
        const baseUrl = '/scores';
        const params = new URLSearchParams();
        
        params.append('term', fields.termSelect);
        params.append('class_id', fields.classSelect);
        
        switch(entryType) {
            case 'single':
                params.append('student_id', fields.studentSelect);
                url = baseUrl + '/single-entry?' + params.toString();
                break;
            case 'bulk':
                params.append('subject_id', fields.subjectSelect);
                url = baseUrl + '/bulk-entry?' + params.toString();
                break;
            case 'import':
                params.append('subject_id', fields.subjectSelect);
                url = baseUrl + '/import?' + params.toString();
                break;
            case 'auto':
                params.append('subject_id', fields.subjectSelect);
                url = baseUrl + '/auto-fill?' + params.toString();
                break;
            case 'positions':
                params.append('subject_id', fields.subjectSelect);
                url = baseUrl + '/calculate-positions?' + params.toString();
                break;
            default:
                url = baseUrl + '/single-entry?' + params.toString();
        }
        
        const modal = bootstrap.Modal.getInstance(document.getElementById('selectExamModal'));
        modal.hide();
        
        setTimeout(() => {
            window.location.href = url;
        }, 300);
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && document.getElementById('selectExamModal').classList.contains('show')) {
            e.preventDefault();
            proceedToEntry();
        }
    });
</script>
@endpush
@endsection