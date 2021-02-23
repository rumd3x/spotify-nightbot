@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">App Settings</div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('app.settings') }}" method="POST" enctype="multipart/form-data">

                        @foreach ($inputs as $in)

                            @if ($in['type'] == 'file')
                                <div class="custom-file mb-4">
                                    <label class="custom-file-label" for="{{ $in['name'] }}">{{ ($in['display'] ? 'Current: '.$in['display'] : '') }}</label>
                                    <input type="file" class="custom-file-input" name="{{ $in['name'] }}" id="{{ $in['name'] }}">
                                    <small>{{ $in['description'] }}</small>
                                </div>
                                @continue
                            @endif

                            <div class="form-group">
                                <label for="{{ $in['name'] }}">{{ $in['display'] }}</label>
                                <input class="form-control{{ $errors->has($in['name']) ? ' is-invalid' : '' }}" type="{{ $in['type'] }}" name="{{ $in['name'] }}" id="{{ $in['name'] }}" value="{{ $errors->any() ? old($in['name']) : ($in['value'] ? $in['value'] : '') }}">
                            </div>

                        @endforeach

                        @csrf
                        <button class="btn btn-primary" type="submit">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/settings.js') }}"></script>
@endsection
