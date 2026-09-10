@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-3">Jobs</h1>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if(session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search">
        </div>
        <div class="col-md-3">
            <select name="department" class="form-select">
                <option value="">All departments</option>
                @foreach(($departments ?? []) as $dep)
                    <option value="{{ $dep }}" @selected(request('department') === $dep)>{{ $dep }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="job_type" class="form-select">
                <option value="">All types</option>
                @foreach(($jobTypes ?? []) as $type)
                    <option value="{{ $type }}" @selected(request('job_type') === $type)>{{ $type }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Filter</button>
        </div>
    </form>

    <div class="list-group">
        @forelse($jobs as $job)
            <a class="list-group-item list-group-item-action" href="{{ route('recruitment.jobs.show', $job->id) }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="fw-bold">{{ $job->title }}</div>
                        <div class="text-muted" style="font-size: 13px;">{{ $job->department }} • Deadline: {{ $job->application_deadline?->format('Y-m-d') }}</div>
                    </div>
                    @if($job->is_featured)
                        <span class="badge bg-success">Featured</span>
                    @endif
                </div>
            </a>
        @empty
            <div class="text-muted">No jobs found.</div>
        @endforelse
    </div>

    <div class="mt-3">
        {{ $jobs->withQueryString()->links() }}
    </div>
</div>
@endsection

