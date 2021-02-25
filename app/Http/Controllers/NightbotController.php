<?php

namespace App\Http\Controllers;

use App\Repositories\IntegrationRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Rumd3x\NightbotAPI\NightbotAPI;
use Rumd3x\NightbotAPI\NightbotProvider;

class NightbotController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function authenticate(Request $request) {
        $provider = new NightbotProvider(
            env('NIGHTBOT_ID'), 
            env('NIGHTBOT_SECRET'), 
            route('nightbot.callback')
        );

        $opts = ['scope' => [
            'channel_send',
        ]];

        return redirect($provider->getAuthorizationUrl($opts));
    }

    public function callbackHandler(Request $request) {
        try {
            $provider = new NightbotProvider(
                env('NIGHTBOT_ID'), 
                env('NIGHTBOT_SECRET'), 
                route('nightbot.callback')
            );
    
            
            $accessToken = $provider->getAccessToken('authorization_code', [
                'code' => $request->get('code'),
            ]);
            
            IntegrationRepository::updateNightbotRefreshToken(Auth::user()->id, $accessToken->getRefreshToken());
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return Redirect::to('config')->with('info', 'An error ocurred while trying to connect to Nightbot.');
        }

        return Redirect::to('config')->with('info', 'Nightbot successfully connected.');
    }

    public function disconnect() {
        IntegrationRepository::updateNightbotRefreshToken(Auth::user()->id, '');
        return Redirect::back()->with('info', 'Nightbot integration removed successfully.');
    }

    public function sendTestMessage() {
        try {
            $provider = new NightbotProvider(
                env('NIGHTBOT_ID'), 
                env('NIGHTBOT_SECRET'), 
                route('nightbot.callback')
            );    
            
            $accessToken = $provider->getAccessToken('refresh_token', [
                'refresh_token' => Auth::user()->integration->nightbot_refresh_token,
            ]);

            $api = new NightbotAPI($accessToken);
            $api->sendChannelMessage('This is a test message from Spotify -> Nightbot!');        
            
            IntegrationRepository::updateNightbotRefreshToken(Auth::user()->id, $accessToken->getRefreshToken());
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            IntegrationRepository::updateNightbotRefreshToken(Auth::user()->id, '');
            return Redirect::to('config')->with('info', 'An error ocurred while trying to connect to Nightbot.');
        }
        
        return Redirect::to('config')->with('info', 'Message sent!');
    }

}
