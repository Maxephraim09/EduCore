@extends('layouts.app')

@section('title', 'Salary Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">Salary Report</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Report</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.salary') }}" class="row">
                <div class="col-md-3">
                    <label>Year</label>
                    <select name="year" class="form-control">
                        @for($i = date('Y') - 2; $i <= date('Y'); $i++)
                            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Month</label>
                    <select name="month" class="form-control">
                        <option value="">All Months</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Department</label>
                    <select name="department" class="form-control">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ $department == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Generate Report</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="mb-0">Total Salaries Paid</h6>
                    <h3 class="mt-2 mb-0">₦{{ number_format($salarySummary['total_salaries'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="mb-0">Employees Paid</h6>
                    <h3 class="mt-2 mb-0">{{ number_format($salarySummary['total_employees']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="mb-0">Average Salary</h6>
                    <h3 class="mt-2 mb-0">₦{{ number_format($salarySummary['average_salary'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="mb-0">Total Deductions</h6>
                    <h3 class="mt-2 mb-0">₦{{ number_format($salarySummary['total_deductions'], 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Monthly Trend Chart -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Monthly Salary Trend ({{ $year }})</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyTrendChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Department-wise Summary -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>Department-wise</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Department</th>
                                    <th>Employees</th>
                                    <th>Total Salary</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($departmentWise as $dept)
                                <tr>
                                    <td>{{ $dept->department }}</td>
                                    <td>{{ $dept->employees }}</td>
                                    <td>₦{{ number_format($dept->total, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Top Earners -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-trophy me-2"></i>Top 10 Highest Earners ({{ $year }})</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Employee</th>
                                    <th>Position</th>
                                    <th>Department</th>
                                    <th>Total Earned</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topEarners as $index => $earner)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $earner->first_name }} {{ $earner->last_name }}<br>
                                        <small class="text-muted">{{ $earner->employee_id }}</small>
                                    </td>
                                    <td>{{ $earner->position }}</td>
                                    <td>{{ $earner->department }}</td>
                                    <td>₦{{ number_format($earner->total_earned, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Salary Payments</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Employee</th>
                                    <th>Month/Year</th>
                                    <th>Net Salary</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentPayments as $payment)
                                <tr>
                                    <td>{{ $payment->payment_date->format('d M Y') }}</td>
                                    <td>{{ $payment->employee->full_name }}</td>
                                    <td>{{ $payment->month }} {{ $payment->year }}</td>
                                    <td>₦{{ number_format($payment->net_salary, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Export Buttons -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body text-center">
                    <a href="{{ route('reports.export-salary', ['year' => $year, 'month' => $month, 'department' => $department]) }}" 
                       class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </a>
                    <a href="{{ route('reports.export-salary-pdf', ['year' => $year, 'month' => $month, 'department' => $department]) }}" 
                       class="btn btn-danger">
                        <i class="fas fa-file-pdf"></i> Export to PDF
                    </a>
                    <button onclick="window.print()" class="btn btn-info">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Monthly Trend Chart
    const monthlyCtx = document.getElementById('monthlyTrendChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($monthlyTrend, 'month')) !!},
            datasets: [{
                label: 'Salary Amount (₦)',
                data: {!! json_encode(array_column($monthlyTrend, 'amount')) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }, {
                label: 'Number of Employees',
                data: {!! json_encode(array_column($monthlyTrend, 'count')) !!},
                backgroundColor: 'rgba(255, 99, 132, 0.5)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1,
                type: 'line',
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: { display: true, text: 'Amount (₦)' },
                    ticks: { callback: function(value) { return '₦' + value.toLocaleString(); } }
                },
                y1: {
                    position: 'right',
                    beginAtZero: true,
                    title: { display: true, text: 'Number of Employees' },
                    grid: { drawOnChartArea: false }
                }
            }
        }
    });
</script>
@endpush
@endsection