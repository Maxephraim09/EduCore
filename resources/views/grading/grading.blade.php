@extends('layouts.app')

@section('title', 'Grading Settings - ' . $questionPaper->title)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('question-papers.index') }}">Question Papers</a></li>
    <li class="breadcrumb-item"><a href="{{ route('question-papers.show', $questionPaper->id) }}">{{ $questionPaper->title }}</a></li>
    <li class="breadcrumb-item active">Grading Settings</li>
@endsection

@section('styles')
<style>
    .grading-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        transition: all 0.3s ease;
        background: #fff;
    }
    .grading-card:hover {
        border-color: #667eea;
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.08);
    }
    .grading-card .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        border-radius: 12px 12px 0 0 !important;
        padding: 15px 20px;
    }
    .grading-card .card-body {
        padding: 20px;
    }
    .grading-card .form-label {
        font-weight: 600;
        font-size: 13px;
        color: #374151;
        margin-bottom: 4px;
    }
    .grading-card .form-control,
    .grading-card .form-select {
        border-radius: 8px;
        border: 1.5px solid #e5e7eb;
        padding: 8px 12px;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    .grading-card .form-control:focus,
    .grading-card .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }
    .grading-card .input-group-text {
        background: #f8f9fa;
        border: 1.5px solid #e5e7eb;
        font-weight: 600;
    }
    .total-display {
        font-size: 28px;
        font-weight: 700;
        padding: 10px 15px;
        border-radius: 8px;
        text-align: center;
    }
    .total-display.success {
        background: #d1fae5;
        color: #065f46;
    }
    .total-display.warning {
        background: #fef3c7;
        color: #92400e;
    }
    .total-display.danger {
        background: #fee2e2;
        color: #991b1b;
    }
    .grade-scale-table th {
        background: #f8f9fa;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6b7280;
    }
    .grade-scale-table td {
        vertical-align: middle;
    }
    .preview-badge {
        font-size: 14px;
        padding: 6px 16px;
        border-radius: 20px;
        font-weight: 600;
    }
    .input-with-icon {
        position: relative;
    }
    .input-with-icon .form-control {
        padding-right: 40px;
    }
    .input-with-icon .input-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        font-size: 14px;
    }
    .btn-group-gap {
        gap: 8px;
    }
    .btn-group-gap .btn {
        border-radius: 8px !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Header Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-sliders-h me-2"></i>Grading Settings
                        </h5>
                        <div>
                            <span class="badge bg-light text-dark me-2">{{ $questionPaper->subject->name ?? 'N/A' }}</span>
                            <span class="badge bg-light text-dark">{{ $questionPaper->class->full_class_name ?? 'N/A' }}</span>
                            <span class="badge bg-light text-dark">{{ $questionPaper->term }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div>
                                <strong>Configure Grading System</strong>
                                <p class="mb-0">Set up the score distribution for this subject. The total marks must equal 100. 
                                Grades will be automatically calculated based on the grade scale.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Updated form action to use the correct route -->
                    <form action="{{ route('grading.store-question-paper', $questionPaper->id) }}" method="POST" id="gradingForm">
                        @csrf
                        @method('POST')

                        <!-- Score Distribution Section -->
                        <div class="grading-card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0 fw-bold">
                                    <i class="fas fa-star me-2" style="color: #667eea;"></i>Score Distribution
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <p class="text-muted small">Configure how the 100 marks are distributed across different components.</p>
                                    </div>
                                </div>

                                <div class="row g-4">
                                    <!-- CA 1 -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="ca1_max" class="form-label">
                                                <i class="fas fa-pencil-alt me-1" style="color: #667eea;"></i>CA 1
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-with-icon">
                                                <input type="number" name="ca1_max" id="ca1_max" 
                                                       class="form-control @error('ca1_max') is-invalid @enderror"
                                                       value="{{ old('ca1_max', $grading->ca1_max ?? 10) }}" 
                                                       min="0" max="30" required onchange="updateTotal()">
                                                <span class="input-icon">/ 30</span>
                                            </div>
                                            @error('ca1_max')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Max 30 marks</small>
                                        </div>
                                    </div>

                                    <!-- CA 2 -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="ca2_max" class="form-label">
                                                <i class="fas fa-pencil-alt me-1" style="color: #667eea;"></i>CA 2
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-with-icon">
                                                <input type="number" name="ca2_max" id="ca2_max" 
                                                       class="form-control @error('ca2_max') is-invalid @enderror"
                                                       value="{{ old('ca2_max', $grading->ca2_max ?? 10) }}" 
                                                       min="0" max="30" required onchange="updateTotal()">
                                                <span class="input-icon">/ 30</span>
                                            </div>
                                            @error('ca2_max')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Max 30 marks</small>
                                        </div>
                                    </div>

                                    <!-- CA 3 -->
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="ca3_max" class="form-label">
                                                <i class="fas fa-pencil-alt me-1" style="color: #667eea;"></i>CA 3
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-with-icon">
                                                <input type="number" name="ca3_max" id="ca3_max" 
                                                       class="form-control @error('ca3_max') is-invalid @enderror"
                                                       value="{{ old('ca3_max', $grading->ca3_max ?? 10) }}" 
                                                       min="0" max="30" required onchange="updateTotal()">
                                                <span class="input-icon">/ 30</span>
                                            </div>
                                            @error('ca3_max')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Max 30 marks</small>
                                        </div>
                                    </div>

                                    <!-- Exam -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="exam_max" class="form-label">
                                                <i class="fas fa-check-circle me-1" style="color: #10b981;"></i>Exam
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-with-icon">
                                                <input type="number" name="exam_max" id="exam_max" 
                                                       class="form-control @error('exam_max') is-invalid @enderror"
                                                       value="{{ old('exam_max', $grading->exam_max ?? 60) }}" 
                                                       min="0" max="100" required onchange="updateTotal()">
                                                <span class="input-icon">/ 100</span>
                                            </div>
                                            @error('exam_max')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Max 100 marks</small>
                                        </div>
                                    </div>

                                    <!-- Practical -->
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="practical_max" class="form-label">
                                                <i class="fas fa-flask me-1" style="color: #f59e0b;"></i>Practical
                                            </label>
                                            <div class="input-with-icon">
                                                <input type="number" name="practical_max" id="practical_max" 
                                                       class="form-control @error('practical_max') is-invalid @enderror"
                                                       value="{{ old('practical_max', $grading->practical_max ?? 10) }}" 
                                                       min="0" max="30" onchange="updateTotal()" disabled>
                                                <span class="input-icon">/ 30</span>
                                            </div>
                                            @error('practical_max')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-check mt-2">
                                                <input type="checkbox" name="has_practical" id="has_practical" 
                                                       class="form-check-input" value="1" 
                                                       {{ old('has_practical', $grading->has_practical ?? true) ? 'checked' : '' }}
                                                       onchange="togglePractical()">
                                                <label for="has_practical" class="form-check-label small">
                                                    <i class="fas fa-check-circle text-success"></i> Has Practical
                                                </label>
                                            </div>
                                            <small class="text-muted">Optional component</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Total Display -->
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="passing_marks" class="form-label">
                                                <i class="fas fa-flag-checkered me-1" style="color: #ef4444;"></i>Passing Marks
                                                <span class="text-danger">*</span>
                                            </label>
                                            <div class="input-with-icon">
                                                <input type="number" name="passing_marks" id="passing_marks" 
                                                       class="form-control @error('passing_marks') is-invalid @enderror"
                                                       value="{{ old('passing_marks', $grading->passing_marks ?? 40) }}" 
                                                       min="0" max="100" required>
                                                <span class="input-icon">/ 100</span>
                                            </div>
                                            @error('passing_marks')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="text-muted">Minimum marks required to pass</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">
                                                <i class="fas fa-calculator me-1" style="color: #8b5cf6;"></i>Total Marks
                                            </label>
                                            <div class="total-display success" id="totalDisplay">
                                                <span id="totalValue">100</span> / 100
                                            </div>
                                            <input type="hidden" name="total_max" id="total_max" value="100">
                                            <small class="text-muted" id="totalMessage">
                                                <i class="fas fa-check-circle text-success"></i> Total equals 100 marks ✓
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Grade Scale Section -->
                        <div class="grading-card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0 fw-bold">
                                    <i class="fas fa-table me-2" style="color: #667eea;"></i>Grade Scale
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <p class="text-muted small">
                                            Grades are automatically calculated based on the total score using the configured grade scale.
                                        </p>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered grade-scale-table">
                                        <thead>
                                            <tr>
                                                <th width="15%">Grade</th>
                                                <th width="20%">Score Range</th>
                                                <th width="30%">Remark</th>
                                                <th width="20%">Color</th>
                                                <th width="15%">Preview</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $gradeScales = \App\Models\GradeScale::where('is_active', true)
                                                    ->orderBy('min_score', 'desc')
                                                    ->get();
                                            @endphp
                                            @forelse($gradeScales as $grade)
                                                <tr>
                                                    <td class="text-center fw-bold">{{ $grade->grade }}</td>
                                                    <td class="text-center">
                                                        {{ $grade->min_score }} - {{ $grade->max_score }}
                                                    </td>
                                                    <td>{{ $grade->remark }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $grade->color ?? 'secondary' }}">
                                                            {{ $grade->color ?? 'secondary' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="preview-badge bg-{{ $grade->color ?? 'secondary' }} text-white">
                                                            {{ $grade->grade }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-3">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        No grade scales configured. Please contact administrator.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <div class="alert alert-secondary mt-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <small>
                                            <strong>How it works:</strong> When you enter results, the system will:
                                            <ul class="mb-0 mt-1">
                                                <li>Calculate total score from all components</li>
                                                <li>Automatically assign the appropriate grade based on the score range</li>
                                                <li>Display the grade with the configured color</li>
                                            </ul>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Summary Card -->
                        <div class="grading-card mb-4">
                            <div class="card-header">
                                <h6 class="mb-0 fw-bold">
                                    <i class="fas fa-clipboard-list me-2" style="color: #667eea;"></i>Summary
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="text-center p-3 border rounded">
                                            <div class="text-muted small">Total CA Marks</div>
                                            <div class="h4 mb-0" id="caTotal">30</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 border rounded">
                                            <div class="text-muted small">Exam Marks</div>
                                            <div class="h4 mb-0" id="examTotal">60</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 border rounded">
                                            <div class="text-muted small">Practical Marks</div>
                                            <div class="h4 mb-0" id="practicalTotal">10</div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 border rounded bg-success bg-opacity-10">
                                            <div class="text-muted small">Grand Total</div>
                                            <div class="h4 mb-0 text-success" id="grandTotal">100</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-group d-flex btn-group-gap flex-wrap">
                            <button type="submit" class="btn btn-primary btn-lg px-5" id="submitBtn">
                                <i class="fas fa-save me-2"></i> Save Grading Settings
                            </button>
                            <a href="{{ route('question-papers.show', $questionPaper->id) }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-times me-2"></i> Cancel
                            </a>
                            <button type="button" class="btn btn-warning btn-lg" onclick="resetToDefault()">
                                <i class="fas fa-undo me-2"></i> Reset to Default
                            </button>
                            <a href="{{ route('grading.index') }}" class="btn btn-info btn-lg">
                                <i class="fas fa-list me-2"></i> All Gradings
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Default values
    const defaultValues = {
        ca1: 10,
        ca2: 10,
        ca3: 10,
        exam: 60,
        practical: 10,
        passing: 40
    };

    function updateTotal() {
        let ca1 = parseInt(document.getElementById('ca1_max').value) || 0;
        let ca2 = parseInt(document.getElementById('ca2_max').value) || 0;
        let ca3 = parseInt(document.getElementById('ca3_max').value) || 0;
        let exam = parseInt(document.getElementById('exam_max').value) || 0;
        let practical = parseInt(document.getElementById('practical_max').value) || 0;
        
        // Validate inputs
        validateInput('ca1_max', 30);
        validateInput('ca2_max', 30);
        validateInput('ca3_max', 30);
        validateInput('exam_max', 100);
        validateInput('practical_max', 30);

        // Calculate totals
        let caTotal = ca1 + ca2 + ca3;
        let total = caTotal + exam;
        
        // Add practical if enabled
        const hasPractical = document.getElementById('has_practical');
        const practicalInput = document.getElementById('practical_max');
        
        if (hasPractical.checked) {
            total += practical;
            practicalInput.disabled = false;
        } else {
            practicalInput.disabled = true;
            practicalInput.value = 0;
            practical = 0;
        }
        
        // Update summary
        document.getElementById('caTotal').textContent = caTotal;
        document.getElementById('examTotal').textContent = exam;
        document.getElementById('practicalTotal').textContent = practical;
        document.getElementById('grandTotal').textContent = total;
        
        // Update display
        const totalDisplay = document.getElementById('totalDisplay');
        const totalValue = document.getElementById('totalValue');
        const totalMessage = document.getElementById('totalMessage');
        const submitBtn = document.getElementById('submitBtn');
        
        totalValue.textContent = total;
        
        // Update styling based on total
        totalDisplay.className = 'total-display';
        if (total === 100) {
            totalDisplay.classList.add('success');
            totalMessage.innerHTML = '<i class="fas fa-check-circle text-success"></i> Total equals 100 marks ✓';
            totalMessage.className = 'text-success';
            submitBtn.disabled = false;
        } else if (total < 100) {
            totalDisplay.classList.add('warning');
            totalMessage.innerHTML = '<i class="fas fa-exclamation-triangle text-warning"></i> Total is less than 100 marks (' + total + '/100)';
            totalMessage.className = 'text-warning';
            submitBtn.disabled = false;
        } else {
            totalDisplay.classList.add('danger');
            totalMessage.innerHTML = '<i class="fas fa-exclamation-circle text-danger"></i> Total exceeds 100 marks (' + total + '/100)';
            totalMessage.className = 'text-danger';
            submitBtn.disabled = true;
        }
        
        document.getElementById('total_max').value = total;
    }

    function validateInput(id, max) {
        const input = document.getElementById(id);
        const value = parseInt(input.value) || 0;
        if (value > max) {
            input.value = max;
        }
        if (value < 0) {
            input.value = 0;
        }
    }

    function togglePractical() {
        const checked = document.getElementById('has_practical').checked;
        const practicalInput = document.getElementById('practical_max');
        practicalInput.disabled = !checked;
        if (!checked) {
            practicalInput.value = 0;
        }
        updateTotal();
    }

    function resetToDefault() {
        if (confirm('Reset all values to default? (CA1=10, CA2=10, CA3=10, Exam=60, Practical=10, Passing=40)')) {
            document.getElementById('ca1_max').value = defaultValues.ca1;
            document.getElementById('ca2_max').value = defaultValues.ca2;
            document.getElementById('ca3_max').value = defaultValues.ca3;
            document.getElementById('exam_max').value = defaultValues.exam;
            document.getElementById('practical_max').value = defaultValues.practical;
            document.getElementById('passing_marks').value = defaultValues.passing;
            document.getElementById('has_practical').checked = true;
            togglePractical();
            updateTotal();
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        togglePractical();
        updateTotal();

        // Auto-update on input change
        const inputs = document.querySelectorAll('#ca1_max, #ca2_max, #ca3_max, #exam_max, #practical_max, #passing_marks');
        inputs.forEach(input => {
            input.addEventListener('input', updateTotal);
            input.addEventListener('change', updateTotal);
        });
    });

    // Form validation before submit
    document.getElementById('gradingForm').addEventListener('submit', function(e) {
        const total = parseInt(document.getElementById('total_max').value) || 0;
        if (total !== 100) {
            e.preventDefault();
            alert('Total marks must equal 100. Current total: ' + total);
            return false;
        }
    });
</script>
@endpush
@endsection