<?php

namespace App\Http\Controllers;

use App\Repositories\IntegrationRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use SpotifyWebAPI\Session as SpotifySession;
use SpotifyWebAPI\SpotifyWebAPI;

class SpotifyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->only(['disconnect']);
    }

    public function authenticate(Request $request) {
        $session = new SpotifySession(
            env('SPOTIFY_ID'), 
            env('SPOTIFY_SECRET'), 
            route('spotify.callback')
        );

        $opts = ['scope' => [
            'user-read-email',
            'user-read-private',
            'user-read-playback-state',
            'user-read-currently-playing',
        ]];

        return redirect($session->getAuthorizeUrl($opts));
    }

    public function callbackHandler(Request $request) {
        try {
            $session = new SpotifySession(
                env('SPOTIFY_ID'), 
                env('SPOTIFY_SECRET'), 
                route('spotify.callback')
            );
    
            $api = new SpotifyWebAPI();
    
            $session->requestAccessToken($request->get('code'));
            $api->setAccessToken($session->getAccessToken());
            $spotifyUser = $api->me();
            $user = UserRepository::findBySpotifyLogin($spotifyUser->id);

            if (!$user) {
                Log::info("Creating new user '{$spotifyUser->id}'");

                $user = UserRepository::insert(
                    $spotifyUser->display_name, 
                    $spotifyUser->id, 
                    $spotifyUser->email, 
                    $spotifyUser->country
                );
            }

            Log::info("Updating user '{$spotifyUser->id}' information");
            UserRepository::update(
                $user->id,
                $spotifyUser->display_name, 
                $spotifyUser->id, 
                $spotifyUser->email, 
                $spotifyUser->country,
                empty($spotifyUser->images) ? '' : $spotifyUser->images[0]->url
            );
            IntegrationRepository::updateSpotifyRefreshToken($user->id, $session->getRefreshToken());

            if (Auth::user()) {
                return redirect()->route('config')->with('info', 'Spotify successfully connected.');
            }
            
            Auth::login($user);    
            return redirect()->route('home');

        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('config')->with('info', 'An error ocurred while trying to connect to Spotify.');
        }
    }

    public function disconnect() {
        IntegrationRepository::updateSpotifyRefreshToken(Auth::user()->id, '');
        return back()->with('info', 'Spotify integration removed successfully.');
    }

}
