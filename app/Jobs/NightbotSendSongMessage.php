<?php

namespace App\Jobs;

use App\Repositories\IntegrationRepository;
use App\Repositories\NotificationRepository;
use App\Repositories\PlaybackSummaryRepository;
use App\User;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
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
        try {
            $login = $this->user->spotify->login;
            if (empty($this->user->integration->nightbot_refresh_token)) {
                Log::info("Nightbot integration disconnected on user '{$login}'");
                return;
            }
    
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
            $api->sendChatMessage($message);
            
            IntegrationRepository::updateNightbotRefreshToken($this->user->id, $accessToken->getRefreshToken());
        } catch (Exception $e) {

            if ($e instanceof IdentityProviderException) {
                Log::error($e->getMessage());
                $message = "An error ocurred when we last tried to send a chat message using Nightbot. Please go to your configurations page and try to reconnect the Nightbot integration.";
                NotificationRepository::sendToUserId($this->user->id, $message, 'warning');
                IntegrationRepository::updateNightbotRefreshToken($this->user->id, '');
                return;
            }

            if ($e instanceof GuzzleException) {
                Log::error($e->getMessage());
                return;
            }

            throw $e;
        }
    }
}
