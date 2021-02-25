<?php

namespace App\Jobs;

use App\Repositories\IntegrationRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\SongHistoryRepository;
use App\Repositories\PlaybackSummaryRepository;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use SpotifyWebAPI\Session as SpotifySession;
use SpotifyWebAPI\SpotifyWebAPI;

class SpotifyPollingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The User to be Polled
     *
     * @var User
     */
    private $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $login = $this->user->spotify->login;
            if (empty($this->user->integration->spotify_refresh_token)) {
                Log::info("Spotify integration disconnected on user '{$login}'");
                return;
            }
    
            $session = new SpotifySession(
                env('SPOTIFY_ID'), 
                env('SPOTIFY_SECRET'), 
                route('spotify.callback')
            );
            
            $session->refreshAccessToken($this->user->integration->spotify_refresh_token);
            $accessToken = $session->getAccessToken();       
            $api = new SpotifyWebAPI();
            $api->setAccessToken($accessToken);
            $currentTrack = $api->getMyCurrentTrack();
            $refreshToken = $session->getRefreshToken();
            $userPlaybackSummary = PlaybackSummaryRepository::getByUserId($this->user->id);
    
            $songUpdated = false;
            $playbackStatus = "Stopped";
    
            if (!empty($currentTrack)) {
                $song = $currentTrack->item->name ?: "Unknown"; 
                $playbackStatus = $currentTrack->is_playing ? "Playing" : "Paused";
                $artist = empty($currentTrack->item->artists) ? "No Artist" : $currentTrack->item->artists[0]->name;
                $songUpdated = PlaybackSummaryRepository::updateCurrentSong($userPlaybackSummary, $artist, $song);
            }
            PlaybackSummaryRepository::updatePlaybackStatus($userPlaybackSummary, $playbackStatus);
            Log::info("Updated '{$login}' playback summary");
    
            if ($songUpdated) {
                if ($this->user->config->nightbot_alerts_enabled) {
                    NightbotSendSongMessage::dispatch($this->user);
                }
    
                $timezoneList = CarbonTimeZone::listIdentifiers(CarbonTimeZone::PER_COUNTRY, $this->user->spotify->country);
                if (!$timezoneList) {
                    log::warning("Failed to find timezone for country code '{$this->user->spotify->country}'");
                    $defaultTimezone = CarbonTimeZone::create();
                    $timezoneList = [$defaultTimezone];
                }            
                $artistsNamesList = [];
                foreach ($currentTrack->item->artists as $a) {
                    $artistsNamesList[] = $a->name;
                }
                $artists = empty($artistsNamesList) ? "No Artist" : implode(', ', $artistsNamesList);
                $time = Carbon::now($timezoneList[0]);
                SongHistoryRepository::add($this->user->id, $artists, $song, $time);
                Log::info("New song on '{$login}' history");
            }       
            
            if ($refreshToken !== $this->user->integration->spotify_refresh_token) {
                IntegrationRepository::updateSpotifyRefreshToken($this->user->id, $refreshToken);
                Log::info("Updated '{$login}' refresh token");
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $message = "An error ocurred when we last tried to update your playback summary from Spotify. Please go to your configurations page and try to reconnect the Spotify integration.";
            NotificationRepository::sendToUserId($this->user->id, $message, 'warning');
            IntegrationRepository::updateSpotifyRefreshToken($this->user->id, '');
        }
    }
}
