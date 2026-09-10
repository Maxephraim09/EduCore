@extends('layouts.app')

@section('title', 'Financial Reports')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Reports</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Key Metrics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Fees Collected</h6>
                            <h3 class="mt-2 mb-0">₦{{ number_format($metrics['total_fees_collected'], 2) }}</h3>
                            <small>{{ date('Y') }} Year to Date</small>
                        </div>
                        <i class="fas fa-money-bill-wave fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Expenses</h6>
                            <h3 class="mt-2 mb-0">₦{{ number_format($metrics['total_expenses'] + $metrics['total_salaries'], 2) }}</h3>
                            <small>{{ date('Y') }} Year to Date</small>
                        </div>
                        <i class="fas fa-receipt fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Net Profit</h6>
                            <h3 class="mt-2 mb-0">₦{{ number_format($metrics['total_fees_collected'] + $metrics['total_other_income'] - ($metrics['total_expenses'] + $metrics['total_salaries']), 2) }}</h3>
                            <small>{{ date('Y') }} Year to Date</small>
                        </div>
                        <i class="fas fa-chart-line fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Outstanding Fees</h6>
                            <h3 class="mt-2 mb-0">₦{{ number_format($metrics['outstanding_fees'], 2) }}</h3>
                            <small>Due from Students</small>
                        </div>
                        <i class="fas fa-exclamation-triangle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Chart -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Monthly Income vs Expense ({{ $currentYear }})</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Yearly Comparison -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Yearly Comparison</h5>
                </div>
                <div class="card-body">
                    <canvas id="yearlyChart" height="250"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Key Metrics</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th>Total Students</th>
                                    <td class="text-end">{{ number_format($metrics['total_students']) }}</td>
                                    <th>Total Employees</th>
                                    <td class="text-end">{{ number_format($metrics['total_employees']) }}</td>
                                </tr>
                                <tr>
                                    <th>Monthly Fees</th>
                                    <td class="text-end text-success">₦{{ number_format($metrics['monthly_fees'], 2) }}</td>
                                    <th>Other Income (YTD)</th>
                                    <td class="text-end text-success">₦{{ number_format($metrics['total_other_income'], 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Active Loans</th>
                                    <td class="text-end text-warning">₦{{ number_format($metrics['active_loans'], 2) }}</td>
                                    <th>Net Profit Margin</th>
                                    <td class="text-end">
                                        @php
                                            $totalIncome = $metrics['total_fees_collected'] + $metrics['total_other_income'];
                                            $totalExpense = $metrics['total_expenses'] + $metrics['total_salaries'];
                                            $margin = $totalIncome > 0 ? (($totalIncome - $totalExpense) / $totalIncome) * 100 : 0;
                                        @endphp
                                        {{ number_format($margin, 1) }}%
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-file-invoice fa-3x text-primary"></i>
                    <h6 class="mt-2">Fee Collection Report</h6>
                    <a href="{{ route('reports.fee-collection') }}" class="btn btn-sm btn-primary">View Report</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-file-invoice-dollar fa-3x text-success"></i>
                    <h6 class="mt-2">Salary Report</h6>
                    <a href="{{ route('reports.salary') }}" class="btn btn-sm btn-success">View Report</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-chart-bar fa-3x text-info"></i>
                    <h6 class="mt-2">Profit & Loss</h6>
                    <a href="{{ route('reports.profit-loss') }}" class="btn btn-sm btn-info">View Report</a>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-download fa-3x text-secondary"></i>
                    <h6 class="mt-2">Export Reports</h6>
                    <button class="btn btn-sm btn-secondary" onclick="alert('Export functionality available in individual report pages')">Export</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Monthly Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyData['months']) !!},
            datasets: [{
                label: 'Income (₦)',
                data: {!! json_encode($monthlyData['income']) !!},
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Expense (₦)',
                data: {!! json_encode($monthlyData['expense']) !!},
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₦' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
    
    // Yearly Chart
    const yearlyCtx = document.getElementById('yearlyChart').getContext('2d');
    new Chart(yearlyCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($yearlyData['years']) !!},
            datasets: [{
                label: 'Income (₦)',
                data: {!! json_encode($yearlyData['income']) !!},
                backgroundColor: 'rgba(40, 167, 69, 0.5)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 1
            }, {
                label: 'Expense (₦)',
                data: {!! json_encode($yearlyData['expense']) !!},
                backgroundColor: 'rgba(220, 53, 69, 0.5)',
                borderColor: 'rgba(220, 53, 69, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₦' + value.toLocaleString();
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection