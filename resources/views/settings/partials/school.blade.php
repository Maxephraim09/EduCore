<div class="row">
    <div class="col-md-12">
        <form action="{{ route('settings.update-school') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>School Information</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>School Name <span class="text-danger">*</span></label>
                                <input type="text" name="school_name" class="form-control" value="{{ $schoolName ?? getSetting('school_name', 'Excellence International School') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>School Tagline</label>
                                <input type="text" name="school_tagline" class="form-control" value="{{ $schoolTagline ?? getSetting('school_tagline', 'Excellence in Education') }}">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label>School Address <span class="text-danger">*</span></label>
                                <textarea name="school_address" class="form-control" rows="3" required>{{ $schoolAddress ?? getSetting('school_address') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="school_phone" class="form-control" value="{{ $schoolPhone ?? getSetting('school_phone') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Email Address <span class="text-danger">*</span></label>
                                <input type="email" name="school_email" class="form-control" value="{{ $schoolEmail ?? getSetting('school_email') }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Website URL</label>
                                <input type="text" name="school_website" class="form-control" value="{{ $schoolWebsite ?? getSetting('school_website') }}" placeholder="www.school.edu.ng">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logo & Branding -->
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-image me-2"></i>Logo & Branding</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>School Logo</label>
                                <input type="file" name="school_logo" class="form-control" accept="image/*">
                                <small class="text-muted">Recommended: 200x200px (PNG, JPG, GIF)</small>
                                @if($schoolLogo ?? getSetting('school_logo_url'))
                                    <div class="mt-3">
                                        <img src="{{ asset($schoolLogo ?? getSetting('school_logo_url')) }}" alt="Logo" style="max-height: 100px; border: 1px solid #e2e8f0; padding: 5px; border-radius: 8px;">
                                        <div class="mt-1">
                                            <small class="text-muted">Current logo</small>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="removeLogo()">
                                                <i class="fas fa-times"></i> Remove
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Favicon</label>
                                <input type="file" name="school_icon" class="form-control" accept="image/*">
                                <small class="text-muted">Recommended: 32x32px (ICO, PNG)</small>
                                @if($schoolIcon ?? getSetting('school_icon'))
                                    <div class="mt-3">
                                        <img src="{{ asset($schoolIcon ?? getSetting('school_icon')) }}" alt="Favicon" style="max-height: 32px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Save School Settings
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function removeLogo() {
        if (confirm('Are you sure you want to remove the logo?')) {
            var form = document.querySelector('form');
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'remove_logo';
            input.value = 'true';
            form.appendChild(input);
            form.submit();
        }
    }
</script>
@endpush