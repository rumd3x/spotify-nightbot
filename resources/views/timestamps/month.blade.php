@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <a href="{{ route('timestamp.months') }}">
                        < Back to Months
                    </a>
                </div>

                <div class="card-body">
                    <h3 class="text-center">
                        <a class="float-left" href="{{ $header['prev']['url'] }}">{{ $header['prev']['display'] }}</a>
                        <b>{{ $header['current']['display'] }}</b>
                        <a class="float-right" href="{{ $header['next']['url'] }}">{{ $header['next']['display'] }}</a>
                    </h3>
                    <hr class="divider">

                    <p><b>Total Time:</b> {{ floor($totalTime / 60) }} Hour(s) and {{ $totalTime - floor($totalTime / 60) * 60 }} Minute(s)</p></p>
                    @if ($estimatedTime > 0)
                    <p><b>Estimated Time:</b> {{ floor($estimatedTime / 60) }} Hour(s) and {{ $estimatedTime - floor($estimatedTime / 60) * 60 }} Minute(s)</p></p>
                    @endif
                    <div class="row">
                        <table class="table table-striped table-bordered text-center">
                            <thead  class="thead-dark">
                                <tr>
                                    @foreach ($weekdays as $w)
                                        <th scope="col">{{ $w }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $week)
                                    <tr scope="row">
                                    @for ($i = 0; $i < $offset; $i++)
                                        <td> - </td>
                                    @endfor
                                    @php
                                        $offset = 0
                                    @endphp
                                        @foreach($week as $day)
                                            <td>
                                                <a href="{{ route('timestamp.day', $day->format('Y-m-d')) }}"
                                                   class="{{ $day == $today ? 'font-weight-bold' : 'font-weight-normal' }}
                                                   {{ App\Utils\Calculator::stateClass($day, Auth::user()) }}">
                                                    {{ $day->day }}
                                                </a>
                                            </td>
                                        @endforeach
                                        @if ($day->dayOfWeek === 6)
                                            @continue
                                        @endif
                                        @for ($i = 0; $i < 7-count($week); $i++)
                                            <td> - </td>
                                        @endfor
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
