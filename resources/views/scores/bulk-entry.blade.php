@extends('layouts.app')

@section('title', 'Bulk Score Entry')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('scores.index') }}">Scores Entry</a></li>
    <li class="breadcrumb-item active">Bulk Entry</li>
@endsection

@section('styles')
<style>
    .score-input-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .score-input-group input {
        width: 80px;
        text-align: center;
    }
    .score-table th {
        background: #f8f9fa;
        font-weight: 600;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .score-table td {
        vertical-align: middle;
    }
    .score-table .total-score {
        font-weight: 700;
        font-size: 16px;
    }
    .score-table .grade-badge {
        font-size: 14px;
        padding: 5px 12px;
    }
    .student-row {
        transition: all 0.3s;
    }
    .student-row:hover {
        background: #f8f9ff;
    }
    .student-row .student-name {
        font-weight: 500;
    }
    .score-input {
        width: 70px !important;
        text-align: center;
        display: inline-block;
    }
    .score-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    .subject-info-card {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border-radius: 10px;
        padding: 15px 20px;
        margin-bottom: 20px;
    }
    .subject-info-card .label {
        opacity: 0.8;
        font-size: 12px;
    }
    .subject-info-card .value {
        font-size: 16px;
        font-weight: 600;
    }
    .summary-stats {
        background: rgba(255,255,255,0.15);
        border-radius: 8px;
        padding: 10px 15px;
    }
    .summary-stats .stat-item {
        text-align: center;
        padding: 5px 10px;
    }
    .summary-stats .stat-item .number {
        font-size: 20px;
        font-weight: 700;
    }
    .summary-stats .stat-item .label {
        font-size: 11px;
        opacity: 0.8;
    }
    .btn-save-all {
        padding: 10px 30px;
        font-weight: 600;
        border-radius: 8px;
    }
    .form-select-sm {
        font-size: 13px;
        padding: 5px 10px;
    }
    .table-responsive {
        max-height: 600px;
        overflow-y: auto;
    }
    .sticky-header {
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .selection-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .empty-state {
        text-align: center;
        padding: 40px 20px;
    }
    .empty-state i {
        font-size: 48px;
        color: #cbd5e1;
    }
    .empty-state h5 {
        margin-top: 15px;
        color: #64748b;
    }
    .empty-state p {
        color: #94a3b8;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-users-cog me-2"></i>Bulk Score Entry</h5>
                    <div class="float-end">
                        <span class="badge bg-success">Bulk Entry</span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Selection Form -->
                    <div class="selection-card">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="classSelect" class="form-label fw-bold">Select Class <span class="text-danger">*</span></label>
                                <select id="classSelect" class="form-select form-select-sm" onchange="loadScores()">
                                    <option value="">-- Select Class --</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ ($classId ?? '') == $class->id ? 'selected' : '' }}>
                                            {{ $class->full_class_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="termSelect" class="form-label fw-bold">Select Term <span class="text-danger">*</span></label>
                                <select id="termSelect" class="form-select form-select-sm" onchange="loadScores()">
                                    <option value="">-- Select Term --</option>
                                    <option value="First Term" {{ ($term ?? '') == 'First Term' ? 'selected' : '' }}>First Term</option>
                                    <option value="Second Term" {{ ($term ?? '') == 'Second Term' ? 'selected' : '' }}>Second Term</option>
                                    <option value="Third Term" {{ ($term ?? '') == 'Third Term' ? 'selected' : '' }}>Third Term</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="subjectSelect" class="form-label fw-bold">Select Subject <span class="text-danger">*</span></label>
                                <select id="subjectSelect" class="form-select form-select-sm" onchange="loadScores()">
                                    <option value="">-- Select Subject --</option>
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ ($subjectId ?? '') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Subject Info -->
                    <div id="subjectInfo" style="display: none;">
                        <div class="subject-info-card">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="label">Subject</div>
                                            <div class="value" id="subjectName">-</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="label">Class</div>
                                            <div class="value" id="className">-</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="label">Total Students</div>
                                            <div class="value" id="totalStudents">0</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row summary-stats">
                                        <div class="col-4 stat-item">
                                            <div class="number text-white" id="avgScore">0%</div>
                                            <div class="label text-white-50">Average</div>
                                        </div>
                                        <div class="col-4 stat-item">
                                            <div class="number text-white" id="passCount">0</div>
                                            <div class="label text-white-50">Passed</div>
                                        </div>
                                        <div class="col-4 stat-item">
                                            <div class="number text-white" id="failCount">0</div>
                                            <div class="label text-white-50">Failed</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Scores Table -->
                    <div id="scoresContainer" style="display: none;">
                        <div class="table-responsive">
                            <table class="table table-bordered score-table">
                                <thead class="sticky-header">
                                    <tr>
                                        <th width="4%">#</th>
                                        <th width="12%">Admission No</th>
                                        <th width="18%">Student Name</th>
                                        <th width="11%">CA 1 (30)</th>
                                        <th width="11%">CA 2 (30)</th>
                                        <th width="11%">CA 3 (30)</th>
                                        <th width="11%">Exam (70)</th>
                                        <th width="10%">Total (100)</th>
                                        <th width="10%">Grade</th>
                                        <th width="7%">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="scoresBody">
                                    <!-- Scores will be loaded here -->
                                </tbody>
                                <tfoot id="scoresFooter" style="display: none;">
                                    <tr class="table-secondary">
                                        <td colspan="7" class="text-end fw-bold">Class Summary</td>
                                        <td id="footerTotal" class="fw-bold">0</td>
                                        <td id="footerGrade" class="fw-bold">-</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div>
                                <button class="btn btn-success btn-save-all" onclick="saveAllScores()">
                                    <i class="fas fa-save me-1"></i> Save All Scores
                                </button>
                                <button class="btn btn-secondary" onclick="resetForm()">
                                    <i class="fas fa-undo me-1"></i> Reset
                                </button>
                                <button class="btn btn-info" onclick="autoFillScores()">
                                    <i class="fas fa-magic me-1"></i> Auto Fill
                                </button>
                            </div>
                            <div>
                                <span class="badge bg-success" id="saveStatus">Ready</span>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div id="emptyState" style="display: block;">
                        <div class="empty-state">
                            <i class="fas fa-users"></i>
                            <h5>No Selection Made</h5>
                            <p>Please select a class, term, and subject to view and enter scores.</p>
                        </div>
                    </div>

                    <!-- No Data Message -->
                    <div id="noDataMessage" style="display: none;">
                        <div class="alert alert-info text-center py-4">
                            <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                            <p class="mb-0">No students found for this class and subject combination.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentClassId = null;
    let currentSubjectId = null;
    let scoresData = {};
    let originalScores = {};
    let studentsList = [];

    // Load scores
    function loadScores() {
        const classId = $('#classSelect').val();
        const subjectId = $('#subjectSelect').val();
        const term = $('#termSelect').val();

        // Hide all content first
        hideAllContent();

        if (!classId || !subjectId || !term) {
            showEmptyState();
            return;
        }

        currentClassId = classId;
        currentSubjectId = subjectId;

        // Show loading
        $('#scoresBody').html('<tr><td colspan="10" class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading scores...</td></tr>');
        $('#scoresContainer').show();
        $('#emptyState').hide();
        $('#noDataMessage').hide();

        $.ajax({
            url: '{{ route("scores.get-bulk-scores") }}',
            method: 'GET',
            data: {
                class_id: classId,
                subject_id: subjectId,
                term: term
            },
            success: function(response) {
                if (response.success) {
                    // Update subject info
                    $('#subjectName').text(response.subject.name);
                    $('#className').text(response.class_name);
                    $('#totalStudents').text(response.students.length);
                    $('#subjectInfo').show();

                    // Store data
                    studentsList = response.students;
                    scoresData = {};
                    if (response.scores) {
                        Object.keys(response.scores).forEach(key => {
                            scoresData[key] = response.scores[key];
                        });
                    }
                    originalScores = JSON.parse(JSON.stringify(scoresData));

                    if (response.students.length === 0) {
                        $('#noDataMessage').show();
                        $('#scoresBody').html('');
                        $('#scoresFooter').hide();
                    } else {
                        $('#noDataMessage').hide();
                        renderScores(response.students, scoresData);
                        updateStats();
                        $('#scoresFooter').show();
                    }
                } else {
                    alert('Error loading scores: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Error loading scores: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    }

    // Render scores table
    function renderScores(students, scores) {
        let html = '';
        let totalMarks = 0;
        let studentCount = 0;
        let passCount = 0;
        let failCount = 0;

        students.forEach((student, index) => {
            const score = scores[student.id] || {};
            const ca1 = score.ca1_score || 0;
            const ca2 = score.ca2_score || 0;
            const ca3 = score.ca3_score || 0;
            const exam = score.exam_score || 0;
            const total = parseInt(ca1) + parseInt(ca2) + parseInt(ca3) + parseInt(exam);
            const grade = getGrade(total);

            totalMarks += total;
            studentCount++;
            if (total >= 40) passCount++;
            else failCount++;

            html += `
                <tr class="student-row" data-student-id="${student.id}">
                    <td>${index + 1}</td>
                    <td><code>${student.admission_number}</code></td>
                    <td class="student-name">${student.full_name}</td>
                    <td>
                        <input type="number" class="form-control form-control-sm score-input ca1-input" 
                               value="${ca1}" min="0" max="30" 
                               data-student="${student.id}" data-type="ca1"
                               onchange="updateScore(${student.id}, 'ca1', this.value)">
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm score-input ca2-input" 
                               value="${ca2}" min="0" max="30" 
                               data-student="${student.id}" data-type="ca2"
                               onchange="updateScore(${student.id}, 'ca2', this.value)">
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm score-input ca3-input" 
                               value="${ca3}" min="0" max="30" 
                               data-student="${student.id}" data-type="ca3"
                               onchange="updateScore(${student.id}, 'ca3', this.value)">
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm score-input exam-input" 
                               value="${exam}" min="0" max="70" 
                               data-student="${student.id}" data-type="exam"
                               onchange="updateScore(${student.id}, 'exam', this.value)">
                    </td>
                    <td class="total-score text-center" id="total_${student.id}">${total}</td>
                    <td>
                        <span class="badge bg-${getGradeColor(grade)} grade-badge" id="grade_${student.id}">${grade}</span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-danger" onclick="clearStudentScores(${student.id})" title="Clear scores">
                            <i class="fas fa-eraser"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        $('#scoresBody').html(html);
        $('#passCount').text(passCount);
        $('#failCount').text(failCount);
        $('#avgScore').text(studentCount > 0 ? Math.round(totalMarks / studentCount) + '%' : '0%');
        
        // Update footer
        const avgGrade = studentCount > 0 ? getGrade(Math.round(totalMarks / studentCount)) : '-';
        $('#footerTotal').text(totalMarks);
        $('#footerGrade').text(avgGrade);
    }

    // Update score
    function updateScore(studentId, type, value) {
        if (!scoresData[studentId]) {
            scoresData[studentId] = {};
        }

        const numValue = parseInt(value) || 0;
        
        // Validate ranges
        if (type === 'ca1' || type === 'ca2' || type === 'ca3') {
            if (numValue > 30) {
                alert('CA score cannot exceed 30');
                $(`input[data-student="${studentId}"][data-type="${type}"]`).val(scoresData[studentId][type + '_score'] || 0);
                return;
            }
        }
        if (type === 'exam') {
            if (numValue > 70) {
                alert('Exam score cannot exceed 70');
                $(`input[data-student="${studentId}"][data-type="${type}"]`).val(scoresData[studentId][type + '_score'] || 0);
                return;
            }
        }
        
        scoresData[studentId][type + '_score'] = numValue;

        // Recalculate total
        const ca1 = parseInt(scoresData[studentId].ca1_score) || 0;
        const ca2 = parseInt(scoresData[studentId].ca2_score) || 0;
        const ca3 = parseInt(scoresData[studentId].ca3_score) || 0;
        const exam = parseInt(scoresData[studentId].exam_score) || 0;
        const total = ca1 + ca2 + ca3 + exam;

        $(`#total_${studentId}`).text(total);
        const grade = getGrade(total);
        const badge = $(`#grade_${studentId}`);
        badge.text(grade);
        badge.removeClass().addClass(`badge bg-${getGradeColor(grade)} grade-badge`);

        updateStats();
        $('#saveStatus').text('Unsaved changes').removeClass('bg-success').addClass('bg-warning');
    }

    // Clear student scores
    function clearStudentScores(studentId) {
        if (confirm('Clear all scores for this student?')) {
            scoresData[studentId] = {};
            $(`input[data-student="${studentId}"]`).val('');
            $(`#total_${studentId}`).text('0');
            $(`#grade_${studentId}`).text('F');
            $(`#grade_${studentId}`).removeClass().addClass('badge bg-danger grade-badge');
            updateStats();
            $('#saveStatus').text('Unsaved changes').removeClass('bg-success').addClass('bg-warning');
        }
    }

    // Save all scores
    function saveAllScores() {
        const classId = currentClassId;
        const subjectId = currentSubjectId;
        const term = $('#termSelect').val();

        if (!classId || !subjectId || !term) {
            alert('Please select class, subject, and term');
            return;
        }

        // Validate all scores
        let hasError = false;
        $('.score-input').each(function() {
            const value = parseInt($(this).val()) || 0;
            const type = $(this).data('type');
            if (type === 'ca1' || type === 'ca2' || type === 'ca3') {
                if (value > 30) {
                    alert('CA scores cannot exceed 30');
                    $(this).focus();
                    hasError = true;
                    return false;
                }
            }
            if (type === 'exam') {
                if (value > 70) {
                    alert('Exam scores cannot exceed 70');
                    $(this).focus();
                    hasError = true;
                    return false;
                }
            }
        });

        if (hasError) return;

        // Prepare data
        const data = {
            class_id: classId,
            subject_id: subjectId,
            term: term,
            scores: scoresData,
            _token: '{{ csrf_token() }}'
        };

        // Show loading
        $('#saveStatus').text('Saving...').removeClass('bg-warning').addClass('bg-info');

        $.ajax({
            url: '{{ route("scores.save-bulk") }}',
            method: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    originalScores = JSON.parse(JSON.stringify(scoresData));
                    $('#saveStatus').text('Saved successfully!').removeClass('bg-info bg-warning').addClass('bg-success');
                    setTimeout(() => {
                        $('#saveStatus').text('Ready').removeClass('bg-success').addClass('bg-success');
                    }, 3000);
                    loadScores();
                } else {
                    alert('Error: ' + response.message);
                    $('#saveStatus').text('Error').removeClass('bg-info bg-warning').addClass('bg-danger');
                }
            },
            error: function(xhr) {
                alert('Error saving scores: ' + (xhr.responseJSON?.message || 'Unknown error'));
                $('#saveStatus').text('Error').removeClass('bg-info bg-warning').addClass('bg-danger');
            }
        });
    }

    // Auto fill scores
    function autoFillScores() {
        if (!studentsList || studentsList.length === 0) {
            alert('No students found to auto-fill scores.');
            return;
        }

        if (confirm('Auto fill scores with random values?')) {
            $('.student-row').each(function() {
                const studentId = $(this).data('student-id');
                const ca1 = Math.floor(Math.random() * 25) + 5;
                const ca2 = Math.floor(Math.random() * 25) + 5;
                const ca3 = Math.floor(Math.random() * 25) + 5;
                const exam = Math.floor(Math.random() * 50) + 20;

                $(this).find('.ca1-input').val(ca1);
                $(this).find('.ca2-input').val(ca2);
                $(this).find('.ca3-input').val(ca3);
                $(this).find('.exam-input').val(exam);

                if (!scoresData[studentId]) {
                    scoresData[studentId] = {};
                }
                scoresData[studentId].ca1_score = ca1;
                scoresData[studentId].ca2_score = ca2;
                scoresData[studentId].ca3_score = ca3;
                scoresData[studentId].exam_score = exam;

                const total = ca1 + ca2 + ca3 + exam;
                $(`#total_${studentId}`).text(total);
                const grade = getGrade(total);
                const badge = $(`#grade_${studentId}`);
                badge.text(grade);
                badge.removeClass().addClass(`badge bg-${getGradeColor(grade)} grade-badge`);
            });
            updateStats();
            $('#saveStatus').text('Unsaved changes').removeClass('bg-success').addClass('bg-warning');
        }
    }

    // Update statistics
    function updateStats() {
        let totalMarks = 0;
        let studentCount = 0;
        let passCount = 0;
        let failCount = 0;

        Object.keys(scoresData).forEach(studentId => {
            const ca1 = parseInt(scoresData[studentId].ca1_score) || 0;
            const ca2 = parseInt(scoresData[studentId].ca2_score) || 0;
            const ca3 = parseInt(scoresData[studentId].ca3_score) || 0;
            const exam = parseInt(scoresData[studentId].exam_score) || 0;
            const total = ca1 + ca2 + ca3 + exam;
            if (total > 0 || ca1 > 0 || ca2 > 0 || ca3 > 0 || exam > 0) {
                totalMarks += total;
                studentCount++;
                if (total >= 40) passCount++;
                else failCount++;
            }
        });

        $('#totalStudents').text(studentCount);
        $('#passCount').text(passCount);
        $('#failCount').text(failCount);
        const avg = studentCount > 0 ? Math.round(totalMarks / studentCount) : 0;
        $('#avgScore').text(avg + '%');
        
        // Update footer
        const avgGrade = studentCount > 0 ? getGrade(avg) : '-';
        $('#footerTotal').text(totalMarks);
        $('#footerGrade').text(avgGrade);
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

    // Reset form
    function resetForm() {
        if (confirm('Reset all scores? Unsaved changes will be lost.')) {
            scoresData = JSON.parse(JSON.stringify(originalScores));
            loadScores();
            $('#saveStatus').text('Ready').removeClass('bg-warning bg-info bg-danger').addClass('bg-success');
        }
    }

    // Hide all content
    function hideAllContent() {
        $('#subjectInfo').hide();
        $('#scoresContainer').hide();
        $('#noDataMessage').hide();
        $('#scoresBody').html('');
        $('#scoresFooter').hide();
    }

    // Show empty state
    function showEmptyState() {
        $('#emptyState').show();
        hideAllContent();
    }

    // Initialize
    $(document).ready(function() {
        showEmptyState();
        
        // Load initial data if all selections are made
        const classId = $('#classSelect').val();
        const subjectId = $('#subjectSelect').val();
        const term = $('#termSelect').val();
        if (classId && subjectId && term) {
            loadScores();
        }
    });
</script>
@endpush
@endsection