@extends('layouts.app')

@section('title', 'Employee Attendance')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employees</a></li>
    <li class="breadcrumb-item active">Attendance</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-calendar-check me-2"></i>Employee Attendance</h5>
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-3">
                    <label>Select Date</label>
                    <input type="date" id="attendanceDate" class="form-control" value="{{ $today }}">
                </div>
                <div class="col-md-3">
                    <label>Select Month for Report</label>
                    <select id="reportMonth" class="form-control">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $i == $currentMonth ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Select Year</label>
                    <select id="reportYear" class="form-control">
                        @for($i = date('Y') - 2; $i <= date('Y') + 1; $i++)
                            <option value="{{ $i }}" {{ $i == $currentYear ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <button class="btn btn-info w-100" onclick="loadAttendanceReport()">
                        <i class="fas fa-chart-bar"></i> View Report
                    </button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered" id="attendanceTable">
                    <thead>
                        <tr>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th>Check In Time</th>
                            <th>Check Out Time</th>
                            <th>Remarks</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                        <tr>
                            <td>{{ $employee->employee_id }} <input type="hidden" class="emp-id" value="{{ $employee->id }}"></td>
                            <td>{{ $employee->full_name }}</td>
                            <td>{{ $employee->department }}</td>
                            <td>{{ $employee->position }}</td>
                            <td>
                                <select class="form-control attendance-status" data-empid="{{ $employee->id }}">
                                    <option value="present">Present</option>
                                    <option value="absent">Absent</option>
                                    <option value="late">Late</option>
                                    <option value="half-day">Half Day</option>
                                    <option value="holiday">Holiday</option>
                                </select>
                            </td>
                            <td>
                                <input type="time" class="form-control check-in-time" placeholder="09:00">
                            </td>
                            <td>
                                <input type="time" class="form-control check-out-time" placeholder="17:00">
                            </td>
                            <td>
                                <input type="text" class="form-control remarks" placeholder="Remarks">
                            </td>
                            <td>
                                <button class="btn btn-sm btn-success save-attendance" data-empid="{{ $employee->id }}">
                                    <i class="fas fa-save"></i> Save
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="text-center mt-3">
                <button class="btn btn-primary" onclick="saveBulkAttendance()">
                    <i class="fas fa-save"></i> Save All Attendance
                </button>
            </div>
        </div>
    </div>
    
    <!-- Attendance Report Modal -->
    <div class="modal fade" id="reportModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Attendance Report</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="reportContent">
                    <!-- Report content will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Save individual attendance
    $('.save-attendance').click(function() {
        const empId = $(this).data('empid');
        const row = $(this).closest('tr');
        const date = $('#attendanceDate').val();
        const status = row.find('.attendance-status').val();
        const checkInTime = row.find('.check-in-time').val();
        const checkOutTime = row.find('.check-out-time').val();
        const remarks = row.find('.remarks').val();
        
        $.ajax({
            url: '{{ route("employees.attendance.record") }}',
            method: 'POST',
            data: {
                employee_id: empId,
                date: date,
                status: status,
                check_in_time: checkInTime,
                check_out_time: checkOutTime,
                remarks: remarks,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert('Attendance saved successfully!');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error saving attendance');
            }
        });
    });
    
    // Save bulk attendance
    function saveBulkAttendance() {
        const date = $('#attendanceDate').val();
        const attendances = [];
        
        $('#attendanceTable tbody tr').each(function() {
            const empId = $(this).find('.emp-id').val();
            const status = $(this).find('.attendance-status').val();
            
            if (empId && status) {
                attendances.push({
                    employee_id: empId,
                    status: status
                });
            }
        });
        
        $.ajax({
            url: '{{ route("employees.attendance.bulk") }}',
            method: 'POST',
            data: {
                date: date,
                attendances: attendances,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert('Bulk attendance saved successfully for ' + attendances.length + ' employees!');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error saving bulk attendance');
            }
        });
    }
    
    // Load attendance report
    function loadAttendanceReport() {
        const month = $('#reportMonth').val();
        const year = $('#reportYear').val();
        
        $.ajax({
            url: '{{ route("employees.attendance.report") }}',
            method: 'GET',
            data: { month: month, year: year },
            success: function(data) {
                let html = '<h6>Attendance Summary for ' + $('#reportMonth option:selected').text() + ' ' + year + '</h6>';
                html += '<div class="row">';
                
                const colors = {
                    'present': 'success',
                    'absent': 'danger',
                    'late': 'warning',
                    'half-day': 'info',
                    'holiday': 'secondary'
                };
                
                for (const [status, count] of Object.entries(data)) {
                    html += `
                        <div class="col-md-3 mb-3">
                            <div class="card bg-${colors[status] || 'primary'} text-white">
                                <div class="card-body text-center">
                                    <h5 class="mb-0">${status.toUpperCase()}</h5>
                                    <h2 class="mt-2 mb-0">${count}</h2>
                                    <small>Days</small>
                                </div>
                            </div>
                        </div>
                    `;
                }
                
                html += '</div>';
                $('#reportContent').html(html);
                $('#reportModal').modal('show');
            },
            error: function() {
                alert('Error loading attendance report');
            }
        });
    }
</script>
@endpush
@endsection