@extends('layouts.app')

@section('title', 'Student Subjects')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('subjects.index') }}">Subjects</a></li>
<li class="breadcrumb-item active">Student Subjects</li>
@endsection

@section('styles')
<style>
    .student-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        transition: all 0.3s ease;
        background: #fff;
    }
    .student-card:hover {
        border-color: #667eea;
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.08);
    }
    .student-card .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        border-radius: 12px 12px 0 0 !important;
        padding: 15px 20px;
    }
    .student-card .card-body {
        padding: 20px;
    }
    .student-item {
        background: #f8fafc;
        border-radius: 8px;
        padding: 12px 15px;
        margin-bottom: 8px;
        border-left: 4px solid #667eea;
        transition: all 0.3s ease;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .student-item:hover {
        background: #e9ecef;
        transform: translateX(5px);
    }
    .student-item .student-name {
        font-weight: 600;
        color: #1e293b;
    }
    .student-item .student-class {
        color: #64748b;
        font-size: 13px;
    }
    .student-item .student-admission {
        color: #64748b;
        font-size: 12px;
    }
    .subject-tag {
        display: inline-block;
        background: #e0e7ff;
        color: #4f46e5;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 12px;
        margin: 2px;
    }
    .subject-tag:hover {
        background: #4f46e5;
        color: white;
        cursor: pointer;
    }
    .subject-tag .remove-subject {
        margin-left: 4px;
        cursor: pointer;
        font-weight: bold;
    }
    .subject-tag .remove-subject:hover {
        color: #dc2626;
    }
    .filter-card {
        background: #f8fafc;
        border-radius: 8px;
        padding: 15px;
    }
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

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Filter Section -->
            <div class="filter-card mb-4">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filterClass">Filter by Class</label>
                            <select id="filterClass" class="form-control" onchange="filterStudents()">
                                <option value="">All Classes</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->full_class_name ?? $class->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="filterSubject">Filter by Subject</label>
                            <select id="filterSubject" class="form-control" onchange="filterStudents()">
                                <option value="">All Subjects</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="searchStudent">Search Student</label>
                            <input type="text" id="searchStudent" class="form-control" placeholder="Search by name or admission number..." onkeyup="filterStudents()">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="button" class="btn btn-primary btn-block" onclick="filterStudents()">
                                <i class="fas fa-search me-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assign Subject to Student Form -->
            <div class="student-card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-user-plus me-2" style="color: #667eea;"></i>Assign Subject to Student
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('subjects.student-subjects.assign') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="student_id" class="form-label">Select Student <span class="text-danger">*</span></label>
                                    <select name="student_id" id="student_id" class="form-select @error('student_id') is-invalid @enderror" required>
                                        <option value="">Choose a student...</option>
                                        @foreach($students as $student)
                                            <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>
                                                {{ $student->full_name }} ({{ $student->admission_number }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('student_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="subject_id" class="form-label">Select Subject <span class="text-danger">*</span></label>
                                    <select name="subject_id" id="subject_id" class="form-select @error('subject_id') is-invalid @enderror" required>
                                        <option value="">Choose a subject...</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                                {{ $subject->name }} ({{ $subject->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('subject_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-user-plus me-2"></i>Assign Subject
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Student List -->
            <div class="student-card">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-users me-2" style="color: #667eea;"></i>Students & Their Subjects
                        <span class="badge badge-primary ms-2" id="studentCount">{{ $students->count() }} students</span>
                    </h6>
                </div>
                <div class="card-body">
                    @if($students->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="studentTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Admission No.</th>
                                        <th>Student Name</th>
                                        <th>Class</th>
                                        <th>Subjects</th>
                                        <th>Count</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="studentTableBody">
                                    @foreach($students as $index => $student)
                                        @php
                                            $studentSubjects = $student->subjects;
                                        @endphp
                                        <tr class="student-row" data-student-id="{{ $student->id }}" data-class="{{ $student->class_id }}" data-name="{{ strtolower($student->full_name) }}" data-admission="{{ $student->admission_number }}">
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <span class="badge badge-secondary">{{ $student->admission_number }}</span>
                                            </td>
                                            <td>
                                                <strong>{{ $student->full_name }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge badge-info">{{ $student->class_name }}</span>
                                            </td>
                                            <td>
                                                @if($studentSubjects->count() > 0)
                                                    @foreach($studentSubjects as $subject)
                                                        <span class="subject-tag" data-subject-id="{{ $subject->id }}" data-student-id="{{ $student->id }}">
                                                            {{ $subject->name }}
                                                            <span class="remove-subject" onclick="removeSubject({{ $student->id }}, {{ $subject->id }})" title="Remove subject">
                                                                <i class="fas fa-times"></i>
                                                            </span>
                                                        </span>
                                                    @endforeach
                                                @else
                                                    <span class="text-muted">No subjects assigned</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge badge-primary">{{ $studentSubjects->count() }}</span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <button type="button" class="btn btn-info" onclick="viewStudentSubjects({{ $student->id }})" title="View Subjects">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-success" onclick="assignSubjectToStudent({{ $student->id }})" title="Add Subject">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-warning" onclick="bulkAssign({{ $student->id }})" title="Bulk Assign">
                                                        <i class="fas fa-layer-group"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted">No students found.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Student Subjects Modal -->
<div class="modal fade" id="viewSubjectsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-book me-2"></i>Student Subjects
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="viewSubjectsBody">
                <!-- Dynamic content -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Assign Subject Modal -->
<div class="modal fade" id="assignSubjectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2"></i>Assign Subject to Student
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('subjects.student-subjects.assign') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="student_id" id="modal_student_id">
                    <div class="form-group">
                        <label for="modal_subject_id">Select Subject <span class="text-danger">*</span></label>
                        <select name="subject_id" id="modal_subject_id" class="form-control" required>
                            <option value="">Choose a subject...</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->code }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Assign Subject</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Assign Modal -->
<div class="modal fade" id="bulkAssignModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">
                    <i class="fas fa-layer-group me-2"></i>Bulk Assign Subjects
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('subjects.student-subjects.bulk') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="class_id" id="bulk_class_id">
                    <div class="form-group">
                        <label for="bulk_subject_id">Select Subject <span class="text-danger">*</span></label>
                        <select name="subject_id" id="bulk_subject_id" class="form-control" required>
                            <option value="">Choose a subject...</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }} ({{ $subject->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This will assign the selected subject to all students in the class.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Bulk Assign</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function filterStudents() {
        const classFilter = document.getElementById('filterClass').value;
        const subjectFilter = document.getElementById('filterSubject').value;
        const searchTerm = document.getElementById('searchStudent').value.toLowerCase();

        const rows = document.querySelectorAll('.student-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const classId = row.dataset.class;
            const name = row.dataset.name;
            const admission = row.dataset.admission;
            
            let show = true;

            // Class filter
            if (classFilter && classId != classFilter) {
                show = false;
            }

            // Subject filter
            if (subjectFilter && show) {
                const subjectTags = row.querySelectorAll('.subject-tag');
                let hasSubject = false;
                subjectTags.forEach(tag => {
                    if (tag.dataset.subjectId == subjectFilter) {
                        hasSubject = true;
                    }
                });
                if (!hasSubject) {
                    show = false;
                }
            }

            // Search filter
            if (searchTerm && show) {
                if (!name.includes(searchTerm) && !admission.includes(searchTerm)) {
                    show = false;
                }
            }

            row.style.display = show ? '' : 'none';
            if (show) visibleCount++;
        });

        document.getElementById('studentCount').textContent = visibleCount + ' students';
    }

    function assignSubjectToStudent(studentId) {
        document.getElementById('modal_student_id').value = studentId;
        $('#assignSubjectModal').modal('show');
    }

    function bulkAssign(studentId) {
        const row = document.querySelector(`tr[data-student-id="${studentId}"]`);
        if (row) {
            const classId = row.dataset.class;
            document.getElementById('bulk_class_id').value = classId;
        }
        $('#bulkAssignModal').modal('show');
    }

    function viewStudentSubjects(studentId) {
        const modalBody = document.getElementById('viewSubjectsBody');
        modalBody.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x mb-3"></i><p>Loading student subjects...</p></div>';
        $('#viewSubjectsModal').modal('show');

        fetch(`/subjects/student-subjects/student/${studentId}`)
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message || 'Unable to load subjects');
                }

                const subjects = data.subjects || [];
                let html = '<div class="mb-3">';
                html += `<p class="mb-1"><strong>Student:</strong> ${data.student.full_name} (${data.student.admission_number})</p>`;
                html += `<p class="mb-0"><strong>Class:</strong> ${data.student.class_name}</p>`;
                html += '</div>';
                html += '<div class="mb-3">';
                html += `<span class="badge badge-info me-1">Assigned ${data.assigned_count}</span>`;
                html += `<span class="badge badge-secondary">Class subjects ${data.class_subject_count}</span>`;
                html += '</div>';
                html += '<div class="list-group mb-3">';

                if (subjects.length === 0) {
                    html += '<div class="alert alert-warning">No class subjects found for this student.</div>';
                }

                subjects.forEach(subject => {
                    html += `
                        <div class="list-group-item d-flex align-items-center justify-content-between">
                            <div>
                                <h6 class="mb-1">${subject.name}</h6>
                                <small class="text-muted">${subject.code} ${subject.is_required ? '(Required)' : '(Elective)'}</small>
                            </div>
                            <div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="subjectToggle_${subject.id}" ${subject.assigned ? 'checked' : ''} ${subject.is_required ? 'disabled' : ''}
                                           onchange="toggleStudentSubject(${studentId}, ${subject.id}, this.checked, ${subject.is_required ? 'true' : 'false'})">
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                html += '<div class="alert alert-info small">Required subjects are automatically assigned and cannot be removed.</div>';
                modalBody.innerHTML = html;
            })
            .catch(error => {
                modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>${error.message}
                    </div>
                `;
            });
    }

    function toggleStudentSubject(studentId, subjectId, assign, isRequired) {
        if (!assign && isRequired) {
            alert('Required subjects cannot be removed.');
            document.getElementById(`subjectToggle_${subjectId}`).checked = true;
            return;
        }

        const url = assign ? '/subjects/student-subjects' : `/subjects/student-subjects/${studentId}/${subjectId}`;
        const options = {
            method: assign ? 'POST' : 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: assign ? JSON.stringify({ student_id: studentId, subject_id: subjectId }) : null,
        };

        fetch(url, options)
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message || 'Unable to update subject assignment');
                }
                // Refresh the page so counts and subject tags update properly
                location.reload();
            })
            .catch(error => {
                alert(error.message);
                const checkbox = document.getElementById(`subjectToggle_${subjectId}`);
                if (checkbox) checkbox.checked = !assign;
            });
    }

    function removeSubject(studentId, subjectId) {
        if (confirm('Are you sure you want to remove this subject from the student?')) {
            fetch(`/subjects/student-subjects/${studentId}/${subjectId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Failed to remove subject. Please try again.');
                }
            })
            .catch(() => {
                alert('An error occurred. Please try again.');
            });
        }
    }
</script>
@endpush
@endsection