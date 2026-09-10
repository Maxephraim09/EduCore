@extends('layouts.app')

@section('title', 'Edit Overdraft')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('staff-overdrafts.index') }}">Overdrafts</a></li>
    <li class="breadcrumb-item"><a href="{{ route('overdrafts.show', $overdraft->id) }}">Overdraft Details</a></li>
    <li class="breadcrumb-item active">Edit Overdraft</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Overdraft Facility</h5>
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

            <form action="{{ route('overdrafts.update', $overdraft->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Employee *</label>
                        <select name="employee_id" class="form-control" required>
                            <option value="">Select Employee</option>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ $overdraft->employee_id == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->employee_id }} - {{ $employee->full_name }} ({{ $employee->position }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Interest Rate (%) *</label>
                        <input type="number" step="0.1" name="interest_rate" class="form-control" value="{{ $overdraft->interest_rate }}" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Limit Amount (₦) *</label>
                        <input type="number" step="0.01" name="limit_amount" class="form-control" value="{{ $overdraft->limit_amount }}" required>
                        <small class="text-muted">Current used amount: ₦{{ number_format($overdraft->used_amount, 2) }}</small>
                        @if($overdraft->used_amount > 0)
                            <div class="alert alert-warning mt-1">
                                <i class="fas fa-exclamation-triangle"></i> 
                                Reducing limit below used amount will require immediate repayment of the difference.
                            </div>
                        @endif
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Expiry Date *</label>
                        <input type="date" name="expiry_date" class="form-control" value="{{ $overdraft->expiry_date }}" required>
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label>Status *</label>
                        <select name="status" class="form-control" required>
                            <option value="active" {{ $overdraft->status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="suspended" {{ $overdraft->status == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            <option value="closed" {{ $overdraft->status == 'closed' ? 'selected' : '' }}>Closed</option>
                        </select>
                    </div>
                    
                    <div class="col-md-12 mb-3">
                        <label>Remarks</label>
                        <textarea name="remarks" class="form-control" rows="3">{{ $overdraft->remarks }}</textarea>
                    </div>
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> 
                    <strong>Note:</strong> Any changes to the limit or status will be applied immediately.
                </div>
                
                <div class="text-center">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Update Overdraft
                    </button>
                    <a href="{{ route('overdrafts.show', $overdraft->id) }}" class="btn btn-secondary btn-lg">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
