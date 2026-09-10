@extends('layouts.app')

@section('title', 'Overdraft Reports')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('staff-overdrafts.index') }}">Overdrafts</a></li>
    <li class="breadcrumb-item active">Reports</li>
@endsection

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card"><div class="card-body"><small class="text-muted">Total Limit</small><h4>{{ number_format($totalOverdrafts, 2) }}</h4></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><small class="text-muted">Utilized</small><h4>{{ number_format($totalUtilized, 2) }}</h4></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><small class="text-muted">Available</small><h4>{{ number_format($totalAvailable, 2) }}</h4></div></div></div>
    <div class="col-md-3"><div class="card"><div class="card-body"><small class="text-muted">Utilization Rate</small><h4>{{ number_format($utilizationRate, 1) }}%</h4></div></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white"><h5 class="mb-0">By Department</h5></div>
            <div class="card-body table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>Department</th><th>Count</th><th>Limit</th><th>Used</th></tr></thead>
                    <tbody>
                        @forelse($overdraftsByDepartment as $item)
                            <tr><td>{{ $item->department }}</td><td>{{ $item->count }}</td><td>{{ number_format($item->total_limit, 2) }}</td><td>{{ number_format($item->total_used, 2) }}</td></tr>
                        @empty
                            <tr><td colspan="4" class="text-muted">No overdraft data available.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white"><h5 class="mb-0">Top Users</h5></div>
            <div class="card-body table-responsive">
                <table class="table table-sm">
                    <thead><tr><th>Employee</th><th>Limit</th><th>Used</th><th>Available</th></tr></thead>
                    <tbody>
                        @forelse($topUsers as $user)
                            <tr><td>{{ $user->first_name }} {{ $user->last_name }}</td><td>{{ number_format($user->limit_amount, 2) }}</td><td>{{ number_format($user->used_amount, 2) }}</td><td>{{ number_format($user->available_amount, 2) }}</td></tr>
                        @empty
                            <tr><td colspan="4" class="text-muted">No overdraft users available.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
