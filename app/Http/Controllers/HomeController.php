<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateAllUsersSongsJob;
use App\Repositories\SongHistoryRepository;
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
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function history()
    {
        return view('history', ['history' => SongHistoryRepository::getUserLast50(Auth::user()->id)]);
    }

    public function debug()
    {
        // UpdateAllUsersSongsJob::dispatch();
        // return 'ok';

        $session = new SpotifySession(
            env('SPOTIFY_ID'), 
            env('SPOTIFY_SECRET'), 
            route('spotify.callback')
        );
        
        $session->refreshAccessToken(Auth::user()->integration->spotify_refresh_token);
        $accessToken = $session->getAccessToken();   
       
        $api = new SpotifyWebAPI();
        $api->setAccessToken($accessToken);
        
        dump(Auth::user());
        dump($api->me());
        dump($api->getMyCurrentTrack());

        $refreshToken = $session->getRefreshToken();
        if ($refreshToken !== Auth::user()->integration->spotify_refresh_token) {
            Auth::user()->integration->spotify_refresh_token = $refreshToken;
            Auth::user()->spotify->save();    

            $login = Auth::user()->spotify->login;
            dump("Updated '{$login}' refresh token");
        }
    }
}
