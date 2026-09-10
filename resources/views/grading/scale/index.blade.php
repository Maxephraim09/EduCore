@extends('layouts.app')

@section('title', 'Manage Grade Scale')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item"><a href="{{ route('grading.index') }}">Grading Settings</a></li>
<li class="breadcrumb-item active">Grade Scale</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
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

            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-table me-2"></i>Grade Scale Management
                    </h5>
                    <a href="{{ route('grading.scale.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus-circle me-1"></i> Add New Grade
                    </a>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Grade Scale:</strong> Define the score ranges and corresponding grades for the system.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Grade</th>
                                    <th>Score Range</th>
                                    <th>Remark</th>
                                    <th>Color</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($gradeScales as $index => $grade)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="text-center">
                                            <span class="badge bg-{{ $grade->color ?? 'secondary' }} p-2" 
                                                  style="font-size: 16px; min-width: 40px;">
                                                {{ $grade->grade }}
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            {{ $grade->min_score }}% - {{ $grade->max_score }}%
                                        </td>
                                        <td>{{ $grade->remark }}</td>
                                        <td>
                                            <span class="badge bg-{{ $grade->color ?? 'secondary' }}">
                                                {{ $grade->color ?? 'secondary' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($grade->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-danger">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('grading.scale.edit', $grade->id) }}" 
                                                   class="btn btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('grading.scale.destroy', $grade->id) }}" 
                                                      method="POST" 
                                                      onsubmit="return confirm('Are you sure you want to delete this grade scale?')"
                                                      style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger" title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4">
                                            <i class="fas fa-table fa-3x text-muted mb-3 d-block"></i>
                                            <p class="text-muted mb-0">No grade scales configured.</p>
                                            <a href="{{ route('grading.scale.create') }}" class="btn btn-primary mt-3">
                                                <i class="fas fa-plus-circle me-2"></i>Add First Grade
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <a href="{{ route('grading.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Grading Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection