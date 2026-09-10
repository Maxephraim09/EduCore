@extends('layouts.app')

@section('title', 'Expense Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Expenses</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Expenses</h6>
                            <h2 class="mt-2 mb-0">₦{{ number_format($totalExpenses, 2) }}</h2>
                        </div>
                        <i class="fas fa-receipt fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">This Month</h6>
                            <h2 class="mt-2 mb-0">₦{{ number_format($monthlyExpenses, 2) }}</h2>
                        </div>
                        <i class="fas fa-calendar-alt fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Pending Approvals</h6>
                            <h2 class="mt-2 mb-0">{{ $pendingApprovals }}</h2>
                        </div>
                        <i class="fas fa-clock fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Expense List</h5>
            <div>
                <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add Expense
                </a>
                <a href="{{ route('expense-categories.index') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-tags"></i> Manage Categories
                </a>
                <a href="{{ route('expenses.reports') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-chart-bar"></i> Reports
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
                <table class="table table-bordered table-hover" id="expensesTable">
                    <thead>
                        <tr>
                            <th>Expense No</th>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Vendor</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expenses as $expense)
                        <tr>
                            <td><strong>{{ $expense->expense_number }}</strong></td>
                            <td>{{ date('d M Y', strtotime($expense->expense_date)) }}</td>
                            <td>{{ $expense->category->name }}</td>
                            <td>{{ Str::limit($expense->description, 50) }}</td>
                            <td>{{ $expense->vendor_name ?? 'N/A' }}</td>
                            <td><strong class="text-danger">₦{{ number_format($expense->amount, 2) }}</strong></td>
                            <td>
                                @if($expense->payment_method == 'cash')
                                    <i class="fas fa-money-bill"></i> Cash
                                @elseif($expense->payment_method == 'bank_transfer')
                                    <i class="fas fa-university"></i> Bank Transfer
                                @else
                                    <i class="fas fa-money-check"></i> Cheque
                                @endif
                            </td>
                            <td>
                                @if($expense->status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($expense->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('expenses.show', $expense->id) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($expense->status == 'pending')
                                        <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('expenses.approve', $expense->id) }}" class="btn btn-sm btn-success" title="Approve" onclick="return confirm('Approve this expense?')">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        <a href="{{ route('expenses.reject', $expense->id) }}" class="btn btn-sm btn-danger" title="Reject" onclick="return confirm('Reject this expense?')">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $expense->id }}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $expense->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete this expense?
                                                <div class="alert alert-warning mt-2">
                                                    <strong>Expense #{{ $expense->expense_number }}</strong><br>
                                                    Amount: ₦{{ number_format($expense->amount, 2) }}
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
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
                {{ $expenses->links() }}
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
        $('#expensesTable').DataTable({
            pageLength: 10,
            responsive: true,
            order: [[1, 'desc']]
        });
    });
</script>
@endpush
@endsection