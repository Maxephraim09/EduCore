@extends('layouts.app')

@section('title', 'Blog Categories')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div><h2 class="mb-1">Blog categories and tags</h2><p class="text-muted mb-0">Organise posts for visitors and editors.</p></div>
        <a href="{{ route('admin.blog.index') }}" class="btn btn-outline-secondary">Back to blog</a>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    <div class="row g-4">
        <div class="col-lg-7"><div class="card shadow-sm"><div class="card-body">
            <h5>Create category</h5>
            <form method="POST" action="{{ route('admin.blog.categories.store') }}" class="row g-2 mb-4">@csrf<div class="col-md-4"><input name="name" class="form-control" placeholder="Category name" required></div><div class="col-md-5"><input name="description" class="form-control" placeholder="Description"></div><div class="col-md-3"><button class="btn btn-primary w-100">Create</button></div></form>
            <h5>Existing categories</h5>
            @forelse($categories as $category)
                <form method="POST" action="{{ route('admin.blog.categories.update', $category) }}" class="row g-2 align-items-center border-top py-2">@csrf @method('PUT')<div class="col-md-4"><input name="name" class="form-control form-control-sm" value="{{ $category->name }}" required></div><div class="col-md-4"><input name="description" class="form-control form-control-sm" value="{{ $category->description }}"></div><div class="col-md-2"><small class="text-muted">{{ $category->posts_count }} posts</small></div><div class="col-md-2 text-end"><button class="btn btn-sm btn-outline-primary" title="Update category"><i class="fas fa-save"></i></button><button form="delete-category-{{ $category->id }}" class="btn btn-sm btn-outline-danger" title="Delete category"><i class="fas fa-trash"></i></button></div></form><form id="delete-category-{{ $category->id }}" method="POST" action="{{ route('admin.blog.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category? Posts will remain available.')">@csrf @method('DELETE')</form>
            @empty
                <p class="text-muted">No categories yet.</p>
            @endforelse
        </div></div></div>
        <div class="col-lg-5"><div class="card shadow-sm"><div class="card-body"><h5>New tag</h5><form method="POST" action="{{ route('admin.blog.tags.store') }}" class="d-flex gap-2">@csrf<input name="name" class="form-control" placeholder="Tag name" required><button class="btn btn-primary">Save tag</button></form></div></div></div>
    </div>
</div>
@endsection
