<?php

namespace App\Jobs;

use App\Repositories\IntegrationRepository;
use App\Repositories\PlaybackSummaryRepository;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Rumd3x\NightbotAPI\NightbotAPI;
use Rumd3x\NightbotAPI\NightbotProvider;

class NightbotSendSongMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Undocumented variable
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
        $login = $this->user->spotify->login;
        if (empty($this->user->integration->nightbot_refresh_token)) {
            Log::info("Nightbot integration disconnected on user '{$login}'");
            return;
        }

        $provider = new NightbotProvider(
            env('NIGHTBOT_ID'), 
            env('NIGHTBOT_SECRET'), 
            route('nightbot.callback')
        ); 

        $accessToken = $provider->getAccessToken('refresh_token', [
            'refresh_token' => $this->user->integration->nightbot_refresh_token,
        ]);
        
        $summary = PlaybackSummaryRepository::getByUserId($this->user->id);
        
        $message = "{$summary->song} - {$summary->artist}";
        if ($this->user->preferences->artist_song_order === "artistNamePreceding") {
            $message = "{$summary->artist} - {$summary->song}";
        }
        
        if (!empty($this->user->preferences->preceding_label)) {
            $message = "{$this->user->preferences->preceding_label} {$message}";
        }

        $api = new NightbotAPI($accessToken);
        $api->sendChannelMessage($message);
        
        IntegrationRepository::updateNightbotRefreshToken($this->user->id, $accessToken->getRefreshToken());
    }
}
