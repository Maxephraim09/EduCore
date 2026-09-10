@extends('layouts.app')

@section('title', 'Add Income')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('other-incomes.index') }}">Other Incomes</a></li>
    <li class="breadcrumb-item active">Add Income</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Record Other Income</h5>
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

            <form action="{{ route('other-incomes.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Income Category *</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Income Source *</label>
                        <input type="text" name="source" class="form-control" required placeholder="e.g., Donation, Grant, Investment, Rental">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Amount (₦) *</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required placeholder="0.00">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Income Date *</label>
                        <input type="date" name="income_date" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Payment Method *</label>
                        <select name="payment_method" class="form-control" required>
                            <option value="">Select Payment Method</option>
                            <option value="cash">Cash</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="cheque">Cheque</option>
                            <option value="online">Online Payment</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Reference/Transaction Number</label>
                        <input type="text" name="reference_number" class="form-control" placeholder="Reference number from payment">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Receipt/Attachment</label>
                        <input type="file" name="receipt" class="form-control" accept="image/*,.pdf">
                        <small class="text-muted">Upload receipt or supporting document (Max: 2MB)</small>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label>Description *</label>
                        <textarea name="description" class="form-control" rows="4" required placeholder="Detailed description of the income..."></textarea>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Note:</strong> Other incomes include donations, grants, investments, rentals, etc. that are not student fees.
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save"></i> Record Income
                    </button>
                    <a href="{{ route('other-incomes.index') }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection