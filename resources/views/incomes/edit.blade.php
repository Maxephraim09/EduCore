@extends('layouts.app')

@section('title', 'Edit Income')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('other-incomes.index') }}">Other Incomes</a></li>
    <li class="breadcrumb-item"><a href="{{ route('other-incomes.show', $income->id) }}">Income Details</a></li>
    <li class="breadcrumb-item active">Edit Income</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Income Record</h5>
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

            <form action="{{ route('other-incomes.update', $income->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Income Category *</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $income->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }} ({{ $category->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Income Source *</label>
                        <input type="text" name="source" class="form-control" required value="{{ $income->source }}">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Amount (₦) *</label>
                        <input type="number" step="0.01" name="amount" class="form-control" required value="{{ $income->amount }}">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Income Date *</label>
                        <input type="date" name="income_date" class="form-control" required value="{{ $income->income_date }}">
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <label>Payment Method *</label>
                        <select name="payment_method" class="form-control" required>
                            <option value="cash" {{ $income->payment_method == 'cash' ? 'selected' : '' }}>Cash</option>
                            <option value="bank_transfer" {{ $income->payment_method == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            <option value="cheque" {{ $income->payment_method == 'cheque' ? 'selected' : '' }}>Cheque</option>
                            <option value="online" {{ $income->payment_method == 'online' ? 'selected' : '' }}>Online Payment</option>
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Reference/Transaction Number</label>
                        <input type="text" name="reference_number" class="form-control" value="{{ $income->reference_number }}">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Receipt/Attachment</label>
                        <input type="file" name="receipt" class="form-control" accept="image/*,.pdf">
                        @if($income->receipt_path)
                            <small class="text-muted">Current file: <a href="{{ Storage::url($income->receipt_path) }}" target="_blank">View Receipt</a></small>
                        @endif
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label>Description *</label>
                        <textarea name="description" class="form-control" rows="4" required>{{ $income->description }}</textarea>
                    </div>
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Update Income
                    </button>
                    <a href="{{ route('other-incomes.show', $income->id) }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection