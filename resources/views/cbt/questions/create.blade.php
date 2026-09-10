@extends('layouts.app')

@section('title', 'Add Question to ' . $exam->title)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('cbt.exams.index') }}">CBT Exams</a></li>
    <li class="breadcrumb-item"><a href="{{ route('cbt.exams.show', $exam->id) }}">{{ $exam->title }}</a></li>
    <li class="breadcrumb-item active">Add Question</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-plus-circle me-2"></i>Add Question to "{{ $exam->title }}"</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('cbt.questions.store', $exam->id) }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="type" class="form-label">Question Type <span class="text-danger">*</span></label>
                                    <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required onchange="toggleQuestionFields()">
                                        <option value="multiple_choice" {{ old('type') == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                                        <option value="true_false" {{ old('type') == 'true_false' ? 'selected' : '' }}>True/False</option>
                                        <option value="fill_blank" {{ old('type') == 'fill_blank' ? 'selected' : '' }}>Fill in the Blank</option>
                                        <option value="essay" {{ old('type') == 'essay' ? 'selected' : '' }}>Essay</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="marks" class="form-label">Marks <span class="text-danger">*</span></label>
                                    <input type="number" name="marks" id="marks" 
                                           class="form-control @error('marks') is-invalid @enderror"
                                           value="{{ old('marks', 1) }}" min="1" max="100" required>
                                    @error('marks')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="question" class="form-label">Question <span class="text-danger">*</span></label>
                            <textarea name="question" id="question" 
                                      class="form-control @error('question') is-invalid @enderror"
                                      rows="3" required>{{ old('question') }}</textarea>
                            @error('question')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Multiple Choice Options -->
                        <div id="multiple-choice-fields">
                            <div class="card mb-3">
                                <div class="card-header">
                                    <h6>Options</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-2">
                                                <label>Option A</label>
                                                <input type="text" name="options[a]" class="form-control" placeholder="Enter option A" value="{{ old('options.a') }}">
                                            </div>
                                            <div class="form-group mb-2">
                                                <label>Option C</label>
                                                <input type="text" name="options[c]" class="form-control" placeholder="Enter option C" value="{{ old('options.c') }}">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-2">
                                                <label>Option B</label>
                                                <input type="text" name="options[b]" class="form-control" placeholder="Enter option B" value="{{ old('options.b') }}">
                                            </div>
                                            <div class="form-group mb-2">
                                                <label>Option D</label>
                                                <input type="text" name="options[d]" class="form-control" placeholder="Enter option D" value="{{ old('options.d') }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- True/False Fields -->
                        <div id="true-false-fields" style="display: none;">
                            <div class="form-group mb-3">
                                <label class="form-label">Correct Answer <span class="text-danger">*</span></label>
                                <div class="form-check">
                                    <input type="radio" name="correct_answer" id="true" value="true" {{ old('correct_answer') == 'true' ? 'checked' : '' }}>
                                    <label for="true" class="form-check-label">True</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" name="correct_answer" id="false" value="false" {{ old('correct_answer') == 'false' ? 'checked' : '' }}>
                                    <label for="false" class="form-check-label">False</label>
                                </div>
                            </div>
                        </div>

                        <!-- Fill in the Blank Fields -->
                        <div id="fill-blank-fields" style="display: none;">
                            <div class="form-group mb-3">
                                <label for="correct_answer_fill" class="form-label">Correct Answer <span class="text-danger">*</span></label>
                                <input type="text" name="correct_answer" id="correct_answer_fill" 
                                       class="form-control @error('correct_answer') is-invalid @enderror"
                                       value="{{ old('correct_answer') }}" placeholder="Enter the correct answer">
                                @error('correct_answer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Essay Fields -->
                        <div id="essay-fields" style="display: none;">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                Essay questions will be manually graded by the teacher.
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label for="explanation" class="form-label">Explanation (Optional)</label>
                            <textarea name="explanation" id="explanation" 
                                      class="form-control @error('explanation') is-invalid @enderror"
                                      rows="2" placeholder="Explain why this is the correct answer...">{{ old('explanation') }}</textarea>
                            @error('explanation')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Question
                            </button>
                            <button type="submit" name="add_another" value="1" class="btn btn-info">
                                <i class="fas fa-plus-circle"></i> Save & Add Another
                            </button>
                            <a href="{{ route('cbt.exams.show', $exam->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-info-circle me-2"></i>Exam Info</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th>Title</th>
                            <td>{{ $exam->title }}</td>
                        </tr>
                        <tr>
                            <th>Questions</th>
                            <td>{{ $exam->questions->count() }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td><span class="badge bg-{{ $exam->status_badge }}">{{ ucfirst($exam->status) }}</span></td>
                        </tr>
                    </table>
                    <div class="alert alert-warning">
                        <strong>Tip:</strong> You can add multiple questions quickly by using "Save & Add Another".
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleQuestionFields() {
        const type = document.getElementById('type').value;
        
        document.getElementById('multiple-choice-fields').style.display = type === 'multiple_choice' ? 'block' : 'none';
        document.getElementById('true-false-fields').style.display = type === 'true_false' ? 'block' : 'none';
        document.getElementById('fill-blank-fields').style.display = type === 'fill_blank' ? 'block' : 'none';
        document.getElementById('essay-fields').style.display = type === 'essay' ? 'block' : 'none';
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        toggleQuestionFields();
    });
</script>
@endpush
@endsection