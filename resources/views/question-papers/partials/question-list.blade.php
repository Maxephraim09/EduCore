@if(isset($questions) && $questions->count() > 0)
    @foreach($questions as $index => $question)
        <div class="question-card" data-question-index="{{ $index }}" data-question-id="{{ $question->id }}">
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 32px; height: 32px; font-weight: bold; font-size: 14px;">
                            {{ $index + 1 }}
                        </div>
                        <h6 class="mb-0 fw-bold">Question {{ $index + 1 }}</h6>
                        <span class="badge bg-secondary ms-2" id="questionTypeBadge_{{ $index }}">
                            {{ ucfirst(str_replace('_', ' ', $question->type)) }}
                        </span>
                    </div>
                    <div class="question-actions">
                        <button type="button" class="btn btn-outline-secondary btn-sm me-1" onclick="duplicateQuestion({{ $index }})" title="Duplicate">
                            <i class="fas fa-copy"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm" onclick="removeQuestion({{ $index }})" title="Remove">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group mb-2">
                                <label class="form-label fw-semibold">Question Text <span class="text-danger">*</span></label>
                                <textarea name="questions[{{ $index }}][question]" class="form-control question-text" rows="2" placeholder="Enter the question..." required>{{ old("questions.{$index}.question", $question->question) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label fw-semibold">Type</label>
                                <select name="questions[{{ $index }}][type]" class="form-select question-type" onchange="toggleOptions({{ $index }})">
                                    <option value="multiple_choice" {{ $question->type == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                                    <option value="true_false" {{ $question->type == 'true_false' ? 'selected' : '' }}>True/False</option>
                                    <option value="short_answer" {{ $question->type == 'short_answer' ? 'selected' : '' }}>Short Answer</option>
                                    <option value="essay" {{ $question->type == 'essay' ? 'selected' : '' }}>Essay</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label class="form-label fw-semibold">Marks <span class="text-danger">*</span></label>
                                <input type="number" name="questions[{{ $index }}][marks]" class="form-control question-marks" value="{{ $question->marks }}" min="1" required onchange="updateTotalMarks()">
                            </div>
                        </div>
                    </div>

                    <!-- Options for Multiple Choice -->
                    <div class="options-container mt-2" id="options_{{ $index }}" style="display: {{ $question->type == 'multiple_choice' ? 'block' : 'none' }};">
                        <label class="form-label fw-semibold">Options</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="option-input mb-2">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light fw-bold">A.</span>
                                        <input type="text" name="questions[{{ $index }}][options][a]" class="form-control" placeholder="Option A" value="{{ $question->options['a'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="option-input mb-2">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light fw-bold">C.</span>
                                        <input type="text" name="questions[{{ $index }}][options][c]" class="form-control" placeholder="Option C" value="{{ $question->options['c'] ?? '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="option-input mb-2">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light fw-bold">B.</span>
                                        <input type="text" name="questions[{{ $index }}][options][b]" class="form-control" placeholder="Option B" value="{{ $question->options['b'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="option-input mb-2">
                                    <div class="input-group">
                                        <span class="input-group-text bg-light fw-bold">D.</span>
                                        <input type="text" name="questions[{{ $index }}][options][d]" class="form-control" placeholder="Option D" value="{{ $question->options['d'] ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- True/False Options -->
                    <div class="true-false-container mt-2" id="truefalse_{{ $index }}" style="display: {{ $question->type == 'true_false' ? 'block' : 'none' }};">
                        <label class="form-label fw-semibold">Correct Answer <span class="text-danger">*</span></label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input type="radio" name="questions[{{ $index }}][correct]" value="true" class="form-check-input" {{ ($question->correct_answer ?? '') == 'true' ? 'checked' : '' }} id="true_{{ $index }}">
                                <label class="form-check-label" for="true_{{ $index }}">True</label>
                            </div>
                            <div class="form-check">
                                <input type="radio" name="questions[{{ $index }}][correct]" value="false" class="form-check-input" {{ ($question->correct_answer ?? '') == 'false' ? 'checked' : '' }} id="false_{{ $index }}">
                                <label class="form-check-label" for="false_{{ $index }}">False</label>
                            </div>
                        </div>
                    </div>

                    <!-- Correct Answer & Explanation -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold">Correct Answer</label>
                                <input type="text" name="questions[{{ $index }}][correct_answer]" class="form-control" placeholder="Enter correct answer or option letter (A, B, C, D)" value="{{ $question->correct_answer ?? '' }}">
                                <small class="text-muted"><i class="fas fa-info-circle"></i> For multiple choice, enter the option letter (A, B, C, or D)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-semibold">Explanation <small class="text-muted">(Optional)</small></label>
                                <input type="text" name="questions[{{ $index }}][explanation]" class="form-control" placeholder="Explain why this is the correct answer" value="{{ $question->explanation ?? '' }}">
                            </div>
                        </div>
                    </div>
                    
                    <input type="hidden" name="questions[{{ $index }}][id]" value="{{ $question->id }}">
                </div>
            </div>
        </div>
        
    @endforeach
@endif