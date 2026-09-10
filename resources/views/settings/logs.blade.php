@extends('layouts.app')

@section('title', 'System Logs')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('settings.index') }}">Settings</a></li>
    <li class="breadcrumb-item active">System Logs</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Log Statistics</h5>
                    <div>
                        <button onclick="refreshLogs()" class="btn btn-info btn-sm">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                        <button onclick="clearLogs()" class="btn btn-danger btn-sm" id="clearLogsBtn">
                            <i class="fas fa-trash"></i> Clear All Logs
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <h6>Error Logs</h6>
                                    <h2 class="mb-0">{{ $logLevels['error'] }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h6>Warning Logs</h6>
                                    <h2 class="mb-0">{{ $logLevels['warning'] }}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h6>Info Logs</h6>
                                    <h2 class="mb-0">{{ $logLevels['info'] }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-list me-2"></i>System Log Entries</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="logsTable">
                            <thead>
                                <tr>
                                    <th>Date & Time</th>
                                    <th>Level</th>
                                    <th>Message</th>
                                </td>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                <tr>
                                    <td>{{ $log['date'] }}</td>
                                    <td>
                                        @if(strtolower($log['type']) == 'error')
                                            <span class="badge bg-danger">ERROR</span>
                                        @elseif(strtolower($log['type']) == 'warning')
                                            <span class="badge bg-warning">WARNING</span>
                                        @elseif(strtolower($log['type']) == 'info')
                                            <span class="badge bg-info">INFO</span>
                                        @else
                                            <span class="badge bg-secondary">{{ strtoupper($log['type']) }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $log['message'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i> 
                        Showing last 1000 log entries. Logs are stored in <code>storage/logs/laravel.log</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#logsTable').DataTable({
            pageLength: 50,
            responsive: true,
            order: [[0, 'desc']]
        });
    });
    
    function refreshLogs() {
        location.reload();
    }
    
    function clearLogs() {
        if (confirm('Are you sure you want to clear all system logs? This action cannot be undone.')) {
            $.ajax({
                url: '{{ route("settings.clear-logs") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert('Logs cleared successfully!');
                        location.reload();
                    } else {
                        alert('Failed to clear logs');
                    }
                },
                error: function() {
                    alert('Error clearing logs');
                }
            });
        }
    }
</script>
@endpush
@endsection