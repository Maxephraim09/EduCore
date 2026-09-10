@extends('layouts.portal-dashboard')

@section('title','Select Class Category')
@section('page_title','Choose Category')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Choose Class Category</div>
                <div class="card-body">
                    <form action="{{ route('application.select-category.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Class Category</label>
                            <select name="category_id" class="form-control" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-primary">Continue</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
