<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use SpotifyWebAPI\Session as SpotifySession;
use SpotifyWebAPI\SpotifyWebAPI;

class HomeController extends Controller
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
        $user = Auth::user();

        if ($user) {
            return redirect('login');
        }

        return redirect('home');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function dashboard()
    {
        return view('dashboard');

        $session = new SpotifySession(
            env('SPOTIFY_ID'), 
            env('SPOTIFY_SECRET'), 
            route('spotify.callback')
        );
        
        $session->refreshAccessToken(Auth::user()->spotify->refresh_token);
        $accessToken = $session->getAccessToken();   
       
        $api = new SpotifyWebAPI();
        $api->setAccessToken($accessToken);
        
        dump(Auth::user());
        dump($api->me());
        dump($api->getMyCurrentTrack());

        $refreshToken = $session->getRefreshToken();
        if ($refreshToken !== Auth::user()->spotify->refresh_token) {
            Auth::user()->spotify->refresh_token = $refreshToken;
            Auth::user()->spotify->save();    

            $login = Auth::user()->spotify->login;
            Log::info("Updated '{$login}' refresh token");
        }
    }
}
