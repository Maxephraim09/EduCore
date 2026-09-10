@extends('layouts.portal-dashboard')

@section('title', 'Payment History | Applicant Portal')
@section('page_title', 'Payment History')

@section('content')
    <div class="portal-card">
        <h3 class="mb-4">Your Payments</h3>

        @if($payments->isEmpty())
            <p class="text-muted">No payment records found.</p>
        @else
            <div class="table-responsive">
                <table class="table table-borderless align-middle">
                    <thead class="text-muted small text-uppercase">
                        <tr>
                            <th>Reference</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                            <tr>
                                <td>{{ $payment->reference }}</td>
                                <td>{{ formatCurrency($payment->amount) }}</td>
                                <td>{{ ucfirst($payment->status) }}</td>
                                <td>{{ $payment->created_at->format('M d, Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
