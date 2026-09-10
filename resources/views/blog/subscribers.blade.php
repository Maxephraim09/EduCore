@extends('layouts.app')

@section('title', 'Blog Subscribers')

@section('content')
<div class="container-fluid"><div class="d-flex justify-content-between align-items-center mb-3"><div><h2 class="mb-1">Blog subscribers</h2><p class="text-muted mb-0">People subscribed to school blog updates.</p></div><a href="{{ route('admin.blog.index') }}" class="btn btn-outline-secondary">Back to blog</a></div><div class="card shadow-sm"><div class="table-responsive"><table class="table mb-0"><thead><tr><th>Name</th><th>Email</th><th>Status</th><th>Subscribed</th></tr></thead><tbody>@forelse($subscribers as $subscriber)<tr><td>{{ $subscriber->name ?: 'Not provided' }}</td><td>{{ $subscriber->email }}</td><td><span class="badge bg-{{ $subscriber->is_active ? 'success' : 'secondary' }}">{{ $subscriber->is_active ? 'Active' : 'Inactive' }}</span></td><td>{{ $subscriber->created_at->format('M d, Y') }}</td></tr>@empty<tr><td colspan="4" class="text-center py-4">No subscribers yet.</td></tr>@endforelse</tbody></table></div></div><div class="mt-3">{{ $subscribers->links() }}</div></div>
@endsection
