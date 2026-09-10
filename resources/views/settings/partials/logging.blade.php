<div class="row">
    <div class="col-md-12">
        <form action="{{ route('settings.update-logging') }}" method="POST">
            @csrf
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Logging & Monitoring Settings</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Log Retention (days)</label>
                                <input type="number" name="log_retention_days" class="form-control" 
                                       value="{{ $logRetentionDays ?? getSetting('log_retention_days', 30) }}" min="1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Log Level</label>
                                <select name="log_level" class="form-control">
                                    <option value="debug" {{ (getSetting('log_level', 'debug') == 'debug') ? 'selected' : '' }}>Debug</option>
                                    <option value="info" {{ (getSetting('log_level', 'debug') == 'info') ? 'selected' : '' }}>Info</option>
                                    <option value="notice" {{ (getSetting('log_level', 'debug') == 'notice') ? 'selected' : '' }}>Notice</option>
                                    <option value="warning" {{ (getSetting('log_level', 'debug') == 'warning') ? 'selected' : '' }}>Warning</option>
                                    <option value="error" {{ (getSetting('log_level', 'debug') == 'error') ? 'selected' : '' }}>Error</option>
                                    <option value="critical" {{ (getSetting('log_level', 'debug') == 'critical') ? 'selected' : '' }}>Critical</option>
                                    <option value="alert" {{ (getSetting('log_level', 'debug') == 'alert') ? 'selected' : '' }}>Alert</option>
                                    <option value="emergency" {{ (getSetting('log_level', 'debug') == 'emergency') ? 'selected' : '' }}>Emergency</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Log Channel</label>
                                <select name="log_channel" class="form-control">
                                    <option value="single" {{ (getSetting('log_channel', 'daily') == 'single') ? 'selected' : '' }}>Single</option>
                                    <option value="daily" {{ (getSetting('log_channel', 'daily') == 'daily') ? 'selected' : '' }}>Daily</option>
                                    <option value="stack" {{ (getSetting('log_channel', 'daily') == 'stack') ? 'selected' : '' }}>Stack</option>
                                    <option value="syslog" {{ (getSetting('log_channel', 'daily') == 'syslog') ? 'selected' : '' }}>Syslog</option>
                                    <option value="errorlog" {{ (getSetting('log_channel', 'daily') == 'errorlog') ? 'selected' : '' }}>Error Log</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="enable_audit_log" class="form-check-input" id="auditLog" 
                                           value="1" {{ (getSetting('enable_audit_log', 'true') == 'true') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="auditLog">Enable Audit Log</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="enable_activity_log" class="form-check-input" id="activityLog" 
                                           value="1" {{ (getSetting('enable_activity_log', 'true') == 'true') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="activityLog">Enable Activity Log</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="monitoring_enabled" class="form-check-input" id="monitoring" 
                                           value="1" {{ (getSetting('monitoring_enabled', 'false') == 'true') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="monitoring">Enable Monitoring</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Monitoring Email</label>
                                <input type="email" name="monitoring_email" class="form-control" 
                                       value="{{ $monitoringEmail ?? getSetting('monitoring_email', 'admin@school.edu.ng') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Alert Threshold (%)</label>
                                <input type="number" name="monitoring_alert_threshold" class="form-control" 
                                       value="{{ $monitoringAlertThreshold ?? getSetting('monitoring_alert_threshold', 90) }}" min="1" max="100">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Save Logging Settings
                </button>
            </div>
        </form>
    </div>
</div>