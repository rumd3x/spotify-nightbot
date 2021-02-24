<?php

namespace App\Http\Controllers;

use App\Repositories\PreferenceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Repositories\UserRepository;

class PreferencesController extends Controller
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
        return view('preferences', ['preferences' => Auth::user()->preferences]);
    }

    public function edit(Request $request)
    {
        
        $request->validate([
            'precedingLabel' => 'nullable|max:50|string',
            'artistSongOrder' => 'required',
        ]);

        $success = PreferenceRepository::edit(
            Auth::user()->id,
            $request->input('precedingLabel') ?: '',
            $request->input('artistSongOrder')
        );

        if (!$success){
            return Redirect::back()->with('info', 'Failed to edit preferences.');
        }
        return Redirect::back()->with('info', 'Preferences edited successfully.');
    }
}
