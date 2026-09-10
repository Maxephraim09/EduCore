<div class="row">
    <div class="col-md-12">
        <form action="{{ route('settings.update-security') }}" method="POST">
            @csrf
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Security & Access Settings</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Session Timeout (minutes)</label>
                                <input type="number" name="session_timeout" class="form-control" 
                                       value="{{ $sessionTimeout ?? getSetting('session_timeout', 60) }}" min="5">
                                <small class="text-muted">Auto-logout after inactivity</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Max Login Attempts</label>
                                <input type="number" name="max_login_attempts" class="form-control" 
                                       value="{{ $maxLoginAttempts ?? getSetting('max_login_attempts', 5) }}" min="1">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Minimum Password Length</label>
                                <input type="number" name="password_min_length" class="form-control" 
                                       value="{{ $passwordMinLength ?? getSetting('password_min_length', 8) }}" min="6">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="require_2fa" class="form-check-input" id="require2fa" 
                                           value="1" {{ (getSetting('require_2fa', 'false') == 'true') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="require2fa">Require 2FA for Admin</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Session Driver</label>
                                <select name="session_driver" class="form-control">
                                    <option value="database" {{ (getSetting('session_driver', 'database') == 'database') ? 'selected' : '' }}>Database</option>
                                    <option value="file" {{ (getSetting('session_driver', 'database') == 'file') ? 'selected' : '' }}>File</option>
                                    <option value="cookie" {{ (getSetting('session_driver', 'database') == 'cookie') ? 'selected' : '' }}>Cookie</option>
                                    <option value="redis" {{ (getSetting('session_driver', 'database') == 'redis') ? 'selected' : '' }}>Redis</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Cache Driver</label>
                                <select name="cache_driver" class="form-control">
                                    <option value="database" {{ (getSetting('cache_driver', 'database') == 'database') ? 'selected' : '' }}>Database</option>
                                    <option value="file" {{ (getSetting('cache_driver', 'database') == 'file') ? 'selected' : '' }}>File</option>
                                    <option value="redis" {{ (getSetting('cache_driver', 'database') == 'redis') ? 'selected' : '' }}>Redis</option>
                                    <option value="memcached" {{ (getSetting('cache_driver', 'database') == 'memcached') ? 'selected' : '' }}>Memcached</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Save Security Settings
                </button>
            </div>
        </form>
    </div>
</div>