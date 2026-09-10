<div class="row">
    <div class="col-md-12">
        <form action="{{ route('settings.update-theme') }}" method="POST">
            @csrf
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-palette me-2"></i>Theme & Appearance</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Primary Color</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background-color: {{ $themePrimaryColor ?? getSetting('theme_primary_color', '#667eea') }}; width: 40px;"></span>
                                    <input type="color" name="theme_primary_color" class="form-control form-control-color" 
                                           value="{{ $themePrimaryColor ?? getSetting('theme_primary_color', '#667eea') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Secondary Color</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background-color: {{ $themeSecondaryColor ?? getSetting('theme_secondary_color', '#764ba2') }}; width: 40px;"></span>
                                    <input type="color" name="theme_secondary_color" class="form-control form-control-color" 
                                           value="{{ $themeSecondaryColor ?? getSetting('theme_secondary_color', '#764ba2') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Sidebar Color</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background-color: {{ $themeSidebarColor ?? getSetting('theme_sidebar_color', '#1e3c72') }}; width: 40px;"></span>
                                    <input type="color" name="theme_sidebar_color" class="form-control form-control-color" 
                                           value="{{ $themeSidebarColor ?? getSetting('theme_sidebar_color', '#1e3c72') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Header Color</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="background-color: {{ $themeHeaderColor ?? getSetting('theme_header_color', '#2a5298') }}; width: 40px;"></span>
                                    <input type="color" name="theme_header_color" class="form-control form-control-color" 
                                           value="{{ $themeHeaderColor ?? getSetting('theme_header_color', '#2a5298') }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="theme_dark_mode" class="form-check-input" id="darkMode" 
                                           value="1" {{ (getSetting('theme_dark_mode', 'false') == 'true') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="darkMode">Enable Dark Mode</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <div class="form-check mt-4">
                                    <input type="checkbox" name="theme_show_breadcrumb" class="form-check-input" id="showBreadcrumb" 
                                           value="1" {{ (getSetting('theme_show_breadcrumb', 'true') == 'true') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="showBreadcrumb">Show Breadcrumb</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label>Footer Text</label>
                                <input type="text" name="theme_footer_text" class="form-control" 
                                       value="{{ $themeFooterText ?? getSetting('theme_footer_text', '© 2024 Financial Management System. All rights reserved.') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Custom CSS</label>
                                <textarea name="theme_custom_css" class="form-control" rows="4">{{ $themeCustomCss ?? getSetting('theme_custom_css') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Custom JavaScript</label>
                                <textarea name="theme_custom_js" class="form-control" rows="4">{{ $themeCustomJs ?? getSetting('theme_custom_js') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Save Theme Settings
                </button>
            </div>
        </form>
    </div>
</div>