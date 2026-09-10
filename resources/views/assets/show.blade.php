@extends('layouts.app')

@section('title', 'Asset Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('assets.index') }}">Assets</a></li>
    <li class="breadcrumb-item active">{{ $asset->name }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-building me-2"></i>Asset Details</h5>
            <div>
                <a href="{{ route('assets.edit', $asset->id) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('assets.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Asset Information</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th width="35%">Asset Code</th>
                            <td><strong>{{ $asset->asset_code }}</strong></td>
                        </tr>
                        <tr>
                            <th>Asset Name</th>
                            <td>{{ $asset->name }}
                                                        <tr>
                            <th>Category</th>
                            <td>{{ $asset->category->name }} ({{ $asset->category->depreciation_rate }}% depreciation)</td>
                        </tr>
                        <tr>
                            <th>Serial Number</th>
                            <td>{{ $asset->serial_number ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Purchase Date</th>
                            <td>{{ date('d M Y', strtotime($asset->purchase_date)) }}</td>
                        </tr>
                        <tr>
                            <th>Supplier</th>
                            <td>{{ $asset->supplier }}</td>
                        </tr>
                        <tr>
                            <th>Location</th>
                            <td>{{ $asset->location }}</td>
                        </tr>
                        <tr>
                            <th>Assigned To</th>
                            <td>{{ $asset->assigned_to ?? 'Not Assigned' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($asset->status == 'active')
                                    <span class="badge bg-success">Active</span>
                                @elseif($asset->status == 'depreciated')
                                    <span class="badge bg-warning">Fully Depreciated</span>
                                @else
                                    <span class="badge bg-secondary">Disposed</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Financial Information</h6>
                    <table class="table table-bordered">
                        <tr>
                            <th width="35%">Original Cost</th>
                            <td>₦{{ number_format($asset->purchase_price, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Current Value</th>
                            <td><span class="text-success">₦{{ number_format($asset->current_value, 2) }}</span></td>
                        </tr>
                        <tr>
                            <th>Total Depreciation</th>
                            <td><span class="text-warning">₦{{ number_format($asset->purchase_price - $asset->current_value, 2) }}</span></td>
                        </tr>
                        <tr>
                            <th>Depreciation Rate</th>
                            <td>{{ $asset->category->depreciation_rate }}% per year</small></td>
                        </tr>
                        <tr>
                            <th>Age</th>
                            <td>
                                @php
                                    $age = \Carbon\Carbon::parse($asset->purchase_date)->diff(\Carbon\Carbon::now());
                                    echo $age->y . ' years, ' . $age->m . ' months';
                                @endphp
                            </td>
                        </tr>
                        <tr>
                            <th>Depreciation %</th>
                            <td>
                                @php
                                    $depPercent = $asset->purchase_price > 0 ? (($asset->purchase_price - $asset->current_value) / $asset->purchase_price) * 100 : 0;
                                @endphp
                                <div class="progress">
                                    <div class="progress-bar bg-warning" role="progressbar" 
                                         style="width: {{ $depPercent }}%">
                                        {{ number_format($depPercent, 1) }}%
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            @if($asset->remarks)
            <div class="row mt-3">
                <div class="col-md-12">
                    <h6>Remarks</h6>
                    <div class="alert alert-info">
                        {{ $asset->remarks }}
                    </div>
                </div>
            </div>
            @endif
            
            <div class="row mt-4">
                <div class="col-md-12">
                    <h6>Depreciation Schedule (10 Year Forecast)</h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Year</th>
                                    <th>Year End Date</th>
                                    <th>Depreciation (₦)</th>
                                    <th>Remaining Value (₦)</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($depreciationSchedule as $schedule)
                                <tr>
                                    <td>Year {{ $schedule['year'] }}</small></td>
                                    <td>{{ date('d M Y', strtotime($schedule['year_end_date'])) }}</small></td>
                                    <td>₦{{ number_format($schedule['depreciation'], 2) }}</small></td>
                                    <td>
                                        @if($schedule['remaining_value'] > 0)
                                            ₦{{ number_format($schedule['remaining_value'], 2) }}
                                        @else
                                            <span class="text-success">Fully Depreciated</span>
                                        @endif
                                    </small></td>
                                    <td>
                                        @if($schedule['is_future'])
                                            <span class="badge bg-info">Projected</span>
                                        @elseif($schedule['remaining_value'] <= 0)
                                            <span class="badge bg-success">Fully Depreciated</span>
                                        @else
                                            <span class="badge bg-secondary">Historical</span>
                                        @endif
                                    </small></td>
                                </tr>
                                @endforeach
                            </tbody>
                                                    </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection