@extends('layouts.app')

@section('title', 'Parent Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Parent Dashboard</li>
@endsection

@section('content')
<div class="fade-in">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="fas fa-child text-primary me-2"></i>My Children
                    </h5>
                </div>
                <div class="card-body">
                    @forelse($children ?? [] as $child)
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6>{{ $child->full_name }} ({{ $child->admission_number }})</h6>
                        </div>
                        <div class="card-body">
                            <!-- Child's results here -->
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted">No children registered</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection