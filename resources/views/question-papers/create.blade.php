@extends('layouts.app')

@section('title', 'Manage Question Paper')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('question-papers.index') }}">Question Papers</a></li>
    <li class="breadcrumb-item active">Manage Questions</li>
@endsection

@section('styles')
<style>
    .info-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 20px 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.25);
    }
    .info-card .label {
        opacity: 0.8;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 600;
    }
    .info-card .value {
        font-size: 16px;
        font-weight: 600;
        margin-top: 2px;
    }
    
    .question-card {
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        background: #fff;
        transition: all 0.3s;
        position: relative;
    }
    .question-card:hover {
        border-color: #667eea;
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.1);
    }
    .question-number {
        background: #667eea;
        color: white;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 16px;
        flex-shrink: 0;
    }
    .option-input {
        margin-bottom: 10px;
    }
    .option-input .input-group-text {
        background: #f8f9fa;
        font-weight: bold;
        min-width: 30px;
        justify-content: center;
    }
    .upload-area {
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        padding: 40px 20px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s;
    }
    .upload-area:hover {
        border-color: #667eea;
        background: #f8f9ff;
    }
    .upload-area.dragover {
        border-color: #667eea;
        background: #f0f3ff;
    }
    .btn-add-question {
        border: 2px dashed #667eea;
        color: #667eea;
        background: transparent;
        border-radius: 10px;
        padding: 15px;
        width: 100%;
        transition: all 0.3s;
        font-weight: 500;
    }
    .btn-add-question:hover {
        background: #667eea;
        color: white;
    }
    .required-star {
        color: #dc3545;
    }
    .form-label {
        font-weight: 500;
    }
    .question-stats {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 12px 16px;
    }
    .question-stats .stat-item {
        text-align: center;
        padding: 4px 12px;
        border-right: 1px solid #e5e7eb;
    }
    .question-stats .stat-item:last-child {
        border-right: none;
    }
    .question-stats .stat-item .number {
        font-size: 20px;
        font-weight: 700;
        color: #1b1b18;
    }
    .question-stats .stat-item .label {
        font-size: 11px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .selection-card {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #e5e7eb;
    }
    .selection-card .form-label {
        font-weight: 600;
        font-size: 13px;
        color: #374151;
        margin-bottom: 4px;
    }
    .teacher-display {
        background: white;
        border-radius: 8px;
        padding: 10px 14px;
        border: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .teacher-display i {
        color: #667eea;
        font-size: 18px;
    }
    .teacher-display .teacher-name {
        font-weight: 600;
        color: #1b1b18;
    }
    .teacher-display .teacher-label {
        font-size: 12px;
        color: #6b7280;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Selection Card -->
            <div class="selection-card">
                <form action="{{ route('question-papers.manage-questions') }}" method="GET" id="selectionForm">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label for="academic_year" class="form-label">Academic Year <span class="required-star">*</span></label>
                            <select name="academic_year" id="academic_year" class="form-select" required onchange="document.getElementById('selectionForm').submit()">
                                <option value="">-- Select Year --</option>
                                @foreach($academicYears ?? [] as $year)
                                    <option value="{{ $year }}" {{ ($selectedAcademicYear ?? '') == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="term" class="form-label">Term <span class="required-star">*</span></label>
                            <select name="term" id="term" class="form-select" required onchange="document.getElementById('selectionForm').submit()">
                                <option value="">-- Select Term --</option>
                                <option value="First Term" {{ ($selectedTerm ?? '') == 'First Term' ? 'selected' : '' }}>First Term</option>
                                <option value="Second Term" {{ ($selectedTerm ?? '') == 'Second Term' ? 'selected' : '' }}>Second Term</option>
                                <option value="Third Term" {{ ($selectedTerm ?? '') == 'Third Term' ? 'selected' : '' }}>Third Term</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="class_id" class="form-label">Class <span class="required-star">*</span></label>
                            <select name="class_id" id="class_id" class="form-select" required onchange="document.getElementById('selectionForm').submit()">
                                <option value="">-- Select Class --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ ($selectedClassId ?? '') == $class->id ? 'selected' : '' }}>
                                        {{ $class->full_class_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="subject_id" class="form-label">Subject <span class="required-star">*</span></label>
                            <select name="subject_id" id="subject_id" class="form-select" required onchange="document.getElementById('selectionForm').submit()">
                                <option value="">-- Select Subject --</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ ($selectedSubjectId ?? '') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>

            @if(isset($selectedAcademicYear) && isset($selectedTerm) && isset($selectedClassId) && isset($selectedSubjectId))
                <!-- Information Card -->
                <div class="info-card">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="label">Academic Year</div>
                                    <div class="value">{{ $selectedAcademicYear }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="label">Term</div>
                                    <div class="value">{{ $selectedTerm }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="label">Class</div>
                                    <div class="value">{{ $selectedClass->full_class_name ?? 'N/A' }}</div>
                                </div>
                                <div class="col-md-3">
                                    <div class="label">Subject</div>
                                    <div class="value">{{ $selectedSubject->name ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-md-end">
                            <span class="badge bg-info ms-2">{{ $questions->count() ?? 0 }} Questions</span>
                            <span class="badge bg-success ms-2">{{ $totalMarks ?? 0 }} Marks</span>
                        </div>
                    </div>
                    
                    @if(isset($selectedSubject) && $selectedSubject->teacher)
                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="teacher-display">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                    <div>
                                        <div class="teacher-name">{{ $selectedSubject->teacher->full_name }}</div>
                                        <div class="teacher-label">Subject Teacher</div>
                                    </div>
                                    <span class="badge bg-success ms-auto">Auto-Assigned</span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="card">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Manage Questions</h5>
                        <div>
                            <span class="badge bg-light text-dark me-2">{{ $selectedSubject->name ?? '' }}</span>
                            <span class="badge bg-light text-dark">{{ $selectedClass->full_class_name ?? '' }}</span>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('question-papers.store-questions') }}" method="POST" id="questionForm" enctype="multipart/form-data">
                            @csrf

                            <!-- Hidden fields -->
                            <input type="hidden" name="academic_year" value="{{ $selectedAcademicYear }}">
                            <input type="hidden" name="term" value="{{ $selectedTerm }}">
                            <input type="hidden" name="class_id" value="{{ $selectedClassId }}">
                            <input type="hidden" name="subject_id" value="{{ $selectedSubjectId }}">

                            <!-- Question Stats -->
                            @include('question-papers.partials.question-stats')

                            <!-- Upload Section -->
                            @include('question-papers.partials.upload-section')

                            <!-- Question Builder -->
                            <div class="row">
                                <div class="col-md-12">
                                    <h6 class="border-bottom pb-2 mb-3">
                                        <i class="fas fa-question-circle me-2"></i>Questions
                                        <span class="badge bg-primary ms-2" id="questionCountBadge">{{ $questions->count() }}</span>
                                    </h6>
                                    
                                    <div id="questionsContainer">
                                        @include('question-papers.partials.question-list')
                                    </div>

                                    <button type="button" class="btn-add-question" onclick="addQuestion()">
                                        <i class="fas fa-plus-circle me-2"></i> Add Question
                                    </button>
                                </div>
                            </div>

                            <!-- Hidden inputs -->
                            <input type="hidden" name="questions_data" id="questionsData">

                            <!-- Form Actions -->
                            <div class="form-group mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-save me-2"></i> Save Questions
                                </button>
                                <a href="{{ route('question-papers.index') }}" class="btn btn-secondary btn-lg">
                                    <i class="fas fa-times me-2"></i> Cancel
                                </a>
                                <button type="button" class="btn btn-info btn-lg" onclick="previewQuestions()">
                                    <i class="fas fa-eye me-2"></i> Preview
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @else
                <!-- Selection Prompt -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                        <h5>Select Academic Year, Term, Class and Subject</h5>
                        <p class="text-muted">Please select all required fields above to manage questions.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Questions Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <div class="text-center text-muted">
                    <i class="fas fa-spinner fa-spin fa-2x"></i>
                    <p class="mt-2">Loading preview...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let questionCounter = {{ isset($questions) ? $questions->count() : 0 }};

    // Question Template
    function getQuestionTemplate(index) {
        return `
            <div class="question-card" data-question-index="${index}">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="d-flex align-items-center">
                        <div class="question-number me-3">${index + 1}</div>
                        <h6 class="mb-0">Question ${index + 1}</h6>
                        <span class="badge bg-secondary ms-2" id="questionTypeBadge_${index}">Multiple Choice</span>
                    </div>
                    <div class="question-actions">
                        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="duplicateQuestion(${index})" title="Duplicate">
                            <i class="fas fa-copy"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeQuestion(${index})" title="Remove">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group mb-2">
                            <label>Question Text <span class="required-star">*</span></label>
                            <textarea name="questions[${index}][question]" class="form-control question-text" rows="2" placeholder="Enter the question..." required></textarea>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label>Type</label>
                            <select name="questions[${index}][type]" class="form-select question-type" onchange="toggleOptions(${index})">
                                <option value="multiple_choice">Multiple Choice</option>
                                <option value="true_false">True/False</option>
                                <option value="short_answer">Short Answer</option>
                                <option value="essay">Essay</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group mb-2">
                            <label>Marks <span class="required-star">*</span></label>
                            <input type="number" name="questions[${index}][marks]" class="form-control question-marks" value="1" min="1" required onchange="updateTotalMarks()">
                        </div>
                    </div>
                </div>

                <!-- Options for Multiple Choice -->
                <div class="options-container" id="options_${index}">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="option-input">
                                <div class="input-group">
                                    <span class="input-group-text">A.</span>
                                    <input type="text" name="questions[${index}][options][a]" class="form-control" placeholder="Option A">
                                </div>
                            </div>
                            <div class="option-input">
                                <div class="input-group">
                                    <span class="input-group-text">C.</span>
                                    <input type="text" name="questions[${index}][options][c]" class="form-control" placeholder="Option C">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="option-input">
                                <div class="input-group">
                                    <span class="input-group-text">B.</span>
                                    <input type="text" name="questions[${index}][options][b]" class="form-control" placeholder="Option B">
                                </div>
                            </div>
                            <div class="option-input">
                                <div class="input-group">
                                    <span class="input-group-text">D.</span>
                                    <input type="text" name="questions[${index}][options][d]" class="form-control" placeholder="Option D">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- True/False Options -->
                <div class="true-false-container" id="truefalse_${index}" style="display:none;">
                    <div class="form-group">
                        <label>Correct Answer <span class="required-star">*</span></label>
                        <div class="d-flex gap-3">
                            <div class="form-check">
                                <input type="radio" name="questions[${index}][correct]" value="true" class="form-check-input">
                                <label class="form-check-label">True</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="questions[${index}][correct]" value="false" class="form-check-input">
                                <label class="form-check-label">False</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Correct Answer -->
                <div class="row mt-2">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Correct Answer</label>
                            <input type="text" name="questions[${index}][correct_answer]" class="form-control" placeholder="Enter correct answer or option letter (A, B, C, D)">
                            <small class="text-muted">For multiple choice, enter the option letter (A, B, C, or D)</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Explanation (Optional)</label>
                            <input type="text" name="questions[${index}][explanation]" class="form-control" placeholder="Explain why this is the correct answer">
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Add Question
    function addQuestion() {
        const container = document.getElementById('questionsContainer');
        const index = questionCounter;
        const template = getQuestionTemplate(index);
        
        const div = document.createElement('div');
        div.innerHTML = template;
        container.appendChild(div.firstElementChild);
        
        questionCounter++;
        updateQuestionCount();
        updateQuestionsData();
        updateTotalMarks();
        updateStats();
    }

    // Remove Question
    function removeQuestion(index) {
        if (confirm('Are you sure you want to remove this question?')) {
            const card = document.querySelector(`.question-card[data-question-index="${index}"]`);
            if (card) {
                card.remove();
                renumberQuestions();
                updateQuestionCount();
                updateQuestionsData();
                updateTotalMarks();
                updateStats();
            }
        }
    }

    // Duplicate Question
    function duplicateQuestion(index) {
        const card = document.querySelector(`.question-card[data-question-index="${index}"]`);
        if (card) {
            const clone = card.cloneNode(true);
            const newIndex = questionCounter;
            clone.dataset.questionIndex = newIndex;
            
            const idInput = clone.querySelector('input[name*="[id]"]');
            if (idInput) idInput.remove();
            
            const inputs = clone.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/\[\d+\]/, `[${newIndex}]`);
                }
                if (input.type === 'radio' || input.type === 'checkbox') {
                    input.checked = false;
                }
                if (input.type === 'text' || input.type === 'number') {
                    if (!input.name.includes('options')) {
                        input.value = '';
                    }
                }
                if (input.tagName === 'TEXTAREA') {
                    input.value = '';
                }
            });
            
            const optionsContainer = clone.querySelector('.options-container');
            if (optionsContainer) optionsContainer.id = `options_${newIndex}`;
            const trueFalseContainer = clone.querySelector('.true-false-container');
            if (trueFalseContainer) trueFalseContainer.id = `truefalse_${newIndex}`;
            
            document.getElementById('questionsContainer').appendChild(clone);
            questionCounter++;
            renumberQuestions();
            updateQuestionCount();
            updateQuestionsData();
            updateTotalMarks();
            updateStats();
        }
    }

    // Renumber Questions
    function renumberQuestions() {
        const cards = document.querySelectorAll('.question-card');
        cards.forEach((card, index) => {
            card.dataset.questionIndex = index;
            const numberDiv = card.querySelector('.question-number');
            const title = card.querySelector('h6');
            if (numberDiv) numberDiv.textContent = index + 1;
            if (title) title.textContent = `Question ${index + 1}`;
            
            const inputs = card.querySelectorAll('input, textarea, select');
            inputs.forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
                }
            });
        });
        questionCounter = cards.length;
    }

    // Toggle Options
    function toggleOptions(index) {
        const select = document.querySelector(`select[name="questions[${index}][type]"]`);
        if (!select) return;
        
        const type = select.value;
        const optionsContainer = document.getElementById(`options_${index}`);
        const trueFalseContainer = document.getElementById(`truefalse_${index}`);
        const badge = document.getElementById(`questionTypeBadge_${index}`);
        
        if (optionsContainer) {
            optionsContainer.style.display = type === 'multiple_choice' ? 'block' : 'none';
        }
        if (trueFalseContainer) {
            trueFalseContainer.style.display = type === 'true_false' ? 'block' : 'none';
        }
        if (badge) {
            const labels = {
                'multiple_choice': 'Multiple Choice',
                'true_false': 'True/False',
                'short_answer': 'Short Answer',
                'essay': 'Essay'
            };
            badge.textContent = labels[type] || type;
        }
        
        updateQuestionsData();
        updateStats();
    }

    // Update Functions
    function updateQuestionCount() {
        const count = document.querySelectorAll('.question-card').length;
        document.getElementById('questionCount').textContent = count;
        document.getElementById('questionCountBadge').textContent = count;
    }

    function updateTotalMarks() {
        let total = 0;
        document.querySelectorAll('.question-marks').forEach(input => {
            total += parseInt(input.value) || 0;
        });
        document.getElementById('totalMarksDisplay').textContent = total;
    }

    function updateStats() {
        let mcCount = 0;
        let essayCount = 0;
        document.querySelectorAll('.question-type').forEach(select => {
            if (select.value === 'multiple_choice' || select.value === 'true_false') {
                mcCount++;
            } else {
                essayCount++;
            }
        });
        document.getElementById('mcCount').textContent = mcCount;
        document.getElementById('essayCount').textContent = essayCount;
    }

    function updateQuestionsData() {
        const questions = [];
        const cards = document.querySelectorAll('.question-card');
        cards.forEach(card => {
            const index = card.dataset.questionIndex;
            const question = {
                question: document.querySelector(`textarea[name="questions[${index}][question]"]`)?.value || '',
                type: document.querySelector(`select[name="questions[${index}][type]"]`)?.value || 'multiple_choice',
                marks: parseInt(document.querySelector(`input[name="questions[${index}][marks]"]`)?.value) || 1,
                options: {},
                correct_answer: document.querySelector(`input[name="questions[${index}][correct_answer]"]`)?.value || '',
                explanation: document.querySelector(`input[name="questions[${index}][explanation]"]`)?.value || ''
            };
            
            const idInput = document.querySelector(`input[name="questions[${index}][id]"]`);
            if (idInput) {
                question.id = parseInt(idInput.value);
            }
            
            if (question.type === 'multiple_choice') {
                const optionA = document.querySelector(`input[name="questions[${index}][options][a]"]`)?.value || '';
                const optionB = document.querySelector(`input[name="questions[${index}][options][b]"]`)?.value || '';
                const optionC = document.querySelector(`input[name="questions[${index}][options][c]"]`)?.value || '';
                const optionD = document.querySelector(`input[name="questions[${index}][options][d]"]`)?.value || '';
                question.options = { a: optionA, b: optionB, c: optionC, d: optionD };
            }
            
            if (question.type === 'true_false') {
                const correct = document.querySelector(`input[name="questions[${index}][correct]"]:checked`);
                if (correct) {
                    question.correct_answer = correct.value;
                }
            }
            
            questions.push(question);
        });
        document.getElementById('questionsData').value = JSON.stringify(questions);
    }

    // Preview Questions
    function previewQuestions() {
        updateQuestionsData();
        const questionsData = document.getElementById('questionsData').value;
        const questions = JSON.parse(questionsData || '[]');
        
        let html = '<div class="questions-preview">';
        
        if (questions.length === 0) {
            html += `<div class="alert alert-warning">No questions added yet.</div>`;
        } else {
            questions.forEach((q, i) => {
                html += `
                    <div class="mb-4 p-3 border rounded">
                        <h6>Question ${i + 1} (${q.marks} marks)</h6>
                        <p>${q.question || 'No question text'}</p>
                        ${q.type === 'multiple_choice' ? `
                            <div class="ms-3">
                                ${q.options?.a ? `<div>A. ${q.options.a}</div>` : ''}
                                ${q.options?.b ? `<div>B. ${q.options.b}</div>` : ''}
                                ${q.options?.c ? `<div>C. ${q.options.c}</div>` : ''}
                                ${q.options?.d ? `<div>D. ${q.options.d}</div>` : ''}
                            </div>
                        ` : ''}
                        ${q.type === 'true_false' ? `
                            <div class="ms-3">
                                <div>True</div>
                                <div>False</div>
                            </div>
                        ` : ''}
                        ${q.type === 'short_answer' ? `
                            <div class="ms-3 text-muted">[Short Answer]</div>
                        ` : ''}
                        ${q.type === 'essay' ? `
                            <div class="ms-3 text-muted">[Essay]</div>
                        ` : ''}
                        ${q.correct_answer ? `
                            <div class="mt-2">
                                <span class="badge bg-success">Correct Answer: ${q.correct_answer}</span>
                            </div>
                        ` : ''}
                        ${q.explanation ? `
                            <div class="mt-2 small text-muted">Explanation: ${q.explanation}</div>
                        ` : ''}
                    </div>
                `;
            });
            
            const totalMarks = questions.reduce((sum, q) => sum + parseInt(q.marks), 0);
            html += `
                <div class="alert alert-info">
                    <strong>Summary:</strong> ${questions.length} questions | Total Marks: ${totalMarks}
                </div>
            `;
        }
        
        html += '</div>';
        
        document.getElementById('previewContent').innerHTML = html;
        const modal = new bootstrap.Modal(document.getElementById('previewModal'));
        modal.show();
    }

    // DOM Ready
    document.addEventListener('DOMContentLoaded', function() {
        const existingQuestions = document.querySelectorAll('.question-card').length;
        if (existingQuestions === 0 && document.getElementById('questionsContainer')) {
            addQuestion();
        }
        
        updateQuestionCount();
        updateTotalMarks();
        updateStats();
        updateQuestionsData();
    });

    // Update on change
    document.addEventListener('change', function(e) {
        if (e.target.closest('.question-card')) {
            updateQuestionsData();
            updateTotalMarks();
            updateStats();
        }
    });

    document.addEventListener('input', function(e) {
        if (e.target.closest('.question-card')) {
            updateQuestionsData();
            updateTotalMarks();
            updateStats();
        }
    });
</script>
@endpush
@endsection