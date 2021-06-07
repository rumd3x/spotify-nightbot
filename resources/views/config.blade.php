@extends('layouts.panel')

@section('content')

<!-- DataTables Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Your Configurations</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <form action="{{ route('config') }}" method="POST">
                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="ckbEnableSpotifyPolling" name="spotifyPollingEnabled" value="1" {{ $config->spotify_polling_enabled ? 'checked' : '' }}>
                        <label class="form-check-label" for="ckbEnableSpotifyPolling">Keep my Spotify playback information up to date</label>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="ckbEnableNightbotAlerts" name="nightbotAlertsEnabled" value="1" {{ $config->nightbot_alerts_enabled ? 'checked' : '' }}>
                        <label class="form-check-label" for="ckbEnableNightbotAlerts">Send Nightbot alerts whenever my playback information is updated</label>
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" class="form-check-input" id="ckbEnableNightbotCommand" name="nightbotCommandEnabled" value="1" {{ $config->nightbot_command_enabled ? 'checked' : '' }}>
                        <label class="form-check-label" for="ckbEnableNightbotCommand">Make available a <strong>!song</strong> command for your viewers</label>
                    </div>

                    @csrf
                    <div class=" mb-3">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Your Integrations</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">

                <div class="row mb-3">
                    <div class="col-md-1">
                        <img height="85" src="img/spotify.png" alt="Spotify">
                    </div>
                    <div class="col-md-11">
                        @if (empty(Auth::user()->integration->spotify_refresh_token))
                            Spotify Status: <b>Disconnected</b><br>
                            <a href="{{ route('spotify.login') }}" class="btn btn-primary">Connect</a>
                        @else
                            Spotify Status: <b>Connected</b><br>
                            <a href="{{ route('spotify.disconnect') }}" class="btn btn-danger">Disconnect</a>
                        @endif                        
                    </div>
                </div>

                <hr />

                <div class="row mb-3">
                    <div class="col-md-1">
                        <img height="85" src="img/nightbot.png" alt="Spotify">
                    </div>
                    <div class="col-md-11">
                    @if (empty(Auth::user()->integration->nightbot_refresh_token))
                        Nightbot Status: <b>Disconnected</b><br>
                        <a href="{{ route('nightbot.login') }}" class="btn btn-primary">Connect</a>
                    @else
                        Nightbot Status: <b>Connected</b><br>
                        <a href="{{ route('nightbot.disconnect') }}" class="btn btn-danger">Disconnect</a>
                        <a href="{{ route('nightbot.test') }}" class="btn btn-outline-primary">Send Test Message</a>
                    @endif
                        
                    </div>
                </div>                    
            </div>
        </div>
    </div>
</div>
@endsection
