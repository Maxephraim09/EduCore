@extends('layouts.app')

@section('title', 'Edit Expense')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('expenses.index') }}">Expenses</a></li>
    <li class="breadcrumb-item active">Edit Expense</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Expense</h5>
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

            <form action="{{ route('expenses.update', $expense->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Expense Category *</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $expense->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }} ({{ $category->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Amount (₦) *</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required value="{{ $expense->amount }}">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Expense Date *</label>
                        <input type="date" name="expense_date" class="form-control" required value="{{ $expense->expense_date }}">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Payment Method *</label>
                        <select name="payment_method" class="form-control" required>
                            <option value="cash" {{ $expense->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank_transfer" {{ $expense->payment_method == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="cheque" {{ $expense->payment_method == 'cheque' ? 'selected' : '' }}>Cheque</option>
                        </select>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Vendor Name</label>
                        <input type="text" name="vendor_name" class="form-control" value="{{ $expense->vendor_name }}">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Invoice/Reference Number</label>
                        <input type="text" name="invoice_number" class="form-control" value="{{ $expense->invoice_number }}">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Receipt/Attachment</label>
                        <input type="file" name="receipt" class="form-control" accept="image/*,.pdf">
                        @if($expense->receipt_path)
                            <small class="text-muted">Current file: <a href="{{ Storage::url($expense->receipt_path) }}" target="_blank">View Receipt</a></small>
                        @endif
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label>Description *</label>
                        <textarea name="description" class="form-control" rows="4" required>{{ $expense->description }}</textarea>
                    </div>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Update Expense
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