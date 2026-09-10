@extends('layouts.app')

@section('title', 'Assign Subjects to Classes')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('subjects.index') }}">Subjects</a></li>
<li class="breadcrumb-item active">Assign Subjects</li>
@endsection

@section('styles')
<style>
    .assignment-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        transition: all 0.3s ease;
        background: #fff;
    }
    .assignment-card:hover {
        border-color: #667eea;
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.08);
    }
    .assignment-card .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        border-radius: 12px 12px 0 0 !important;
        padding: 15px 20px;
    }
    .assignment-card .card-body {
        padding: 20px;
    }
    .assignment-item {
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
    .assignment-item:hover {
        background: #e9ecef;
        transform: translateX(5px);
    }
    .assignment-item .subject-name {
        font-weight: 600;
        color: #1e293b;
    }
    .assignment-item .teacher-name {
        color: #64748b;
        font-size: 13px;
    }
    .assignment-item .class-name {
        color: #64748b;
        font-size: 13px;
    }
    .assignment-item .badge-term {
        font-size: 11px;
        padding: 3px 12px;
        border-radius: 12px;
        background: #e0e7ff;
        color: #4f46e5;
    }
    .assignment-item .badge-required {
        font-size: 11px;
        padding: 3px 12px;
        border-radius: 12px;
        background: #d1fae5;
        color: #059669;
    }
    .assignment-item .badge-elective {
        font-size: 11px;
        padding: 3px 12px;
        border-radius: 12px;
        background: #fef3c7;
        color: #d97706;
    }
    .stat-box {
        background: #f8fafc;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }
    .stat-box:hover {
        border-color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .stat-box .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #1e293b;
    }
    .stat-box .stat-label {
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
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

            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-value">{{ $classes->count() }}</div>
                        <div class="stat-label">Total Classes</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-value">{{ $subjects->count() }}</div>
                        <div class="stat-label">Total Subjects</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-value">{{ $teachers->count() }}</div>
                        <div class="stat-label">Total Teachers</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="stat-box">
                        <div class="stat-value">{{ $assignments->count() }}</div>
                        <div class="stat-label">Total Assignments</div>
                    </div>
                </div>
            </div>

            <!-- Assignment Form -->
            <div class="assignment-card mb-4">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-plus-circle me-2" style="color: #667eea;"></i>New Assignment
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('subjects.assign.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="class_id" class="form-label">Select Class <span class="text-danger">*</span></label>
                                    <select name="class_id" id="class_id" class="form-select @error('class_id') is-invalid @enderror" required>
                                        <option value="">Choose a class...</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}" {{ old('class_id') == $class->id ? 'selected' : '' }}>
                                                {{ $class->full_class_name ?? $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('class_id')
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
                                    <label for="teacher_id" class="form-label">Select Teacher <span class="text-danger">*</span></label>
                                    <select name="teacher_id" id="teacher_id" class="form-select @error('teacher_id') is-invalid @enderror" required>
                                        <option value="">Choose a teacher...</option>
                                        @foreach($teachers as $teacher)
                                            <option value="{{ $teacher->id }}" {{ old('teacher_id') == $teacher->id ? 'selected' : '' }}>
                                                {{ $teacher->first_name }} {{ $teacher->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('teacher_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="term" class="form-label">Term <span class="text-danger">*</span></label>
                                    <select name="term" id="term" class="form-select" required>
                                        <option value="First Term">First Term</option>
                                        <option value="Second Term">Second Term</option>
                                        <option value="Third Term">Third Term</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="max_marks" class="form-label">Max Marks</label>
                                    <input type="number" name="max_marks" id="max_marks" class="form-control" value="100" min="1" max="100">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="passing_marks" class="form-label">Passing Marks</label>
                                    <input type="number" name="passing_marks" id="passing_marks" class="form-control" value="40" min="0" max="100">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="is_required" id="is_required" class="form-check-input" value="1" checked>
                                        <label for="is_required" class="form-check-label">Required Subject</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Assign Subject
                                </button>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="fas fa-undo me-2"></i>Reset
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Current Assignments -->
            <div class="assignment-card">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-list me-2" style="color: #667eea;"></i>Current Assignments
                    </h6>
                </div>
                <div class="card-body">
                    @if($assignments->count() > 0)
                        @foreach($assignments as $classId => $classAssignments)
                            @php
                                $class = $classAssignments->first()->class;
                            @endphp
                            <div class="mb-4">
                                <h6 class="fw-bold text-primary">
                                    <i class="fas fa-chalkboard me-2"></i>
                                    {{ $class->full_class_name ?? $class->name }}
                                    <span class="badge badge-secondary ms-2">{{ $classAssignments->count() }} subjects</span>
                                </h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>Subject</th>
                                                <th>Teacher</th>
                                                <th>Term</th>
                                                <th>Max Marks</th>
                                                <th>Passing</th>
                                                <th>Type</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($classAssignments as $index => $assignment)
                                                <tr>
                                                    <td>{{ $index + 1 }}</td>
                                                    <td>
                                                        <strong>{{ $assignment->subject->name }}</strong>
                                                        <br>
                                                        <small class="text-muted">{{ $assignment->subject->code }}</small>
                                                    </td>
                                                    <td>
                                                        @if($assignment->teacher)
                                                            {{ $assignment->teacher->first_name }} {{ $assignment->teacher->last_name }}
                                                        @else
                                                            <span class="text-danger">Not Assigned</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <span class="badge badge-primary">{{ $assignment->term }}</span>
                                                    </td>
                                                    <td>{{ $assignment->max_marks }}</td>
                                                    <td>{{ $assignment->passing_marks }}</td>
                                                    <td>
                                                        @if($assignment->is_required)
                                                            <span class="badge badge-success">Required</span>
                                                        @else
                                                            <span class="badge badge-warning">Elective</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm">
                                                            <button type="button" class="btn btn-warning" 
                                                                    onclick="editAssignment({{ $assignment->id }})" 
                                                                    title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <form action="{{ route('subjects.assign.remove', $assignment->id) }}" 
                                                                  method="POST" 
                                                                  onsubmit="return confirm('Are you sure you want to remove this assignment?')"
                                                                  style="display:inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-danger" title="Remove">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-info-circle fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted">No subjects assigned to classes yet.</p>
                            <p class="text-muted small">Use the form above to assign subjects to classes.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function editAssignment(id) {
        alert('Edit assignment ID: ' + id + ' (Feature coming soon)');
    }
</script>
@endpush
@endsection