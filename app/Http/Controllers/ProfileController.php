<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\UserRepository;

class ProfileController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('myaccount');
    }

    public function edit(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3|max:200|string|alpha_spaces',
            'email' => 'required|email|max:255',
        ]);

        $user = UserRepository::findById(Auth::user()->id);
        $success = UserRepository::edit($user, [
            'name' => $request->input('name'),
            'email' => $request->input('email'),
        ]);

        if (!$success){
            return Redirect::back()->with('info', 'Failed to edit profile.');
        }
        return Redirect::back()->with('info', 'Profile edited successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'currentPassword' => sprintf('required|old_password:%s', Auth::user()->password),
            'newPassword' => 'required|confirmed|min:5|max:255',
        ]);

        $user = UserRepository::findById(Auth::user()->id);
        $success = UserRepository::changePassword($user, $request->input('newPassword'));

        if (!$success) {
            return Redirect::back()->with('info', 'Failed to change user password.');
        }
        return Redirect::back()->with('info', 'User password updated.');
    }
}
