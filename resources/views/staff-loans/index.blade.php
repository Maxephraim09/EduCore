@extends('layouts.app')

@section('title', 'Staff Loans')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Staff Loans</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Loans Disbursed</h6>
                            <h2 class="mt-2 mb-0">₦{{ number_format($totalLoans, 2) }}</h2>
                        </div>
                        <i class="fas fa-hand-holding-usd fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Active Loans Outstanding</h6>
                            <h2 class="mt-2 mb-0">₦{{ number_format($activeLoans, 2) }}</h2>
                        </div>
                        <i class="fas fa-chart-line fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Interest Earned</h6>
                            <h2 class="mt-2 mb-0">₦{{ number_format($totalInterest, 2) }}</h2>
                        </div>
                        <i class="fas fa-percent fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>All Loan Records</h5>
            <div>
                <a href="{{ route('staff-loans.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> New Loan Request
                </a>
                <a href="{{ route('staff-loans.reports') }}" class="btn btn-info btn-sm">
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
                <table class="table table-bordered table-hover" id="loansTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Employee</th>
                            <th>Loan Amount</th>
                            <th>Interest Rate</th>
                            <th>Monthly Installment</th>
                            <th>Total Payable</th>
                            <th>Paid Amount</th>
                            <th>Remaining</th>
                            <th>Status</th>
                            <th>Sanction Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($loans as $loan)
                        <tr>
                            <td>{{ $loan->id }}</td>
                            <td>
                                <strong>{{ $loan->employee->full_name }}</strong><br>
                                <small class="text-muted">{{ $loan->employee->employee_id }}</small>
                            </td>
                            <td>₦{{ number_format($loan->amount, 2) }}</td>
                            <td>{{ $loan->interest_rate }}%</td>
                            <td>₦{{ number_format($loan->monthly_installment, 2) }}</td>
                            <td>₦{{ number_format($loan->total_payable, 2) }}</td>
                            <td>₦{{ number_format($loan->paid_amount, 2) }}</td>
                            <td>
                                <strong class="text-danger">₦{{ number_format($loan->remaining_amount, 2) }}</strong>
                            </td>
                            <td>
                                @if($loan->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($loan->status == 'completed')
                                    <span class="badge bg-info">Completed</span>
                                @else
                                    <span class="badge bg-danger">Defaulted</span>
                                @endif
                            </td>
                            <td>{{ date('d M Y', strtotime($loan->sanction_date)) }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('staff-loans.show', $loan->id) }}" class="btn btn-sm btn-info" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button class="btn btn-sm btn-success" onclick="recordPayment({{ $loan->id }})" title="Record Payment">
                                        <i class="fas fa-money-bill"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $loan->id }}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $loan->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete this loan record?
                                                @if($loan->paid_amount > 0)
                                                    <div class="alert alert-warning mt-2">
                                                        <i class="fas fa-exclamation-triangle"></i> 
                                                        This loan has received payments and cannot be deleted.
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                @if($loan->paid_amount == 0)
                                                    <form action="{{ route('staff-loans.destroy', $loan->id) }}" method="POST">
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
                {{ $loans->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Loan Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="paymentForm">
                    @csrf
                    <input type="hidden" id="loanId" name="loan_id">
                    <div class="mb-3">
                        <label>Payment Amount (₦)</label>
                        <input type="number" step="0.01" id="paymentAmount" name="amount" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Payment Date</label>
                        <input type="date" id="paymentDate" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Payment Method</label>
                        <select id="paymentMethod" name="payment_method" class="form-control" required>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="salary_deduction">Salary Deduction</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitPayment()">Record Payment</button>
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
    let currentLoanId = null;
    
    $(document).ready(function() {
        $('#loansTable').DataTable({
            pageLength: 10,
            responsive: true,
            order: [[0, 'desc']]
        });
    });
    
    function recordPayment(loanId) {
        currentLoanId = loanId;
        $('#loanId').val(loanId);
        $('#paymentModal').modal('show');
    }
    
    function submitPayment() {
        const amount = $('#paymentAmount').val();
        const paymentDate = $('#paymentDate').val();
        const paymentMethod = $('#paymentMethod').val();
        
        if (!amount || amount <= 0) {
            alert('Please enter a valid amount');
            return;
        }
        
        $.ajax({
            url: '/staff-loans/' + currentLoanId + '/payment',
            method: 'POST',
            data: {
                amount: amount,
                payment_date: paymentDate,
                payment_method: paymentMethod,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert('Payment recorded successfully!\nRemaining balance: ₦' + 
                          response.remaining_balance.toLocaleString(undefined, {minimumFractionDigits: 2}));
                    location.reload();
                } else {
                    alert('Error: ' + response.error);
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.error || 'Error recording payment';
                alert(error);
            }
        });
    }
</script>
@endpush
@endsection