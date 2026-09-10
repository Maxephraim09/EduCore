@extends('layouts.app')

@section('title', 'Salary Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Salaries</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-coins me-2"></i>Salary Management</h5>
            <div>
                <a href="{{ route('salaries.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Process Salary
                </a>
                <a href="{{ route('salaries.history') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-history"></i> History
                </a>
                <a href="{{ route('salaries.reports') }}" class="btn btn-success btn-sm">
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
                <table class="table table-bordered table-hover" id="salaryTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Employee</th>
                            <th>Month</th>
                            <th>Year</th>
                            <th>Basic Salary</th>
                            <th>Allowances</th>
                            <th>Deductions</th>
                            <th>Net Salary</th>
                            <th>Status</th>
                            <th>Payment Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($salaries as $salary)
                        <tr>
                            <td>{{ $salary->id }}</td>
                            <td>
                                {{ $salary->employee->full_name }}<br>
                                <small class="text-muted">{{ $salary->employee->employee_id }}</small>
                            </td>
                            <td>{{ $salary->month }}</td>
                            <td>{{ $salary->year }}</td>
                            <td>₦{{ number_format($salary->basic_salary, 2) }}</td>
                            <td>₦{{ number_format($salary->allowances, 2) }}</td>
                            <td>₦{{ number_format($salary->deductions, 2) }}</td>
                            <td><strong>₦{{ number_format($salary->net_salary, 2) }}</strong></td>
                            <td>
                                @if($salary->status == 'paid')
                                    <span class="badge bg-success">Paid</span>
                                @elseif($salary->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-danger">Cancelled</span>
                                @endif
                            </td>
                            <td>{{ $salary->payment_date->format('d M Y') }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('salaries.show', $salary->id) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('salaries.payslip', $salary->id) }}" class="btn btn-sm btn-secondary" title="Download Payslip">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <form action="{{ route('salaries.destroy', $salary->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $salaries->links() }}
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
        $('#salaryTable').DataTable({
            pageLength: 10,
            responsive: true,
            order: [[0, 'desc']]
        });
    });
</script>
@endpush
@endsection