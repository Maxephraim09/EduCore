@extends('layouts.app')

@section('title', 'Create Inquiry')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('inquiries.index') }}">Inquiries</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">New Inquiry</div>
        <div class="card-body">
            <form method="POST" action="{{ route('inquiries.store') }}">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-control" required>
                            <option value="low" @selected(old('priority')==='low')>low</option>
                            <option value="medium" @selected(old('priority')==='medium')>medium</option>
                            <option value="high" @selected(old('priority')==='high')>high</option>
                        </select>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control" value="{{ old('subject') }}" required>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-control" rows="5" required>{{ old('message') }}</textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
    </div>
</div>
@endsection

