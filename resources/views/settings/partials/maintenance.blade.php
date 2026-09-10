<div class="row">
    <div class="col-md-12">
        <form action="{{ route('settings.update-maintenance') }}" method="POST">
            @csrf
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-tools me-2"></i>Maintenance Settings</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    <strong>Warning:</strong> Enabling maintenance mode will make the site inaccessible to users.
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" name="maintenance_mode" class="form-check-input" id="maintenanceMode" 
                                           value="1" {{ (getSetting('maintenance_mode', 'false') == 'true') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="maintenanceMode">Enable Maintenance Mode</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label>Maintenance Message</label>
                                <textarea name="maintenance_message" class="form-control" rows="3">{{ $maintenanceMessage ?? getSetting('maintenance_message', 'We are currently performing scheduled maintenance. Please check back later.') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Allowed IPs (comma separated)</label>
                                <input type="text" name="maintenance_allowed_ips" class="form-control" 
                                       value="{{ $maintenanceAllowedIps ?? getSetting('maintenance_allowed_ips', '127.0.0.1, 192.168.1.1') }}" placeholder="127.0.0.1, 192.168.1.1">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Retry After (seconds)</label>
                                <input type="number" name="maintenance_retry_after" class="form-control" 
                                       value="{{ $maintenanceRetryAfter ?? getSetting('maintenance_retry_after', 60) }}" min="10">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Bypass Secret Key</label>
                                <input type="text" name="maintenance_secret" class="form-control" 
                                       value="{{ $maintenanceSecret ?? getSetting('maintenance_secret') }}" placeholder="Enter secret key to bypass maintenance">
                                <small class="text-muted">Add ?secret=YOUR_KEY to URL to bypass maintenance</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Save Maintenance Settings
                </button>
            </div>
        </form>
    </div>
</div>