@extends('layouts.app')

@section('title', 'Edit Asset')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('assets.index') }}">Assets</a></li>
    <li class="breadcrumb-item"><a href="{{ route('assets.show', $asset->id) }}">{{ $asset->name }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Asset</h5>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('assets.update', $asset->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Asset Code *</label>
                        <input type="text" name="asset_code" class="form-control" value="{{ $asset->asset_code }}" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Asset Name *</label>
                        <input type="text" name="name" class="form-control" value="{{ $asset->name }}" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Category *</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $asset->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }} ({{ $category->depreciation_rate }}% depreciation)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label>Purchase Price (₦) *</label>
                        <input type="number" step="0.01" name="purchase_price" class="form-control" value="{{ $asset->purchase_price }}" required>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label>Purchase Date *</label>
                        <input type="date" name="purchase_date" class="form-control" value="{{ $asset->purchase_date }}" required>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label>Supplier *</label>
                        <input type="text" name="supplier" class="form-control" value="{{ $asset->supplier }}" required>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label>Serial Number</label>
                        <input type="text" name="serial_number" class="form-control" value="{{ $asset->serial_number }}">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Location *</label>
                        <input type="text" name="location" class="form-control" value="{{ $asset->location }}" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Assigned To</label>
                        <input type="text" name="assigned_to" class="form-control" value="{{ $asset->assigned_to }}">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Status *</label>
                        <select name="status" class="form-control" required>
                            <option value="active" {{ $asset->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="depreciated" {{ $asset->status == 'depreciated' ? 'selected' : '' }}>Fully Depreciated</option>
                            <option value="disposed" {{ $asset->status == 'disposed' ? 'selected' : '' }}>Disposed</option>
                        </select>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label>Remarks</label>
                        <textarea name="remarks" class="form-control" rows="3">{{ $asset->remarks }}</textarea>
                    </div>
                </div>
                
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <strong>Note:</strong> Changing the purchase price or date will recalculate the depreciation automatically.
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Update Asset
                    </button>
                    <a href="{{ route('assets.show', $asset->id) }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection