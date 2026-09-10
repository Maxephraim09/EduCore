@extends('layouts.app')

@section('title', 'Visitor Details')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('visitors.index') }}">Visitors</a></li>
    <li class="breadcrumb-item active">Visitor #{{ $visitor->id }}</li>
@endsection

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">Visitor #{{ $visitor->id }}</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <strong>Name:</strong> {{ $visitor->name }}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Phone:</strong> {{ $visitor->phone }}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Email:</strong> {{ $visitor->email ?? '—' }}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>ID Card:</strong> {{ $visitor->id_card ?? '—' }}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Person To See:</strong> {{ $visitor->person_to_see ?? '—' }}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Purpose:</strong> {{ $visitor->purpose ?? '—' }}
                </div>

                <div class="col-md-6 mb-3">
                    <strong>Status:</strong> {{ $visitor->status }}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Check In:</strong> {{ $visitor->check_in ?? '—' }}
                </div>
                <div class="col-md-6 mb-3">
                    <strong>Check Out:</strong> {{ $visitor->check_out ?? '—' }}
                </div>
            </div>

            @if($visitor->status !== 'completed')
                <hr>
                <form method="POST" action="{{ route('visitors.checkout', $visitor) }}">
                    @csrf
                    <button type="submit" class="btn btn-success">Check Out</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection

