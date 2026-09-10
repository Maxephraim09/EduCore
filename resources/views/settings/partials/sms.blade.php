<div class="row">
    <div class="col-md-12">
        <form action="{{ route('settings.update-sms') }}" method="POST">
            @csrf
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                <strong>SMS API Configuration:</strong> Configure your SMS provider to send notifications to students and staff.
            </div>

            <div class="mb-3">
                <label>SMS Provider *</label>
                <select name="sms_provider" class="form-control" required>
                    <option value="twilio" {{ $smsProvider == 'twilio' ? 'selected' : '' }}>Twilio</option>
                    <option value="africastalking" {{ $smsProvider == 'africastalking' ? 'selected' : '' }}>Africa's Talking</option>
                    <option value="messagebird" {{ $smsProvider == 'messagebird' ? 'selected' : '' }}>MessageBird</option>
                    <option value="nexmo" {{ $smsProvider == 'nexmo' ? 'selected' : '' }}>Vonage (Nexmo)</option>
                </select>
            </div>

            <div class="mb-3">
                <label>API Key / SID *</label>
                <input type="text" name="sms_api_key" class="form-control" value="{{ $smsApiKey }}" required>
                <small class="text-muted">Your SMS provider API key or Account SID</small>
            </div>

            <div class="mb-3">
                <label>API Secret / Auth Token *</label>
                <input type="password" name="sms_api_secret" class="form-control" value="{{ $smsApiSecret }}" required>
                <div class="form-check mt-2">
                    <input type="checkbox" class="form-check-input" id="showSmsSecret">
                    <label class="form-check-label" for="showSmsSecret">Show Secret</label>
                </div>
            </div>

            <div class="mb-3">
                <label>Sender ID *</label>
                <input type="text" name="sms_sender_id" class="form-control" value="{{ $smsSenderId }}" maxlength="11" required>
                <small class="text-muted">This will appear as the sender name (max 11 characters)</small>
            </div>

            <div class="mb-3">
                <div class="alert {{ $smsApiKey && $smsApiSecret ? 'alert-success' : 'alert-warning' }}">
                    @if($smsApiKey && $smsApiSecret)
                        <i class="fas fa-check-circle"></i> SMS API is configured
                    @else
                        <i class="fas fa-exclamation-triangle"></i> SMS API is not fully configured
                    @endif
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save SMS Settings
            </button>
            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#testSmsModal">
                <i class="fas fa-vial"></i> Test SMS
            </button>
        </form>
    </div>
</div>

<!-- Test SMS Modal -->
<div class="modal fade" id="testSmsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Test SMS Configuration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Phone Number</label>
                    <input type="text" id="testPhone" class="form-control" placeholder="e.g., +1234567890">
                </div>
                <div class="mb-3">
                    <label>Message</label>
                    <textarea id="testMessage" class="form-control" rows="3">This is a test message from your Financial Management System.</textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="sendTestSms()">Send Test SMS</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $('#showSmsSecret').change(function() {
        $('input[name="sms_api_secret"]').attr('type', this.checked ? 'text' : 'password');
    });
    
    function sendTestSms() {
        const phone = $('#testPhone').val();
        const message = $('#testMessage').val();
        
        if (!phone || !message) {
            alert('Please enter both phone number and message');
            return;
        }
        
        $.ajax({
            url: '{{ route("settings.test-sms") }}',
            method: 'POST',
            data: {
                phone: phone,
                message: message,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert('SMS sent successfully!');
                    $('#testSmsModal').modal('hide');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error sending SMS');
            }
        });
    }
</script>
@endpush