@extends('layouts.app')

@section('title', 'Frontdesk Dashboard')

@section('breadcrumb')
    <li class="breadcrumb-item active">Frontdesk Dashboard</li>
@endsection

@section('content')
<div class="fade-in">
    <div class="row mb-4">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card bg-primary text-white h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small mb-1">Today's Visitors</div>
                            <div class="h2 mb-0">{{ $todayVisitors ?? 0 }}</div>
                            <div class="small mt-2"><i class="fas fa-user"></i> {{ date('d M Y') }}</div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3"><i class="fas fa-user fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card bg-warning text-white h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small mb-1">Pending Registrations</div>
                            <div class="h2 mb-0">{{ $pendingRegistrations ?? 0 }}</div>
                            <div class="small mt-2"><i class="fas fa-user-plus"></i> Awaiting</div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3"><i class="fas fa-user-plus fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card bg-info text-white h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-uppercase small mb-1">Total Inquiries</div>
                            <div class="h2 mb-0">{{ $totalInquiries ?? 0 }}</div>
                            <div class="small mt-2"><i class="fas fa-question-circle"></i> Received</div>
                        </div>
                        <div class="bg-white bg-opacity-25 rounded-circle p-3"><i class="fas fa-question-circle fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">Recent Visitors</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Person To See</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentVisitors ?? [] as $visitor)
                                    <tr>
                                        <td>{{ $visitor->name }}</td>
                                        <td>{{ $visitor->person_to_see }}</td>
                                        <td>{{ $visitor->status }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted">No visitors yet</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header">Recent Inquiries</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-striped align-middle">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentInquiries ?? [] as $inquiry)
                                    <tr>
                                        <td>{{ $inquiry->subject }}</td>
                                        <td>{{ $inquiry->priority }}</td>
                                        <td>{{ $inquiry->status }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted">No inquiries yet</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

