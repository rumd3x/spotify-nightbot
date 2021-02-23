<?php

namespace App\Http\Controllers;

use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use SpotifyWebAPI\Session as SpotifySession;
use SpotifyWebAPI\SpotifyWebAPI;

class SpotifyController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
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

            $user = $api->me();

            var_dump($user);

            UserRepository::insert($user->display_name, $user->id, $user->email, '', $user->country);
    
            // return redirect('home');
        } catch (\Throwable $th) {
            return redirect('home');
        }
    }


}
