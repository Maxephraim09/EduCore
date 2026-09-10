@extends('layouts.app')

@section('title', 'Expense Reports')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Expenses</a></li>
    <li class="breadcrumb-item active">Reports</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Expenses by Category ({{ date('Y') }})</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="300"></canvas>
                    <div class="table-responsive mt-3">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Number of Transactions</th>
                                    <th>Total Amount</th>
                                    <th>Percentage</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $totalAll = $expensesByCategory->sum('total'); @endphp
                                @foreach($expensesByCategory as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->count }}</td>
                                    <td>₦{{ number_format($category->total, 2) }}</td>
                                    <td>
                                        {{ number_format($totalAll > 0 ? ($category->total / $totalAll) * 100 : 0, 1) }}%
                                    </td>
                                </tr>
                                @endforeach
                                <tr class="table-active">
                                    <td><strong>Total</strong></td>
                                    <td><strong>{{ $expensesByCategory->sum('count') }}</strong></td>
                                    <td><strong>₦{{ number_format($totalAll, 2) }}</strong></td>
                                    <td><strong>100%</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-line me-2"></i>Monthly Expenses ({{ date('Y') }})</h5>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="300"></canvas>
                    <div class="table-responsive mt-3">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Month</th>
                                    <th>Number of Transactions</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($monthlyExpenses as $expense)
                                <tr>
                                    <td>{{ date('F', mktime(0, 0, 0, $expense->month, 1)) }}</td>
                                    <td>{{ $expense->count }}</td>
                                    <td>₦{{ number_format($expense->total, 2) }}</td>
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
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-building me-2"></i>Top 10 Vendors</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Vendor Name</th>
                                    <th>Number of Transactions</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topVendors as $index => $vendor)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $vendor->vendor_name }}</td>
                                    <td>{{ $vendor->count }}</td>
                                    <td>₦{{ number_format($vendor->total, 2) }}</td>
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
                                    <th>Number of Transactions</th>
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
                                        @else
                                            <i class="fas fa-money-check"></i> Cheque
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
                    <h5 class="mb-0"><i class="fas fa-download me-2"></i>Export Reports</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <button class="btn btn-success w-100" onclick="exportReport('excel')">
                                <i class="fas fa-file-excel"></i> Export to Excel
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-danger w-100" onclick="exportReport('pdf')">
                                <i class="fas fa-file-pdf"></i> Export to PDF
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary w-100" onclick="window.print()">
                                <i class="fas fa-print"></i> Print Report
                            </button>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-info w-100" onclick="sendReport()">
                                <i class="fas fa-envelope"></i> Email Report
                            </button>
                        </div>
                    </div>
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
            labels: {!! json_encode($expensesByCategory->pluck('name')) !!},
            datasets: [{
                data: {!! json_encode($expensesByCategory->pluck('total')) !!},
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e',
                    '#e74a3b', '#858796', '#5a5c69', '#f8f9fc',
                    '#2e59d9', '#17a673', '#2c9faf', '#f4b619'
                ],
                hoverBackgroundColor: [
                    '#2e59d9', '#17a673', '#2c9faf', '#f4b619',
                    '#c0392b', '#6c757d', '#4a4b5a', '#e2e6ea'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        font: { size: 11 }
                    }
                },
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
            labels: {!! json_encode($monthlyExpenses->map(function($item) {
                return date('F', mktime(0, 0, 0, $item->month, 1));
            })) !!},
            datasets: [{
                label: 'Total Expenses (₦)',
                data: {!! json_encode($monthlyExpenses->pluck('total')) !!},
                backgroundColor: 'rgba(231, 74, 59, 0.5)',
                borderColor: 'rgba(231, 74, 59, 1)',
                borderWidth: 2
            }, {
                label: 'Number of Transactions',
                data: {!! json_encode($monthlyExpenses->pluck('count')) !!},
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
                    title: {
                        display: true,
                        text: 'Amount (₦)'
                    },
                    ticks: {
                        callback: function(value) {
                            return '₦' + value.toLocaleString();
                        }
                    }
                },
                y1: {
                    position: 'right',
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Transactions'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
    
    // Payment Method Chart
    const paymentCtx = document.getElementById('paymentChart').getContext('2d');
    new Chart(paymentCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($paymentMethods->map(function($item) {
                return ucfirst(str_replace('_', ' ', $item->payment_method));
            })) !!},
            datasets: [{
                data: {!! json_encode($paymentMethods->pluck('total')) !!},
                backgroundColor: ['#4e73df', '#1cc88a', '#f6c23e'],
                hoverBackgroundColor: ['#2e59d9', '#17a673', '#f4b619']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
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
    
    // Export functions
    function exportReport(type) {
        if (type === 'excel') {
            window.location.href = '{{ route("expenses.export.excel") }}';
        } else if (type === 'pdf') {
            window.location.href = '{{ route("expenses.export.pdf") }}';
        }
    }
    
    function sendReport() {
        const email = prompt('Enter email address to send report:', 'admin@example.com');
        if (email) {
            window.location.href = '{{ route("expenses.email.report") }}?email=' + email;
        }
    }
</script>
@endpush
@endsection