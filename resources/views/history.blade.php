@extends('layouts.panel')

@section('content')

<!-- DataTables Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Your Last 50 Songs</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Artist</th>
                        <th>Track</th>
                        <th>Played At</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($history as $song)
                    <tr>
                        <td>{{ $song->artist }}</td>
                        <td>{{ $song->song }}</td>
                        <td>{{ $song->time }}</td>
                    </tr> 
                @empty
                    <tr>
                        <td colspan="3">
                            <center>No songs played.</center>
                        </td>
                    </tr> 
                @endforelse
                                                           
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
