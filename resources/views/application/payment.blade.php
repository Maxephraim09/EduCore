@extends('layouts.portal')

@section('title', 'Pay Application Fee')

@section('styles')
<style>
    .payment-card {
        background: #fff;
        border-radius: 16px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.08);
    }
    .payment-summary {
        background: #f8fafc;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid #e2e8f0;
    }
    .payment-summary .summary-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #e2e8f0;
    }
    .payment-summary .summary-item:last-child {
        border-bottom: none;
    }
    .payment-summary .summary-item .label {
        color: #64748b;
    }
    .payment-summary .summary-item .value {
        font-weight: 600;
        color: #1e293b;
    }
    .payment-summary .summary-item.total .value {
        font-size: 18px;
        color: #667eea;
    }
    .payment-options .payment-option {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 15px 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }
    .payment-options .payment-option:hover {
        border-color: #667eea;
        background: #f8fafc;
    }
    .payment-options .payment-option.active {
        border-color: #667eea;
        background: #f0f4ff;
    }
    .payment-options .payment-option input[type="radio"] {
        margin-right: 15px;
        width: 18px;
        height: 18px;
    }
    .payment-options .payment-option .option-icon {
        font-size: 24px;
        margin-right: 15px;
        color: #667eea;
    }
    .payment-options .payment-option .option-text {
        font-weight: 600;
        color: #1e293b;
    }
    .payment-options .payment-option .option-desc {
        font-size: 12px;
        color: #94a3b8;
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="payment-card">
                <div class="text-center mb-4">
                    <h2 class="fw-bold text-primary">
                        <i class="fas fa-credit-card me-2"></i>Pay Application Fee
                    </h2>
                    <p class="text-muted">Complete your payment to proceed with your application</p>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-7">
                        <form action="{{ route('application.process-payment', $application->application_number) }}" method="POST">
                            @csrf
                            
                            <div class="payment-options mb-4">
                                <h6 class="fw-bold mb-3">Select Payment Method</h6>
                                
                                <div class="payment-option active" onclick="selectPayment('card')">
                                    <input type="radio" name="payment_method" value="card" checked>
                                    <span class="option-icon"><i class="fas fa-credit-card"></i></span>
                                    <div>
                                        <div class="option-text">Card Payment</div>
                                        <div class="option-desc">Pay with debit/credit card</div>
                                    </div>
                                </div>
                                
                                <div class="payment-option" onclick="selectPayment('bank_transfer')">
                                    <input type="radio" name="payment_method" value="bank_transfer">
                                    <span class="option-icon"><i class="fas fa-university"></i></span>
                                    <div>
                                        <div class="option-text">Bank Transfer</div>
                                        <div class="option-desc">Pay via bank transfer</div>
                                    </div>
                                </div>
                                
                                <div class="payment-option" onclick="selectPayment('paystack')">
                                    <input type="radio" name="payment_method" value="paystack">
                                    <span class="option-icon"><i class="fas fa-bolt"></i></span>
                                    <div>
                                        <div class="option-text">Paystack</div>
                                        <div class="option-desc">Secure payment via Paystack</div>
                                    </div>
                                </div>
                            </div>

                            <div id="bankTransferDetails" style="display: none;" class="alert alert-info">
                                <h6><i class="fas fa-info-circle me-2"></i>Bank Transfer Details</h6>
                                <p class="mb-1"><strong>Bank:</strong> {{ getBankName() }}</p>
                                <p class="mb-1"><strong>Account Name:</strong> {{ getAccountName() }}</p>
                                <p class="mb-1"><strong>Account Number:</strong> {{ getAccountNumber() }}</p>
                                <p class="mb-0"><strong>Amount:</strong> {{ formatCurrency($application->application_fee) }}</p>
                                <hr>
                                <p class="mb-0 small">
                                    <i class="fas fa-exclamation-circle me-1"></i>
                                    After transfer, enter the transaction reference below
                                </p>
                            </div>

                            <div id="referenceField" style="display: none;">
                                <div class="form-group">
                                    <label for="payment_reference" class="form-label">Transaction Reference <span class="text-danger">*</span></label>
                                    <input type="text" name="payment_reference" id="payment_reference" 
                                           class="form-control" placeholder="Enter bank transaction reference">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg btn-block mt-3">
                                <i class="fas fa-check me-2"></i>Pay {{ formatCurrency($application->application_fee) }}
                            </button>
                        </form>
                    </div>

                    <div class="col-md-5">
                        <div class="payment-summary">
                            <h6 class="fw-bold mb-3">Payment Summary</h6>
                            <div class="summary-item">
                                <span class="label">Application Number</span>
                                <span class="value">{{ $application->application_number }}</span>
                            </div>
                            <div class="summary-item">
                                <span class="label">Applicant</span>
                                <span class="value">{{ $application->full_name }}</span>
                            </div>
                            <div class="summary-item">
                                <span class="label">Email</span>
                                <span class="value">{{ $application->email }}</span>
                            </div>
                            <div class="summary-item">
                                <span class="label">Applied Class</span>
                                <span class="value">{{ $application->class->full_class_name ?? 'N/A' }}</span>
                            </div>
                            <div class="summary-item total">
                                <span class="label">Amount to Pay</span>
                                <span class="value">{{ formatCurrency($application->application_fee) }}</span>
                            </div>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('application.success', $application->application_number) }}" class="btn btn-outline-secondary btn-block">
                                <i class="fas fa-arrow-left me-2"></i>Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function selectPayment(method) {
        // Update radio button
        document.querySelectorAll('input[name="payment_method"]').forEach(el => {
            el.checked = false;
        });
        document.querySelector(`input[name="payment_method"][value="${method}"]`).checked = true;

        // Update active state
        document.querySelectorAll('.payment-option').forEach(el => {
            el.classList.remove('active');
        });
        document.querySelector(`.payment-option:has(input[value="${method}"])`).classList.add('active');

        // Show/hide bank transfer details
        if (method === 'bank_transfer') {
            document.getElementById('bankTransferDetails').style.display = 'block';
            document.getElementById('referenceField').style.display = 'block';
        } else {
            document.getElementById('bankTransferDetails').style.display = 'none';
            document.getElementById('referenceField').style.display = 'none';
        }
    }
</script>
@endpush
@endsection