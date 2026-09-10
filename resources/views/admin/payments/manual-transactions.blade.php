@extends('layouts.app')

@section('title','Manual Transactions')

@section('content')
<div class="container py-4">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5>Manual Payment Transactions</h5>
        </div>
        <div class="card-body">
            <table class="table table-striped" id="manualTxTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Reference</th>
                        <th>Applicant</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $tx)
                    <tr>
                        <td>{{ $tx->id }}</td>
                        <td>{{ $tx->reference }}</td>
                        <td>{{ optional($tx->applicant)->name }}</td>
                        <td>₦{{ number_format($tx->amount,2) }}</td>
                        <td>{{ $tx->status }}</td>
                        <td>{{ $tx->created_at }}</td>
                        <td>
                            @if($tx->status === 'awaiting_manual_verification' || $tx->status === 'pending_manual')
                                <form action="{{ route('application.admin.payments.manual.verify', $tx->id) }}" method="POST" style="display:inline">
                                    @csrf
                                    <button class="btn btn-sm btn-success">Mark Verified</button>
                                </form>
                                <form action="{{ route('application.admin.payments.manual.reject', $tx->id) }}" method="POST" style="display:inline">
                                    @csrf
                                    <button class="btn btn-sm btn-danger">Reject</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
