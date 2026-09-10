@extends('layouts.app')

@section('title', $class->full_class_name . ' - Students')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('classes.index') }}">Classes</a></li>
    <li class="breadcrumb-item"><a href="{{ route('classes.show', $class->id) }}">{{ $class->full_class_name }}</a></li>
    <li class="breadcrumb-item active">Students</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-user-graduate me-2"></i>Students in {{ $class->full_class_name }}</h5>
            <div>
                <span class="badge bg-primary me-2">{{ $class->students->count() }} Students</span>
                <a href="{{ route('students.create') }}?class_id={{ $class->id }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-user-plus"></i> Add Student
                </a>
                <button class="btn btn-success btn-sm" onclick="window.print()">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="studentsTable">
                    <thead>
                        <tr>
                            <th>S/N</th>
                            <th>Admission No</th>
                            <th>Photo</th>
                            <th>Student Name</th>
                            <th>Gender</th>
                            <th>Date of Birth</th>
                            <th>Parent/Guardian</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($class->students as $index => $student)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $student->admission_number }}</td>
                            <td>
                                @if($student->photo)
                                    <img src="{{ Storage::url($student->photo) }}" alt="{{ $student->full_name }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                                @else
                                    <div class="bg-secondary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        {{ strtoupper(substr($student->first_name, 0, 1)) }}
                                    </div>
                                @endif
                            </td>
                            <td>{{ $student->full_name }}</td>
                            <td>{{ $student->gender ?? 'N/A' }}</td>
                            <td>{{ $student->date_of_birth ? date('d M Y', strtotime($student->date_of_birth)) : 'N/A' }}</td>
                            <td>{{ $student->father_name ?? $student->mother_name ?? 'N/A' }}</td>
                            <td>{{ $student->phone ?? $student->father_phone ?? 'N/A' }}</td>
                            <td>
                                @if($student->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('students.show', $student->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('students.edit', $student->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger" onclick="removeStudent({{ $class->id }}, {{ $student->id }})">
                                        <i class="fas fa-user-minus"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#studentsTable').DataTable({
            pageLength: 10,
            responsive: true,
            order: [[1, 'asc']]
        });
    });

    function removeStudent(classId, studentId) {
        if (confirm('Are you sure you want to remove this student from the class?')) {
            $.ajax({
                url: '/classes/' + classId + '/remove-student/' + studentId,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    location.reload();
                },
                error: function(xhr) {
                    alert('Error removing student');
                }
            });
        }
    }
</script>
@endpush
@endsection