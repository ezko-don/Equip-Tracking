@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Error</div>

                <div class="card-body">
                    <div class="alert alert-danger">
                        {{ $message ?? 'An error occurred.' }}
                    </div>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        Return to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 