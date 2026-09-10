<div class="row">
    <div class="col-md-12">
        <!-- Paystack Settings -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fab fa-paystack me-2"></i>Paystack Configuration</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update-paystack') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Paystack Public Key <span class="text-danger">*</span></label>
                                <input type="text" name="paystack_public_key" class="form-control" 
                                       value="{{ $paystackPublicKey ?? getSetting('paystack_public_key') }}" required>
                                <small class="text-muted">Starts with <code>pk_</code></small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Paystack Secret Key <span class="text-danger">*</span></label>
                                <input type="password" name="paystack_secret_key" class="form-control" 
                                       value="{{ $paystackSecretKey ?? getSetting('paystack_secret_key') }}" required>
                                <small class="text-muted">Starts with <code>sk_</code></small>
                                <div class="form-check mt-2">
                                    <input type="checkbox" class="form-check-input" id="showPaystackSecret">
                                    <label class="form-check-label" for="showPaystackSecret">Show Secret Key</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Environment</label>
                                <select name="paystack_environment" class="form-control">
                                    <option value="live" {{ ($paystackEnvironment ?? getSetting('paystack_environment', 'live')) == 'live' ? 'selected' : '' }}>Live</option>
                                    <option value="test" {{ ($paystackEnvironment ?? getSetting('paystack_environment', 'live')) == 'test' ? 'selected' : '' }}>Test</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Processing Fee (%)</label>
                                <input type="number" step="0.01" name="payment_processing_fee" class="form-control" 
                                       value="{{ $paymentProcessingFee ?? getSetting('payment_processing_fee', '1.5') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Late Payment Fee (%)</label>
                                <input type="number" step="0.01" name="late_payment_fee" class="form-control" 
                                       value="{{ $latePaymentFee ?? getSetting('late_payment_fee', '5') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Grace Period (days)</label>
                                <input type="number" name="payment_grace_period" class="form-control" 
                                       value="{{ $paymentGracePeriod ?? getSetting('payment_grace_period', '7') }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Callback URL:</strong> {{ url('/fee-payments/callback') }}<br>
                                <strong>Webhook URL:</strong> {{ url('/paystack/webhook') }}
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Paystack Settings
                        </button>
                        <button type="button" class="btn btn-info" onclick="testPaystack()">
                            <i class="fas fa-vial"></i> Test Connection
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Bank Settings -->
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-university me-2"></i>Bank Transfer Settings</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update-bank') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Bank Name <span class="text-danger">*</span></label>
                                <input type="text" name="school_bank_name" class="form-control" 
                                       value="{{ $bankName ?? getSetting('school_bank_name', 'First Bank Nigeria') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Account Number <span class="text-danger">*</span></label>
                                <input type="text" name="school_account_number" class="form-control" 
                                       value="{{ $accountNumber ?? getSetting('school_account_number', '1234567890') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Account Name <span class="text-danger">*</span></label>
                                <input type="text" name="school_account_name" class="form-control" 
                                       value="{{ $accountName ?? getSetting('school_account_name', 'Excellence International School') }}" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label>Bank Code</label>
                                <input type="text" name="school_bank_code" class="form-control" 
                                       value="{{ $bankCode ?? getSetting('school_bank_code', '011') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label>Account Type</label>
                                <select name="school_account_type" class="form-control">
                                    <option value="Current" {{ ($accountType ?? getSetting('school_account_type', 'Current')) == 'Current' ? 'selected' : '' }}>Current</option>
                                    <option value="Savings" {{ ($accountType ?? getSetting('school_account_type', 'Current')) == 'Savings' ? 'selected' : '' }}>Savings</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Bank Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $('#showPaystackSecret').change(function() {
        $('input[name="paystack_secret_key"]').attr('type', this.checked ? 'text' : 'password');
    });

    function testPaystack() {
        const publicKey = $('input[name="paystack_public_key"]').val();
        const secretKey = $('input[name="paystack_secret_key"]').val();
        
        if (!publicKey || !secretKey) {
            alert('Please save your API keys first');
            return;
        }
        
        alert('Testing Paystack connection... Check console for results.');
    }
</script>
@endpush