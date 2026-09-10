@extends('layouts.app')

@section('title', 'Blog Hero and Ads')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h2 class="mb-1">Blog hero, social links and ads</h2>
            <p class="text-muted mb-0">Control the public blog presentation from one place.</p>
        </div>
        <a href="{{ route('admin.blog.index') }}" class="btn btn-outline-secondary">Back to blog</a>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card shadow-sm"><div class="card-body">
                <h5>Hero and social links</h5>
                <form method="POST" enctype="multipart/form-data" action="{{ route('admin.blog.presentation.update') }}">
                    @csrf @method('PUT')
                    <label class="form-label">Hero title</label>
                    <input name="hero_title" class="form-control mb-2" value="{{ $settings->hero_title }}" required>
                    <label class="form-label">Hero text</label>
                    <textarea name="hero_text" class="form-control mb-2" rows="3">{{ $settings->hero_text }}</textarea>
                    <label class="form-label">Hero image</label>
                    <input name="hero_image" type="file" accept="image/jpeg,image/png,image/webp" class="form-control mb-2">
                    @if($settings->hero_image)<img src="{{ asset('storage/' . $settings->hero_image) }}" class="rounded mb-3" style="max-width:100%;height:120px;object-fit:cover" alt="Current hero image">@endif
                    <div class="row g-2">
                        @foreach(['facebook_url' => 'Facebook URL', 'instagram_url' => 'Instagram URL', 'youtube_url' => 'YouTube URL', 'x_url' => 'X URL'] as $field => $label)
                            <div class="col-md-6"><label class="form-label">{{ $label }}</label><input name="{{ $field }}" type="url" class="form-control" value="{{ $settings->$field }}"></div>
                        @endforeach
                    </div>
                    <button class="btn btn-primary mt-3">Save presentation</button>
                </form>
            </div></div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm"><div class="card-body">
                <h5>Create advertisement</h5>
                <form method="POST" enctype="multipart/form-data" action="{{ route('admin.blog.ads.store') }}">
                    @csrf
                    <input name="title" class="form-control mb-2" placeholder="Ad title" required>
                    <textarea name="description" class="form-control mb-2" placeholder="Description"></textarea>
                    <input name="url" type="url" class="form-control mb-2" placeholder="Destination URL">
                    <input name="image" type="file" accept="image/jpeg,image/png,image/webp" class="form-control mb-2">
                    <div class="form-check"><input name="is_active" value="1" type="checkbox" class="form-check-input" checked><label class="form-check-label">Active</label></div>
                    <button class="btn btn-primary mt-3">Add advertisement</button>
                </form>
                <hr>
                <h5>Existing ads</h5>
                @forelse($ads as $ad)
                    <div class="border-top py-3">
                        <form method="POST" enctype="multipart/form-data" action="{{ route('admin.blog.ads.update', $ad) }}">
                            @csrf @method('PUT')
                            <input name="title" class="form-control mb-2" value="{{ $ad->title }}" required>
                            <textarea name="description" class="form-control mb-2">{{ $ad->description }}</textarea>
                            <input name="url" type="url" class="form-control mb-2" value="{{ $ad->url }}" placeholder="Destination URL">
                            <input name="image" type="file" accept="image/jpeg,image/png,image/webp" class="form-control mb-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <label><input name="is_active" value="1" type="checkbox" @checked($ad->is_active)> Active</label>
                                <button class="btn btn-sm btn-outline-primary">Update</button>
                            </div>
                        </form>
                        <form method="POST" action="{{ route('admin.blog.ads.destroy', $ad) }}" class="mt-2" onsubmit="return confirm('Delete this ad?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                        </form>
                    </div>
                @empty
                    <p class="text-muted">No advertisements yet.</p>
                @endforelse
            </div></div>
        </div>
    </div>
</div>
@endsection
