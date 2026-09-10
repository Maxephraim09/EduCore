@extends('layouts.app')

@section('title', 'Certificates')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
    <li class="breadcrumb-item active">Certificates</li>
@endsection

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-certificate me-2"></i>Certificates</h5>
            <a href="{{ route('certificates.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Add Certificate
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            
            <div class="table-responsive">
                <table class="table table-bordered" id="certificatesTable">
                    <thead>
                        <tr>
                            <th>Certificate No</th>
                            <th>Student</th>
                            <th>Type</th>
                            <th>Issue Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($certificates as $certificate)
                        <tr>
                            <td>{{ $certificate->certificate_number }}</td>
                            <td>{{ $certificate->student->full_name }}</td>
                            <td>{!! $certificate->type_label !!}</td>
                            <td>{{ $certificate->issue_date->format('d M Y') }}</td>
                            <td>{!! $certificate->status_badge !!}</td>
                            <td>
                                <div class="btn-group">
                                    <a href="{{ route('certificates.show', $certificate->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('certificates.generate', $certificate->id) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-download"></i>
                                    </a>
                                    <form action="{{ route('certificates.destroy', $certificate->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $certificates->links() }}
            </div>
        </div>
    </div>
</div>

@push('styles')
<link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        $('#certificatesTable').DataTable({
            pageLength: 10,
            responsive: true,
            order: [[0, 'desc']]
        });
    });
</script>
@endpush
@endsection