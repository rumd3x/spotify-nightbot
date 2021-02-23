@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Months - {{ $currentYear }}</div>

                <div class="card-body text-center">
                    <h3>
                        <a class="float-left" href="{{ url('/timestamps/'.$prevYear) }}">{{ $prevYear }}</a>
                        <b>{{ $currentYear }}</b>
                        <a class="float-right" href="{{ url('/timestamps/'.$nextYear) }}">{{ $nextYear }}</a>
                    </h3>
                    <div class="row">

                        @foreach ($months as $m => $month)
                            <div class="col-md-3 pt-5 pb-5 border rounded bg-light text-center">
                                <a href="{{ url('/timestamps/'.$currentYear.'/month/'.$m) }}">
                                    @if ($month == $currentMonth)
                                        <b>
                                    @endif
                                        {{ $month }}
                                    @if ($month == $currentMonth)
                                        </b>
                                    @endif
                                </a>
                            </div>
                        @endforeach

                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
