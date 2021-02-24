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
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ Auth::user()->spotify->summary->artist }} - {{ Auth::user()->spotify->summary->song }}</div>
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
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ Auth::user()->spotify->summary->playback_status }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-play fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
