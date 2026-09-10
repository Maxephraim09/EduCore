@extends('layouts.app')

@section('title', 'Profit & Loss Statement')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('reports.index') }}">Reports</a></li>
    <li class="breadcrumb-item active">Profit & Loss</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Filter Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Report</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('reports.profit-loss') }}" class="row">
                <div class="col-md-4">
                    <label>Year</label>
                    <select name="year" class="form-control">
                        @for($i = date('Y') - 2; $i <= date('Y'); $i++)
                            <option value="{{ $i }}" {{ $year == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Month (Optional)</label>
                    <select name="month" class="form-control">
                        <option value="">Full Year</option>
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $month == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 1)) }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Generate Statement</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="mb-0">Total Income</h6>
                    <h3 class="mt-2 mb-0">₦{{ number_format($totalIncome, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h6 class="mb-0">Total Expenses</h6>
                    <h3 class="mt-2 mb-0">₦{{ number_format($totalExpense, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card {{ $netProfit >= 0 ? 'bg-primary' : 'bg-warning' }} text-white">
                <div class="card-body">
                    <h6 class="mb-0">Net Profit / (Loss)</h6>
                    <h3 class="mt-2 mb-0">₦{{ number_format($netProfit, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="mb-0">Profit Margin</h6>
                    <h3 class="mt-2 mb-0">{{ number_format($profitMargin, 1) }}%</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Income Statement -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-arrow-up me-2"></i>Income Statement</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Source</th>
                                    <th class="text-end">Amount (₦)</th>
                                    <th class="text-end">% of Income</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($incomeBreakdown as $income)
                                <tr>
                                    <td>{{ $income->source }}</td>
                                    <td class="text-end text-success">₦{{ number_format($income->total, 2) }}</td>
                                    <td class="text-end">{{ $totalIncome > 0 ? number_format(($income->total / $totalIncome) * 100, 1) : 0 }}%</td>
                                </tr>
                                @endforeach
                                <tr class="table-success">
                                    <td><strong>TOTAL INCOME</strong></td>
                                    <td class="text-end"><strong>₦{{ number_format($totalIncome, 2) }}</strong></td>
                                    <td class="text-end"><strong>100%</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Expense Statement -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-arrow-down me-2"></i>Expense Statement</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th class="text-end">Amount (₦)</th>
                                    <th class="text-end">% of Expense</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expenseBreakdown as $expense)
                                <tr>
                                    <td>{{ $expense->category }}</td>
                                    <td class="text-end text-danger">₦{{ number_format($expense->total, 2) }}</td>
                                    <td class="text-end">{{ $totalExpense > 0 ? number_format(($expense->total / $totalExpense) * 100, 1) : 0 }}%</td>
                                </tr>
                                @endforeach
                                <tr class="table-danger">
                                    <td><strong>TOTAL EXPENSES</strong></td>
                                    <td class="text-end"><strong>₦{{ number_format($totalExpense, 2) }}</strong></td>
                                    <td class="text-end"><strong>100%</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly P&L Chart -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Monthly Profit & Loss ({{ $year }})</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyPLChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Breakdown Table -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-table me-2"></i>Monthly Breakdown</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th class="text-end">Income (₦)</th>
                                    <th class="text-end">Expense (₦)</th>
                                    <th class="text-end">Profit/Loss (₦)</th>
                                    <th class="text-end">Margin</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyPL as $month)
                                <tr>
                                    <td>{{ $month['month'] }}</td>
                                    <td class="text-end text-success">₦{{ number_format($month['income'], 2) }}</td>
                                    <td class="text-end text-danger">₦{{ number_format($month['expense'], 2) }}</td>
                                    <td class="text-end {{ $month['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        ₦{{ number_format($month['profit'], 2) }}
                                    </td>
                                    <td class="text-end">
                                        @php
                                            $margin = $month['income'] > 0 ? ($month['profit'] / $month['income']) * 100 : 0;
                                        @endphp
                                        {{ number_format($margin, 1) }}%
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="table-dark">
                                    <th>TOTAL</th>
                                    <th class="text-end">₦{{ number_format(collect($monthlyPL)->sum('income'), 2) }}</th>
                                    <th class="text-end">₦{{ number_format(collect($monthlyPL)->sum('expense'), 2) }}</th>
                                    <th class="text-end">₦{{ number_format(collect($monthlyPL)->sum('profit'), 2) }}</th>
                                    <th class="text-end"></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Yearly Comparison -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Yearly Comparison</h5>
                </div>
                <div class="card-body">
                    <canvas id="yearlyPLChart" height="300"></canvas>
                    <div class="table-responsive mt-3">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Year</th>
                                    <th class="text-end">Income (₦)</th>
                                    <th class="text-end">Expense (₦)</th>
                                    <th class="text-end">Profit/Loss (₦)</th>
                                    <th class="text-end">Growth</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($yearlyComparison as $index => $yearData)
                                <tr>
                                    <td>{{ $yearData['year'] }}</td>
                                    <td class="text-end text-success">₦{{ number_format($yearData['income'], 2) }}</td>
                                    <td class="text-end text-danger">₦{{ number_format($yearData['expense'], 2) }}</td>
                                    <td class="text-end {{ $yearData['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                        ₦{{ number_format($yearData['profit'], 2) }}
                                    </td>
                                    <td class="text-end">
                                        @if($index > 0)
                                            @php
                                                $prevProfit = $yearlyComparison[$index - 1]['profit'];
                                                $growth = $prevProfit != 0 ? (($yearData['profit'] - $prevProfit) / abs($prevProfit)) * 100 : 0;
                                            @endphp
                                            <span class="{{ $growth >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ $growth >= 0 ? '+' : '' }}{{ number_format($growth, 1) }}%
                                            </span>
                                        @else
                                            --
                                        @endif
                                    </td>
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
                    <a href="{{ route('reports.export-pl', ['year' => $year, 'month' => $month]) }}" 
                       class="btn btn-success">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </a>
                    <a href="{{ route('reports.export-pl-pdf', ['year' => $year, 'month' => $month]) }}" 
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
    // Monthly P&L Chart
    const monthlyCtx = document.getElementById('monthlyPLChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($monthlyPL, 'month')) !!},
            datasets: [{
                label: 'Income',
                data: {!! json_encode(array_column($monthlyPL, 'income')) !!},
                backgroundColor: 'rgba(40, 167, 69, 0.5)',
                borderColor: 'rgba(40, 167, 69, 1)',
                borderWidth: 1
            }, {
                label: 'Expense',
                data: {!! json_encode(array_column($monthlyPL, 'expense')) !!},
                backgroundColor: 'rgba(220, 53, 69, 0.5)',
                borderColor: 'rgba(220, 53, 69, 1)',
                borderWidth: 1
            }, {
                label: 'Profit/Loss',
                data: {!! json_encode(array_column($monthlyPL, 'profit')) !!},
                type: 'line',
                borderColor: '#ffc107',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
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
                    title: { display: true, text: 'Profit/Loss (₦)' },
                    grid: { drawOnChartArea: false },
                    ticks: { callback: function(value) { return '₦' + value.toLocaleString(); } }
                }
            }
        }
    });
    
    // Yearly P&L Chart
    const yearlyCtx = document.getElementById('yearlyPLChart').getContext('2d');
    new Chart(yearlyCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($yearlyComparison, 'year')) !!},
            datasets: [{
                label: 'Income',
                data: {!! json_encode(array_column($yearlyComparison, 'income')) !!},
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Expense',
                data: {!! json_encode(array_column($yearlyComparison, 'expense')) !!},
                borderColor: '#dc3545',
                backgroundColor: 'rgba(220, 53, 69, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Profit/Loss',
                data: {!! json_encode(array_column($yearlyComparison, 'profit')) !!},
                borderColor: '#ffc107',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                tension: 0.4,
                fill: true,
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: function(value) { return '₦' + value.toLocaleString(); } }
                }
            }
        }
    });
</script>
@endpush
@endsection