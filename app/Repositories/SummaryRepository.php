<?php

namespace App\Repositories;

use App\SpotifySummary;
use App\SpotifyUser;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

final class SummaryRepository
{

    /**
     * Undocumented function
     *
     * @param integer $userId
     * @return void
     */
    public static function empty(int $userId)
    {
        $user = User::find($userId);

        SpotifySummary::updateOrCreate(
            ['spotify_user_id' => $user->spotify->id],
            ['song' => 'Unavailable', 'artist' => 'Unavailable', 'playback_status' => 'Unavailable']
        );
    }

    /**
     * Undocumented function
     *
     * @param integer $userId
     * @return SpotifySummary
     */
    public static function getByUserId(int $userId)
    {
        $user = User::find($userId);
        return SpotifySummary::whereSpotifyUserId($user->spotify->id)->first();
    }

    /**
     * Undocumented function
     *
     * @param SpotifySummary $summary
     * @param string $artist
     * @param string $song
     * @return bool
     */
    public static function updateCurrentSong(SpotifySummary $summary, string $artist, string $song)
    {
        if ($summary->song === $song || $summary->artist === $artist) {
            return false;
        }
        
        return $summary->update(['song' => $song, 'artist' => $artist]);
    }

    /**
     * Undocumented function
     *
     * @param SpotifySummary $summary
     * @param string $newPlaybackStatus
     * @return bool
     */
    public static function updatePlaybackStatus(SpotifySummary $summary, string $newPlaybackStatus)
    {
        return $summary->update(['playback_status' => $newPlaybackStatus]);
    }
}
