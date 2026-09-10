@extends('layouts.app')

@section('title', 'Asset Management')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Assets</li>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Total Assets Value</h6>
                            <h3 class="mt-2 mb-0">₦{{ number_format($totalAssets, 2) }}</h3>
                        </div>
                        <i class="fas fa-building fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Original Cost</h6>
                            <h3 class="mt-2 mb-0">₦{{ number_format($totalPurchase, 2) }}</h3>
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
                            <h6 class="mb-0">Total Depreciation</h6>
                            <h3 class="mt-2 mb-0">₦{{ number_format($totalDepreciation, 2) }}</h3>
                        </div>
                        <i class="fas fa-chart-line fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="mb-0">Active Assets</h6>
                            <h3 class="mt-2 mb-0">{{ $activeAssets }}</h3>
                        </div>
                        <i class="fas fa-check-circle fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Asset List</h5>
            <div>
                <a href="{{ route('assets.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus"></i> Add Asset
                </a>
                <a href="{{ route('asset-categories.index') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-tags"></i> Manage Categories
                </a>
                <a href="{{ route('assets.depreciation') }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-chart-line"></i> Depreciation Report
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="assetsTable">
                    <thead>
                        <tr>
                            <th>Asset Code</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Purchase Date</th>
                            <th>Original Cost</th>
                            <th>Current Value</th>
                            <th>Depreciation</th>
                            <th>Status</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assets as $asset)
                        <tr>
                            <td><strong>{{ $asset->asset_code }}</strong>
                                                        <td>{{ $asset->name }}
                                                                                        <td>{{ $asset->asset_code }}</td>
                            <td>{{ $asset->name }}<br>
                                <small class="text-muted">SN: {{ $asset->serial_number ?? 'N/A' }}</small>
                            </td>
                            <td>{{ $asset->category->name }}</td>
                            <td>{{ date('d M Y', strtotime($asset->purchase_date)) }}</td>
                            <td>₦{{ number_format($asset->purchase_price, 2) }}</td>
                            <td>
                                <span class="text-success">₦{{ number_format($asset->current_value, 2) }}</span>
                            </td>
                            <td>
                                @php
                                    $depPercent = $asset->purchase_price > 0 ? (($asset->purchase_price - $asset->current_value) / $asset->purchase_price) * 100 : 0;
                                @endphp
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-warning" role="progressbar" 
                                         style="width: {{ $depPercent }}%">
                                        {{ number_format($depPercent, 1) }}%
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($asset->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($asset->status == 'depreciated')
                                    <span class="badge bg-warning">Depreciated</span>
                                @else
                                    <span class="badge bg-secondary">Disposed</span>
                                @endif
                            </td>
                            <td>{{ $asset->location }}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('assets.show', $asset->id) }}" class="btn btn-sm btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('assets.edit', $asset->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $asset->id }}" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                <!-- Delete Modal -->
                                <div class="modal fade" id="deleteModal{{ $asset->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirm Delete</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                Are you sure you want to delete <strong>{{ $asset->name }}</strong>?
                                            </div>
                                            <div class="modal-footer">
                                                <form action="{{ route('assets.destroy', $asset->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" class="btn btn-danger">Delete</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $assets->links() }}
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#assetsTable').DataTable({
            pageLength: 10,
            responsive: true,
            order: [[0, 'desc']]
        });
    });
</script>
@endpush
@endsection