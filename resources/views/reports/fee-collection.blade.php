@extends('layouts.app')

@section('title', 'Fee Collection Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">Fee Collection</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Report</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.fee-collection') }}" class="row">
                <div class="col-md-3">
                    <label>Year</label>
                    <select name="year" class="form-control">
                        @for($i = date('Y') - 2; $i <= date('Y'); $i++)
                            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Month (Optional)</label>
                    <select name="month" class="form-control">
                        <option value="">All Months</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Class (Optional)</label>
                    <select name="class" class="form-control">
                        <option value="">All Classes</option>
                        @foreach($classes as $c)
                            <option value="{{ $c }}" {{ $class == $c ? 'selected' : '' }}>{{ $c }}</option>
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
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="mb-0">Total Collected</h6>
                    <h3 class="mt-2 mb-0">₦{{ number_format($feeSummary['total_collected'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="mb-0">Total Transactions</h6>
                    <h3 class="mt-2 mb-0">{{ number_format($feeSummary['total_transactions']) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="mb-0">Average Payment</h6>
                    <h3 class="mt-2 mb-0">₦{{ number_format($feeSummary['average_amount'], 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="mb-0">Students Paid</h6>
                    <h3 class="mt-2 mb-0">{{ number_format($feeSummary['students_paid']) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Monthly Trend Chart -->
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Monthly Collection Trend ({{ $year }})</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyTrendChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Payment Methods</h5>
                </div>
                <div class="card-body">
                    <canvas id="paymentChart" height="250"></canvas>
                    <div class="table-responsive mt-3">
                        <table class="table table-sm">
                            @foreach($paymentMethods as $method)
                            <tr>
                                <td>{{ ucfirst($method->payment_method) }}
                                                                    <td>₦{{ number_format($method->total, 2) }} ({{
                                    $feeSummary['total_collected'] > 0 ? 
                                    number_format(($method->total / $feeSummary['total_collected']) * 100, 1) : 0
                                }}%)</td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Class-wise Collection -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Class-wise Collection</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Class</th>
                                    <th>Students Paid</th>
                                    <th>Transactions</th>
                                    <th>Total Amount</th>
                                    <th>% of Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($classWiseCollection as $class)
                                <tr>
                                    <td>{{ $class->class }}</td>
                                    <td>{{ $class->students_paid }}</td>
                                    <td>{{ $class->transactions }}</td>
                                    <td>₦{{ number_format($class->total, 2) }}</td>
                                    <td>
                                        {{ $feeSummary['total_collected'] > 0 ? 
                                            number_format(($class->total / $feeSummary['total_collected']) * 100, 1) : 0 }}%
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Transactions</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Receipt No</th>
                                    <th>Student</th>
                                    <th>Amount</th>
                                    <th>Method</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->payment_date->format('d M Y') }}</td>
                                    <td>{{ $transaction->receipt_number }}</td>
                                    <td>{{ $transaction->student->full_name }}</td>
                                    <td>₦{{ number_format($transaction->amount, 2) }}</td>
                                    <td>{{ ucfirst($transaction->payment_method) }}
                                                                            <td>₦{{ number_format($transaction->amount, 2) }}</td>
                                    <td>{{ ucfirst($transaction->payment_method) }}</td>
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
                    <a href="{{ route('reports.export-fee', ['year' => $year, 'month' => $month, 'class' => $class]) }}" 
                       class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </a>
                    <a href="{{ route('reports.export-fee-pdf', ['year' => $year, 'month' => $month, 'class' => $class]) }}" 
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
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($monthlyTrend, 'month')) !!},
            datasets: [{
                label: 'Amount Collected (₦)',
                data: {!! json_encode(array_column($monthlyTrend, 'amount')) !!},
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Number of Transactions',
                data: {!! json_encode(array_column($monthlyTrend, 'count')) !!},
                borderColor: '#17a2b8',
                backgroundColor: 'rgba(23, 162, 184, 0.1)',
                tension: 0.4,
                fill: true,
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
                    title: { display: true, text: 'Number of Transactions' },
                    grid: { drawOnChartArea: false }
                }
            }
        }
    });
    
    // Payment Methods Chart
    const paymentCtx = document.getElementById('paymentChart').getContext('2d');
    new Chart(paymentCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($paymentMethods->pluck('payment_method')->map(function($m) { return ucfirst($m); })) !!},
            datasets: [{
                data: {!! json_encode($paymentMethods->pluck('total')) !!},
                backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e', '#e74a3b']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.raw || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ₦${value.toLocaleString()} (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection