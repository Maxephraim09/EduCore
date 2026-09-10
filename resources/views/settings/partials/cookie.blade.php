<div class="row">
    <div class="col-md-12">
        <form action="{{ route('settings.update-cookie') }}" method="POST">
            @csrf
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-cookie-bite me-2"></i>Cookie & GDPR Settings</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="cookie_consent_enabled" class="form-check-input" id="cookieConsent" 
                                           value="1" {{ (getSetting('cookie_consent_enabled', 'true') == 'true') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="cookieConsent">Enable Cookie Consent Banner</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="gdpr_enabled" class="form-check-input" id="gdpr" 
                                           value="1" {{ (getSetting('gdpr_enabled', 'true') == 'true') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="gdpr">Enable GDPR Compliance</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label>Cookie Consent Text</label>
                                <textarea name="cookie_consent_text" class="form-control" rows="3">{{ $cookieConsentText ?? getSetting('cookie_consent_text', 'This site uses cookies to enhance your experience. By continuing to use this site, you consent to our use of cookies.') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Cookie Policy URL</label>
                                <input type="text" name="cookie_policy_url" class="form-control" 
                                       value="{{ $cookiePolicyUrl ?? getSetting('cookie_policy_url', '/cookie-policy') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Privacy Policy URL</label>
                                <input type="text" name="privacy_policy_url" class="form-control" 
                                       value="{{ $privacyPolicyUrl ?? getSetting('privacy_policy_url', '/privacy-policy') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Terms of Use URL</label>
                                <input type="text" name="terms_url" class="form-control" 
                                       value="{{ $termsUrl ?? getSetting('terms_url', '/terms-of-use') }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label>Data Retention Notice</label>
                                <textarea name="data_retention_notice" class="form-control" rows="2">{{ $dataRetentionNotice ?? getSetting('data_retention_notice', 'Your data is retained for 5 years as per our privacy policy.') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Save Cookie & GDPR Settings
                </button>
            </div>
        </form>
    </div>
</div>