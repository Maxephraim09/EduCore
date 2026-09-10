@extends('layouts.app')

@section('title', 'Asset Depreciation Report')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('assets.index') }}">Assets</a></li>
    <li class="breadcrumb-item active">Depreciation Report</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="mb-0">Total Original Cost</h6>
                    <h3 class="mt-2 mb-0">₦{{ number_format($totalOriginalCost, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="mb-0">Current Value</h6>
                    <h3 class="mt-2 mb-0">₦{{ number_format($totalCurrentValue, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="mb-0">Total Depreciation</h6>
                    <h3 class="mt-2 mb-0">₦{{ number_format($totalDepreciation, 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="mb-0">Depreciation Rate</h6>
                    <h3 class="mt-2 mb-0">{{ number_format($depreciationPercentage, 1) }}%</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Depreciation by Category Chart -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-pie me-2"></i>Depreciation by Category</h5>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="300"></canvas>
                </div>
            </div>
        </div>

        <!-- Assets by Status -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-chart-donut me-2"></i>Assets by Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Depreciation by Category Table -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-table me-2"></i>Depreciation by Category</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Assets Count</th>
                                    <th>Original Cost</th>
                                    <th>Current Value</th>
                                    <th>Depreciation</th>
                                    <th>Depreciation %</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($depreciationByCategory as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->count }}</td>
                                    <td>₦{{ number_format($category->original_cost, 2) }}</td>
                                    <td>₦{{ number_format($category->current_value, 2) }}</td>
                                    <td>₦{{ number_format($category->depreciation, 2) }}</td>
                                    <td>
                                        @php
                                            $catDepPercent = $category->original_cost > 0 ? ($category->depreciation / $category->original_cost) * 100 : 0;
                                        @endphp
                                        <div class="progress">
                                            <div class="progress-bar bg-warning" style="width: {{ $catDepPercent }}%">
                                                {{ number_format($catDepPercent, 1) }}%
                                            </div>
                                        </div>
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

    <!-- Assets Nearing End of Life -->
    @if($nearingEndOfLife->count() > 0)
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i>Assets Nearing End of Life (Value < 10% of Original)</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Asset Code</th>
                                    <th>Asset Name</th>
                                    <th>Original Cost</th>
                                    <th>Current Value</th>
                                    <th>Depreciation %</th>
                                    <th>Age</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($nearingEndOfLife as $asset)
                                <tr>
                                    <td>{{ $asset->asset_code }}</td>
                                    <td>{{ $asset->name }}</td>
                                    <td>₦{{ number_format($asset->purchase_price, 2) }}</td>
                                    <td>₦{{ number_format($asset->current_value, 2) }}</td>
                                    <td>
                                        @php
                                            $depPercent = $asset->purchase_price > 0 ? (($asset->purchase_price - $asset->current_value) / $asset->purchase_price) * 100 : 0;
                                        @endphp
                                        {{ number_format($depPercent, 1) }}%
                                    </td>
                                    <td>
                                        @php
                                            $age = \Carbon\Carbon::parse($asset->purchase_date)->diff(\Carbon\Carbon::now());
                                            echo $age->y . ' yrs, ' . $age->m . ' mos';
                                        @endphp
                                    </td>
                                    <td>
                                        <a href="{{ route('assets.show', $asset->id) }}" class="btn btn-sm btn-info">View</a>
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
    @endif

    <!-- Fully Depreciated Assets -->
    @if($fullyDepreciated->count() > 0)
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>Fully Depreciated Assets</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Asset Code</th>
                                    <th>Asset Name</th>
                                    <th>Original Cost</th>
                                    <th>Current Value</th>
                                    <th>Purchase Date</th>
                                    <th>Age</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($fullyDepreciated as $asset)
                                <tr>
                                    <td>{{ $asset->asset_code }}</td>
                                    <td>{{ $asset->name }}</td>
                                    <td>₦{{ number_format($asset->purchase_price, 2) }}</td>
                                    <td>₦{{ number_format($asset->current_value, 2) }}</td>
                                    <td>{{ date('d M Y', strtotime($asset->purchase_date)) }}</td>
                                    <td>
                                        @php
                                            $age = \Carbon\Carbon::parse($asset->purchase_date)->diff(\Carbon\Carbon::now());
                                            echo $age->y . ' years';
                                        @endphp
                                    </td>
                                    <td>
                                        <a href="{{ route('assets.show', $asset->id) }}" class="btn btn-sm btn-info">View</a>
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
    @endif

    <!-- Export Buttons -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body text-center">
                    <button onclick="window.print()" class="btn btn-info">
                        <i class="fas fa-print"></i> Print Report
                    </button>
                    <button class="btn btn-success" onclick="exportToExcel()">
                        <i class="fas fa-file-excel"></i> Export to Excel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Category Depreciation Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($depreciationByCategory->pluck('name')) !!},
            datasets: [
                {
                    label: 'Original Cost',
                    data: {!! json_encode($depreciationByCategory->pluck('original_cost')) !!},
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Current Value',
                    data: {!! json_encode($depreciationByCategory->pluck('current_value')) !!},
                    backgroundColor: 'rgba(40, 167, 69, 0.5)',
                    borderColor: 'rgba(40, 167, 69, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
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
    
    // Status Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const activeCount = {{ $assets->where('status', 'active')->count() }};
    const depreciatedCount = {{ $assets->where('status', 'depreciated')->count() }};
    const disposedCount = {{ $assets->where('status', 'disposed')->count() }};
    
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Fully Depreciated', 'Disposed'],
            datasets: [{
                data: [activeCount, depreciatedCount, disposedCount],
                backgroundColor: ['#28a745', '#ffc107', '#6c757d'],
                hoverBackgroundColor: ['#218838', '#e0a800', '#5a6268']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    
    function exportToExcel() {
        alert('Excel export functionality will be implemented soon.');
    }
</script>
@endpush
@endsection