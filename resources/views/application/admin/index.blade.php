@extends('layouts.app')

@section('title', 'Manage Applications')

@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
<li class="breadcrumb-item active">Applications</li>
@endsection

@push('styles')
<style>
    /* Clean, modern stat cards */
    .stat-card {
        background: #fff;
        border-radius: 12px;
        padding: 18px 20px;
        border-left: 6px solid transparent;
        box-shadow: 0 6px 18px rgba(15,23,42,0.06);
        transition: transform .18s ease, box-shadow .18s ease;
        position: relative;
        overflow: hidden;
    }
    .stat-card:hover { transform: translateY(-6px); box-shadow: 0 12px 32px rgba(15,23,42,0.08); }
    .stat-number { font-size: 26px; font-weight: 700; color: #0f172a; }
    .stat-label { font-size: 12px; color: #64748b; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; }
    .stat-icon { font-size: 36px; opacity: .08; position: absolute; right: 16px; top: 12px; }
    .stat-card.pending { border-left-color: #f59e0b; }
    .stat-card.under_review { border-left-color: #3b82f6; }
    .stat-card.approved { border-left-color: #10b981; }
    .stat-card.admitted { border-left-color: #8b5cf6; }
    .stat-card.rejected { border-left-color: #ef4444; }
    .stat-card.paid { border-left-color: #06b6d4; }

    .filter-section { background: #fbfdff; border-radius: 12px; padding: 16px; margin-bottom: 20px; border: 1px solid rgba(99,102,241,0.04); }

    /* Table improvements */
    .table thead th { background: #fafafa; border-bottom: 2px solid #eef2ff; }
    .table tbody tr:hover { background: #fbfcff; }
    .table td, .table th { vertical-align: middle; }

    /* Responsive card view for smaller devices */
    @media (max-width: 767px) {
        .table-responsive { display: block; }
        .table { display: none; }
        .application-card { display: block; margin-bottom: 12px; }
    }
    @media (min-width: 768px) {
        .application-card { display: none; }
    }

    .application-card { background: #fff; border-radius: 12px; padding: 14px; box-shadow: 0 6px 18px rgba(15,23,42,0.04); }

    /* Badges (Bootstrap 5 friendly) */
    .badge-status { padding: .45em .6em; border-radius: .5rem; font-weight:600; }
    .badge-status.pending { background:#fef3c7; color:#92400e; }
    .badge-status.under_review { background:#dbeafe; color:#1e40af; }
    .badge-status.approved { background:#dcfce7; color:#065f46; }
    .badge-status.admitted { background:#ede9fe; color:#5b21b6; }
    .badge-status.rejected { background:#fee2e2; color:#991b1b; }
    .badge-status.paid { background:#cffafe; color:#075985; }
</style>
@endpush

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

            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card pending position-relative">
                        <span class="stat-icon"><i class="fas fa-clock"></i></span>
                        <div class="stat-number">{{ $stats['pending'] }}</div>
                        <div class="stat-label">Pending</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card under_review position-relative">
                        <span class="stat-icon"><i class="fas fa-search"></i></span>
                        <div class="stat-number">{{ $stats['under_review'] }}</div>
                        <div class="stat-label">Under Review</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card approved position-relative">
                        <span class="stat-icon"><i class="fas fa-check-circle"></i></span>
                        <div class="stat-number">{{ $stats['approved'] }}</div>
                        <div class="stat-label">Approved</div>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="stat-card admitted position-relative">
                        <span class="stat-icon"><i class="fas fa-user-check"></i></span>
                        <div class="stat-number">{{ $stats['admitted'] }}</div>
                        <div class="stat-label">Admitted</div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4 col-sm-6">
                    <div class="stat-card rejected position-relative">
                        <span class="stat-icon"><i class="fas fa-times-circle"></i></span>
                        <div class="stat-number">{{ $stats['rejected'] }}</div>
                        <div class="stat-label">Rejected</div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="stat-card paid position-relative">
                        <span class="stat-icon"><i class="fas fa-credit-card"></i></span>
                        <div class="stat-number">{{ $stats['paid'] }}</div>
                        <div class="stat-label">Payment Completed</div>
                    </div>
                </div>
            </div>

            <!-- Filter Section -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="stat-card position-relative d-flex justify-content-between align-items-center" style="padding:12px 20px;">
                        <div>
                            <div class="stat-label">Application Portal</div>
                            <div class="stat-number mt-1">{{ url(route('application.portal.index', [], false)) }}</div>
                        </div>
                        <div>
                            <button class="btn btn-outline-secondary btn-sm" id="copyPortalLink" data-url="{{ url(route('application.portal.index', [], false)) }}">
                                <i class="fas fa-copy me-1"></i>Copy Portal Link
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="filter-section">
                <form action="{{ route('application.admin.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="under_review" {{ request('status') == 'under_review' ? 'selected' : '' }}>Under Review</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                            <option value="admitted" {{ request('status') == 'admitted' ? 'selected' : '' }}>Admitted</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="payment_status" class="form-select">
                            <option value="">All Payment Status</option>
                            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="search" class="form-control" placeholder="Search by name, email, or application number..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-1"></i>Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Applications Table -->
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Applications
                        <span class="badge bg-light text-dark ms-2">{{ $applications->total() }}</span>
                    </h5>
                    <div>
                        <button type="button" class="btn btn-light btn-sm" onclick="window.location.reload()">
                            <i class="fas fa-sync me-1"></i>Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    @if($applications->count() > 0)
                        <div class="table-responsive">
                            <form action="{{ route('application.admin.bulk-action') }}" method="POST" id="bulkActionForm">
                                @csrf
                                <table class="table table-hover table-striped align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="40">
                                                <input type="checkbox" id="selectAll" onchange="toggleAllCheckboxes(this)">
                                            </th>
                                            <th>#</th>
                                            <th>Application No.</th>
                                            <th>Applicant</th>
                                            <th>Email</th>
                                            <th>Class</th>
                                            <th>Status</th>
                                            <th>Payment</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($applications as $index => $application)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="application_ids[]" value="{{ $application->id }}" class="application-checkbox">
                                                </td>
                                                <td>{{ $applications->firstItem() + $index }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <a href="{{ route('application.success', $application->application_number) }}" target="_blank" class="me-2">
                                                            <span class="badge bg-secondary text-white">{{ $application->application_number }}</span>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-secondary copy-app-link" data-url="{{ route('application.success', $application->application_number) }}" title="Copy application link">
                                                            <i class="fas fa-copy"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td>
                                                    <strong>{{ $application->full_name }}</strong>
                                                    <br>
                                                    <small class="text-muted">{{ $application->phone }}</small>
                                                </td>
                                                <td>{{ $application->email }}</td>
                                                <td>
                                                    <span class="badge bg-info text-dark">{{ $application->class->full_class_name ?? 'N/A' }}</span>
                                                </td>
                                                <td>
                                                    @if($application->status == 'pending')
                                                        <span class="badge-status pending">Pending</span>
                                                    @elseif($application->status == 'under_review')
                                                        <span class="badge-status under_review">Under Review</span>
                                                    @elseif($application->status == 'approved')
                                                        <span class="badge-status approved">Approved</span>
                                                    @elseif($application->status == 'admitted')
                                                        <span class="badge-status admitted">Admitted</span>
                                                    @elseif($application->status == 'rejected')
                                                        <span class="badge-status rejected">Rejected</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($application->payment_status == 'pending')
                                                        <span class="badge-status pending">Pending</span>
                                                    @elseif($application->payment_status == 'paid')
                                                        <span class="badge-status paid">Paid</span>
                                                    @else
                                                        <span class="badge-status rejected">Failed</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small>{{ $application->created_at->format('d/m/Y') }}</small>
                                                    <br>
                                                    <small class="text-muted">{{ $application->created_at->format('h:i A') }}</small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group" aria-label="Actions">
                                                        <a href="{{ route('application.admin.details', $application->id) }}" 
                                                           class="btn btn-outline-primary" title="View Details">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>

                                {{-- Mobile card view (visible on small screens) --}}
                                <div class="d-md-none mt-3">
                                    @foreach($applications as $application)
                                        <div class="application-card">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <div class="mb-1"><strong>{{ $application->full_name }}</strong> <small class="text-muted">• {{ $application->application_number }}</small></div>
                                                    <div class="small text-muted">{{ $application->email }} • {{ $application->phone }}</div>
                                                    <div class="mt-2">
                                                        <span class="badge-status {{ $application->status }}">{{ ucfirst(str_replace('_',' ', $application->status)) }}</span>
                                                        <span class="badge-status {{ $application->payment_status }} ms-2">{{ ucfirst($application->payment_status) }}</span>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <div class="mb-2 small text-muted">{{ $application->created_at->format('d/m/Y') }}</div>
                                                    <a href="{{ route('application.admin.details', $application->id) }}" class="btn btn-sm btn-info">View</a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="btn-group">
                                            <select name="action" class="form-select" style="width: auto;" required>
                                                <option value="">Bulk Action</option>
                                                <option value="under_review">Mark Under Review</option>
                                                <option value="approve">Approve</option>
                                                <option value="reject">Reject</option>
                                            </select>
                                            <button type="submit" class="btn btn-primary" onclick="return confirm('Are you sure you want to perform this action?')">
                                                <i class="fas fa-check me-1"></i>Apply
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6 text-end">
                                        {{ $applications->links() }}
                                    </div>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3 d-block"></i>
                            <p class="text-muted">No applications found.</p>
                            <p class="text-muted small">Applications will appear here when students apply.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleAllCheckboxes(checkbox) {
        document.querySelectorAll('.application-checkbox').forEach(cb => {
            cb.checked = checkbox.checked;
        });
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function(){
        // Copy portal link
        document.getElementById('copyPortalLink')?.addEventListener('click', function(e){
            const url = e.currentTarget.getAttribute('data-url');
            navigator.clipboard.writeText(url).then(()=>{
                alert('Portal link copied to clipboard');
            }).catch(()=>{ alert('Unable to copy link'); });
        });

        // Copy individual application link
        document.querySelectorAll('.copy-app-link').forEach(btn => {
            btn.addEventListener('click', function(){
                const url = this.getAttribute('data-url');
                navigator.clipboard.writeText(url).then(()=>{
                    alert('Application link copied');
                }).catch(()=>{ alert('Unable to copy link'); });
            });
        });
    });
</script>
@endpush
@endsection