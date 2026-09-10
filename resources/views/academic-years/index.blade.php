@extends('layouts.app')

@section('title','Academic Years')

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="mb-0">Academic Years</h4>
        <a href="{{ route('academic-years.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i> Add Year
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
                        <th>Name</th>
                        <th>Year Range</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Current</th>
                        <th style="width: 260px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($academicYears as $year)
                    <tr>
                        <td>{{ $year->name }}</td>
                        <td>{{ $year->year_range }}</td>
                        <td>{{ optional($year->start_date)->format('Y-m-d') }}</td>
                        <td>{{ optional($year->end_date)->format('Y-m-d') }}</td>
                        <td>
                            @if($year->is_current)
                                <span class="badge bg-success">Yes</span>
                            @else
                                <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-2 flex-wrap">
                                <a href="{{ route('academic-years.show',$year) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('academic-years.edit',$year) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>

                                @if(!$year->is_current)
                                    <form method="POST" action="{{ route('academic-years.set-current',$year) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success">
                                            <i class="fas fa-check"></i> Set Current
                                        </button>
                                    </form>
                                @endif

                                <form method="POST" action="{{ route('academic-years.destroy',$year) }}" onsubmit="return confirm('Delete this academic year?');">
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
                    <tr><td colspan="6" class="text-center text-muted">No academic years found.</td></tr>
                @endforelse
                </tbody>
            </table>

            <div class="mt-3">
                {{ $academicYears->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

