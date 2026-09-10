@extends('layouts.app')

@section('title', 'Overdraft Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('staff-overdrafts.index') }}">Overdrafts</a></li>
    <li class="breadcrumb-item active">Overdraft Details</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Overdraft Details</h5>
            <div>
                @if($overdraft->status == 'active')
                    <button class="btn btn-success btn-sm" onclick="recordWithdrawal({{ $overdraft->id }})">
                        <i class="fas fa-money-bill-wave"></i> Withdraw
                    </button>
                    <button class="btn btn-primary btn-sm" onclick="recordRepayment({{ $overdraft->id }})">
                        <i class="fas fa-hand-holding-usd"></i> Repay
                    </button>
                @endif
                <a href="{{ route('overdrafts.statement', $overdraft->id) }}" class="btn btn-info btn-sm">
                    <i class="fas fa-file-invoice"></i> Statement
                </a>
                <a href="{{ route('overdrafts.edit', $overdraft->id) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('staff-overdrafts.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Employee Information</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th width="35%">Employee Name</th>
                            <td>{{ $overdraft->employee->full_name }}</td>
                        </tr>
                        <tr>
                            <th>Employee ID</th>
                            <td>{{ $overdraft->employee->employee_id }}</td>
                        </tr>
                        <tr>
                            <th>Position</th>
                            <td>{{ $overdraft->employee->position }}</td>
                        </tr>
                        <tr>
                            <th>Department</th>
                            <td>{{ $overdraft->employee->department }}</td>
                        </tr>
                        <tr>
                            <th>Monthly Salary</th>
                            <td>₦{{ number_format($overdraft->employee->base_salary + $overdraft->employee->allowances, 2) }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Overdraft Information</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th width="35%">Limit Amount</th>
                            <td>₦{{ number_format($overdraft->limit_amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Used Amount</th>
                            <td><span class="text-warning">₦{{ number_format($overdraft->used_amount, 2) }}</span></td>
                        </tr>
                        <tr>
                            <th>Available Amount</th>
                            <td><span class="text-success">₦{{ number_format($overdraft->available_amount, 2) }}</span></td>
                        </tr>
                        <tr>
                            <th>Interest Rate</th>
                            <td>{{ $overdraft->interest_rate }}% per annum</td>
                        </tr>
                        <tr>
                            <th>Sanction Date</th>
                            <td>{{ date('d M Y', strtotime($overdraft->sanction_date)) }}</td>
                        </tr>
                        <tr>
                            <th>Expiry Date</th>
                            <td>
                                {{ date('d M Y', strtotime($overdraft->expiry_date)) }}
                                @if($overdraft->expiry_date < now())
                                    <span class="badge bg-danger ms-2">Expired</span>
                                @endif
                                                        </td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($overdraft->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($overdraft->status == 'suspended')
                                    <span class="badge bg-warning">Suspended</span>
                                @else
                                    <span class="badge bg-secondary">Closed</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="row mt-4">
                <div class="col-md-12">
                    <h6>Utilization Summary</h6>
                    @php
                        $percentage = ($overdraft->used_amount / $overdraft->limit_amount) * 100;
                    @endphp
                    <div class="progress mb-3" style="height: 30px;">
                        <div class="progress-bar {{ $percentage > 80 ? 'bg-danger' : ($percentage > 50 ? 'bg-warning' : 'bg-success') }}" 
                             role="progressbar" 
                             style="width: {{ $percentage }}%">
                            {{ number_format($percentage, 1) }}% Used (₦{{ number_format($overdraft->used_amount, 2) }})
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h6>Total Limit</h6>
                                    <h4>₦{{ number_format($overdraft->limit_amount, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h6>Used Amount</h6>
                                    <h4>₦{{ number_format($overdraft->used_amount, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h6>Available Limit</h6>
                                    <h4>₦{{ number_format($overdraft->available_amount, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            @if($overdraft->remarks)
            <div class="row mt-4">
                <div class="col-md-12">
                    <h6>Remarks</h6>
                    <div class="alert alert-info">
                        {{ $overdraft->remarks }}
                    </div>
                </div>
            </div>
            @endif
            
            @if($transactions->count() > 0)
            <div class="row mt-4">
                <div class="col-md-12">
                    <h6>Recent Transactions</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                    <th>Reference</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($transactions->take(10) as $transaction)
                                <tr>
                                    <td>{{ date('d M Y', strtotime($transaction->transaction_date)) }}</td>
                                    <td>
                                        @if($transaction->type == 'withdrawal')
                                            <span class="badge bg-danger">Withdrawal</span>
                                        @else
                                            <span class="badge bg-success">Repayment</span>
                                        @endif
                                    </td>
                                    <td>₦{{ number_format($transaction->amount, 2) }}</td>
                                    <td>{{ $transaction->description }}</td>
                                    <td>{{ $transaction->reference_number ?? 'N/A' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($transactions->count() > 10)
                            <div class="text-center">
                                <a href="{{ route('overdrafts.statement', $overdraft->id) }}" class="btn btn-info btn-sm">
                                    View Full Statement
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Withdrawal Modal -->
<div class="modal fade" id="withdrawalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Withdrawal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="withdrawalForm">
                    @csrf
                    <input type="hidden" id="withdrawalOverdraftId" name="overdraft_id">
                    <div class="mb-3">
                        <label>Amount (₦) *</label>
                        <input type="number" step="0.01" id="withdrawalAmount" name="amount" class="form-control" required>
                        <small class="text-muted">Available Limit: ₦{{ number_format($overdraft->available_amount, 2) }}</small>
                    </div>
                    <div class="mb-3">
                        <label>Purpose *</label>
                        <textarea id="withdrawalPurpose" name="purpose" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Transaction Date *</label>
                        <input type="date" id="withdrawalDate" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" onclick="submitWithdrawal()">Record Withdrawal</button>
            </div>
        </div>
    </div>
</div>

<!-- Repayment Modal -->
<div class="modal fade" id="repaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Record Repayment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="repaymentForm">
                    @csrf
                    <input type="hidden" id="repaymentOverdraftId" name="overdraft_id">
                    <div class="mb-3">
                        <label>Amount (₦) *</label>
                        <input type="number" step="0.01" id="repaymentAmount" name="amount" class="form-control" required>
                        <small class="text-muted">Outstanding Balance: ₦{{ number_format($overdraft->used_amount, 2) }}</small>
                    </div>
                    <div class="mb-3">
                        <label>Payment Method *</label>
                        <select id="repaymentMethod" name="payment_method" class="form-control" required>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="salary_deduction">Salary Deduction</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Reference Number</label>
                        <input type="text" id="repaymentReference" name="reference_number" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label>Transaction Date *</label>
                        <input type="date" id="repaymentDate" name="transaction_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="submitRepayment()">Record Repayment</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentOverdraftId = {{ $overdraft->id }};
    
    function recordWithdrawal(overdraftId) {
        currentOverdraftId = overdraftId;
        $('#withdrawalOverdraftId').val(overdraftId);
        $('#withdrawalModal').modal('show');
    }
    
    function recordRepayment(overdraftId) {
        currentOverdraftId = overdraftId;
        $('#repaymentOverdraftId').val(overdraftId);
        $('#repaymentModal').modal('show');
    }
    
    function submitWithdrawal() {
        const amount = $('#withdrawalAmount').val();
        const purpose = $('#withdrawalPurpose').val();
        const date = $('#withdrawalDate').val();
        
        if (!amount || amount <= 0) {
            alert('Please enter a valid amount');
            return;
        }
        
        if (!purpose) {
            alert('Please enter the purpose');
            return;
        }
        
        $.ajax({
            url: '/overdrafts/' + currentOverdraftId + '/withdraw',
            method: 'POST',
            data: {
                amount: amount,
                purpose: purpose,
                transaction_date: date,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert('Withdrawal recorded successfully!\nUsed: ₦' + 
                          response.used_amount.toLocaleString(undefined, {minimumFractionDigits: 2}) + 
                          '\nAvailable: ₦' + response.available_amount.toLocaleString(undefined, {minimumFractionDigits: 2}));
                    location.reload();
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.error || 'Error recording withdrawal';
                alert(error);
            }
        });
    }
    
    function submitRepayment() {
        const amount = $('#repaymentAmount').val();
        const paymentMethod = $('#repaymentMethod').val();
        const reference = $('#repaymentReference').val();
        const date = $('#repaymentDate').val();
        
        if (!amount || amount <= 0) {
            alert('Please enter a valid amount');
            return;
        }
        
        $.ajax({
            url: '/overdrafts/' + currentOverdraftId + '/repay',
            method: 'POST',
            data: {
                amount: amount,
                payment_method: paymentMethod,
                reference_number: reference,
                transaction_date: date,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert('Repayment recorded successfully!\nRemaining balance: ₦' + 
                          response.remaining_balance.toLocaleString(undefined, {minimumFractionDigits: 2}));
                    location.reload();
                }
            },
            error: function(xhr) {
                const error = xhr.responseJSON?.error || 'Error recording repayment';
                alert(error);
            }
        });
    }
</script>
@endpush
@endsection
