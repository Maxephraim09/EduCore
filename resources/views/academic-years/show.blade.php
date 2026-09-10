@extends('layouts.app')

@section('title','Academic Year Details')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-0">{{ $academicYear->name }}</h4>
            <small class="text-muted">{{ $academicYear->year_range }} • {{ $academicYear->duration }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('academic-years.edit',$academicYear) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('terms.create',$academicYear) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus-circle me-2"></i>Add Term
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="text-muted">Total Terms</div>
                    <div class="h3 mb-0">{{ $stats['total_terms'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="text-muted">Active Terms</div>
                    <div class="h3 mb-0">{{ $stats['active_terms'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="text-muted">Status</div>
                    <div class="h3 mb-0">
                        @if($academicYear->is_current)
                            <span class="badge bg-success">Current</span>
                        @else
                            <span class="badge bg-secondary">Not Current</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mt-3">
        <div class="card-body">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Sequence</th>
                        <th>Name</th>
                        <th>Label</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Current</th>
                        <th style="width: 290px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($terms as $term)
                    <tr>
                        <td>{{ $term->sequence }}</td>
                        <td>{{ $term->name }}</td>
                        <td>{{ $term->term_label }}</td>
                        <td>{{ optional($term->start_date)->format('Y-m-d') }}</td>
                        <td>{{ optional($term->end_date)->format('Y-m-d') }}</td>
                        <td>
                            @if($term->is_current)
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('terms.edit',$term) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>

                                @if(!$term->is_current)
                                    <form method="POST" action="{{ route('terms.set-current',$term) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i>
                                            Set Current
                                        </button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('terms.destroy',$term) }}" onsubmit="return confirm('Delete this term?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">No terms found. Add your first term.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

