@extends('layouts.app')

@section('title', 'Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="fade-in">
    <!-- Stats Cards Row 1 -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-primary text-white h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small mb-1">Total Students</div>
                            <div class="h2 mb-0">{{ $totalStudents ?? 0 }}</div>
                            <div class="small mt-2">
                                <i class="fas fa-user-graduate"></i> Enrolled Students
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-user-graduate fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-success text-white h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small mb-1">Today's Fees</div>
                            <div class="h2 mb-0">₦{{ number_format($todayFees ?? 0, 2) }}</div>
                            <div class="small mt-2">
                                <i class="fas fa-calendar"></i> {{ date('d M Y') }}
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-info text-white h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small mb-1">Monthly Fees</div>
                            <div class="h2 mb-0">₦{{ number_format($monthFees ?? 0, 2) }}</div>
                            <div class="small mt-2">
                                <i class="fas fa-chart-line"></i> {{ date('F Y') }}
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-warning text-white h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small mb-1">Total Employees</div>
                            <div class="h2 mb-0">{{ $totalEmployees ?? 0 }}</div>
                            <div class="small mt-2">
                                <i class="fas fa-users"></i> Active Staff
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards Row 2 -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-danger text-white h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small mb-1">Monthly Expenses</div>
                            <div class="h3 mb-0">₦{{ number_format(($monthExpenses ?? 0) + ($monthSalaries ?? 0), 2) }}</div>
                            <div class="small mt-2">
                                <i class="fas fa-receipt"></i> Operating Costs
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-receipt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-secondary text-white h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small mb-1">Active Loans</div>
                            <div class="h3 mb-0">₦{{ number_format($activeLoans ?? 0, 2) }}</div>
                            <div class="small mt-2">
                                <i class="fas fa-hand-holding-usd"></i> Outstanding
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-hand-holding-usd fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-dark text-white h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small mb-1">Total Assets</div>
                            <div class="h3 mb-0">₦{{ number_format($totalAssets ?? 0, 2) }}</div>
                            <div class="small mt-2">
                                <i class="fas fa-building"></i> Asset Value
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-building fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card bg-teal text-white h-100 shadow-sm" style="background: linear-gradient(135deg, #20c997 0%, #20c997 100%);">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small mb-1">Bank Balance</div>
                            <div class="h3 mb-0">₦{{ number_format($bankBalance ?? 0, 2) }}</div>
                            <div class="small mt-2">
                                <i class="fas fa-university"></i> Available Funds
                            </div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3">
                            <i class="fas fa-university fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Financial Summary Row -->
    <div class="row">
        <div class="col-xl-6 col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line text-primary me-2"></i>Monthly Financial Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <td><strong>Fees Collected:</strong></td>
                                    <td class="text-end text-success">₦{{ number_format($monthFees ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Other Income:</strong></td>
                                    <td class="text-end text-success">₦{{ number_format($monthIncome ?? 0, 2) }}</td>
                                </tr>
                                <tr class="table-active">
                                    <td><strong>Total Income:</strong></td>
                                    <td class="text-end"><strong>₦{{ number_format(($monthFees ?? 0) + ($monthIncome ?? 0), 2) }}</strong></td>
                                </tr>
                                <tr>
                                    <td><strong>Expenses:</strong></td>
                                    <td class="text-end text-danger">₦{{ number_format($monthExpenses ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Salary Expenses:</strong></td>
                                    <td class="text-end text-danger">₦{{ number_format($monthSalaries ?? 0, 2) }}</td>
                                </tr>
                                <tr class="table-success">
                                    <td><strong>Net Profit/Loss:</strong></td>
                                    <td class="text-end">
                                        <strong class="text-success">
                                            ₦{{ number_format((($monthFees ?? 0) + ($monthIncome ?? 0)) - (($monthExpenses ?? 0) + ($monthSalaries ?? 0)), 2) }}
                                        </strong>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-6 col-md-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie text-info me-2"></i>Financial Health
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <tbody>
                                <tr>
                                    <td><strong>Active Loans Outstanding:</strong></td>
                                    <td class="text-end text-warning">₦{{ number_format($activeLoans ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Total Asset Value:</strong></td>
                                    <td class="text-end text-info">₦{{ number_format($totalAssets ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Bank Balance:</strong></td>
                                    <td class="text-end text-primary">₦{{ number_format($bankBalance ?? 0, 2) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Monthly Operating Cost:</strong></td>
                                    <td class="text-end text-danger">₦{{ number_format(($monthExpenses ?? 0) + ($monthSalaries ?? 0), 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Financial Chart -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-area text-primary me-2"></i>Income vs Expenses
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="financialChart" height="90"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-clock text-warning me-2"></i>Recent Fee Payments
                    </h5>
                    <a href="{{ route('fee-payments.history') }}" class="btn btn-sm btn-outline-primary">
                        View All <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Receipt No</th>
                                    <th>Student</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentPayments ?? [] as $payment)
                                <tr>
                                    <td>{{ $payment->receipt_number ?? 'N/A' }}</td>
                                    <td>{{ $payment->first_name ?? '' }} {{ $payment->last_name ?? '' }}</td>
                                    <td>₦{{ number_format($payment->amount_paid ?? 0, 2) }}</td>
                                    <td>{{ \Carbon\Carbon::parse($payment->payment_date ?? now())->format('d M Y') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">No recent payments found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-clock text-warning me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('students.create') }}" class="btn btn-primary w-100">
                                <i class="fas fa-user-plus me-2"></i>Add Student
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('fee-payments.create') }}" class="btn btn-success w-100">
                                <i class="fas fa-credit-card me-2"></i>Collect Payment
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('salaries.create') }}" class="btn btn-info w-100">
                                <i class="fas fa-money-bill me-2"></i>Process Salary
                            </a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('reports.index') }}" class="btn btn-warning w-100">
                                <i class="fas fa-chart-line me-2"></i>View Reports
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    @if(!\App\Models\SystemSetting::getValue('paystack_public_key'))
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle"></i> 
        <strong>Paystack not configured!</strong> Please go to 
        <a href="{{ route('settings.index') }}" class="alert-link">Settings → Paystack API Settings</a> 
        to add your API keys and start collecting payments online.
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartCanvas = document.getElementById('financialChart');

        if (!chartCanvas || typeof Chart === 'undefined') {
            return;
        }

        new Chart(chartCanvas, {
            type: 'line',
            data: {
                labels: @json(($chartData['months'] ?? collect())->values()),
                datasets: [
                    {
                        label: 'Income',
                        data: @json(($chartData['income'] ?? collect())->values()),
                        borderColor: '#198754',
                        backgroundColor: 'rgba(25, 135, 84, 0.12)',
                        tension: 0.35,
                        fill: true
                    },
                    {
                        label: 'Expenses',
                        data: @json(($chartData['expense'] ?? collect())->values()),
                        borderColor: '#dc3545',
                        backgroundColor: 'rgba(220, 53, 69, 0.12)',
                        tension: 0.35,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>
@endpush
