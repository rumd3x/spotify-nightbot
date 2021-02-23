@extends('layouts.panel')

@section('content')
<!-- Content Row -->
<div class="row">

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-md-6 col-sm-12 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Last Song</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Orochi - Vermelho Ferrari</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-music fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings (Monthly) Card Example -->
    <div class="col-md-6 col-sm-12 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Playback Status</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Stopped</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-play fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

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
                    <tr>
                        <td>Orochi</td>
                        <td>Vermelho Ferrari</td>
                        <td>Feb 23, 18:50</td>
                    </tr>                                        
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
