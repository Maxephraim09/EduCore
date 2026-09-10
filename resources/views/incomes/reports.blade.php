@extends('layouts.app')

@section('title', 'Income Reports')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('other-incomes.index') }}">Other Incomes</a></li>
    <li class="breadcrumb-item active">Reports</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="mb-0">Total Income ({{ date('Y') }})</h6>
                    <h3 class="mt-2 mb-0">₦{{ number_format($incomeByCategory->sum('total'), 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="mb-0">Average Monthly</h6>
                    <h3 class="mt-2 mb-0">₦{{ number_format($incomeByCategory->sum('total') / 12, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="mb-0">Total Transactions</h6>
                    <h3 class="mt-2 mb-0">{{ $incomeByCategory->sum('count') }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="mb-0">Categories</h6>
                    <h3 class="mt-2 mb-0">{{ $incomeByCategory->count() }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Income by Category ({{ date('Y') }})</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="300"></canvas>
                    <div class="table-responsive mt-3">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Transactions</th>
                                    <th>Total Amount</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalAll = $incomeByCategory->sum('total'); @endphp
                                @foreach($incomeByCategory as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->count }}</td>
                                    <td>₦{{ number_format($category->total, 2) }}</td>
                                    <td>{{ number_format($totalAll > 0 ? ($category->total / $totalAll) * 100 : 0, 1) }}%</td>
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
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Monthly Income ({{ date('Y') }})</h5>
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
                    <h5 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Top Income Sources</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Source</th>
                                    <th>Transactions</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($incomeSources as $index => $source)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $source->source }}</td>
                                    <td>{{ $source->count }}</td>
                                    <td>₦{{ number_format($source->total, 2) }}</td>
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
                    <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Payment Method Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="paymentChart" height="250"></canvas>
                    <div class="table-responsive mt-3">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Payment Method</th>
                                    <th>Transactions</th>
                                    <th>Total Amount</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalPayments = $paymentMethods->sum('total'); @endphp
                                @foreach($paymentMethods as $method)
                                <tr>
                                    <td>
                                        @if($method->payment_method == 'cash')
                                            <i class="fas fa-money-bill"></i> Cash
                                        @elseif($method->payment_method == 'bank_transfer')
                                            <i class="fas fa-university"></i> Bank Transfer
                                        @elseif($method->payment_method == 'cheque')
                                            <i class="fas fa-money-check"></i> Cheque
                                        @else
                                            <i class="fab fa-paypal"></i> Online
                                        @endif
                                    </td>
                                    <td>{{ $method->count }}</td>
                                    <td>₦{{ number_format($method->total, 2) }}</td>
                                    <td>{{ number_format($totalPayments > 0 ? ($method->total / $totalPayments) * 100 : 0, 1) }}%</td>
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
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Yearly Comparison</h5>
                </div>
                <div class="card-body">
                    <canvas id="yearlyChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Category Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($incomeByCategory->pluck('name')) !!},
            datasets: [{
                data: {!! json_encode($incomeByCategory->pluck('total')) !!},
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#f4b619', '#c0392b', '#6c757d', '#4a4b5a']
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
    
    // Monthly Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(monthlyCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($monthlyIncome->map(function($item) {
                return date('F', mktime(0, 0, 0, $item->month, 1));
            })) !!},
            datasets: [{
                label: 'Total Income (₦)',
                data: {!! json_encode($monthlyIncome->pluck('total')) !!},
                backgroundColor: 'rgba(28, 200, 138, 0.5)',
                borderColor: 'rgba(28, 200, 138, 1)',
                borderWidth: 2
            }, {
                label: 'Number of Transactions',
                data: {!! json_encode($monthlyIncome->pluck('count')) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 2,
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
                    title: { display: true, text: 'Number of Transactions' },
                    grid: { drawOnChartArea: false }
                }
            }
        }
    });
    
    // Payment Chart
    const paymentCtx = document.getElementById('paymentChart').getContext('2d');
    new Chart(paymentCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($paymentMethods->map(function($item) {
                return ucfirst(str_replace('_', ' ', $item->payment_method));
            })) !!},
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
    
    // Yearly Chart
    const yearlyCtx = document.getElementById('yearlyChart').getContext('2d');
    new Chart(yearlyCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($yearlyIncome->pluck('year')) !!},
            datasets: [{
                label: 'Total Income (₦)',
                data: {!! json_encode($yearlyIncome->pluck('total')) !!},
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Number of Transactions',
                data: {!! json_encode($yearlyIncome->pluck('count')) !!},
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
</script>
@endpush
@endsection