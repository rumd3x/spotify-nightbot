@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    <h2>
                        <a href="{{ route('timestamp.day', $today->format('Y-m-d')) }}">
                            <b>Today:</b> {{ $today->format('l F dS, Y') }}
                        </a>
                    </h2>
                    <hr class="divider">
                    <p>Last Entered: {{ $lastEnteredString  }}</p>
                    <p>Last Exited: {{ $lastExitedString  }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
