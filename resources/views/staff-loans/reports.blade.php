@extends('layouts.app')

@section('title', 'Loan Reports')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('staff-loans.index') }}">Staff Loans</a></li>
    <li class="breadcrumb-item active">Reports</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="mb-0">Total Loans Disbursed</h6>
                    <h3 class="mt-2 mb-0">₦{{ number_format($totalLoans, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="mb-0">Total Interest Earned</h6>
                    <h3 class="mt-2 mb-0">₦{{ number_format($totalInterest, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="mb-0">Active Loan Balance</h6>
                    <h3 class="mt-2 mb-0">₦{{ number_format($activeLoanAmount, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="mb-0">Loan Statistics</h6>
                    <h3 class="mt-2 mb-0">
                        {{ $activeLoans }} / {{ $completedLoans }} / {{ $defaultedLoans }}
                    </h3>
                    <small>Active / Completed / Defaulted</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Monthly Loan Disbursements</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>Loans by Department</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Department</th>
                                    <th>Number of Loans</th>
                                    <th>Total Amount</th>
                                    <th>Remaining Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($loansByDepartment as $dept)
                                <tr>
                                    <td>{{ $dept->department }}</td>
                                    <td>{{ $dept->count }}</td>
                                    <td>₦{{ number_format($dept->total_amount, 2) }}</td>
                                    <td>₦{{ number_format($dept->remaining_amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Top 10 Borrowers</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Employee</th>
                                    <th>Employee ID</th>
                                    <th>Total Borrowed</th>
                                    <th>Remaining</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topBorrowers as $borrower)
                                <tr>
                                    <td>{{ $borrower->first_name }} {{ $borrower->last_name }}</td>
                                    <td>{{ $borrower->employee_id }}</td>
                                    <td>₦{{ number_format($borrower->total_borrowed, 2) }}</td>
                                    <td>₦{{ number_format($borrower->total_remaining, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($monthlyDisbursements->pluck('month')) !!},
            datasets: [{
                label: 'Total Amount (₦)',
                data: {!! json_encode($monthlyDisbursements->pluck('total_amount')) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }, {
                label: 'Number of Loans',
                data: {!! json_encode($monthlyDisbursements->pluck('count')) !!},
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
                type: 'line'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            if (this.chart?.data?.datasets[0]?.label === 'Total Amount (₦)') {
                                return '₦' + value.toLocaleString();
                            }
                            return value;
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection