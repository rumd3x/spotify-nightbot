@extends('layouts.panel')

@section('content')

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Widget</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group row mb-3">
                    <label for="txtWidgetURL" class="col-md-1 col-form-label">Widget URL</label>
                    <div class="col-md-8">
                        <input type="text" readonly class="form-control" id="txtWidgetURL" value="{{ route('widget.box', ['id' => $widget->code]) }}">
                        <label for="txtWidgetURL"><small>Note: Widget URL contains sensitive information. It should not be shared with other users or sites.</small></label>
                    </div>
                    <div class="col-md-3">
                        <a type="button" href="#" onclick="event.preventDefault(); var copyText = document.getElementById('txtWidgetURL'); copyText.select(); copyText.setSelectionRange(0, 99999); document.execCommand('copy');" class="btn btn-secondary">Copy URL</a>
                        <a type="button" href="#" onclick="event.preventDefault(); window.open('{{ route('widget.box', ['id' => $widget->code]) }}', 'Spotify-Nightbot Widget Box', 'menubar=no,location=no,resizable=yes,scrollbars=no,status=no,width=850,height=200,top=100,left=100');" class="btn btn-secondary">Launch</a>
                    </div>
                </div>                
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Widget Customization</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <form action="{{ route('widget') }}" method="POST">
                    <div class="form-group row mb-3">
                        <label for="txtColor" class="col-md-1 col-form-label">Background Color</label>
                        <div class="col-md-10">
                            <input type="color" class="form-control" id="txtColor" name="backgroundColor" value="{{ $widget->background_color }}">
                            <label for="txtColor"><small>Note: This background color is for preview purposes only. It will not be shown in your streaming software.</small></label>
                        </div>
                    </div> 

                    <div class="form-group row mb-3">
                        <label for="txtColor" class="col-md-1 col-form-label">Text Color</label>
                        <div class="col-md-10">
                            <input type="color" class="form-control" id="txtColor" name="textColor" value="{{ $widget->text_color }}">
                        </div>
                    </div>  

                    <div class="form-group row mb-3">
                        <label for="txtFont" class="col-md-1 col-form-label">Font</label>
                        <div class="col-md-10">
                            <select type="color" class="form-control" id="txtFont" name="fontFamily">
                                @foreach ($fonts as $font)
                                    <option value="{{ $font["value"] }}" {{ $font["value"] == $widget->font_family ? 'selected' : '' }}>{{ $font["name"] }}</option> 
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group row mb-3">
                        <label for="txtWidgetURL" class="col-md-1 col-form-label">Font Size</label>
                        <div class="col-md-10">
                            <select type="color" class="form-control" id="txtColor" name="fontSize">
                            @foreach ($sizes as $size)
                                <option value="{{ $size }}" {{ $size == $widget->font_size ? 'selected' : '' }}>{{ $size }}px</option>
                            @endforeach
                            </select>
                        </div>
                    </div>                  

                    @csrf
                    <div class="mb-3">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>                
            </div>
        </div>
    </div>
</div>


@endsection
