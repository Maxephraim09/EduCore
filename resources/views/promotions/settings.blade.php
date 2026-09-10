@extends('layouts.app')

@section('title', 'Promotion Settings')

@push('styles')
<style>
    .promotion-settings-card {
        border: 0;
        border-radius: 12px;
        box-shadow: 0 4px 18px rgba(31, 41, 55, 0.08);
    }

    .promotion-settings-card .card-header {
        background: #fff;
        border-bottom: 1px solid #e9ecef;
        padding: 1.25rem 1.5rem;
    }

    .promotion-settings-card .card-body {
        padding: 1.5rem;
    }

    .promotion-settings-card .table thead th {
        background: #f8f9fa;
        border-bottom-width: 1px;
        color: #495057;
        font-size: 0.78rem;
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    @media (max-width: 575.98px) {
        .promotion-settings-card .card-header,
        .promotion-settings-card .card-body {
            padding: 1rem;
        }

        .promotion-settings-card .card-header {
            align-items: flex-start !important;
            flex-direction: column;
            gap: 0.75rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container mt-4">
    <div class="card promotion-settings-card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0">Promotion Mapping Settings</h4>
                <small class="text-muted">Configure the class progression path for each current class.</small>
            </div>
            <a href="{{ route('promotions.index') }}" class="btn btn-outline-secondary btn-sm">Back to review</a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('promotions.settings.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Current class</label>
                        <select name="source_class_id" class="form-select" required>
                            <option value="">Select class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->full_class_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Target type</label>
                        <select name="target_type" id="target_type" class="form-select" required>
                            <option value="class">Target class</option>
                            <option value="alumni">Send to alumni</option>
                        </select>
                    </div>
                    <div class="col-md-5" id="target_class_block">
                        <label class="form-label">Target class</label>
                        <select name="target_class_id" id="target_class_id" class="form-select">
                            <option value="">Select target class</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->full_class_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">Save mapping</button>
                </div>
            </form>

            <hr class="my-4">

            <h5 class="mb-3">Configured mappings</h5>
            @if(count($promotionMappings) > 0)
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Source class</th>
                                <th>Target</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($promotionMappings as $key => $mapping)
                                <tr>
                                    <td>{{ $mapping['source_class_name'] ?? 'Unknown' }}</td>
                                    <td>{{ $mapping['target_type'] === 'alumni' ? 'Alumni' : ($mapping['target_class_name'] ?? 'Not set') }}</td>
                                    <td>
                                        <form method="POST" action="{{ route('promotions.settings.remove') }}" class="d-inline">
                                            @csrf
                                            <input type="hidden" name="mapping_key" value="{{ $key }}">
                                            <button type="submit" class="btn btn-sm btn-outline-danger">Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info mb-0">No promotion mappings created yet.</div>
            @endif
        </div>
    </div>
</div>

<script>
    const targetType = document.getElementById('target_type');
    const targetClassBlock = document.getElementById('target_class_block');
    const targetClass = document.getElementById('target_class_id');

    function updateTargetClassState() {
        const hasClassTarget = targetType?.value === 'class';

        if (!targetClassBlock || !targetClass) {
            return;
        }

        targetClassBlock.hidden = !hasClassTarget;
        targetClass.disabled = !hasClassTarget;
        targetClass.required = hasClassTarget;

        if (!hasClassTarget) {
            targetClass.value = '';
        }
    }

    targetType?.addEventListener('change', updateTargetClassState);
    updateTargetClassState();
</script>
@endsection
