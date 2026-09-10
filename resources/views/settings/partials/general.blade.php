<div class="row">
    <div class="col-md-12">
        <form action="{{ route('settings.update-general') }}" method="POST">
            @csrf
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-globe me-2"></i>General Settings</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Application Name <span class="text-danger">*</span></label>
                                <input type="text" name="app_name" class="form-control" 
                                       value="{{ $appName ?? getSetting('app_name', 'Financial Management System') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Application Environment</label>
                                <select name="app_environment" class="form-control">
                                    <option value="local" {{ ($appEnvironment ?? getSetting('app_environment', 'production')) == 'local' ? 'selected' : '' }}>Local</option>
                                    <option value="development" {{ ($appEnvironment ?? getSetting('app_environment', 'production')) == 'development' ? 'selected' : '' }}>Development</option>
                                    <option value="production" {{ ($appEnvironment ?? getSetting('app_environment', 'production')) == 'production' ? 'selected' : '' }}>Production</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Application URL</label>
                                <input type="url" name="app_url" class="form-control" 
                                       value="{{ $appUrl ?? getSetting('app_url', url('/')) }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="app_debug" class="form-check-input" id="appDebug" 
                                           value="1" {{ (getSetting('app_debug', 'false') == 'true') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="appDebug">Enable Debug Mode</label>
                                    <small class="d-block text-warning">Enable only during development. Disable in production.</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Email Primary Color</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background-color: {{ $emailPrimaryColor ?? getSetting('email_primary_color', '#1e3c72') }}; width: 40px;"></span>
                                    <input type="color" name="email_primary_color" class="form-control form-control-color" 
                                           value="{{ $emailPrimaryColor ?? getSetting('email_primary_color', '#1e3c72') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Email Secondary Color</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background-color: {{ $emailSecondaryColor ?? getSetting('email_secondary_color', '#2a5298') }}; width: 40px;"></span>
                                    <input type="color" name="email_secondary_color" class="form-control form-control-color" 
                                           value="{{ $emailSecondaryColor ?? getSetting('email_secondary_color', '#2a5298') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>System Status</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> 
                                <strong>Laravel Version:</strong> {{ app()->version() }}<br>
                                <strong>PHP Version:</strong> {{ phpversion() }}<br>
                                <strong>Database:</strong> {{ config('database.default') }}<br>
                                <strong>Environment:</strong> {{ app()->environment() }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i>
                                <strong>Timezone:</strong> {{ config('app.timezone') }}<br>
                                <strong>Current Date/Time:</strong> {{ now()->format('Y-m-d H:i:s') }}<br>
                                <strong>Locale:</strong> {{ app()->getLocale() }}<br>
                                <strong>Session Driver:</strong> {{ config('session.driver') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Save General Settings
                </button>
                <a href="{{ route('settings.clear-cache') }}" class="btn btn-warning btn-lg" onclick="return confirm('Are you sure you want to clear the application cache? This may temporarily slow down the system.')">
                    <i class="fas fa-trash-alt"></i> Clear Application Cache
                </a>
            </div>
        </form>
    </div>
</div>