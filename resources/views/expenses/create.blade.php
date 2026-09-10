@extends('layouts.app')

@section('title', 'Add Expense')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Expenses</a></li>
    <li class="breadcrumb-item active">Add Expense</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Record New Expense</h5>
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

            <form action="{{ route('expenses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Expense Category *</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Amount (₦) *</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required placeholder="0.00">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Expense Date *</label>
                        <input type="date" name="expense_date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Payment Method *</label>
                        <select name="payment_method" class="form-control" required>
                            <option value="">Select Payment Method</option>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Vendor Name</label>
                        <input type="text" name="vendor_name" class="form-control" placeholder="Vendor/Supplier name">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Invoice/Reference Number</label>
                        <input type="text" name="invoice_number" class="form-control" placeholder="Invoice or reference number">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Receipt/Attachment</label>
                        <input type="file" name="receipt" class="form-control" accept="image/*,.pdf">
                        <small class="text-muted">Upload receipt or supporting document (Max: 2MB)</small>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label>Description *</label>
                        <textarea name="description" class="form-control" rows="4" required placeholder="Detailed description of the expense..."></textarea>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Note:</strong> Expenses will require approval before being finalized. You can still edit pending expenses.
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Submit Expense
                    </button>
                    <a href="{{ route('expenses.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection