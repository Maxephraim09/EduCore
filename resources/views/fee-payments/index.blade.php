@extends('layouts.app')

@section('title', 'Fee Payments')

@section('breadcrumb')
    <li class="breadcrumb-item active">Fee Payments</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> Fee Payments</h5>
            <div class="d-flex gap-2">
                <a href="{{ route('fee-payments.history') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-history"></i> Payment History
                </a>
                <a href="{{ route('fee-payments.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Collect Payment
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Receipt No</th>
                            <th>Student</th>
                            <th>Amount</th>
                            <th>Paid</th>
                            <th>Balance</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>{{ $payment->receipt_number ?? 'N/A' }}</td>
                                <td>
                                    {{ optional($payment->student)->full_name ?? 'N/A' }}<br>
                                    <small class="text-muted">{{ optional($payment->student)->admission_number }}</small>
                                </td>
                                <td>{!! formatCurrency($payment->amount ?? 0) !!}</td>
                                <td>{!! formatCurrency($payment->amount_paid ?? $payment->amount ?? 0) !!}</td>
                                <td>{!! formatCurrency($payment->balance ?? 0) !!}</td>
                                <td>{{ ucfirst($payment->payment_method ?? 'N/A') }}</td>
                                <td>
                                    @php($status = $payment->payment_status ?? $payment->status ?? 'pending')
                                    @if($status === 'success' || $status === 'completed')
                                        <span class="badge bg-success">Success</span>
                                    @elseif($status === 'failed')
                                        <span class="badge bg-danger">Failed</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                                <td>{{ optional($payment->payment_date)->format('d M Y') ?? 'N/A' }}</td>
                                <td>
                                    <a href="{{ route('fee-payments.receipt', $payment->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-receipt"></i> Receipt
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">No fee payments found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $payments->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
