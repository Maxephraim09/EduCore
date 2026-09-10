@extends('layouts.app')

@section('title', 'Take Exam - ' . $exam->title)

@section('content')
<style>
    .exam-timer {
        font-size: 24px;
        font-weight: bold;
        color: #dc3545;
    }
    .question-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }
    .question-nav .btn {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .question-nav .btn-answered {
        background-color: #28a745;
        color: white;
    }
    .question-nav .btn-flagged {
        background-color: #ffc107;
    }
    .question-nav .btn-current {
        border: 3px solid #007bff;
        font-weight: bold;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Exam Header -->
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-pencil-alt me-2"></i>{{ $exam->title }}</h5>
                    <div class="d-flex align-items-center gap-3">
                        <span class="exam-timer" id="timer">00:00:00</span>
                        <span class="badge bg-info">Question <span id="current-q">1</span> of {{ $totalQuestions }}</span>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Question Area -->
                <div class="col-md-9">
                    <div class="card">
                        <div class="card-body">
                            <div id="question-container">
                                <!-- Questions will be loaded here -->
                            </div>
                            
                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-secondary" id="prev-question">
                                    <i class="fas fa-arrow-left"></i> Previous
                                </button>
                                <div>
                                    <button type="button" class="btn btn-warning" id="flag-question">
                                        <i class="fas fa-flag"></i> Flag
                                    </button>
                                    <button type="button" class="btn btn-outline-danger" id="clear-answer">
                                        <i class="fas fa-eraser"></i> Clear
                                    </button>
                                </div>
                                <button type="button" class="btn btn-primary" id="next-question">
                                    Next <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>

                            <div class="mt-3">
                                <button type="button" class="btn btn-success w-100" id="submit-exam">
                                    <i class="fas fa-check-circle"></i> Submit Exam
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Navigation Panel -->
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h6><i class="fas fa-list me-2"></i>Question Navigator</h6>
                        </div>
                        <div class="card-body">
                            <div class="question-nav" id="question-nav">
                                <!-- Navigation buttons will be generated -->
                            </div>
                            <hr>
                            <div class="small">
                                <span class="badge bg-success">Answered</span>
                                <span class="badge bg-warning">Flagged</span>
                                <span class="badge bg-secondary">Not Answered</span>
                            </div>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-outline-danger" id="submit-exam-btn">
                                    <i class="fas fa-check-circle"></i> Submit All
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    let currentQuestion = 1;
    let totalQuestions = {{ $totalQuestions }};
    let timeLeft = {{ $timeLeft }};
    let timerInterval;
    let answers = {};
    let flaggedQuestions = new Set();
    let attemptId = {{ $attemptId }};

    // Load saved answers from session
    function loadSavedAnswers() {
        $.ajax({
            url: '{{ route("cbt.student.get-saved-answers") }}',
            method: 'GET',
            data: { attempt_id: attemptId },
            success: function(response) {
                if (response.answers) {
                    answers = response.answers;
                }
            }
        });
    }

    // Timer function
    function startTimer() {
        timerInterval = setInterval(function() {
            timeLeft--;
            updateTimerDisplay();
            if (timeLeft <= 0) {
                clearInterval(timerInterval);
                autoSubmit();
            }
            
            // Auto-save every 30 seconds
            if (timeLeft % 30 === 0) {
                saveProgress();
            }
        }, 1000);
    }

    function updateTimerDisplay() {
        const hours = Math.floor(timeLeft / 3600);
        const minutes = Math.floor((timeLeft % 3600) / 60);
        const seconds = timeLeft % 60;
        document.getElementById('timer').textContent = 
            `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        
        // Change color when time is running low
        if (timeLeft < 300) {
            document.getElementById('timer').style.color = '#dc3545';
            document.getElementById('timer').classList.add('animate__animated', 'animate__pulse');
        }
    }

    function loadQuestion(index) {
        $.ajax({
            url: '{{ route("cbt.student.get-question") }}',
            method: 'GET',
            data: { 
                exam_id: {{ $exam->id }}, 
                attempt_id: attemptId,
                question_index: index 
            },
            success: function(response) {
                $('#question-container').html(response.html);
                currentQuestion = index;
                document.getElementById('current-q').textContent = index;
                updateNavigation();
                restoreAnswer();
                updateNavButtons();
            },
            error: function(xhr) {
                alert('Error loading question. Please refresh the page.');
            }
        });
    }

    function saveAnswer() {
        const selectedAnswer = $('input[name="answer"]:checked').val();
        if (selectedAnswer) {
            answers[currentQuestion] = selectedAnswer;
        } else {
            delete answers[currentQuestion];
        }
        updateNavButtons();
        saveProgress();
    }

    function restoreAnswer() {
        if (answers[currentQuestion]) {
            $(`input[name="answer"][value="${answers[currentQuestion]}"]`).prop('checked', true);
        }
    }

    function saveProgress() {
        $.ajax({
            url: '{{ route("cbt.student.save-progress") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                attempt_id: attemptId,
                answers: answers,
                current_question: currentQuestion
            },
            error: function() {
                // Silent fail - will try again on next save
            }
        });
    }

    function updateNavigation() {
        const nav = document.getElementById('question-nav');
        nav.innerHTML = '';
        for (let i = 1; i <= totalQuestions; i++) {
            const btn = document.createElement('button');
            btn.className = 'btn btn-outline-secondary btn-sm';
            btn.textContent = i;
            btn.onclick = function() {
                saveAnswer();
                loadQuestion(i);
            };
            if (i === currentQuestion) {
                btn.classList.add('btn-current');
            }
            if (answers[i]) {
                btn.classList.remove('btn-outline-secondary');
                btn.classList.add('btn-answered');
            }
            if (flaggedQuestions.has(i)) {
                btn.classList.add('btn-flagged');
            }
            nav.appendChild(btn);
        }
    }

    function updateNavButtons() {
        const buttons = document.querySelectorAll('#question-nav .btn');
        buttons.forEach((btn, index) => {
            const qNum = index + 1;
            btn.classList.remove('btn-answered', 'btn-flagged', 'btn-current');
            if (answers[qNum]) {
                btn.classList.add('btn-answered');
            }
            if (flaggedQuestions.has(qNum)) {
                btn.classList.add('btn-flagged');
            }
            if (qNum === currentQuestion) {
                btn.classList.add('btn-current');
            }
        });
    }

    function autoSubmit() {
        if (confirm('Time is up! Your exam will be submitted automatically.')) {
            submitExam();
        }
    }

    function submitExam() {
        saveAnswer();
        if (confirm('Are you sure you want to submit your exam? You cannot change your answers after submission.')) {
            $.ajax({
                url: '{{ route("cbt.student.submit-exam", $exam->id) }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    attempt_id: attemptId,
                    answers: answers
                },
                success: function(response) {
                    clearInterval(timerInterval);
                    window.location.href = '{{ route("cbt.student.results", $exam->id) }}';
                },
                error: function(xhr) {
                    alert('Error submitting exam: ' + xhr.responseJSON.message);
                }
            });
        }
    }

    $(document).ready(function() {
        loadSavedAnswers();
        loadQuestion(1);
        startTimer();

        $('#next-question').click(function() {
            saveAnswer();
            if (currentQuestion < totalQuestions) {
                loadQuestion(currentQuestion + 1);
            }
        });

        $('#prev-question').click(function() {
            saveAnswer();
            if (currentQuestion > 1) {
                loadQuestion(currentQuestion - 1);
            }
        });

        $('#flag-question').click(function() {
            if (flaggedQuestions.has(currentQuestion)) {
                flaggedQuestions.delete(currentQuestion);
                $(this).removeClass('btn-warning').addClass('btn-outline-warning');
            } else {
                flaggedQuestions.add(currentQuestion);
                $(this).removeClass('btn-outline-warning').addClass('btn-warning');
            }
            updateNavButtons();
        });

        $('#clear-answer').click(function() {
            delete answers[currentQuestion];
            $('input[name="answer"]').prop('checked', false);
            updateNavButtons();
            saveProgress();
        });

        $('#submit-exam, #submit-exam-btn').click(function() {
            submitExam();
        });

        // Keyboard shortcuts
        $(document).keydown(function(e) {
            if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                e.preventDefault();
                $('#next-question').click();
            } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                e.preventDefault();
                $('#prev-question').click();
            }
        });
    });
</script>
@endpush
@endsection