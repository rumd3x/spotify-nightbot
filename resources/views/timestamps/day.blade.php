@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('timestamp.month', [$header['current']->format('Y'), $header['current']->format('m')]) }}">
                        < Back to {{ $header['current']->format('F') }}
                    </a>
                </div>

                <div class="card-body">
                    <h3 class="text-center">
                        <a class="float-left" href="{{ url('/timestamps/day/'.$header['prev']->format('Y-m-d')) }}">{{ $header['prev']->format('l F dS, Y') }}</a>
                        <b>{{ $header['current']->format('l F dS, Y') }}</b>
                        <a class="float-right" href="{{ url('/timestamps/day/'.$header['next']->format('Y-m-d')) }}">{{ $header['next']->format('l F dS, Y') }}</a>
                    </h3>
                    <hr class="divider">

                    <div class="row">
                        <div class="col-md-12">

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <p><b>Total Time:</b> {{ floor($totalTime / 60) }} Hour(s) and {{ $totalTime - floor($totalTime / 60) * 60 }} Minute(s)</p>
                            <p><b>Centesimal Time:</b> {{ sprintf("%.2f", $totalTime / 60) }} Hour(s)</p>
                            <form action="{{ route('timestamp.insert') }}" method="POST" class="form-inline">
                                <input class="form-control mb-2 mr-sm-2" type="text" name="hour" id="txtHour" onfocus="this.value = ''" onblur="if (this.value == '') { this.value = '{{ Carbon\Carbon::now()->format('H:i') }}' }" value="{{ Carbon\Carbon::now()->format('H:i') }}">
                                <input type="hidden" name="date" value="{{ $header['current']->format('Y-m-d') }}">
                                <select name="entry" id="cmbEntry" class="form-control mb-2 mr-sm-2">
                                    <option value="1" selected>Entry</option>
                                    <option value="0">Exit</option>
                                </select>
                                <button type="submit" class="btn btn-primary mb-2">Insert Timestamp</button>
                                @csrf
                            </form>
                            <ul class="list-group list-group-flush">
                                @forelse ($timestamps as $t)
                                    <form action="{{ route('timestamp.delete', [$t->id]) }}" method="post">
                                        <li class="list-group-item list-group-item-action {{ $t->entry ? '' : 'list-group-item-secondary' }}">
                                            {{ $t->entry ? 'Entered' : 'Exited'}} at {{ Carbon\Carbon::parse("$t->date $t->time")->format('g:iA') }}
                                            <button type="submit" class="btn btn-sm btn-danger float-right">Remove</button>
                                            @csrf
                                        </li>
                                    </form>
                                @empty
                                    <li class="list-group-item list-group-item-action">
                                        No Timestamps today
                                    </li>
                                @endforelse
                            </ul>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection
