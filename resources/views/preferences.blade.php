@extends('layouts.panel')

@section('content')

<!-- DataTables Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Your Preferences</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <form action="{{ route('preferences') }}" method="POST">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="txtPrecedingLabel" name="precedingLabel" value="{{ $preferences->preceding_label }}">
                        <label for="txtPrecedingLabel"><small>Set a text to be sent preceding the song name. Ex: "Current Song:"</small></label>
                    </div>
                    
                    <div class="form-floating form-check">
                        <input class="form-check-input" type="radio" name="artistSongOrder" id="ckbSongPreceding" value="songNamePreceding" {{ $preferences->artist_song_order == 'songNamePreceding' ? 'checked' : '' }}>
                        <label class="form-check-label" for="ckbSongPreceding">
                            Song Name - Artist Name
                        </label>                        
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="artistSongOrder" id="ckbArtistPreceding" value="artistNamePreceding" {{ $preferences->artist_song_order == 'artistNamePreceding' ? 'checked' : '' }}>
                        <label class="form-check-label" for="ckbArtistPreceding">
                            Artist Name - Song Name
                        </label>
                    </div>                    
                    <div class="form-floating mb-3">
                        <small class="form-check-label">
                            Choose the format that the song name and artist should be sent as.
                        </small>                        
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
@endsection
