@extends('layouts.app')

@section('title', 'Duty Roster')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item active">Duty Roster</li>
@endsection

@section('styles')
<style>
    .duty-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        transition: all 0.3s ease;
        background: #fff;
    }
    .duty-card:hover {
        border-color: #667eea;
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.08);
    }
    .duty-card .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        border-radius: 12px 12px 0 0 !important;
        padding: 15px 20px;
    }
    .duty-card .card-body {
        padding: 20px;
    }
    .duty-item {
        background: #f8fafc;
        border-radius: 8px;
        padding: 12px 15px;
        margin-bottom: 8px;
        border-left: 4px solid #667eea;
        transition: all 0.3s ease;
    }
    .duty-item:hover {
        background: #e9ecef;
        transform: translateX(5px);
    }
    .duty-item .duty-time {
        font-weight: 600;
        color: #667eea;
        font-size: 13px;
    }
    .duty-item .duty-person {
        font-weight: 600;
        color: #1e293b;
    }
    .duty-item .duty-role {
        color: #64748b;
        font-size: 13px;
    }
    .duty-item .duty-badge {
        font-size: 11px;
        padding: 3px 12px;
        border-radius: 12px;
    }
    .duty-item .duty-badge.morning { background: #e0e7ff; color: #4f46e5; }
    .duty-item .duty-badge.afternoon { background: #fef3c7; color: #d97706; }
    .duty-item .duty-badge.evening { background: #d1fae5; color: #059669; }
    .duty-item .duty-badge.night { background: #e0e7ff; color: #4f46e5; }
    .duty-status.active { background: #d1fae5; color: #059669; }
    .duty-status.completed { background: #e0e7ff; color: #4f46e5; }
    .duty-status.pending { background: #fef3c7; color: #d97706; }
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
                        <i class="fas fa-user-clock me-2"></i>Duty Roster
                    </h5>
                    <div>
                        <button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#addDutyModal">
                            <i class="fas fa-plus-circle me-1"></i> Assign Duty
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
                                <label for="filterDate">Date</label>
                                <input type="date" id="filterDate" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filterShift">Shift</label>
                                <select id="filterShift" class="form-control">
                                    <option value="">All Shifts</option>
                                    <option value="morning">Morning</option>
                                    <option value="afternoon">Afternoon</option>
                                    <option value="evening">Evening</option>
                                    <option value="night">Night</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="filterRole">Role</label>
                                <select id="filterRole" class="form-control">
                                    <option value="">All Roles</option>
                                    <option value="teacher">Teacher</option>
                                    <option value="admin">Admin</option>
                                    <option value="security">Security</option>
                                    <option value="cleaner">Cleaner</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="button" class="btn btn-primary btn-block" onclick="filterDuties()">
                                    <i class="fas fa-search me-1"></i> Filter
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Duty Roster Display -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="duty-card">
                                <div class="card-header">
                                    <h6 class="mb-0 fw-bold">
                                        <i class="fas fa-list me-2" style="color: #667eea;"></i>Today's Duties
                                        <span class="badge badge-primary ms-2">{{ date('F d, Y') }}</span>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <!-- Duty Items -->
                                    <div class="duty-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="duty-time">7:00 AM - 8:00 AM</span>
                                                <span class="duty-person ms-3">John Smith</span>
                                                <span class="badge duty-badge morning ms-2">Morning</span>
                                            </div>
                                            <div>
                                                <span class="duty-role me-3">Teacher</span>
                                                <span class="badge duty-status active">Active</span>
                                            </div>
                                        </div>
                                        <div class="duty-details mt-1">
                                            <small class="text-muted">📍 School Gate - Student Supervision</small>
                                        </div>
                                    </div>

                                    <div class="duty-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="duty-time">8:00 AM - 10:00 AM</span>
                                                <span class="duty-person ms-3">Mary Johnson</span>
                                                <span class="badge duty-badge morning ms-2">Morning</span>
                                            </div>
                                            <div>
                                                <span class="duty-role me-3">Admin</span>
                                                <span class="badge duty-status active">Active</span>
                                            </div>
                                        </div>
                                        <div class="duty-details mt-1">
                                            <small class="text-muted">📍 Admin Office - Visitor Management</small>
                                        </div>
                                    </div>

                                    <div class="duty-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="duty-time">12:00 PM - 2:00 PM</span>
                                                <span class="duty-person ms-3">Peter Obi</span>
                                                <span class="badge duty-badge afternoon ms-2">Afternoon</span>
                                            </div>
                                            <div>
                                                <span class="duty-role me-3">Security</span>
                                                <span class="badge duty-status pending">Pending</span>
                                            </div>
                                        </div>
                                        <div class="duty-details mt-1">
                                            <small class="text-muted">📍 Main Entrance - Security Check</small>
                                        </div>
                                    </div>

                                    <div class="duty-item">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="duty-time">3:00 PM - 5:00 PM</span>
                                                <span class="duty-person ms-3">Sarah Williams</span>
                                                <span class="badge duty-badge evening ms-2">Evening</span>
                                            </div>
                                            <div>
                                                <span class="duty-role me-3">Cleaner</span>
                                                <span class="badge duty-status completed">Completed</span>
                                            </div>
                                        </div>
                                        <div class="duty-details mt-1">
                                            <small class="text-muted">📍 Classrooms - Cleaning & Sanitization</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <!-- Statistics -->
                            <div class="duty-card mb-3">
                                <div class="card-header">
                                    <h6 class="mb-0 fw-bold">
                                        <i class="fas fa-chart-pie me-2" style="color: #667eea;"></i>Statistics
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Total Duties Today</span>
                                        <span class="fw-bold">12</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Active</span>
                                        <span class="fw-bold text-success">5</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Pending</span>
                                        <span class="fw-bold text-warning">4</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span>Completed</span>
                                        <span class="fw-bold text-primary">3</span>
                                    </div>
                                    <div class="progress mt-2" style="height: 8px;">
                                        <div class="progress-bar bg-success" style="width: 42%"></div>
                                        <div class="progress-bar bg-warning" style="width: 33%"></div>
                                        <div class="progress-bar bg-primary" style="width: 25%"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Actions -->
                            <div class="duty-card">
                                <div class="card-header">
                                    <h6 class="mb-0 fw-bold">
                                        <i class="fas fa-bolt me-2" style="color: #667eea;"></i>Quick Actions
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="d-grid gap-2">
                                        <button class="btn btn-primary" data-toggle="modal" data-target="#addDutyModal">
                                            <i class="fas fa-plus-circle me-2"></i>Assign New Duty
                                        </button>
                                        <button class="btn btn-info">
                                            <i class="fas fa-exchange-alt me-2"></i>Swap Duty
                                        </button>
                                        <button class="btn btn-warning">
                                            <i class="fas fa-edit me-2"></i>Edit Roster
                                        </button>
                                        <button class="btn btn-success">
                                            <i class="fas fa-download me-2"></i>Export Roster
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Duty Modal -->
<div class="modal fade" id="addDutyModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>Assign Duty
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="#" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="duty_person">Staff Member <span class="text-danger">*</span></label>
                        <select id="duty_person" class="form-control" required>
                            <option value="">Select Staff</option>
                            <option value="1">John Smith</option>
                            <option value="2">Mary Johnson</option>
                            <option value="3">Peter Obi</option>
                            <option value="4">Sarah Williams</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="duty_role">Role <span class="text-danger">*</span></label>
                        <select id="duty_role" class="form-control" required>
                            <option value="">Select Role</option>
                            <option value="teacher">Teacher</option>
                            <option value="admin">Admin</option>
                            <option value="security">Security</option>
                            <option value="cleaner">Cleaner</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="duty_date">Date <span class="text-danger">*</span></label>
                                <input type="date" id="duty_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="duty_shift">Shift <span class="text-danger">*</span></label>
                                <select id="duty_shift" class="form-control" required>
                                    <option value="morning">Morning</option>
                                    <option value="afternoon">Afternoon</option>
                                    <option value="evening">Evening</option>
                                    <option value="night">Night</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="duty_from">From <span class="text-danger">*</span></label>
                                <input type="time" id="duty_from" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="duty_to">To <span class="text-danger">*</span></label>
                                <input type="time" id="duty_to" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="duty_location">Location <span class="text-danger">*</span></label>
                        <input type="text" id="duty_location" class="form-control" placeholder="e.g., School Gate, Admin Office" required>
                    </div>
                    <div class="form-group">
                        <label for="duty_notes">Additional Notes</label>
                        <textarea id="duty_notes" class="form-control" rows="2" placeholder="Special instructions..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Assign Duty</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function filterDuties() {
        const date = document.getElementById('filterDate').value;
        const shift = document.getElementById('filterShift').value;
        const role = document.getElementById('filterRole').value;
        
        // AJAX call to filter duties
        alert('Filtering: Date=' + date + ', Shift=' + shift + ', Role=' + role);
    }
</script>
@endpush
@endsection