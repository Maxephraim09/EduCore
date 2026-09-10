@extends('layouts.app')

@section('title', 'Expense Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Expenses</a></li>
    <li class="breadcrumb-item active">Expense Details</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Expense Details</h5>
            <div>
                @if($expense->status == 'pending')
                    <a href="{{ route('expenses.edit', $expense->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('expenses.approve', $expense->id) }}" class="btn btn-success btn-sm" onclick="return confirm('Approve this expense?')">
                        <i class="fas fa-check"></i> Approve
                    </a>
                    <a href="{{ route('expenses.reject', $expense->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Reject this expense?')">
                        <i class="fas fa-times"></i> Reject
                    </a>
                @endif
                <a href="{{ route('expenses.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="35%">Expense Number</th>
                            <td><strong>{{ $expense->expense_number }}</strong></td>
                        </tr>
                        <tr>
                            <th>Expense Date</th>
                            <td>{{ date('d M Y', strtotime($expense->expense_date)) }}</td>
                        </tr>
                        <tr>
                            <th>Category</th>
                            <td>{{ $expense->category->name }} ({{ $expense->category->code }})</td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <td><h4 class="text-danger">₦{{ number_format($expense->amount, 2) }}</h4></td>
                        </tr>
                        <tr>
                            <th>Payment Method</th>
                            <td>
                                @if($expense->payment_method == 'cash')
                                    <i class="fas fa-money-bill"></i> Cash
                                @elseif($expense->payment_method == 'bank_transfer')
                                    <i class="fas fa-university"></i> Bank Transfer
                                @else
                                    <i class="fas fa-money-check"></i> Cheque
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Vendor Name</th>
                            <td>{{ $expense->vendor_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Invoice Number</th>
                            <td>{{ $expense->invoice_number ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="35%">Status</th>
                            <td>
                                @if($expense->status == 'approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($expense->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created By</th>
                            <td>{{ $expense->creator->name ?? 'System' }}</td>
                        </tr>
                        <tr>
                            <th>Created Date</th>
                            <td>{{ $expense->created_at->format('d M Y h:i A') }}</td>
                        </tr>
                        @if($expense->approved_by)
                        <tr>
                            <th>Approved By</th>
                            <td>{{ $expense->approver->name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Approved Date</th>
                            <td>{{ $expense->updated_at->format('d M Y h:i A') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-12">
                    <h6>Description</h6>
                    <div class="card bg-light">
                        <div class="card-body">
                            {{ $expense->description }}
                        </div>
                    </div>
                </div>
            </div>
            
            @if($expense->receipt_path)
            <div class="row mt-3">
                <div class="col-md-12">
                    <h6>Receipt/Attachment</h6>
                    <div class="card">
                        <div class="card-body text-center">
                            @php
                                $extension = pathinfo($expense->receipt_path, PATHINFO_EXTENSION);
                            @endphp
                            @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                <img src="{{ Storage::url($expense->receipt_path) }}" alt="Receipt" style="max-width: 100%; max-height: 400px;">
                            @else
                                <a href="{{ Storage::url($expense->receipt_path) }}" target="_blank" class="btn btn-primary">
                                    <i class="fas fa-download"></i> Download Receipt
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection