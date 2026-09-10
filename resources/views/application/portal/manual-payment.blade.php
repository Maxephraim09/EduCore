@extends('layouts.portal-dashboard')

@section('title','Manual Payment Instructions')
@section('page_title','Manual Payment')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Manual Bank Transfer Instructions</div>
                <div class="card-body">
                    <p>Please transfer <strong>{{ formatCurrency($tx->amount) }}</strong> to the following account and then click <strong>I have paid</strong> after you have transferred funds.</p>
                    <ul>
                        <li><strong>Bank:</strong> {{ $bankName }}</li>
                        <li><strong>Account Number:</strong> {{ $accountNumber }}</li>
                        <li><strong>Account Name:</strong> {{ $accountName }}</li>
                        <li><strong>Reference:</strong> {{ $tx->reference }}</li>
                    </ul>
                    <form action="{{ route('application.pay.manual.verify', $tx->id ?? 0) }}" method="POST">
                        @csrf
                        <button class="btn btn-primary">I have paid</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
