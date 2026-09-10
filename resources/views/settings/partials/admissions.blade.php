<div class="row">
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-user-graduate me-2"></i>Admissions Settings</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update-admissions') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Applications Open</label>
                                <select name="applications_open" class="form-control">
                                    <option value="true" {{ (getSetting('applications_open', 'true') == 'true') ? 'selected' : '' }}>Open</option>
                                    <option value="false" {{ (getSetting('applications_open', 'true') == 'false') ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Application Fee (NGN)</label>
                                <input type="number" name="application_fee" class="form-control" step="0.01" min="0"
                                       value="{{ old('application_fee', getSetting('application_fee', 5000)) }}">
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h6>Registration Fees By Class</h6>
                    <p class="text-muted">Set the registration fee for each class. These values will be stored as a JSON map and used when admitting students.</p>
                    <div class="row">
                        @if(!empty($classes) && $classes->count())
                            @php
                                $regFees = json_decode(getSetting('registration_fees', '{}'), true) ?? [];
                            @endphp
                            @foreach($classes as $class)
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label>{{ $class->full_class_name ?? $class->name }}</label>
                                        <input type="number" name="registration_fees[{{ $class->id }}]" class="form-control" step="0.01" min="0"
                                               value="{{ old('registration_fees.' . $class->id, isset($regFees[$class->id]) ? $regFees[$class->id] : '') }}">
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-12">
                                <div class="alert alert-warning">No classes found. Please create classes first.</div>
                            </div>
                        @endif
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Admissions Settings
                        </button>
                    </div>
                </form>
                <form action="{{ route('settings.update-admissions') }}" method="POST" enctype="multipart/form-data" class="mt-4">
                    @csrf
                    <hr>
                    <h6>Admission Letter Settings</h6>
                    <p class="text-muted">Customize the admission letter content, signatory, and signature image for generated letters.</p>

                    @php
                        $currentSignature = getSetting('admission_letter_signatory_signature', '');
                    @endphp

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label>Admission Letter Content</label>
                                <textarea name="admission_letter_content" class="form-control" rows="8" placeholder="Enter the admission letter body. Use placeholders like [STUDENT_NAME], [CLASS_NAME], [TERM_LABEL], [SCHOOL_NAME], [SCHOOL_PHONE], [SCHOOL_EMAIL], [ADMISSION_DATE], [ADMISSION_NUMBER].">{{ old('admission_letter_content', getSetting('admission_letter_content', '')) }}</textarea>
                                <small class="text-muted">Use placeholders to insert dynamic data into the letter body.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Admission Letter Signatory Name</label>
                                <input type="text" name="admission_letter_signatory_name" class="form-control"
                                       value="{{ old('admission_letter_signatory_name', getSetting('admission_letter_signatory_name', 'MR. MAXWELL EPHRAIM HALILU')) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Signatory Title</label>
                                <input type="text" name="admission_letter_signatory_title" class="form-control"
                                       value="{{ old('admission_letter_signatory_title', getSetting('admission_letter_signatory_title', 'Principal')) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label>Principal Signature Image</label>
                                <input type="file" name="admission_letter_signature" class="form-control" accept="image/*">
                            </div>
                            @if($currentSignature)
                                <div class="mb-3">
                                    <label>Current Signature</label>
                                    <div>
                                        <img src="{{ filter_var($currentSignature, FILTER_VALIDATE_URL) ? $currentSignature : asset($currentSignature) }}" alt="Current signature" style="max-width: 200px; max-height: 80px; display: block;">
                                    </div>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="remove_admission_letter_signature" id="remove_signature" value="true">
                                    <label class="form-check-label" for="remove_signature">Remove current signature</label>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Admission Letter Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
