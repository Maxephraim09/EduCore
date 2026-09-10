@extends('layouts.app')

@section('title', 'Manage Blog')

@section('content')
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div><h2 class="mb-1">Blog management</h2><p class="text-muted mb-0">Review, publish and maintain school stories.</p></div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.blog.categories') }}" class="btn btn-outline-secondary">Categories & tags</a>
            @if(auth()->user()->hasPermission('manage-blog'))
                <a href="{{ route('admin.blog.comments') }}" class="btn btn-outline-secondary">Manage comments</a>
                <a href="{{ route('admin.blog.presentation') }}" class="btn btn-outline-secondary">Hero & ads</a>
                <a href="{{ route('admin.blog.subscribers') }}" class="btn btn-outline-secondary">Subscribers</a>
            @endif
            <a href="{{ route('admin.blog.create') }}" class="btn btn-primary">New post</a>
        </div>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    <div class="card shadow-sm"><div class="table-responsive"><table class="table mb-0 align-middle"><thead><tr><th>Post</th><th>Author</th><th>Status</th><th>Reach</th><th>Public URL</th><th class="text-end">Actions</th></tr></thead><tbody>
    @forelse($posts as $post)
        <tr><td>@if($post->featured_image)<img src="{{ asset('storage/' . $post->featured_image) }}" alt="" class="rounded me-2" style="width:58px;height:42px;object-fit:cover">@endif<strong>{{ $post->title }}</strong><br><small class="text-muted">{{ $post->category->name ?? 'Uncategorised' }}</small></td><td>{{ $post->author->name ?? 'Deleted user' }}</td><td><span class="badge bg-{{ $post->status === 'approved' ? 'success' : ($post->status === 'rejected' ? 'danger' : 'warning') }}">{{ ucfirst($post->status) }}</span>@if($post->rejection_reason)<br><small class="text-danger">{{ $post->rejection_reason }}</small>@endif</td><td>{{ $post->views }} views<br><small>{{ $post->approved_comments_count }} approved comments</small></td><td><div class="input-group input-group-sm" style="min-width:220px"><input id="post-url-{{ $post->id }}" class="form-control" readonly value="{{ route('blog.public.show', $post) }}"><button class="btn btn-outline-secondary" type="button" onclick="copyBlogUrl('post-url-{{ $post->id }}', this)" title="Copy public URL"><i class="fas fa-copy"></i></button></div></td><td class="text-end text-nowrap"><a href="{{ route('blog.public.show', $post) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="View public post"><i class="fas fa-external-link-alt"></i></a> <a href="{{ route('admin.blog.edit', $post) }}" class="btn btn-sm btn-outline-primary">Edit</a>@if(auth()->user()->hasPermission('approve-blog-posts') && $post->status !== 'approved')<form method="POST" action="{{ route('admin.blog.approve', $post) }}" class="d-inline">@csrf<button class="btn btn-sm btn-success">Approve</button></form><form method="POST" action="{{ route('admin.blog.reject', $post) }}" class="d-inline">@csrf<input type="hidden" name="rejection_reason" value="Please revise and resubmit."><button class="btn btn-sm btn-outline-danger">Reject</button></form>@endif @if(auth()->user()->hasPermission('manage-blog'))<form method="POST" action="{{ route('admin.blog.destroy', $post) }}" class="d-inline" onsubmit="return confirm('Delete this blog post?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" title="Delete post"><i class="fas fa-trash"></i></button></form>@endif</td></tr>
    @empty<tr><td colspan="6" class="text-center py-4">No posts found.</td></tr>@endforelse
    </tbody></table></div></div><div class="mt-3">{{ $posts->links() }}</div>
</div>
@push('scripts')<script>function copyBlogUrl(id,button){navigator.clipboard.writeText(document.getElementById(id).value).then(()=>{const icon=button.querySelector('i');icon.className='fas fa-check';setTimeout(()=>icon.className='fas fa-copy',1200);});}</script>@endpush
@endsection
