<div class="row">
    <div class="col-md-12">
        <form action="{{ route('settings.update-academic') }}" method="POST">
            @csrf
            <div class="card mb-4">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Academic Settings</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Academic Year <span class="text-danger">*</span></label>
                                <input type="text" name="academic_year" class="form-control" 
                                       value="{{ $academicYear ?? getSetting('academic_year', date('Y') . '/' . (date('Y') + 1)) }}" 
                                       placeholder="e.g., 2024/2025" required>
                                <small class="text-muted">Format: YYYY/YYYY</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Current Term <span class="text-danger">*</span></label>
                                <select name="term" class="form-control" required>
                                    <option value="1st Term" {{ ($term ?? getSetting('term', '1st Term')) == '1st Term' ? 'selected' : '' }}>1st Term</option>
                                    <option value="2nd Term" {{ ($term ?? getSetting('term', '1st Term')) == '2nd Term' ? 'selected' : '' }}>2nd Term</option>
                                    <option value="3rd Term" {{ ($term ?? getSetting('term', '1st Term')) == '3rd Term' ? 'selected' : '' }}>3rd Term</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="card mb-4 border-primary">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">Promotion Settings</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="mb-3 form-check">
                                                <input type="hidden" name="promotion_enabled" value="false">
                                                <input type="checkbox" name="promotion_enabled" class="form-check-input" id="promotion_enabled" value="true" {{ ($promotionEnabled ?? getSetting('promotion_enabled', 'true')) == 'true' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="promotion_enabled">Enable Promotion</label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label>Min Average Score</label>
                                                <input type="number" name="promotion_min_average_score" class="form-control" min="0" max="100" step="0.1"
                                                       value="{{ old('promotion_min_average_score', $promotionMinAverageScore ?? getSetting('promotion_min_average_score', 50)) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label>Max Absenteeism (days)</label>
                                                <input type="number" name="promotion_max_absenteeism" class="form-control" min="0" step="1"
                                                       value="{{ old('promotion_max_absenteeism', $promotionMaxAbsenteeism ?? getSetting('promotion_max_absenteeism', 10)) }}">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label>Min Passing Subjects</label>
                                                <input type="number" name="promotion_min_passing_subjects" class="form-control" min="0" step="1"
                                                       value="{{ old('promotion_min_passing_subjects', $promotionMinPassingSubjects ?? getSetting('promotion_min_passing_subjects', 7)) }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label>Core Subjects</label>
                                                <input type="text" name="promotion_core_subjects" class="form-control"
                                                       value="{{ old('promotion_core_subjects', $promotionCoreSubjects ?? getSetting('promotion_core_subjects', 'English, Mathematics')) }}"
                                                       placeholder="English, Mathematics">
                                                <small class="text-muted">Comma-separated list of core subjects that must not be failed.</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="mb-3 form-check">
                                                <input type="hidden" name="promotion_auto_promote" value="false">
                                                <input type="checkbox" name="promotion_auto_promote" class="form-check-input" id="promotion_auto_promote" value="true" {{ ($promotionAutoPromote ?? getSetting('promotion_auto_promote', 'true')) == 'true' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="promotion_auto_promote">Auto Promote Eligible Students</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Term Start Date</label>
                                <input type="date" name="term_start_date" class="form-control" 
                                       value="{{ $termStartDate ?? getSetting('term_start_date') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Term End Date</label>
                                <input type="date" name="term_end_date" class="form-control" 
                                       value="{{ $termEndDate ?? getSetting('term_end_date') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Next Term Start Date</label>
                                <input type="date" name="next_term_start" class="form-control" 
                                       value="{{ $nextTermStart ?? getSetting('next_term_start') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label>Vacation Period</label>
                                <div class="row">
                                    <div class="col-6">
                                        <input type="date" name="vacation_start" class="form-control" 
                                               value="{{ $vacationStart ?? getSetting('vacation_start') }}" 
                                               placeholder="Start">
                                    </div>
                                    <div class="col-6">
                                        <input type="date" name="vacation_end" class="form-control" 
                                               value="{{ $vacationEnd ?? getSetting('vacation_end') }}" 
                                               placeholder="End">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-save"></i> Save Academic Settings
                </button>
            </div>
        </form>
    </div>
</div>