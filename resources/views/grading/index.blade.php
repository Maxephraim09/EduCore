@extends('layouts.app')

@section('title', 'Grading Settings')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
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
        font-weight: 600;
    }
    .component-icon {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 14px;
        margin-right: 8px;
    }
    .component-icon.ca { background: #e0e7ff; color: #4f46e5; }
    .component-icon.exam { background: #d1fae5; color: #059669; }
    .component-icon.practical { background: #fef3c7; color: #d97706; }
    .component-icon.passing { background: #fee2e2; color: #dc2626; }
    .stat-box {
        background: #f8fafc;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        border: 1px solid #e9ecef;
        transition: all 0.3s ease;
    }
    .stat-box:hover {
        border-color: #667eea;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .stat-box .stat-value {
        font-size: 24px;
        font-weight: 700;
        color: #1e293b;
    }
    .stat-box .stat-label {
        font-size: 12px;
        color: #64748b;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stat-box .stat-icon {
        font-size: 24px;
        margin-bottom: 6px;
        display: block;
    }
    .stat-box.success .stat-value { color: #059669; }
    .stat-box.warning .stat-value { color: #d97706; }
    .stat-box.primary .stat-value { color: #4f46e5; }
    .stat-box.danger .stat-value { color: #dc2626; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Alert Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <!-- Header Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-sliders-h me-2"></i>Global Grading Settings
                        </h5>
                        <div>
                            <span class="badge bg-light text-dark">
                                <i class="fas fa-globe me-1"></i> System-wide Configuration
                            </span>
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
                                <strong>Global Grading System</strong>
                                <p class="mb-0">These settings apply to all subjects across the system. 
                                The total marks must equal 100. Grades are automatically calculated based on the configured grade scale.</p>
                            </div>
                        </div>
                    </div>

                    <form action="{{ route('grading.update') }}" method="POST" id="gradingForm">
                        @csrf
                        @method('PUT')

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
                                                <span class="component-icon ca"><i class="fas fa-pencil-alt"></i></span>
                                                CA 1
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
                                                <span class="component-icon ca"><i class="fas fa-pencil-alt"></i></span>
                                                CA 2
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
                                                <span class="component-icon ca"><i class="fas fa-pencil-alt"></i></span>
                                                CA 3
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
                                                <span class="component-icon exam"><i class="fas fa-check-circle"></i></span>
                                                Exam
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
                                                <span class="component-icon practical"><i class="fas fa-flask"></i></span>
                                                Practical
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

                                <!-- Passing Marks & Total Display -->
                                <div class="row mt-4">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="passing_marks" class="form-label">
                                                <span class="component-icon passing"><i class="fas fa-flag-checkered"></i></span>
                                                Passing Marks
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
                                                <span class="component-icon" style="background: #ede9fe; color: #7c3aed;">
                                                    <i class="fas fa-calculator"></i>
                                                </span>
                                                Total Marks
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

                        <!-- Statistics Summary -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-3">
                                <div class="stat-box primary">
                                    <span class="stat-icon"><i class="fas fa-pencil-alt"></i></span>
                                    <div class="stat-value" id="statCA">30</div>
                                    <div class="stat-label">Total CA Marks</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-box success">
                                    <span class="stat-icon"><i class="fas fa-check-circle"></i></span>
                                    <div class="stat-value" id="statExam">60</div>
                                    <div class="stat-label">Exam Marks</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-box warning">
                                    <span class="stat-icon"><i class="fas fa-flask"></i></span>
                                    <div class="stat-value" id="statPractical">10</div>
                                    <div class="stat-label">Practical Marks</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="stat-box success" style="border-color: #10b981; background: #ecfdf5;">
                                    <span class="stat-icon"><i class="fas fa-star"></i></span>
                                    <div class="stat-value text-success" id="statTotal">100</div>
                                    <div class="stat-label">Grand Total</div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="form-group d-flex gap-3 flex-wrap">
                            <button type="submit" class="btn btn-primary btn-lg px-5" id="submitBtn">
                                <i class="fas fa-save me-2"></i> Update Grading Settings
                            </button>
                            <button type="button" class="btn btn-warning btn-lg px-4" onclick="resetToDefault()">
                                <i class="fas fa-undo me-2"></i> Reset to Default
                            </button>
                            <a href="{{ route('grading.scale') }}" class="btn btn-info btn-lg px-4">
                                <i class="fas fa-table me-2"></i> Manage Grade Scale
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
        
        // Update statistics
        document.getElementById('statCA').textContent = caTotal;
        document.getElementById('statExam').textContent = exam;
        document.getElementById('statPractical').textContent = practical;
        document.getElementById('statTotal').textContent = total;
        
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