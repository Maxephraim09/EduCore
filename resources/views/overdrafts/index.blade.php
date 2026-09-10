@extends('layouts.app')

@section('title', 'Staff Overdrafts')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Overdrafts</li>
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
                            <h6 class="mb-0">Total Limit</h6>
                            <h3 class="mt-2 mb-0">₦{{ number_format($totalOverdrafts, 2) }}</h3>
                        </div>
                        <i class="fas fa-credit-card fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Utilized</h6>
                            <h3 class="mt-2 mb-0">₦{{ number_format($totalUsed, 2) }}</h3>
                        </div>
                        <i class="fas fa-chart-line fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Available</h6>
                            <h3 class="mt-2 mb-0">₦{{ number_format($totalAvailable, 2) }}</h3>
                        </div>
                        <i class="fas fa-hand-holding-usd fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Active / Expired</h6>
                            <h3 class="mt-2 mb-0">{{ $activeOverdrafts }} / {{ $expiredOverdrafts }}</h3>
                        </div>
                        <i class="fas fa-chart-bar fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>All Overdraft Facilities</h5>
            <div>
                <a href="{{ route('overdrafts.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Apply Overdraft
                </a>
                <a href="{{ route('overdrafts.reports') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-chart-bar"></i> Reports
                </a>
                <a href="{{ route('overdrafts.auto-expire') }}" class="btn btn-warning btn-sm" onclick="return confirm('Mark expired overdrafts?')">
                    <i class="fas fa-clock"></i> Auto-Expire
                </a>
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

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="overdraftsTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Employee</th>
                            <th>Limit Amount</th>
                            <th>Used Amount</th>
                            <th>Available</th>
                            <th>Interest Rate</th>
                            <th>Utilization</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($overdrafts as $overdraft)
                        <tr>
                            <td>{{ $overdraft->id }}</td>
                            <td>
                                <strong>{{ $overdraft->employee->full_name }}</strong><br>
                                <small class="text-muted">{{ $overdraft->employee->employee_id }}</small>
                            </td>
                            <td>₦{{ number_format($overdraft->limit_amount, 2) }}</td>
                            <td class="text-danger">₦{{ number_format($overdraft->used_amount, 2) }}</td>
                            <td class="text-success">₦{{ number_format($overdraft->available_amount, 2) }}</td>
                            <td>{{ $overdraft->interest_rate }}%</td>
                            <td>
                                @php
                                    $percentage = ($overdraft->used_amount / $overdraft->limit_amount) * 100;
                                @endphp
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-{{ $percentage > 80 ? 'danger' : ($percentage > 50 ? 'warning' : 'success') }}" 
                                         role="progressbar" 
                                         style="width: {{ $percentage }}%" 
                                         aria-valuenow="{{ $percentage }}" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        {{ number_format($percentage, 1) }}%
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($overdraft->expiry_date < now())
                                    <span class="badge bg-danger">Expired</span>
                                @else
                                    {{ date('d M Y', strtotime($overdraft->expiry_date)) }}
                                @endif
                            </td>
                            <td>
                                @if($overdraft->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($overdraft->status == 'expired')
                                    <span class="badge bg-danger">Expired</span>
                                @else
                                    <span class="badge bg-secondary">Closed</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('overdrafts.show', $overdraft->id) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($overdraft->status == 'active')
                                        <button class="btn btn-sm btn-success" onclick="recordTransaction({{ $overdraft->id }})" title="Record Draw/Repay">
                                            <i class="fas fa-exchange-alt"></i>
                                        </button>
                                        <a href="{{ route('overdrafts.edit', $overdraft->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $overdraft->id }}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $overdraft->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete overdraft for {{ $overdraft->employee->full_name }}?
                                                @if($overdraft->used_amount > 0)
                                                    <div class="alert alert-warning mt-2">
                                                        <i class="fas fa-exclamation-triangle"></i> 
                                                        This overdraft has utilized amount of ₦{{ number_format($overdraft->used_amount, 2) }} and cannot be deleted.
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                @if($overdraft->used_amount == 0)
                                                    <form action="{{ route('overdrafts.destroy', $overdraft->id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                        <button type="submit" class="btn btn-danger">Delete</button>
                                                    </form>
                                                @else
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $overdrafts->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Transaction Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Overdraft Transaction</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="transactionForm">
                    @csrf
                    <input type="hidden" id="overdraftId" name="overdraft_id">
                    <div class="mb-3">
                        <label>Transaction Type</label>
                        <select id="transactionType" name="type" class="form-control" required>
                            <option value="draw">Draw Money (Increase Usage)</option>
                            <option value="repay">Repay Money (Decrease Usage)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Amount (₦)</label>
                        <input type="number" step="0.01" id="transactionAmount" name="amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Remarks (Optional)</label>
                        <textarea id="transactionRemarks" name="remarks" class="form-control" rows="2"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitTransaction()">Record Transaction</button>
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
    let currentOverdraftId = null;
    
    $(document).ready(function() {
        $('#overdraftsTable').DataTable({
            pageLength: 10,
            responsive: true,
            order: [[0, 'desc']]
        });
    });
    
    function recordTransaction(overdraftId) {
        currentOverdraftId = overdraftId;
        $('#overdraftId').val(overdraftId);
        $('#transactionModal').modal('show');
    }
    
    function submitTransaction() {
        const type = $('#transactionType').val();
        const amount = $('#transactionAmount').val();
        const remarks = $('#transactionRemarks').val();
        
        if (!amount || amount <= 0) {
            alert('Please enter a valid amount');
            return;
        }
        
        $.ajax({
            url: '/overdrafts/' + currentOverdraftId + '/usage',
            method: 'POST',
            data: {
                amount: amount,
                type: type,
                remarks: remarks,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    location.reload();
                } else {
                    alert('Error: ' + response.error);
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.error || 'Error recording transaction';
                alert(error);
            }
        });
    }
</script>
@endpush
@endsection
