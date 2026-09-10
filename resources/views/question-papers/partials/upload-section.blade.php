<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-light">
                <h6 class="mb-0"><i class="fas fa-upload me-2"></i>Upload Questions (Optional)</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-7">
                        <div class="upload-area" id="uploadArea">
                            <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                            <h6>Drag & Drop or Click to Upload</h6>
                            <p class="text-muted">Upload Word, Excel, or PDF files with questions</p>
                            <input type="file" name="question_file" id="questionFile" class="d-none" accept=".docx,.doc,.xlsx,.xls,.pdf">
                            <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('questionFile').click()">
                                <i class="fas fa-folder-open"></i> Browse Files
                            </button>
                        </div>
                        <div id="fileInfo" class="mt-2" style="display:none;">
                            <div class="alert alert-info">
                                <strong>Selected File:</strong> <span id="fileName"></span>
                                <button type="button" class="btn btn-sm btn-danger float-end" onclick="clearFile()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="alert alert-info">
                            <strong><i class="fas fa-info-circle"></i> Supported Formats:</strong>
                            <ul class="mb-0">
                                <li>Word (.docx, .doc)</li>
                                <li>Excel (.xlsx, .xls)</li>
                                <li>PDF (.pdf)</li>
                            </ul>
                        </div>
                        <div class="alert alert-warning">
                            <strong><i class="fas fa-exclamation-triangle"></i> Note:</strong>
                            <ul class="mb-0">
                                <li>Upload will parse questions automatically</li>
                                <li>Each question should be numbered</li>
                                <li>Options should be labeled A, B, C, D</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>