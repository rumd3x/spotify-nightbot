<?php

namespace App\Jobs;

use App\Repositories\IntegrationRepository;
use App\Repositories\NotificationRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use SpotifyWebAPI\Session as SpotifySession;


class SpotifyRenewRefreshTokens implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $integrations = IntegrationRepository::getIntegrationsWithSpotify();

        foreach ($integrations as $integration) {

            try {
                $session = new SpotifySession(
                    env('SPOTIFY_ID'), 
                    env('SPOTIFY_SECRET'), 
                    route('spotify.callback')
                );

                $success = $session->refreshAccessToken($integration->spotify_refresh_token);
                $newRefreshToken = $session->getRefreshToken();

                if (!$success) {
                    $message = "An error ocurred while trying to connect you to Spotify. Please go to your configurations page and make sure everything is okay.";
                    Log::warning("Removed refresh token for user '{$integration->user_id}' old token '$integration->spotify_refresh_token'");
                    NotificationRepository::sendToUserId($integration->user_id, $message, 'warning');
                    IntegrationRepository::updateSpotifyRefreshToken($this->user->id, '');
                    return;
                }
                
                if ($newRefreshToken !== $integration->spotify_refresh_token) {
                    IntegrationRepository::updateSpotifyRefreshToken($integration->user_id, $newRefreshToken);
                    Log::info("Refreshed Token for user id {$integration->user_id}.");
                }
            } catch (Exception $e) {
                Log::error("Refresh failed for user id '{$integration->user_id}': {$e->getMessage()}");
            }

            Log::info("Refresh Process Job Done for User ID: {$integration->user_id}");
        }
    }
}
