@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(Session::has('info'))
                <div class="alert alert-info">
                    <p>{{ Session::get('info') }}</p>
                </div>
            @endif

            <div class="card">
                <div class="card-header">API</div>
                <div class="card-body">
                    <label for="txtApi">Your API Key</label>
                    <div class="input-group">
                        <input class="form-control" type="password" name="api" id="txtApi" value="{{ Auth::user()->api_key }}" onclick="this.select()">
                        <div class="input-group-append">
                            <button class="btn btn-outline-primary" onclick="document.getElementById('txtApi').type = 'text;'">Show Key</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">Profile</div>
                <div class="card-body">
                    <form action="{{ route('profile.settings') }}" method="POST">
                        <div class="form-group">
                            <label for="txtName">Name</label>
                            <input class="form-control" type="text" name="name" id="txtName" value="{{ Auth::user()->name }}">
                        </div>
                        <div class="form-group">
                            <label for="txtEmail">E-mail</label>
                            <input class="form-control" type="text" name="email" id="txtEmail" value="{{ Auth::user()->email }}">
                        </div>
                        @csrf
                        <button class="btn btn-primary" type="submit">Save</button>
                    </form>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">Password</div>
                <div class="card-body">
                    <form action="{{ route('profile.password') }}" method="POST">
                        <div class="form-group">
                            <label for="txtCurrentPassword">Current Password</label>
                            <input class="form-control" type="password" name="currentPassword" id="txtCurrentPassword">

                            <label for="txtNewPassword1">New Password</label>
                            <input class="form-control" type="password" name="newPassword" id="txtNewPassword1">

                            <label for="txtNewPassword2">Repeat new Password</label>
                            <input class="form-control" type="password" name="newPassword_confirmation" id="txtNewPassword2">
                        </div>

                        @csrf
                        <button class="btn btn-primary" type="submit">Change Password</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
