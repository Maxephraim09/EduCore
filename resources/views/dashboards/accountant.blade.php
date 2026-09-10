@extends('layouts.app')

@section('title', 'Accountant Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Accountant Dashboard</li>
@endsection

@section('styles')
<style>
    .stat-card {
        border: none;
        border-radius: 15px;
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    .stat-card .stat-icon {
        position: absolute;
        right: 15px;
        top: 15px;
        font-size: 3rem;
        opacity: 0.2;
    }
    .stat-card .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 5px;
    }
    .stat-card .stat-label {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        opacity: 0.8;
    }
    .stat-card .stat-change {
        font-size: 0.8rem;
        margin-top: 5px;
    }
    .stat-card .stat-change.positive {
        color: #28a745;
    }
    .stat-card .stat-change.negative {
        color: #dc3545;
    }
    .stat-card.bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .stat-card.bg-gradient-success {
        background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
        color: #1a3a2a;
    }
    .stat-card.bg-gradient-danger {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }
    .stat-card.bg-gradient-warning {
        background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
        color: #4a3520;
    }
    .stat-card.bg-gradient-info {
        background: linear-gradient(135deg, #a1c4fd 0%, #c2e9fb 100%);
        color: #1a3a4a;
    }
    .stat-card.bg-gradient-dark {
        background: linear-gradient(135deg, #2c3e50 0%, #2c3e50 100%);
        color: white;
    }
    .stat-card.bg-gradient-teal {
        background: linear-gradient(135deg, #20c997 0%, #20c997 100%);
        color: white;
    }
    .stat-card.bg-gradient-purple {
        background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%);
        color: #2d1b3d;
    }
    .stat-card.bg-gradient-orange {
        background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
        color: #4a3520;
    }
    
    .quick-action-btn {
        border-radius: 10px;
        padding: 15px 20px;
        transition: all 0.3s ease;
        text-align: center;
        border: 2px solid #e9ecef;
        background: white;
        color: #333;
        text-decoration: none;
        display: block;
        height: 100%;
    }
    .quick-action-btn:hover {
        transform: translateY(-3px);
        border-color: #667eea;
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.15);
        color: #667eea;
    }
    .quick-action-btn i {
        font-size: 2rem;
        margin-bottom: 10px;
        display: block;
        color: #667eea;
    }
    .quick-action-btn:hover i {
        color: #667eea;
    }
    .quick-action-btn .action-title {
        font-weight: 600;
        font-size: 0.9rem;
    }
    .quick-action-btn .action-desc {
        font-size: 0.75rem;
        color: #6c757d;
    }
    
    .activity-item {
        border-left: 3px solid #667eea;
        padding-left: 15px;
        margin-bottom: 15px;
    }
    .activity-item .activity-time {
        font-size: 0.75rem;
        color: #6c757d;
    }
    .activity-item .activity-text {
        font-size: 0.9rem;
    }
    .activity-item .activity-icon {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        background: #f8f9fa;
    }
    
    .chart-container {
        position: relative;
        height: 300px;
    }
    
    .table-transactions td {
        vertical-align: middle;
    }
    
    .badge-status {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    .badge-status.paid { background: #d4edda; color: #155724; }
    .badge-status.pending { background: #fff3cd; color: #856404; }
    .badge-status.failed { background: #f8d7da; color: #721c24; }
    .badge-status.partial { background: #d1ecf1; color: #0c5460; }
    
    .financial-summary {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 15px;
    }
    .financial-summary .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 5px 0;
        border-bottom: 1px solid #e9ecef;
    }
    .financial-summary .summary-item:last-child {
        border-bottom: none;
    }
    .financial-summary .summary-item .label {
        color: #6c757d;
    }
    .financial-summary .summary-item .value {
        font-weight: 600;
    }
    .financial-summary .summary-item .value.positive { color: #28a745; }
    .financial-summary .summary-item .value.negative { color: #dc3545; }
</style>
@endsection

@section('content')
<div class="fade-in">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card bg-gradient-primary text-white shadow-sm border-0">
                <div class="card-body py-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-user-circle me-2"></i>Welcome back, {{ Auth::user()->name }}!</h4>
                            <p class="mb-0 opacity-75">
                                <i class="fas fa-calendar-alt me-1"></i> {{ now()->format('l, F j, Y') }} | 
                                <i class="fas fa-clock ms-2 me-1"></i> {{ now()->format('h:i A') }}
                            </p>
                        </div>
                        <div>
                            <span class="badge bg-white text-primary p-2">
                                <i class="fas fa-building me-1"></i> {{ getSchoolName() }}
                            </span>
                            <span class="badge bg-white text-primary p-2 ms-2">
                                <i class="fas fa-calendar-alt me-1"></i> {{ now()->format('M Y') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-gradient-success">
                <div class="card-body">
                    <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="stat-value">₦{{ number_format($totalRevenue ?? 0, 2) }}</div>
                    <div class="stat-label">Total Revenue</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up me-1"></i> {{ $revenueGrowth ?? 0 }}% from last month
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-gradient-danger">
                <div class="card-body">
                    <div class="stat-icon"><i class="fas fa-receipt"></i></div>
                    <div class="stat-value">₦{{ number_format($totalExpenses ?? 0, 2) }}</div>
                    <div class="stat-label">Total Expenses</div>
                    <div class="stat-change negative">
                        <i class="fas fa-arrow-up me-1"></i> {{ $expenseGrowth ?? 0 }}% from last month
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-gradient-warning">
                <div class="card-body">
                    <div class="stat-icon"><i class="fas fa-clock"></i></div>
                    <div class="stat-value">{{ $pendingInvoices ?? 0 }}</div>
                    <div class="stat-label">Pending Invoices</div>
                    <div class="stat-change">
                        <i class="fas fa-info-circle me-1"></i> Awaiting payment
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stat-card bg-gradient-info">
                <div class="card-body">
                    <div class="stat-icon"><i class="fas fa-exchange-alt"></i></div>
                    <div class="stat-value">{{ $totalTransactions ?? 0 }}</div>
                    <div class="stat-label">Total Transactions</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up me-1"></i> {{ $transactionGrowth ?? 0 }}% from last month
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row Stats -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="stat-card bg-gradient-dark">
                <div class="card-body">
                    <div class="stat-icon"><i class="fas fa-users"></i></div>
                    <div class="stat-value">{{ $totalStudents ?? 0 }}</div>
                    <div class="stat-label">Total Students</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="stat-card bg-gradient-teal">
                <div class="card-body">
                    <div class="stat-icon"><i class="fas fa-user-check"></i></div>
                    <div class="stat-value">{{ $activeStudents ?? 0 }}</div>
                    <div class="stat-label">Active Students</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="stat-card bg-gradient-purple">
                <div class="card-body">
                    <div class="stat-icon"><i class="fas fa-money-bill"></i></div>
                    <div class="stat-value">₦{{ number_format($totalFeesCollected ?? 0, 2) }}</div>
                    <div class="stat-label">Fees Collected</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="stat-card bg-gradient-orange">
                <div class="card-body">
                    <div class="stat-icon"><i class="fas fa-hand-holding-usd"></i></div>
                    <div class="stat-value">₦{{ number_format($totalOutstanding ?? 0, 2) }}</div>
                    <div class="stat-label">Outstanding Balance</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="stat-card bg-gradient-danger">
                <div class="card-body">
                    <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="stat-value">{{ $overduePayments ?? 0 }}</div>
                    <div class="stat-label">Overdue Payments</div>
                </div>
            </div>
        </div>
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="stat-card bg-gradient-success">
                <div class="card-body">
                    <div class="stat-icon"><i class="fas fa-percent"></i></div>
                    <div class="stat-value">{{ $collectionRate ?? 0 }}%</div>
                    <div class="stat-label">Collection Rate</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Summary & Quick Actions -->
    <div class="row mb-4">
        <div class="col-xl-6 col-lg-12 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie text-primary me-2"></i>Financial Summary
                    </h5>
                    <span class="badge bg-primary">{{ now()->format('M Y') }}</span>
                </div>
                <div class="card-body">
                    <div class="financial-summary">
                        <div class="summary-item">
                            <span class="label"><i class="fas fa-arrow-up text-success me-1"></i> Total Income</span>
                            <span class="value positive">₦{{ number_format($totalIncome ?? 0, 2) }}</span>
                        </div>
                        <div class="summary-item">
                            <span class="label"><i class="fas fa-arrow-down text-danger me-1"></i> Total Expenses</span>
                            <span class="value negative">₦{{ number_format($totalExpenses ?? 0, 2) }}</span>
                        </div>
                        <div class="summary-item">
                            <span class="label"><i class="fas fa-balance-scale text-primary me-1"></i> Net Profit</span>
                            <span class="value {{ ($netProfit ?? 0) >= 0 ? 'positive' : 'negative' }}">
                                ₦{{ number_format($netProfit ?? 0, 2) }}
                            </span>
                        </div>
                        <div class="summary-item">
                            <span class="label"><i class="fas fa-money-bill-wave text-success me-1"></i> Fees Collected</span>
                            <span class="value positive">₦{{ number_format($feesCollected ?? 0, 2) }}</span>
                        </div>
                        <div class="summary-item">
                            <span class="label"><i class="fas fa-credit-card text-warning me-1"></i> Pending Payments</span>
                            <span class="value">₦{{ number_format($pendingPayments ?? 0, 2) }}</span>
                        </div>
                        <div class="summary-item">
                            <span class="label"><i class="fas fa-file-invoice text-danger me-1"></i> Overdue</span>
                            <span class="value negative">₦{{ number_format($overdueAmount ?? 0, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-lg-12 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt text-primary me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <a href="{{ route('fee-payments.create') }}" class="quick-action-btn">
                                <i class="fas fa-plus-circle"></i>
                                <div class="action-title">Collect Payment</div>
                                <div class="action-desc">Record new fee payment</div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('fee-payments.history') }}" class="quick-action-btn">
                                <i class="fas fa-history"></i>
                                <div class="action-title">Payment History</div>
                                <div class="action-desc">View all transactions</div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('expenses.create') }}" class="quick-action-btn">
                                <i class="fas fa-receipt"></i>
                                <div class="action-title">Add Expense</div>
                                <div class="action-desc">Record new expense</div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('reports.fee-collection') }}" class="quick-action-btn">
                                <i class="fas fa-file-alt"></i>
                                <div class="action-title">Fee Report</div>
                                <div class="action-desc">Generate reports</div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('fee-structure.index') }}" class="quick-action-btn">
                                <i class="fas fa-file-invoice"></i>
                                <div class="action-title">Fee Structure</div>
                                <div class="action-desc">Manage fee structure</div>
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('reports.index') }}" class="quick-action-btn">
                                <i class="fas fa-chart-line"></i>
                                <div class="action-title">Financial Reports</div>
                                <div class="action-desc">View analytics</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-exchange-alt text-primary me-2"></i>Recent Transactions
                    </h5>
                    <div>
                        <a href="{{ route('fee-payments.history') }}" class="btn btn-sm btn-outline-primary me-2">
                            View All <i class="fas fa-arrow-right"></i>
                        </a>
                        <a href="{{ route('fee-payments.create') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-plus-circle"></i> New Payment
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if(isset($recentTransactions) && $recentTransactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-transactions">
                                <thead>
                                    <tr>
                                        <th>Transaction ID</th>
                                        <th>Student</th>
                                        <th>Amount</th>
                                        <th>Type</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTransactions as $transaction)
                                    <tr>
                                        <td>
                                            <span class="fw-bold text-primary">#{{ $transaction->id }}</span>
                                            <br>
                                            <small class="text-muted">{{ $transaction->reference ?? 'N/A' }}</small>
                                        </td>
                                        <td>
                                            @if($transaction->student)
                                                <strong>{{ $transaction->student->full_name }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $transaction->student->admission_number }}</small>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="fw-bold">₦{{ number_format($transaction->amount, 2) }}</span>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ ucfirst($transaction->type ?? 'payment') }}</span>
                                        </td>
                                        <td>
                                            <small>{{ $transaction->created_at->format('d/m/Y') }}</small>
                                            <br>
                                            <small class="text-muted">{{ $transaction->created_at->format('h:i A') }}</small>
                                        </td>
                                        <td>
                                            @if($transaction->status == 'completed' || $transaction->status == 'paid')
                                                <span class="badge-status paid">Paid</span>
                                            @elseif($transaction->status == 'pending')
                                                <span class="badge-status pending">Pending</span>
                                            @elseif($transaction->status == 'failed')
                                                <span class="badge-status failed">Failed</span>
                                            @else
                                                <span class="badge-status partial">Partial</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('fee-payments.receipt', $transaction->id) }}" 
                                                   class="btn btn-outline-info" title="View Receipt">
                                                    <i class="fas fa-file-invoice"></i>
                                                </a>
                                                <button class="btn btn-outline-secondary" title="More Info">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted">No recent transactions found.</p>
                            <a href="{{ route('fee-payments.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle me-2"></i>Make First Payment
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Chart and Recent Activities -->
    <div class="row">
        <div class="col-xl-8 col-lg-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line text-primary me-2"></i>Revenue & Expenses Overview
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <button type="button" class="btn btn-outline-primary active" onclick="updateChart('monthly')">Monthly</button>
                        <button type="button" class="btn btn-outline-primary" onclick="updateChart('quarterly')">Quarterly</button>
                        <button type="button" class="btn btn-outline-primary" onclick="updateChart('yearly')">Yearly</button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-12 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-clock text-primary me-2"></i>Recent Activities
                    </h5>
                </div>
                <div class="card-body" style="max-height: 380px; overflow-y: auto;">
                    @if(isset($recentActivities) && $recentActivities->count() > 0)
                        @foreach($recentActivities as $activity)
                        <div class="activity-item">
                            <div class="d-flex align-items-start">
                                <div class="activity-icon">
                                    @if($activity->type == 'payment')
                                        <i class="fas fa-money-bill-wave text-success"></i>
                                    @elseif($activity->type == 'expense')
                                        <i class="fas fa-receipt text-danger"></i>
                                    @elseif($activity->type == 'invoice')
                                        <i class="fas fa-file-invoice text-warning"></i>
                                    @else
                                        <i class="fas fa-bell text-info"></i>
                                    @endif
                                </div>
                                <div>
                                    <div class="activity-text">{{ $activity->description }}</div>
                                    <div class="activity-time">
                                        <i class="far fa-clock me-1"></i> {{ $activity->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-bell fa-2x text-muted mb-2 d-block"></i>
                            <p class="text-muted">No recent activities</p>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-white">
                    <a href="{{ route('notifications.index') }}" class="btn btn-link btn-sm text-decoration-none">
                        View All Activities <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Revenue Chart
    let revenueChart = null;
    
    function initChart() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        // Sample data - replace with actual data from controller
        const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        const revenueData = {!! json_encode($chartRevenue ?? []) !!};
        const expenseData = {!! json_encode($chartExpenses ?? []) !!};
        
        revenueChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Revenue',
                        data: revenueData.length ? revenueData : [12000, 19000, 15000, 25000, 22000, 30000, 28000, 35000, 31000, 40000, 38000, 45000],
                        backgroundColor: 'rgba(102, 126, 234, 0.7)',
                        borderColor: '#667eea',
                        borderWidth: 2,
                        borderRadius: 4,
                    },
                    {
                        label: 'Expenses',
                        data: expenseData.length ? expenseData : [8000, 12000, 10000, 15000, 14000, 18000, 16000, 20000, 19000, 25000, 22000, 28000],
                        backgroundColor: 'rgba(245, 87, 108, 0.7)',
                        borderColor: '#f5576c',
                        borderWidth: 2,
                        borderRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += '₦' + context.parsed.y.toLocaleString();
                                }
                                return label;
                            }
                        }
                    }
                },
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
    }

    function updateChart(period) {
        // Update chart based on period
        // This will be implemented with AJAX to fetch data for the selected period
        alert('Updating chart for ' + period + ' period. (Feature coming soon)');
    }

    // Initialize chart when page loads
    document.addEventListener('DOMContentLoaded', function() {
        initChart();
    });

    // Auto-refresh stats every 60 seconds
    setInterval(function() {
        // AJAX call to refresh stats
        // This will be implemented with AJAX to keep data up-to-date
    }, 60000);
</script>
@endpush
@endsection