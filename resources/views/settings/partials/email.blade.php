<div class="row">
    <div class="col-md-12">
        <form action="{{ route('settings.update-email') }}" method="POST">
            @csrf
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> 
                <strong>Email Configuration:</strong> Configure SMTP settings for sending emails.
            </div>

            <div class="mb-3">
                <label>Mail Driver *</label>
                <select name="mail_driver" class="form-control" required>
                    <option value="smtp" {{ $mailDriver == 'smtp' ? 'selected' : '' }}>SMTP</option>
                    <option value="sendmail" {{ $mailDriver == 'sendmail' ? 'selected' : '' }}>Sendmail</option>
                    <option value="mailgun" {{ $mailDriver == 'mailgun' ? 'selected' : '' }}>Mailgun</option>
                    <option value="ses" {{ $mailDriver == 'ses' ? 'selected' : '' }}>Amazon SES</option>
                    <option value="postmark" {{ $mailDriver == 'postmark' ? 'selected' : '' }}>Postmark</option>
                </select>
            </div>

            <div id="smtpSettings" style="{{ $mailDriver == 'smtp' ? '' : 'display:none' }}">
                <div class="mb-3">
                    <label>SMTP Host *</label>
                    <input type="text" name="mail_host" class="form-control" value="{{ $mailHost }}" placeholder="smtp.gmail.com">
                </div>

                <div class="mb-3">
                    <label>SMTP Port *</label>
                    <input type="number" name="mail_port" class="form-control" value="{{ $mailPort }}" placeholder="587">
                </div>

                <div class="mb-3">
                    <label>Encryption *</label>
                    <select name="mail_encryption" class="form-control">
                        <option value="tls" {{ $mailEncryption == 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ $mailEncryption == 'ssl' ? 'selected' : '' }}>SSL</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label>SMTP Username</label>
                <input type="text" name="mail_username" class="form-control" value="{{ $mailUsername }}" placeholder="your-email@gmail.com">
            </div>

            <div class="mb-3">
                <label>SMTP Password</label>
                <input type="password" name="mail_password" class="form-control" value="{{ $mailPassword }}">
                <div class="form-check mt-2">
                    <input type="checkbox" class="form-check-input" id="showEmailPassword">
                    <label class="form-check-label" for="showEmailPassword">Show Password</label>
                </div>
            </div>

            <div class="mb-3">
                <label>From Email Address *</label>
                <input type="email" name="mail_from_address" class="form-control" value="{{ $mailFromAddress }}" required>
            </div>

            <div class="mb-3">
                <label>From Name *</label>
                <input type="text" name="mail_from_name" class="form-control" value="{{ $mailFromName }}" required>
            </div>

            <div class="mb-3">
                <div class="alert {{ $mailFromAddress ? 'alert-success' : 'alert-warning' }}">
                    @if($mailFromAddress)
                        <i class="fas fa-check-circle"></i> Email is configured
                    @else
                        <i class="fas fa-exclamation-triangle"></i> Email is not fully configured
                    @endif
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Save Email Settings
            </button>
            <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#testEmailModal">
                <i class="fas fa-vial"></i> Test Email
            </button>
        </form>
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
                <div class="mb-3">
                    <label>Email Address</label>
                    <input type="email" id="testEmailAddress" class="form-control" placeholder="test@example.com">
                </div>
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
    $('select[name="mail_driver"]').change(function() {
        if ($(this).val() == 'smtp') {
            $('#smtpSettings').show();
        } else {
            $('#smtpSettings').hide();
        }
    });
    
    $('#showEmailPassword').change(function() {
        $('input[name="mail_password"]').attr('type', this.checked ? 'text' : 'password');
    });
    
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