@extends('layouts.app')

@section('title', 'Other Incomes')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Other Incomes</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Income</h6>
                            <h2 class="mt-2 mb-0">₦{{ number_format($totalIncome, 2) }}</h2>
                        </div>
                        <i class="fas fa-chart-line fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">This Month</h6>
                            <h2 class="mt-2 mb-0">₦{{ number_format($monthlyIncome, 2) }}</h2>
                        </div>
                        <i class="fas fa-calendar-alt fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Today's Income</h6>
                            <h2 class="mt-2 mb-0">₦{{ number_format($todayIncome, 2) }}</h2>
                        </div>
                        <i class="fas fa-calendar-day fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Income Records</h5>
            <div>
                <a href="{{ route('other-incomes.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add Income
                </a>
                <a href="{{ route('income-categories.index') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-tags"></i> Manage Categories
                </a>
                <a href="{{ route('other-incomes.reports') }}" class="btn btn-success btn-sm">
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
                <table class="table table-bordered table-hover" id="incomesTable">
                    <thead>
                        <tr>
                            <th>Income No</th>
                            <th>Date</th>
                            <th>Source</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Reference</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($incomes as $income)
                        <tr>
                            <td><strong>{{ $income->income_number }}</strong></td>
                            <td>{{ date('d M Y', strtotime($income->income_date)) }}</td>
                            <td>{{ $income->source }}</td>
                            <td>{{ $income->category->name }}</td>
                            <td><strong class="text-success">₦{{ number_format($income->amount, 2) }}</strong></td>
                            <td>
                                @if($income->payment_method == 'cash')
                                    <i class="fas fa-money-bill"></i> Cash
                                @elseif($income->payment_method == 'bank_transfer')
                                    <i class="fas fa-university"></i> Bank Transfer
                                @elseif($income->payment_method == 'cheque')
                                    <i class="fas fa-money-check"></i> Cheque
                                @else
                                    <i class="fab fa-paypal"></i> Online
                                @endif
                            </td>
                            <td>{{ $income->reference_number ?? 'N/A' }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('other-incomes.show', $income->id) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('other-incomes.edit', $income->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $income->id }}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $income->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete this income record?
                                                <div class="alert alert-warning mt-2">
                                                    <strong>{{ $income->source }}</strong><br>
                                                    Amount: ₦{{ number_format($income->amount, 2) }}
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <form action="{{ route('other-incomes.destroy', $income->id) }}" method="POST">
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
                {{ $incomes->links() }}
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
        $('#incomesTable').DataTable({
            pageLength: 10,
            responsive: true,
            order: [[1, 'desc']]
        });
    });
</script>
@endpush
@endsection