@extends('layouts.app')

@section('title', 'Add Asset')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('assets.index') }}">Assets</a></li>
    <li class="breadcrumb-item active">Add Asset</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Add New Asset</h5>
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

            <form action="{{ route('assets.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Asset Code *</label>
                        <input type="text" name="asset_code" class="form-control" required placeholder="AST-001">
                        <small class="text-muted">Unique identifier for this asset</small>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Asset Name *</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Category *</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">
                                    {{ $category->name }} ({{ $category->depreciation_rate }}% depreciation)
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label>Purchase Price (₦) *</label>
                        <input type="number" step="0.01" name="purchase_price" class="form-control" required>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label>Purchase Date *</label>
                        <input type="date" name="purchase_date" class="form-control" required>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label>Supplier *</label>
                        <input type="text" name="supplier" class="form-control" required>
                    </div>
                    
                    <div class="col-md-3 mb-3">
                        <label>Serial Number</label>
                        <input type="text" name="serial_number" class="form-control">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Location *</label>
                        <input type="text" name="location" class="form-control" required placeholder="e.g., Admin Building, Room 101">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Assigned To</label>
                        <input type="text" name="assigned_to" class="form-control" placeholder="Department or Person">
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label>Remarks</label>
                        <textarea name="remarks" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Depreciation Calculation:</strong> Asset value will be automatically depreciated based on the category's depreciation rate.
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Save Asset
                    </button>
                    <a href="{{ route('assets.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection