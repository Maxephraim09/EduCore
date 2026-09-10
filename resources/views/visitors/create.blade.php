@extends('layouts.app')

@section('title', 'Check In Visitor')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('visitors.index') }}">Visitors</a></li>
    <li class="breadcrumb-item active">Check In</li>
@endsection

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">Check In Visitor</div>
        <div class="card-body">
            <form method="POST" action="{{ route('visitors.store') }}">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ID Card</label>
                        <input type="text" name="id_card" class="form-control" value="{{ old('id_card') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Person To See</label>
                        <input type="text" name="person_to_see" class="form-control" value="{{ old('person_to_see') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Purpose</label>
                        <input type="text" name="purpose" class="form-control" value="{{ old('purpose') }}" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Check In Time</label>
                        <input type="time" name="check_in" class="form-control" value="{{ old('check_in') }}" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Check In</button>
            </form>
        </div>
    </div>
</div>
@endsection

