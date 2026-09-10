@extends('layouts.app')

@section('title', 'Single Score Entry')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('scores.index') }}">Scores Entry</a></li>
    <li class="breadcrumb-item active">Single Entry</li>
@endsection

@section('styles')
<style>
    /* Card Improvements */
    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .card-header {
        background: white;
        border-bottom: 1px solid #f1f3f5;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .card-header h5 {
        font-weight: 700;
        font-size: 18px;
        color: #1b1b18;
        margin: 0;
    }
    .card-header h5 i {
        color: #667eea;
    }
    .card-body {
        padding: 28px 32px;
    }

    /* Selection Card */
    .selection-card {
        background: linear-gradient(135deg, #f8f9fa, #f1f3f5);
        border-radius: 12px;
        padding: 24px 28px;
        margin-bottom: 24px;
        border: 1px solid #e5e7eb;
    }
    .selection-card .form-label {
        font-weight: 600;
        font-size: 14px;
        color: #374151;
        margin-bottom: 6px;
    }
    .selection-card .form-select {
        border-radius: 10px;
        border: 1.5px solid #e5e7eb;
        padding: 10px 14px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: white;
    }
    .selection-card .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    /* Student Info Card */
    .student-info-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 20px 24px;
        margin-bottom: 20px;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.25);
    }
    .student-info-card .label {
        opacity: 0.8;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }
    .student-info-card .value {
        font-size: 16px;
        font-weight: 600;
        margin-top: 2px;
    }
    .summary-stats {
        background: rgba(255,255,255,0.15);
        border-radius: 10px;
        padding: 12px 16px;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.1);
    }
    .summary-stats .stat-item {
        text-align: center;
        padding: 4px 8px;
    }
    .summary-stats .stat-item .number {
        font-size: 22px;
        font-weight: 700;
        color: white;
    }
    .summary-stats .stat-item .label {
        font-size: 10px;
        color: rgba(255,255,255,0.7);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Score Table */
    .score-table {
        margin-bottom: 0;
        font-size: 14px;
    }
    .score-table th {
        background: #f8f9fa;
        font-weight: 600;
        color: #374151;
        padding: 12px 10px;
        border-bottom: 2px solid #e5e7eb;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    .score-table td {
        padding: 10px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f3f5;
    }
    .score-table .total-score {
        font-weight: 700;
        font-size: 16px;
        color: #1b1b18;
    }
    .score-table .grade-badge {
        font-size: 13px;
        padding: 4px 14px;
        border-radius: 20px;
        font-weight: 600;
    }
    .subject-row:hover {
        background: #f8f9ff;
        transition: background 0.2s;
    }
    .subject-row .subject-name {
        font-weight: 500;
        color: #1b1b18;
    }

    /* Score Inputs */
    .score-input {
        width: 65px !important;
        text-align: center;
        display: inline-block;
        padding: 6px 8px !important;
        font-size: 14px !important;
        border-radius: 8px !important;
        border: 1.5px solid #e5e7eb !important;
        transition: all 0.3s ease !important;
    }
    .score-input:focus {
        border-color: #667eea !important;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.15) !important;
    }
    .score-input::-webkit-inner-spin-button,
    .score-input::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .score-input[type="number"] {
        -moz-appearance: textfield;
    }

    /* Buttons */
    .btn-save-all {
        padding: 10px 30px;
        font-weight: 600;
        border-radius: 10px;
        font-size: 14px;
        background: linear-gradient(135deg, #10b981, #059669);
        border: none;
        color: white;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
        transition: all 0.3s ease;
    }
    .btn-save-all:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.35);
        color: white;
    }
    .btn-save-all:active {
        transform: translateY(0);
    }

    /* Table Responsive */
    .table-responsive {
        max-height: 500px;
        overflow-y: auto;
        border-radius: 12px;
        border: 1px solid #f1f3f5;
    }
    .table-responsive::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .table-responsive::-webkit-scrollbar-track {
        background: #f1f3f5;
        border-radius: 3px;
    }
    .table-responsive::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 3px;
    }
    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

    /* Footer Row */
    .table-secondary {
        background: #f8f9fa !important;
    }
    .table-secondary td {
        font-weight: 600;
        padding: 12px 10px;
        border-top: 2px solid #e5e7eb;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    .empty-state i {
        font-size: 56px;
        color: #d1d5db;
        margin-bottom: 16px;
    }
    .empty-state h5 {
        font-size: 20px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }
    .empty-state p {
        color: #6b7280;
        font-size: 14px;
        max-width: 400px;
        margin: 0 auto;
    }

    /* No Data Message */
    .no-data-message {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 40px 20px;
        text-align: center;
    }
    .no-data-message i {
        color: #3b82f6;
    }
    .no-data-message p {
        color: #1e40af;
        font-weight: 500;
    }

    /* Status Badge */
    #saveStatus {
        padding: 8px 16px;
        font-weight: 600;
        font-size: 13px;
        border-radius: 20px;
    }
    .badge.bg-success {
        background: linear-gradient(135deg, #10b981, #059669) !important;
    }
    .badge.bg-warning {
        background: linear-gradient(135deg, #f59e0b, #d97706) !important;
        color: white;
    }
    .badge.bg-info {
        background: linear-gradient(135deg, #3b82f6, #2563eb) !important;
        color: white;
    }
    .badge.bg-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626) !important;
        color: white;
    }

    /* Action Buttons in Table */
    .btn-outline-danger {
        border-radius: 8px;
        padding: 4px 8px;
        font-size: 12px;
        border-color: #fca5a5;
        color: #dc2626;
        transition: all 0.2s;
    }
    .btn-outline-danger:hover {
        background: #dc2626;
        border-color: #dc2626;
        color: white;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .card-body {
            padding: 16px 20px;
        }
        .selection-card {
            padding: 16px 20px;
        }
        .student-info-card {
            padding: 16px;
        }
        .student-info-card .value {
            font-size: 14px;
        }
        .summary-stats .stat-item .number {
            font-size: 18px;
        }
        .score-input {
            width: 50px !important;
            font-size: 12px !important;
            padding: 4px 6px !important;
        }
        .score-table th,
        .score-table td {
            padding: 6px 4px;
            font-size: 12px;
        }
        .score-table .total-score {
            font-size: 14px;
        }
        .score-table .grade-badge {
            font-size: 11px;
            padding: 2px 10px;
        }
        .btn-save-all {
            padding: 8px 20px;
            font-size: 13px;
        }
        .btn-secondary {
            padding: 8px 16px;
            font-size: 13px;
        }
    }

    @media (max-width: 576px) {
        .summary-stats .stat-item .number {
            font-size: 16px;
        }
        .summary-stats .stat-item .label {
            font-size: 9px;
        }
        .score-input {
            width: 40px !important;
            font-size: 11px !important;
            padding: 2px 4px !important;
        }
        .empty-state i {
            font-size: 40px;
        }
        .empty-state h5 {
            font-size: 17px;
        }
    }

    /* Animation */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .student-info-card {
        animation: fadeIn 0.4s ease;
    }
    .subject-row {
        animation: fadeIn 0.3s ease;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-user-edit me-2"></i>Single Score Entry</h5>
                    <span class="badge bg-info">Student Scores</span>
                </div>
                <div class="card-body">
                    <!-- Selection Form -->
                    <div class="selection-card">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label for="classSelect" class="form-label">Select Class <span class="text-danger">*</span></label>
                                <select id="classSelect" class="form-select" onchange="loadStudents()">
                                    <option value="">-- Select Class --</option>
                                    @foreach($classes as $class)
                                        <option value="{{ $class->id }}" {{ ($classId ?? '') == $class->id ? 'selected' : '' }}>
                                            {{ $class->full_class_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="termSelect" class="form-label">Select Term <span class="text-danger">*</span></label>
                                <select id="termSelect" class="form-select" onchange="loadStudentScores()">
                                    <option value="">-- Select Term --</option>
                                    <option value="First Term" {{ ($term ?? '') == 'First Term' ? 'selected' : '' }}>First Term</option>
                                    <option value="Second Term" {{ ($term ?? '') == 'Second Term' ? 'selected' : '' }}>Second Term</option>
                                    <option value="Third Term" {{ ($term ?? '') == 'Third Term' ? 'selected' : '' }}>Third Term</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="studentSelect" class="form-label">Select Student <span class="text-danger">*</span></label>
                                <select id="studentSelect" class="form-select" onchange="loadStudentScores()">
                                    <option value="">-- Select Student --</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Student Info -->
                    <div id="studentInfo" style="display: none;">
                        <div class="student-info-card">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="label">Student Name</div>
                                            <div class="value" id="studentName">-</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="label">Admission Number</div>
                                            <div class="value" id="admissionNumber">-</div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="label">Class</div>
                                            <div class="value" id="studentClass">-</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row summary-stats">
                                        <div class="col-4 stat-item">
                                            <div class="number" id="totalSubjects">0</div>
                                            <div class="label">Subjects</div>
                                        </div>
                                        <div class="col-4 stat-item">
                                            <div class="number" id="totalMarks">0</div>
                                            <div class="label">Total Marks</div>
                                        </div>
                                        <div class="col-4 stat-item">
                                            <div class="number" id="avgScore">0%</div>
                                            <div class="label">Average</div>
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
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="20%">Subject</th>
                                        <th width="12%">CA 1 (30)</th>
                                        <th width="12%">CA 2 (30)</th>
                                        <th width="12%">CA 3 (30)</th>
                                        <th width="12%">Exam (70)</th>
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
                                        <td colspan="6" class="text-end fw-bold">Summary</td>
                                        <td id="footerTotal" class="fw-bold">0</td>
                                        <td id="footerGrade" class="fw-bold">-</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="d-flex flex-wrap justify-content-between align-items-center mt-3">
                            <div class="d-flex gap-2 flex-wrap">
                                <button class="btn btn-save-all" onclick="saveAllScores()">
                                    <i class="fas fa-save me-1"></i> Save All Scores
                                </button>
                                <button class="btn btn-secondary" onclick="resetForm()">
                                    <i class="fas fa-undo me-1"></i> Reset
                                </button>
                            </div>
                            <div class="mt-2 mt-md-0">
                                <span class="badge bg-success" id="saveStatus">Ready</span>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State (No Selection) -->
                    <div id="emptyState" style="display: block;">
                        <div class="empty-state">
                            <i class="fas fa-user-graduate"></i>
                            <h5>No Student Selected</h5>
                            <p>Please select a class, term, and student to view and enter scores.</p>
                        </div>
                    </div>

                    <!-- No Data Message -->
                    <div id="noDataMessage" style="display: none;">
                        <div class="no-data-message">
                            <i class="fas fa-info-circle fa-2x mb-2 d-block"></i>
                            <p class="mb-0">No subjects found for this student. Please check the student's class registration.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Define the route with a placeholder
    const studentsByClassRoute = @json(route('api.students-by-class', ['classId' => '__CLASS_ID__'], false));

    let currentStudentId = null;
    let currentClassId = null;
    let currentExamId = null;
    let scoresData = {};
    let originalScores = {};

    // Load students based on selected class
    function loadStudents() {
        const classId = $('#classSelect').val();
        if (!classId) {
            $('#studentSelect').html('<option value="">-- Select Student --</option>');
            showEmptyState();
            return;
        }

        currentClassId = classId;

        // Build URL with the actual class ID
        let url = studentsByClassRoute.replace('__CLASS_ID__', classId);

        $.ajax({
            url: url,
            method: 'GET',
            success: function(students) {
                let options = '<option value="">-- Select Student --</option>';
                students.forEach(student => {
                    options += `<option value="${student.id}">${student.admission_number} - ${student.full_name}</option>`;
                });
                $('#studentSelect').html(options);
                hideAllContent();
                showEmptyState();
                $('#scoresBody').html('');
                $('#scoresFooter').hide();
                
                // Auto-load if student was previously selected
                const selectedStudent = '{{ $studentId ?? '' }}';
                if (selectedStudent) {
                    $('#studentSelect').val(selectedStudent);
                    loadStudentScores();
                }
            },
            error: function(xhr) {
                console.error('Error loading students:', xhr);
                alert('Error loading students. Please refresh and try again.');
            }
        });
    }

    // Load student scores
    function loadStudentScores() {
        const studentId = $('#studentSelect').val();
        const term = $('#termSelect').val();

        if (!studentId || !term) {
            if (!studentId) {
                hideAllContent();
                showEmptyState();
                $('#scoresBody').html('');
                $('#scoresFooter').hide();
            }
            return;
        }

        currentStudentId = studentId;

        // Show loading
        $('#scoresBody').html('<tr><td colspan="9" class="text-center py-4"><i class="fas fa-spinner fa-spin me-2"></i> Loading scores...</td></tr>');
        $('#scoresContainer').show();
        $('#emptyState').hide();
        $('#noDataMessage').hide();

        $.ajax({
            url: '{{ route("scores.get-student-scores") }}',
            method: 'GET',
            data: {
                student_id: studentId,
                class_id: currentClassId,
                term: term
            },
            success: function(response) {
                if (response.success) {
                    // Update student info
                    $('#studentName').text(response.student.full_name);
                    $('#admissionNumber').text(response.student.admission_number);
                    $('#studentClass').text(response.class_name);
                    $('#studentInfo').show();

                    // Preserve exam ID for update flow
                    currentExamId = response.exam_id || null;

                    // Store scores data
                    scoresData = {};
                    if (response.scores) {
                        Object.keys(response.scores).forEach(key => {
                            scoresData[key] = response.scores[key];
                        });
                    }
                    originalScores = JSON.parse(JSON.stringify(scoresData));

                    renderScores(response.subjects, scoresData);
                    updateStats();

                    if (response.subjects.length === 0) {
                        $('#noDataMessage').show();
                        $('#scoresBody').html('');
                        $('#scoresFooter').hide();
                    } else {
                        $('#noDataMessage').hide();
                        $('#scoresFooter').show();
                        $('#scoresContainer').show();
                    }
                } else {
                    alert('Error loading scores: ' + response.message);
                }
            },
            error: function(xhr) {
                console.error('Error loading scores:', xhr);
                alert('Error loading scores: ' + (xhr.responseJSON?.message || 'Unknown error'));
            }
        });
    }

    // Render scores table
    function renderScores(subjects, scores) {
        let html = '';
        let totalMarks = 0;
        let subjectCount = 0;

        if (!subjects || subjects.length === 0) {
            $('#scoresBody').html('');
            $('#scoresFooter').hide();
            return;
        }

        subjects.forEach((subject, index) => {
            const score = scores[subject.id] || {};
            const ca1 = score.ca1_score || 0;
            const ca2 = score.ca2_score || 0;
            const ca3 = score.ca3_score || 0;
            const exam = score.exam_score || 0;
            const total = parseInt(ca1) + parseInt(ca2) + parseInt(ca3) + parseInt(exam);
            const grade = getGrade(total);

            totalMarks += total;
            subjectCount++;

            html += `
                <tr class="subject-row" data-subject-id="${subject.id}">
                    <td>${index + 1}</td>
                    <td class="subject-name">${subject.name}</td>
                    <td>
                        <input type="number" class="form-control form-control-sm score-input ca1-input" 
                               value="${ca1}" min="0" max="30" 
                               data-subject="${subject.id}" data-type="ca1"
                               onchange="updateScore(${subject.id}, 'ca1', this.value)">
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm score-input ca2-input" 
                               value="${ca2}" min="0" max="30" 
                               data-subject="${subject.id}" data-type="ca2"
                               onchange="updateScore(${subject.id}, 'ca2', this.value)">
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm score-input ca3-input" 
                               value="${ca3}" min="0" max="30" 
                               data-subject="${subject.id}" data-type="ca3"
                               onchange="updateScore(${subject.id}, 'ca3', this.value)">
                    </td>
                    <td>
                        <input type="number" class="form-control form-control-sm score-input exam-input" 
                               value="${exam}" min="0" max="70" 
                               data-subject="${subject.id}" data-type="exam"
                               onchange="updateScore(${subject.id}, 'exam', this.value)">
                    </td>
                    <td class="total-score text-center" id="total_${subject.id}">${total}</td>
                    <td>
                        <span class="badge bg-${getGradeColor(grade)} grade-badge" id="grade_${subject.id}">${grade}</span>
                    </td>
                    <td>
                        <button class="btn btn-outline-danger" onclick="clearSubjectScores(${subject.id})" title="Clear scores">
                            <i class="fas fa-eraser"></i>
                        </button>
                    </td>
                </tr>
            `;
        });

        $('#scoresBody').html(html);
        $('#scoresFooter').show();
        $('#totalSubjects').text(subjectCount);
        $('#totalMarks').text(totalMarks);
        $('#avgScore').text(subjectCount > 0 ? Math.round(totalMarks / subjectCount) + '%' : '0%');
        
        // Update footer
        const avgGrade = subjectCount > 0 ? getGrade(Math.round(totalMarks / subjectCount)) : '-';
        $('#footerTotal').text(totalMarks);
        $('#footerGrade').text(avgGrade);
    }

    // Update score
    function updateScore(subjectId, type, value) {
        if (!scoresData[subjectId]) {
            scoresData[subjectId] = {};
        }

        const numValue = parseInt(value) || 0;
        
        // Validate ranges
        if (type === 'ca1' || type === 'ca2' || type === 'ca3') {
            if (numValue > 30) {
                alert('CA score cannot exceed 30');
                $(`input[data-subject="${subjectId}"][data-type="${type}"]`).val(scoresData[subjectId][type + '_score'] || 0);
                return;
            }
        }
        if (type === 'exam') {
            if (numValue > 70) {
                alert('Exam score cannot exceed 70');
                $(`input[data-subject="${subjectId}"][data-type="${type}"]`).val(scoresData[subjectId][type + '_score'] || 0);
                return;
            }
        }
        
        scoresData[subjectId][type + '_score'] = numValue;

        // Recalculate total
        const ca1 = parseInt(scoresData[subjectId].ca1_score) || 0;
        const ca2 = parseInt(scoresData[subjectId].ca2_score) || 0;
        const ca3 = parseInt(scoresData[subjectId].ca3_score) || 0;
        const exam = parseInt(scoresData[subjectId].exam_score) || 0;
        const total = ca1 + ca2 + ca3 + exam;

        $(`#total_${subjectId}`).text(total);
        const grade = getGrade(total);
        const badge = $(`#grade_${subjectId}`);
        badge.text(grade);
        badge.removeClass().addClass(`badge bg-${getGradeColor(grade)} grade-badge`);

        updateStats();
        $('#saveStatus').text('Unsaved changes').removeClass('bg-success').addClass('bg-warning');
    }

    // Clear subject scores
    function clearSubjectScores(subjectId) {
        if (confirm('Clear all scores for this subject?')) {
            scoresData[subjectId] = {};
            $(`input[data-subject="${subjectId}"]`).val('');
            $(`#total_${subjectId}`).text('0');
            $(`#grade_${subjectId}`).text('F');
            $(`#grade_${subjectId}`).removeClass().addClass('badge bg-danger grade-badge');
            updateStats();
            $('#saveStatus').text('Unsaved changes').removeClass('bg-success').addClass('bg-warning');
        }
    }

    // Save all scores
    function saveAllScores() {
        const studentId = currentStudentId;
        const term = $('#termSelect').val();

        if (!studentId || !term) {
            alert('Please select student and term');
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
            student_id: studentId,
            class_id: currentClassId,
            term: term,
            exam_id: currentExamId,
            scores: scoresData,
            _token: '{{ csrf_token() }}'
        };

        // Show loading
        $('#saveStatus').text('Saving...').removeClass('bg-warning bg-success').addClass('bg-info');

        $.ajax({
            url: '{{ route("scores.save-single") }}',
            method: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    originalScores = JSON.parse(JSON.stringify(scoresData));
                    $('#saveStatus').text('✓ Saved successfully!').removeClass('bg-info bg-warning').addClass('bg-success');
                    setTimeout(() => {
                        $('#saveStatus').text('Ready').removeClass('bg-success');
                    }, 3000);
                    loadStudentScores();
                } else {
                    alert('Error: ' + response.message);
                    $('#saveStatus').text('Error').removeClass('bg-info bg-warning').addClass('bg-danger');
                }
            },
            error: function(xhr) {
                console.error('Error saving scores:', xhr);
                alert('Error saving scores: ' + (xhr.responseJSON?.message || 'Unknown error'));
                $('#saveStatus').text('Error').removeClass('bg-info bg-warning').addClass('bg-danger');
            }
        });
    }

    // Update statistics
    function updateStats() {
        let totalMarks = 0;
        let subjectCount = 0;

        Object.keys(scoresData).forEach(subjectId => {
            const ca1 = parseInt(scoresData[subjectId].ca1_score) || 0;
            const ca2 = parseInt(scoresData[subjectId].ca2_score) || 0;
            const ca3 = parseInt(scoresData[subjectId].ca3_score) || 0;
            const exam = parseInt(scoresData[subjectId].exam_score) || 0;
            const total = ca1 + ca2 + ca3 + exam;
            if (total > 0 || ca1 > 0 || ca2 > 0 || ca3 > 0 || exam > 0) {
                totalMarks += total;
                subjectCount++;
            }
        });

        $('#totalSubjects').text(subjectCount);
        $('#totalMarks').text(totalMarks);
        const avg = subjectCount > 0 ? Math.round(totalMarks / subjectCount) : 0;
        $('#avgScore').text(avg + '%');
        
        // Update footer
        const avgGrade = subjectCount > 0 ? getGrade(avg) : '-';
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
            loadStudentScores();
            $('#saveStatus').text('Ready').removeClass('bg-warning bg-info bg-danger').addClass('bg-success');
        }
    }

    // Hide all content
    function hideAllContent() {
        $('#studentInfo').hide();
        $('#scoresContainer').hide();
        $('#noDataMessage').hide();
    }

    // Show empty state
    function showEmptyState() {
        $('#emptyState').show();
        hideAllContent();
    }

    // Initialize
    $(document).ready(function() {
        // Show empty state initially
        showEmptyState();
        
        // Load initial data if class and term are preselected
        const classId = $('#classSelect').val();
        const term = $('#termSelect').val();
        if (classId && term) {
            loadStudents();
        }
        
        // Auto-load if student is preselected from URL
        const studentId = '{{ $studentId ?? '' }}';
        if (studentId) {
            setTimeout(() => {
                $('#studentSelect').val(studentId);
                loadStudentScores();
            }, 500);
        }
    });
</script>
@endpush