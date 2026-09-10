<div class="row">
    <div class="col-md-12">
        <!-- SMS Settings -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-sms me-2"></i>SMS Configuration</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update-sms') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>SMS Provider <span class="text-danger">*</span></label>
                                <select name="sms_provider" class="form-control" required>
                                    <option value="twilio" {{ ($smsProvider ?? getSetting('sms_provider', 'twilio')) == 'twilio' ? 'selected' : '' }}>Twilio</option>
                                    <option value="africastalking" {{ ($smsProvider ?? getSetting('sms_provider', 'twilio')) == 'africastalking' ? 'selected' : '' }}>Africa's Talking</option>
                                    <option value="messagebird" {{ ($smsProvider ?? getSetting('sms_provider', 'twilio')) == 'messagebird' ? 'selected' : '' }}>MessageBird</option>
                                    <option value="nexmo" {{ ($smsProvider ?? getSetting('sms_provider', 'twilio')) == 'nexmo' ? 'selected' : '' }}>Vonage (Nexmo)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Sender ID <span class="text-danger">*</span></label>
                                <input type="text" name="sms_sender_id" class="form-control" 
                                       value="{{ $smsSenderId ?? getSetting('sms_sender_id', 'FMSystem') }}" maxlength="11" required>
                                <small class="text-muted">Max 11 characters</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>API Key / SID <span class="text-danger">*</span></label>
                                <input type="text" name="sms_api_key" class="form-control" 
                                       value="{{ $smsApiKey ?? getSetting('sms_api_key') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>API Secret / Auth Token <span class="text-danger">*</span></label>
                                <input type="password" name="sms_api_secret" class="form-control" 
                                       value="{{ $smsApiSecret ?? getSetting('sms_api_secret') }}" required>
                                <div class="form-check mt-2">
                                    <input type="checkbox" class="form-check-input" id="showSmsSecret">
                                    <label class="form-check-label" for="showSmsSecret">Show Secret</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="sms_enabled" class="form-check-input" id="smsEnabled" 
                                               value="1" {{ (getSetting('sms_enabled', 'true') == 'true') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="smsEnabled">Enable SMS</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="sms_for_fees" class="form-check-input" id="smsForFees" 
                                               value="1" {{ (getSetting('sms_for_fees', 'true') == 'true') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="smsForFees">Fee Payments</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="sms_for_results" class="form-check-input" id="smsForResults" 
                                               value="1" {{ (getSetting('sms_for_results', 'true') == 'true') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="smsForResults">Results</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input type="checkbox" name="sms_for_attendance" class="form-check-input" id="smsForAttendance" 
                                               value="1" {{ (getSetting('sms_for_attendance', 'true') == 'true') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="smsForAttendance">Attendance</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save SMS Settings
                        </button>
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#testSmsModal">
                            <i class="fas fa-vial"></i> Test SMS
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Email Settings -->
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-envelope me-2"></i>Email Configuration</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update-email') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Mail Driver <span class="text-danger">*</span></label>
                                <select name="mail_driver" class="form-control" required>
                                    <option value="smtp" {{ ($mailDriver ?? getSetting('mail_driver', 'smtp')) == 'smtp' ? 'selected' : '' }}>SMTP</option>
                                    <option value="sendmail" {{ ($mailDriver ?? getSetting('mail_driver', 'smtp')) == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                                    <option value="mailgun" {{ ($mailDriver ?? getSetting('mail_driver', 'smtp')) == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                                    <option value="ses" {{ ($mailDriver ?? getSetting('mail_driver', 'smtp')) == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                                    <option value="postmark" {{ ($mailDriver ?? getSetting('mail_driver', 'smtp')) == 'postmark' ? 'selected' : '' }}>Postmark</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>SMTP Host</label>
                                <input type="text" name="mail_host" class="form-control" 
                                       value="{{ $mailHost ?? getSetting('mail_host', 'smtp.gmail.com') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>SMTP Port</label>
                                <input type="number" name="mail_port" class="form-control" 
                                       value="{{ $mailPort ?? getSetting('mail_port', 587) }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>SMTP Username</label>
                                <input type="text" name="mail_username" class="form-control" 
                                       value="{{ $mailUsername ?? getSetting('mail_username') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>SMTP Password</label>
                                <input type="password" name="mail_password" class="form-control" 
                                       value="{{ $mailPassword ?? getSetting('mail_password') }}">
                                <div class="form-check mt-2">
                                    <input type="checkbox" class="form-check-input" id="showEmailPassword">
                                    <label class="form-check-label" for="showEmailPassword">Show Password</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Encryption</label>
                                <select name="mail_encryption" class="form-control">
                                    <option value="tls" {{ ($mailEncryption ?? getSetting('mail_encryption', 'tls')) == 'tls' ? 'selected' : '' }}>TLS</option>
                                    <option value="ssl" {{ ($mailEncryption ?? getSetting('mail_encryption', 'tls')) == 'ssl' ? 'selected' : '' }}>SSL</option>
                                    <option value="" {{ ($mailEncryption ?? getSetting('mail_encryption', 'tls')) == '' ? 'selected' : '' }}>None</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>From Email <span class="text-danger">*</span></label>
                                <input type="email" name="mail_from_address" class="form-control" 
                                       value="{{ $mailFromAddress ?? getSetting('mail_from_address') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>From Name <span class="text-danger">*</span></label>
                                <input type="text" name="mail_from_name" class="form-control" 
                                       value="{{ $mailFromName ?? getSetting('mail_from_name', 'Financial Management System') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Reply-To Email</label>
                                <input type="email" name="mail_reply_to" class="form-control" 
                                       value="{{ $mailReplyTo ?? getSetting('mail_reply_to') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Email Footer Text</label>
                                <input type="text" name="email_footer_text" class="form-control" 
                                       value="{{ $emailFooterText ?? getSetting('email_footer_text', 'This is an automated message. Please do not reply.') }}">
                            </div>
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Email Settings
                        </button>
                        <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#testEmailModal">
                            <i class="fas fa-vial"></i> Test Email
                        </button>
                    </div>
                </form>
            </div>
        </div>
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
                <form id="testSmsForm">
                    @csrf
                    <div class="mb-3">
                        <label>Phone Number</label>
                        <input type="text" id="testPhone" class="form-control" placeholder="e.g., +2348012345678">
                    </div>
                    <div class="mb-3">
                        <label>Message</label>
                        <textarea id="testMessage" class="form-control" rows="3">This is a test message from your Financial Management System.</textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="sendTestSms()">Send Test SMS</button>
            </div>
        </div>
    </div>
</div>

<!-- Test Email Modal -->
<div class="modal fade" id="testEmailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Test Email Configuration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="testEmailForm">
                    @csrf
                    <div class="mb-3">
                        <label>Email Address</label>
                        <input type="email" id="testEmailAddress" class="form-control" placeholder="test@example.com">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="sendTestEmail()">Send Test Email</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Show/hide secret keys
    $('#showSmsSecret').change(function() {
        $('input[name="sms_api_secret"]').attr('type', this.checked ? 'text' : 'password');
    });

    $('#showEmailPassword').change(function() {
        $('input[name="mail_password"]').attr('type', this.checked ? 'text' : 'password');
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

    function sendTestEmail() {
        const email = $('#testEmailAddress').val();
        
        if (!email) {
            alert('Please enter an email address');
            return;
        }
        
        $.ajax({
            url: '{{ route("settings.test-email") }}',
            method: 'POST',
            data: {
                email: email,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert('Email sent successfully! Check your inbox.');
                    $('#testEmailModal').modal('hide');
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function() {
                alert('Error sending email');
            }
        });
    }
</script>
@endpush