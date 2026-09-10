@extends('layouts.app')

@section('title', 'Add New Grade')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('grading.index') }}">Grading Settings</a></li>
<li class="breadcrumb-item"><a href="{{ route('grading.scale') }}">Grade Scale</a></li>
<li class="breadcrumb-item active">Add New</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-plus-circle me-2"></i>Add New Grade
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('grading.scale.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="grade">Grade <span class="text-danger">*</span></label>
                                    <input type="text" name="grade" id="grade" 
                                           class="form-control @error('grade') is-invalid @enderror"
                                           value="{{ old('grade') }}" 
                                           placeholder="e.g., A, B+, C-" required maxlength="2">
                                    @error('grade')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="remark">Remark <span class="text-danger">*</span></label>
                                    <input type="text" name="remark" id="remark" 
                                           class="form-control @error('remark') is-invalid @enderror"
                                           value="{{ old('remark') }}" 
                                           placeholder="e.g., Excellent, Good, Pass" required>
                                    @error('remark')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="min_score">Minimum Score (%) <span class="text-danger">*</span></label>
                                    <input type="number" name="min_score" id="min_score" 
                                           class="form-control @error('min_score') is-invalid @enderror"
                                           value="{{ old('min_score') }}" 
                                           min="0" max="100" required>
                                    @error('min_score')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="max_score">Maximum Score (%) <span class="text-danger">*</span></label>
                                    <input type="number" name="max_score" id="max_score" 
                                           class="form-control @error('max_score') is-invalid @enderror"
                                           value="{{ old('max_score') }}" 
                                           min="0" max="100" required>
                                    @error('max_score')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="color">Color <span class="text-danger">*</span></label>
                                    <select name="color" id="color" 
                                            class="form-control @error('color') is-invalid @enderror" required>
                                        <option value="">Select Color</option>
                                        <option value="success" {{ old('color') == 'success' ? 'selected' : '' }}>Success (Green)</option>
                                        <option value="primary" {{ old('color') == 'primary' ? 'selected' : '' }}>Primary (Blue)</option>
                                        <option value="info" {{ old('color') == 'info' ? 'selected' : '' }}>Info (Cyan)</option>
                                        <option value="warning" {{ old('color') == 'warning' ? 'selected' : '' }}>Warning (Yellow)</option>
                                        <option value="danger" {{ old('color') == 'danger' ? 'selected' : '' }}>Danger (Red)</option>
                                        <option value="secondary" {{ old('color') == 'secondary' ? 'selected' : '' }}>Secondary (Gray)</option>
                                        <option value="dark" {{ old('color') == 'dark' ? 'selected' : '' }}>Dark</option>
                                        <option value="purple" {{ old('color') == 'purple' ? 'selected' : '' }}>Purple</option>
                                        <option value="pink" {{ old('color') == 'pink' ? 'selected' : '' }}>Pink</option>
                                        <option value="orange" {{ old('color') == 'orange' ? 'selected' : '' }}>Orange</option>
                                    </select>
                                    @error('color')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <div class="form-check mt-2">
                                        <input type="checkbox" name="is_active" id="is_active" 
                                               class="form-check-input" value="1" 
                                               {{ old('is_active', true) ? 'checked' : '' }}>
                                        <label for="is_active" class="form-check-label">
                                            <i class="fas fa-check-circle text-success"></i> Active
                                        </label>
                                    </div>
                                    <small class="text-muted">Inactive grades will not be used in the system.</small>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Grade
                            </button>
                            <a href="{{ route('grading.scale') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection