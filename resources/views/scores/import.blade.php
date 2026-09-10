@extends('layouts.app')

@section('title', 'Import Scores')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('scores.index') }}">Scores Entry</a></li>
    <li class="breadcrumb-item active">Import Scores</li>
@endsection

@section('styles')
<style>
    .import-card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    .import-card:hover {
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        padding: 40px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .upload-area:hover {
        border-color: #667eea;
        background: #f8f9ff;
    }
    .upload-area.dragover {
        border-color: #667eea;
        background: #f0f3ff;
    }
    .upload-area i {
        font-size: 48px;
        color: #cbd5e1;
        margin-bottom: 15px;
    }
    .upload-area h6 {
        color: #64748b;
        margin-bottom: 5px;
    }
    .upload-area p {
        color: #94a3b8;
        font-size: 14px;
    }
    .file-info {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 10px 15px;
        margin-top: 10px;
        display: none;
    }
    .file-info .file-name {
        font-weight: 500;
        color: #667eea;
    }
    .file-info .file-size {
        color: #6c757d;
        font-size: 12px;
    }
    .instruction-step {
        display: flex;
        align-items: flex-start;
        gap: 15px;
        padding: 10px 0;
        border-bottom: 1px solid #f1f1f1;
    }
    .instruction-step:last-child {
        border-bottom: none;
    }
    .instruction-step .step-number {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: #667eea;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
        flex-shrink: 0;
    }
    .instruction-step .step-content {
        flex: 1;
    }
    .instruction-step .step-content h6 {
        margin-bottom: 2px;
        font-size: 14px;
    }
    .instruction-step .step-content p {
        margin-bottom: 0;
        font-size: 13px;
        color: #6c757d;
    }
    .selection-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Selection Form -->
            <div class="selection-card">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="classSelect" class="form-label fw-bold">Select Class <span class="text-danger">*</span></label>
                        <select id="classSelect" class="form-select form-select-sm" onchange="updateTemplateInfo()">
                            <option value="">-- Select Class --</option>
                            @foreach($classes as $class)
                                <option value="{{ $class->id }}" {{ ($classId ?? '') == $class->id ? 'selected' : '' }}>
                                    {{ $class->full_class_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="termSelect" class="form-label fw-bold">Select Term <span class="text-danger">*</span></label>
                        <select id="termSelect" class="form-select form-select-sm">
                            <option value="">-- Select Term --</option>
                            <option value="First Term" {{ ($term ?? '') == 'First Term' ? 'selected' : '' }}>First Term</option>
                            <option value="Second Term" {{ ($term ?? '') == 'Second Term' ? 'selected' : '' }}>Second Term</option>
                            <option value="Third Term" {{ ($term ?? '') == 'Third Term' ? 'selected' : '' }}>Third Term</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="subjectSelect" class="form-label fw-bold">Select Subject <span class="text-danger">*</span></label>
                        <select id="subjectSelect" class="form-select form-select-sm">
                            <option value="">-- Select Subject --</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}" {{ ($subjectId ?? '') == $subject->id ? 'selected' : '' }}>
                                    {{ $subject->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Instructions Card -->
                <div class="col-md-5">
                    <div class="card import-card">
                        <div class="card-header">
                            <h6><i class="fas fa-info-circle me-2 text-primary"></i>Import Instructions</h6>
                        </div>
                        <div class="card-body">
                            <div class="instruction-step">
                                <div class="step-number">1</div>
                                <div class="step-content">
                                    <h6>Download Template</h6>
                                    <p>Download the CSV template file with the correct format</p>
                                </div>
                            </div>
                            <div class="instruction-step">
                                <div class="step-number">2</div>
                                <div class="step-content">
                                    <h6>Fill in Scores</h6>
                                    <p>Enter student admission numbers and scores for each subject</p>
                                </div>
                            </div>
                            <div class="instruction-step">
                                <div class="step-number">3</div>
                                <div class="step-content">
                                    <h6>Upload File</h6>
                                    <p>Upload the completed file (CSV or Excel format)</p>
                                </div>
                            </div>
                            <div class="instruction-step">
                                <div class="step-number">4</div>
                                <div class="step-content">
                                    <h6>Review & Import</h6>
                                    <p>Review the data and confirm the import</p>
                                </div>
                            </div>

                            <hr>

                            <div class="d-grid gap-2">
                                <a href="#" id="downloadTemplateBtn" class="btn btn-primary" onclick="downloadTemplate()">
                                    <i class="fas fa-download me-1"></i> Download Template
                                </a>
                            </div>

                            <div class="alert alert-info mt-3 mb-0">
                                <i class="fas fa-info-circle me-1"></i>
                                <small>Template includes columns for: Admission Number, Student Name, CA1, CA2, CA3, Exam Score</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upload Card -->
                <div class="col-md-7">
                    <div class="card import-card">
                        <div class="card-header">
                            <h6><i class="fas fa-upload me-2 text-success"></i>Upload Scores</h6>
                        </div>
                        <div class="card-body">
                            <form id="importForm" action="{{ route('scores.process-import') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="class_id" id="importClassId">
                                <input type="hidden" name="subject_id" id="importSubjectId">
                                <input type="hidden" name="term" id="importTerm">

                                <!-- Upload Area -->
                                <div class="upload-area" id="uploadArea">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <h6>Drag & Drop or Click to Upload</h6>
                                    <p>Supported formats: CSV, XLSX, XLS</p>
                                    <input type="file" name="file" id="fileInput" class="d-none" accept=".csv,.xlsx,.xls" required>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('fileInput').click()">
                                        <i class="fas fa-folder-open"></i> Browse Files
                                    </button>
                                </div>

                                <!-- File Info -->
                                <div class="file-info" id="fileInfo">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-file-alt text-primary me-2"></i>
                                            <span class="file-name" id="fileName">file.csv</span>
                                            <span class="file-size" id="fileSize">(0 KB)</span>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-danger" onclick="clearFile()">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="skipFirstRow" name="skip_first_row" checked>
                                            <label class="form-check-label" for="skipFirstRow">Skip first row (headers)</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="overwriteExisting" name="overwrite_existing">
                                            <label class="form-check-label" for="overwriteExisting">Overwrite existing scores</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid gap-2 mt-3">
                                    <button type="submit" class="btn btn-success" id="importBtn" disabled>
                                        <i class="fas fa-upload me-1"></i> Import Scores
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Template Preview -->
            <div class="card mt-4">
                <div class="card-header">
                    <h6><i class="fas fa-table me-2"></i>Template Preview</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Admission Number</th>
                                    <th>Student Name</th>
                                    <th>CA 1 (30)</th>
                                    <th>CA 2 (30)</th>
                                    <th>CA 3 (30)</th>
                                    <th>Exam Score (70)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><code>STU-2024-001</code></td>
                                    <td>Alice Student</td>
                                    <td>25</td>
                                    <td>28</td>
                                    <td>27</td>
                                    <td>65</td>
                                </tr>
                                <tr>
                                    <td><code>STU-2024-002</code></td>
                                    <td>Bob Student</td>
                                    <td>22</td>
                                    <td>24</td>
                                    <td>26</td>
                                    <td>58</td>
                                </tr>
                                <tr>
                                    <td><code>STU-2024-003</code></td>
                                    <td>Carol Student</td>
                                    <td>20</td>
                                    <td>22</td>
                                    <td>25</td>
                                    <td>55</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="alert alert-info mt-2 mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        <small>Each row represents a student. Enter scores for CA1, CA2, CA3 (max 30 each) and Exam Score (max 70).</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // File upload handling
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('fileInput');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const importBtn = document.getElementById('importBtn');

    // Drag and drop
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFile(files[0]);
        }
    });

    uploadArea.addEventListener('click', function() {
        fileInput.click();
    });

    fileInput.addEventListener('change', function() {
        if (this.files.length > 0) {
            handleFile(this.files[0]);
        }
    });

    // Handle file
    function handleFile(file) {
        const validTypes = [
            'text/csv',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];
        
        const fileExt = '.' + file.name.split('.').pop().toLowerCase();
        const validExts = ['.csv', '.xlsx', '.xls'];

        if (!validTypes.includes(file.type) && !validExts.includes(fileExt)) {
            alert('Please upload a valid CSV or Excel file');
            return;
        }

        // Show file info
        fileName.textContent = file.name;
        fileSize.textContent = '(' + (file.size / 1024).toFixed(1) + ' KB)';
        fileInfo.style.display = 'block';
        uploadArea.style.display = 'none';
        
        // Enable import button
        importBtn.disabled = false;
        
        // Update form
        document.getElementById('importClassId').value = document.getElementById('classSelect').value;
        document.getElementById('importSubjectId').value = document.getElementById('subjectSelect').value;
        document.getElementById('importTerm').value = document.getElementById('termSelect').value;
    }

    // Clear file
    function clearFile() {
        fileInput.value = '';
        fileInfo.style.display = 'none';
        uploadArea.style.display = 'block';
        importBtn.disabled = true;
    }

    // Download template
    function downloadTemplate() {
        const classId = document.getElementById('classSelect').value;
        const subjectId = document.getElementById('subjectSelect').value;
        const term = document.getElementById('termSelect').value;

        if (!classId || !subjectId || !term) {
            alert('Please select class, term, and subject first');
            return;
        }

        window.location.href = '{{ route("scores.download-template") }}?class_id=' + classId + '&subject_id=' + subjectId + '&term=' + term;
    }

    // Update template info
    function updateTemplateInfo() {
        // Enable/disable download button based on selections
        const classId = document.getElementById('classSelect').value;
        const subjectId = document.getElementById('subjectSelect').value;
        const term = document.getElementById('termSelect').value;
        
        const btn = document.getElementById('downloadTemplateBtn');
        if (classId && subjectId && term) {
            btn.style.opacity = '1';
            btn.style.pointerEvents = 'auto';
        } else {
            btn.style.opacity = '0.5';
            btn.style.pointerEvents = 'none';
        }
    }

    // Validate form before submit
    document.getElementById('importForm').addEventListener('submit', function(e) {
        const classId = document.getElementById('classSelect').value;
        const subjectId = document.getElementById('subjectSelect').value;
        const term = document.getElementById('termSelect').value;

        if (!classId || !subjectId || !term) {
            e.preventDefault();
            alert('Please select class, term, and subject');
            return;
        }

        if (!fileInput.files || fileInput.files.length === 0) {
            e.preventDefault();
            alert('Please select a file to import');
            return;
        }

        // Update hidden fields
        document.getElementById('importClassId').value = classId;
        document.getElementById('importSubjectId').value = subjectId;
        document.getElementById('importTerm').value = term;

        if (!confirm('Are you sure you want to import scores? This may overwrite existing data.')) {
            e.preventDefault();
        }
    });

    // Initialize - check if selections are pre-filled
    document.addEventListener('DOMContentLoaded', function() {
        updateTemplateInfo();
        
        // Auto-enable download if all fields are selected
        ['classSelect', 'subjectSelect', 'termSelect'].forEach(id => {
            document.getElementById(id).addEventListener('change', updateTemplateInfo);
        });
    });
</script>
@endpush
@endsection