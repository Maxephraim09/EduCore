@extends('layouts.app')

@section('title', $questionPaper->title)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('question-papers.index') }}">Question Papers</a></li>
    <li class="breadcrumb-item active">{{ $questionPaper->title }}</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Header Card -->
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-1">{{ $questionPaper->title }}</h4>
                            <p class="mb-0">
                                <span class="badge bg-light text-dark me-2">{{ $questionPaper->code }}</span>
                                <span class="badge bg-{{ $questionPaper->is_published ? 'success' : 'warning' }}">
                                    {{ $questionPaper->is_published ? 'Published' : 'Draft' }}
                                </span>
                                <span class="badge bg-info">{{ $questionPaper->term }}</span>
                                <span class="badge bg-secondary">{{ $questionPaper->academic_year }}</span>
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('question-papers.manage-questions', ['academic_year' => $questionPaper->academic_year, 'term' => $questionPaper->term, 'class_id' => $questionPaper->class_id, 'subject_id' => $questionPaper->subject_id]) }}" class="btn btn-light">
                                <i class="fas fa-edit"></i> Edit Questions
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $questionPaper->subject->name ?? 'N/A' }}</h3>
                            <small class="text-muted">Subject</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $questionPaper->class->full_class_name ?? 'N/A' }}</h3>
                            <small class="text-muted">Class</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $questionPaper->questions->count() }}</h3>
                            <small class="text-muted">Total Questions</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-body text-center">
                            <h3 class="mb-0">{{ $questionPaper->questions->sum('marks') }}</h3>
                            <small class="text-muted">Total Marks</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Questions List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-question-circle me-2"></i>Questions</h5>
                </div>
                <div class="card-body">
                    @if($questionPaper->questions->count() > 0)
                        @foreach($questionPaper->questions as $index => $question)
                            <div class="question-preview-card mb-3 p-3 border rounded">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6>Question {{ $index + 1 }} ({{ $question->marks }} marks)</h6>
                                        <p>{{ $question->question }}</p>
                                        
                                        @if($question->type == 'multiple_choice' && $question->options)
                                            <div class="ms-3">
                                                @foreach(['a', 'b', 'c', 'd'] as $letter)
                                                    @if(!empty($question->options[$letter] ?? ''))
                                                        <div class="{{ (isset($question->correct_answer) && strtolower($question->correct_answer) == $letter) ? 'text-success fw-bold' : '' }}">
                                                            {{ strtoupper($letter) }}. {{ $question->options[$letter] }}
                                                            @if(isset($question->correct_answer) && strtolower($question->correct_answer) == $letter)
                                                                <span class="badge bg-success ms-2">Correct</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif

                                        @if($question->type == 'true_false')
                                            <div class="ms-3">
                                                <div class="{{ (isset($question->correct_answer) && $question->correct_answer == 'true') ? 'text-success fw-bold' : '' }}">
                                                    True
                                                    @if(isset($question->correct_answer) && $question->correct_answer == 'true')
                                                        <span class="badge bg-success ms-2">Correct</span>
                                                    @endif
                                                </div>
                                                <div class="{{ (isset($question->correct_answer) && $question->correct_answer == 'false') ? 'text-success fw-bold' : '' }}">
                                                    False
                                                    @if(isset($question->correct_answer) && $question->correct_answer == 'false')
                                                        <span class="badge bg-success ms-2">Correct</span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        @if($question->correct_answer)
                                            <div class="mt-2">
                                                <span class="badge bg-info">Correct Answer: {{ $question->correct_answer }}</span>
                                            </div>
                                        @endif

                                        @if($question->explanation)
                                            <div class="mt-2 small text-muted">
                                                <strong>Explanation:</strong> {{ $question->explanation }}
                                            </div>
                                        @endif
                                    </div>
                                    <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $question->type)) }}</span>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                            <p class="text-muted">No questions added to this question paper yet.</p>
                            <a href="{{ route('question-papers.manage-questions', ['academic_year' => $questionPaper->academic_year, 'term' => $questionPaper->term, 'class_id' => $questionPaper->class_id, 'subject_id' => $questionPaper->subject_id]) }}" class="btn btn-primary">
                                <i class="fas fa-plus-circle"></i> Add Questions
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection