<div class="row">
    <div class="col-md-12">
        <form action="{{ route('settings.update-localization') }}" method="POST">
            @csrf
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-globe-americas me-2"></i>Currency & Localization</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Currency <span class="text-danger">*</span></label>
                                <select name="currency" class="form-control" required>
                                    <option value="NGN" {{ ($currency ?? getSetting('currency', 'NGN')) == 'NGN' ? 'selected' : '' }}>Nigerian Naira (NGN)</option>
                                    <option value="USD" {{ ($currency ?? getSetting('currency', 'NGN')) == 'USD' ? 'selected' : '' }}>US Dollar (USD)</option>
                                    <option value="GBP" {{ ($currency ?? getSetting('currency', 'NGN')) == 'GBP' ? 'selected' : '' }}>British Pound (GBP)</option>
                                    <option value="EUR" {{ ($currency ?? getSetting('currency', 'NGN')) == 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                                    <option value="GHS" {{ ($currency ?? getSetting('currency', 'NGN')) == 'GHS' ? 'selected' : '' }}>Ghana Cedi (GHS)</option>
                                    <option value="KES" {{ ($currency ?? getSetting('currency', 'NGN')) == 'KES' ? 'selected' : '' }}>Kenya Shilling (KES)</option>
                                    <option value="ZAR" {{ ($currency ?? getSetting('currency', 'NGN')) == 'ZAR' ? 'selected' : '' }}>South African Rand (ZAR)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Currency Symbol <span class="text-danger">*</span></label>
                                <input type="text" name="currency_symbol" class="form-control" 
                                       value="{{ $currencySymbol ?? getSetting('currency_symbol', '₦') }}" maxlength="5" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Time Zone <span class="text-danger">*</span></label>
                                <select name="timezone" class="form-control" required>
                                    <option value="Africa/Lagos" {{ ($timezone ?? getSetting('timezone', 'Africa/Lagos')) == 'Africa/Lagos' ? 'selected' : '' }}>Africa/Lagos (WAT)</option>
                                    <option value="Africa/Johannesburg" {{ ($timezone ?? getSetting('timezone', 'Africa/Lagos')) == 'Africa/Johannesburg' ? 'selected' : '' }}>Africa/Johannesburg (SAST)</option>
                                    <option value="Africa/Nairobi" {{ ($timezone ?? getSetting('timezone', 'Africa/Lagos')) == 'Africa/Nairobi' ? 'selected' : '' }}>Africa/Nairobi (EAT)</option>
                                    <option value="UTC" {{ ($timezone ?? getSetting('timezone', 'Africa/Lagos')) == 'UTC' ? 'selected' : '' }}>UTC</option>
                                    <option value="America/New_York" {{ ($timezone ?? getSetting('timezone', 'Africa/Lagos')) == 'America/New_York' ? 'selected' : '' }}>America/New York (EST)</option>
                                    <option value="Europe/London" {{ ($timezone ?? getSetting('timezone', 'Africa/Lagos')) == 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Date Format</label>
                                <select name="date_format" class="form-control">
                                    <option value="Y-m-d" {{ ($dateFormat ?? getSetting('date_format', 'Y-m-d')) == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                    <option value="d/m/Y" {{ ($dateFormat ?? getSetting('date_format', 'Y-m-d')) == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                    <option value="m/d/Y" {{ ($dateFormat ?? getSetting('date_format', 'Y-m-d')) == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                    <option value="d M Y" {{ ($dateFormat ?? getSetting('date_format', 'Y-m-d')) == 'd M Y' ? 'selected' : '' }}>DD Mon YYYY</option>
                                    <option value="l, F j, Y" {{ ($dateFormat ?? getSetting('date_format', 'Y-m-d')) == 'l, F j, Y' ? 'selected' : '' }}>Full Date Format</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Time Format</label>
                                <select name="time_format" class="form-control">
                                    <option value="h:i A" {{ ($timeFormat ?? getSetting('time_format', 'h:i A')) == 'h:i A' ? 'selected' : '' }}>12-hour (h:i A)</option>
                                    <option value="H:i" {{ ($timeFormat ?? getSetting('time_format', 'h:i A')) == 'H:i' ? 'selected' : '' }}>24-hour (H:i)</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Save Localization Settings
                </button>
            </div>
        </form>
    </div>
</div>