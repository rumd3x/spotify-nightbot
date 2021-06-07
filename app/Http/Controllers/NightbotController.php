<?php

namespace App\Http\Controllers;

use App\Repositories\IntegrationRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
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
        $redirectUrl = route('nightbot.callback');
        if (App::environment('production')) {
            $redirectUrl = str_replace('http://', 'https://', $redirectUrl);
        }

        $provider = new NightbotProvider(
            env('NIGHTBOT_ID'), 
            env('NIGHTBOT_SECRET'), 
            $redirectUrl
        );

        $opts = ['scope' => [
            'channel_send',
            'commands'
        ]];

        return redirect($provider->getAuthorizationUrl($opts));
    }

    public function callbackHandler(Request $request) {
        try {
            $redirectUrl = route('nightbot.callback');
            if (App::environment('production')) {
                $redirectUrl = str_replace('http://', 'https://', $redirectUrl);
            }

            $provider = new NightbotProvider(
                env('NIGHTBOT_ID'), 
                env('NIGHTBOT_SECRET'), 
                $redirectUrl
            );   
            
            $accessToken = $provider->getAccessToken('authorization_code', [
                'code' => $request->get('code'),
            ]);
            
            IntegrationRepository::updateNightbotRefreshToken(Auth::user()->id, $accessToken->getRefreshToken());
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('config')->with('info', 'An error ocurred while trying to connect to Nightbot.');
        }

        return redirect()->route('config')->with('info', 'Nightbot successfully connected.');
    }

    public function disconnect() {
        IntegrationRepository::updateNightbotRefreshToken(Auth::user()->id, '');
        return back()->with('info', 'Nightbot integration removed successfully.');
    }

    public function sendTestMessage() {
        try {
            $redirectUrl = route('nightbot.callback');
            if (App::environment('production')) {
                $redirectUrl = str_replace('http://', 'https://', $redirectUrl);
            }

            $provider = new NightbotProvider(
                env('NIGHTBOT_ID'), 
                env('NIGHTBOT_SECRET'), 
                $redirectUrl
            );    
            
            $accessToken = $provider->getAccessToken('refresh_token', [
                'refresh_token' => Auth::user()->integration->nightbot_refresh_token,
            ]);

            $api = new NightbotAPI($accessToken);
            $api->sendChatMessage('This is a test message from Spotify -> Nightbot!');    
            
            IntegrationRepository::updateNightbotRefreshToken(Auth::user()->id, $accessToken->getRefreshToken());
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            IntegrationRepository::updateNightbotRefreshToken(Auth::user()->id, '');
            return redirect()->route('config')->with('info', 'An error ocurred while trying to connect to Nightbot.');
        }
        
        return redirect()->route('config')->with('info', 'Message sent!');
    }

}
