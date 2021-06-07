<?php

namespace App\Http\Controllers;

use App\Repositories\ConfigRepository;
use App\Repositories\PreferenceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class ConfigController extends Controller
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
        return view('config', ['config' => ConfigRepository::findByUserId(Auth::user()->id)]);
    }

    public function edit(Request $request)
    {
        $request->validate([
            'spotifyPollingEnabled' => 'boolean',
            'nightbotAlertsEnabled' => 'boolean',
            'nightbotCommandEnabled' => 'boolean',
        ]);

        $success = ConfigRepository::edit(
            Auth::user()->id,
            $request->input('spotifyPollingEnabled') ?: false,
            $request->input('nightbotAlertsEnabled') ?: false,
            $request->input('nightbotCommandEnabled') ?: false
        );

        if (!$success){
            return back()->with('info', 'Failed to edit preferences.');
        }
        return back()->with('info', 'Preferences edited successfully.');
    }
}
