@extends('layouts.app')

@section('title','Terms')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <h4 class="mb-0">Terms • {{ $academicYear->name }}</h4>
            <small class="text-muted">Manage terms for this academic year.</small>
        </div>
        <a href="{{ route('terms.create',$academicYear) }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i> Add Term
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Sequence</th>
                        <th>Name</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Current</th>
                        <th style="width: 260px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($terms as $term)
                    <tr>
                        <td>{{ $term->sequence }}</td>
                        <td>{{ $term->name }}</td>
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
                                            <i class="fas fa-check"></i> Set Current
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
                    <tr><td colspan="6" class="text-center text-muted">No terms yet.</td></tr>
                @endforelse
                </tbody>
            </table>

            <a href="{{ route('academic-years.show',$academicYear) }}" class="btn btn-outline-secondary btn-sm mt-2">
                <i class="fas fa-arrow-left me-2"></i> Back to Year Details
            </a>
        </div>
    </div>
</div>
@endsection

