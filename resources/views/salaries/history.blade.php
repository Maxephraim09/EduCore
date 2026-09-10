@extends('layouts.app')

@section('title', 'Salary History')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('salaries.index') }}">Salaries</a></li>
    <li class="breadcrumb-item active">History</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-history me-2"></i>Salary Payment History</h5>
            <div class="d-flex gap-2">
                <select id="filterEmployee" class="form-select form-select-sm w-auto">
                    <option value="">All Employees</option>
                    @foreach($employees as $employee)
                        <option value="{{ $employee->id }}" {{ $employeeId == $employee->id ? 'selected' : '' }}>
                            {{ $employee->employee_id }} - {{ $employee->full_name }}
                        </option>
                    @endforeach
                </select>
                <div class="alert alert-info mb-0 py-1">
                    <strong>Total Paid:</strong> ₦{{ number_format($totalPaid, 2) }}
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Receipt No</th>
                            <th>Employee</th>
                            <th>Month/Year</th>
                            <th>Gross Salary</th>
                            <th>Deductions</th>
                            <th>Net Salary</th>
                            <th>Payment Method</th>
                            <th>Payment Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salaries as $salary)
                        <tr>
                            <td>SAL-{{ $salary->id }}</td>
                            <td>
                                <strong>{{ $salary->employee->full_name }}</strong><br>
                                <small>{{ $salary->employee->position }}</small>
                            </td>
                            <td>{{ $salary->month }} {{ $salary->year }}</td>
                            <td>₦{{ number_format($salary->basic_salary + $salary->allowances, 2) }}</td>
                            <td>₦{{ number_format($salary->deductions, 2) }}</td>
                            <td><strong class="text-success">₦{{ number_format($salary->net_salary, 2) }}</strong></td>
                            <td>
                                @if($salary->payment_method == 'bank_transfer')
                                    <i class="fas fa-university"></i> Bank Transfer
                                @elseif($salary->payment_method == 'cash')
                                    <i class="fas fa-money-bill"></i> Cash
                                @else
                                    <i class="fas fa-money-check"></i> Cheque
                                @endif
                            </td>
                            <td>{{ $salary->payment_date->format('d M Y') }}</td>
                            <td>
                                @if($salary->status == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @else
                                    <span class="badge bg-warning">Pending</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('salaries.payslip', $salary->id) }}" class="btn btn-sm btn-secondary" target="_blank">
                                    <i class="fas fa-download"></i> Payslip
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">No salary records found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $salaries->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $('#filterEmployee').on('change', function() {
        const employeeId = $(this).val();
        if (employeeId) {
            window.location.href = '{{ route("salaries.history", "") }}/' + employeeId;
        } else {
            window.location.href = '{{ route("salaries.history") }}';
        }
    });
</script>
@endpush
@endsection