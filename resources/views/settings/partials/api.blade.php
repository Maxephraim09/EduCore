<div class="row">
    <div class="col-md-12">
        <form action="{{ route('settings.update-api') }}" method="POST">
            @csrf
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-code me-2"></i>API Settings</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="api_enabled" class="form-check-input" id="apiEnabled" 
                                           value="1" {{ (getSetting('api_enabled', 'true') == 'true') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="apiEnabled">Enable API Access</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="api_sandbox_mode" class="form-check-input" id="apiSandbox" 
                                           value="1" {{ (getSetting('api_sandbox_mode', 'false') == 'true') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="apiSandbox">Sandbox Mode</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Rate Limit (per minute)</label>
                                <input type="number" name="api_rate_limit" class="form-control" 
                                       value="{{ $apiRateLimit ?? getSetting('api_rate_limit', 60) }}" min="1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Throttle Attempts</label>
                                <input type="number" name="api_throttle_attempts" class="form-control" 
                                       value="{{ $apiThrottleAttempts ?? getSetting('api_throttle_attempts', 100) }}" min="1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Throttle Decay (minutes)</label>
                                <input type="number" name="api_throttle_decay" class="form-control" 
                                       value="{{ $apiThrottleDecay ?? getSetting('api_throttle_decay', 1) }}" min="1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="api_debug" class="form-check-input" id="apiDebug" 
                                           value="1" {{ (getSetting('api_debug', 'false') == 'true') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="apiDebug">API Debug Mode</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>API Version</label>
                                <input type="text" name="api_version" class="form-control" 
                                       value="{{ $apiVersion ?? getSetting('api_version', 'v1') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>API Documentation URL</label>
                                <input type="text" name="api_docs_url" class="form-control" 
                                       value="{{ $apiDocsUrl ?? getSetting('api_docs_url', '/api/docs') }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Save API Settings
                </button>
            </div>
        </form>
    </div>
</div>