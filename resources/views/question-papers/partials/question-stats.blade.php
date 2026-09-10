<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card bg-primary text-white h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small mb-1">Total Questions</div>
                        <div class="h2 mb-0" id="questionCount">{{ $questions->count() ?? 0 }}</div>
                        <div class="small mt-2">
                            <i class="fas fa-question-circle"></i> Questions Added
                        </div>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle p-3">
                        <i class="fas fa-question-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card bg-success text-white h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small mb-1">Total Marks</div>
                        <div class="h2 mb-0" id="totalMarksDisplay">{{ $totalMarks ?? 0 }}</div>
                        <div class="small mt-2">
                            <i class="fas fa-star"></i> Marks Available
                        </div>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle p-3">
                        <i class="fas fa-star fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card bg-info text-white h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small mb-1">Multiple Choice</div>
                        <div class="h2 mb-0" id="mcCount">0</div>
                        <div class="small mt-2">
                            <i class="fas fa-list-ul"></i> MC Questions
                        </div>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle p-3">
                        <i class="fas fa-list-ul fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-3">
        <div class="card bg-warning text-white h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-uppercase small mb-1">Essay/Short Answer</div>
                        <div class="h2 mb-0" id="essayCount">0</div>
                        <div class="small mt-2">
                            <i class="fas fa-pen"></i> Subjective Questions
                        </div>
                    </div>
                    <div class="bg-white bg-opacity-25 rounded-circle p-3">
                        <i class="fas fa-pen fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>