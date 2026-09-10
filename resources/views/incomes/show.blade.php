@extends('layouts.app')

@section('title', 'Income Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('other-incomes.index') }}">Other Incomes</a></li>
    <li class="breadcrumb-item active">Income Details</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-file-invoice me-2"></i>Income Details</h5>
            <div>
                <a href="{{ route('other-incomes.edit', $income->id) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit"></i> Edit
                </a>
                <a href="{{ route('other-incomes.index') }}" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="35%">Income Number</th>
                            <td><strong>{{ $income->income_number }}</strong></td>
                        </tr>
                        <tr>
                            <th>Income Date</th>
                            <td>{{ date('d M Y', strtotime($income->income_date)) }}
                                                        <tr>
                            <th>Category</th>
                            <td>{{ $income->category->name }} ({{ $income->category->code }})</td>
                        </tr>
                        <tr>
                            <th>Source</th>
                            <td>{{ $income->source }}</td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <td><h4 class="text-success">₦{{ number_format($income->amount, 2) }}</h4></td>
                        </tr>
                        <tr>
                            <th>Payment Method</th>
                            <td>
                                @if($income->payment_method == 'cash')
                                    <i class="fas fa-money-bill"></i> Cash
                                @elseif($income->payment_method == 'bank_transfer')
                                    <i class="fas fa-university"></i> Bank Transfer
                                @elseif($income->payment_method == 'cheque')
                                    <i class="fas fa-money-check"></i> Cheque
                                @else
                                    <i class="fab fa-paypal"></i> Online Payment
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Reference Number</th>
                            <td>{{ $income->reference_number ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-bordered">
                        <tr>
                            <th width="35%">Received By</th>
                            <td>{{ $income->receiver->name ?? 'System' }}</td>
                        </tr>
                        <tr>
                            <th>Recorded Date</th>
                            <td>{{ $income->created_at->format('d M Y h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated</th>
                            <td>{{ $income->updated_at->format('d M Y h:i A') }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-12">
                    <h6>Description</h6>
                    <div class="card bg-light">
                        <div class="card-body">
                            {{ $income->description }}
                        </div>
                    </div>
                </div>
            </div>
            
            @if($income->receipt_path)
            <div class="row mt-3">
                <div class="col-md-12">
                    <h6>Receipt/Attachment</h6>
                    <div class="card">
                        <div class="card-body text-center">
                            @php
                                $extension = pathinfo($income->receipt_path, PATHINFO_EXTENSION);
                            @endphp
                            @if(in_array($extension, ['jpg', 'jpeg', 'png', 'gif']))
                                <img src="{{ Storage::url($income->receipt_path) }}" alt="Receipt" style="max-width: 100%; max-height: 400px;">
                            @else
                                <a href="{{ Storage::url($income->receipt_path) }}" target="_blank" class="btn btn-primary">
                                    <i class="fas fa-download"></i> Download Receipt
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection