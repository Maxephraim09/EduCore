@extends('layouts.app')

@section('title', 'Edit Staff Loan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('staff-loans.index') }}">Staff Loans</a></li>
    <li class="breadcrumb-item active">Edit Loan</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="fas fa-edit me-2"></i>Edit Loan Status</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('staff-loans.update', $loan->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Employee</label>
                    <input class="form-control" value="{{ optional($loan->employee)->first_name }} {{ optional($loan->employee)->last_name }}" disabled>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Loan Amount</label>
                    <input class="form-control" value="{{ number_format($loan->amount, 2) }}" disabled>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Remaining Amount</label>
                    <input class="form-control" value="{{ number_format($loan->remaining_amount, 2) }}" disabled>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select" required>
                        @foreach(['active' => 'Active', 'completed' => 'Completed', 'defaulted' => 'Defaulted'] as $value => $label)
                            <option value="{{ $value }}" {{ $loan->status == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Remarks</label>
                    <input name="remarks" class="form-control" value="{{ old('remarks', $loan->remarks) }}">
                </div>
            </div>
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Loan</button>
                <a href="{{ route('staff-loans.show', $loan->id) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
