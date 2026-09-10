@extends('layouts.app')

@section('title', 'Payment History')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('fee-payments.index') }}">Fee Payments</a></li>
    <li class="breadcrumb-item active">Payment History</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-history"></i> Payment History</h5>
            <div class="d-flex gap-2">
                <select id="filterStudent" class="form-select form-select-sm w-auto">
                    <option value="">All Students</option>
                    @foreach($students as $student)
                        <option value="{{ $student->id }}" {{ $studentId == $student->id ? 'selected' : '' }}>
                            {{ $student->admission_number }} - {{ $student->full_name }}
                        </option>
                    @endforeach
                </select>
                <a href="{{ route('fee-payments.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> New Payment
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

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="historyTable">
                    <thead>
                        <tr>
                            <th>Receipt No</th>
                            <th>Student</th>
                            <th>Amount (₦)</th>
                            <th>Payment Method</th>
                            <th>Term</th>
                            <th>Academic Year</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                        <tr>
                            <td>{{ $payment->receipt_number }}</td>
                            <td>{{ $payment->student->full_name }}<br>
                                <small class="text-muted">{{ $payment->student->admission_number }}</small>
                            </td>
                            <td>₦{{ number_format($payment->amount_paid, 2) }} / <br>
                                <small>Balance: ₦{{ number_format($payment->balance, 2) }}</small>
                            </td>
                            <td>{{ ucfirst($payment->payment_method) }}</td>
                            <td>{{ $payment->term }}</td>
                            <td>{{ $payment->academic_year }}</td>
                            <td>{{ $payment->payment_date->format('d M Y') }}</td>
                            <td>
                                @if($payment->payment_status == 'success')
                                    <span class="badge bg-success">Success</span>
                                @elseif($payment->payment_status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-danger">Failed</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('fee-payments.receipt', $payment->id) }}" class="btn btn-sm btn-info" target="_blank">
                                    <i class="fas fa-receipt"></i> Receipt
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $payments->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        const table = $('#historyTable').DataTable({
            pageLength: 10,
            responsive: true,
            order: [[6, 'desc']]
        });
        
        $('#filterStudent').on('change', function() {
            const studentId = $(this).val();
            if (studentId) {
                window.location.href = '{{ route("fee-payments.history", "") }}/' + studentId;
            } else {
                window.location.href = '{{ route("fee-payments.history") }}';
            }
        });
    });
</script>
@endpush
@endsection