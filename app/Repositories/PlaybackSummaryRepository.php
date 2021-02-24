<?php

namespace App\Repositories;

use App\PlaybackSummary;
use App\SpotifyUser;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

final class PlaybackSummaryRepository
{

    /**
     * Undocumented function
     *
     * @param integer $userId
     * @return void
     */
    public static function empty(int $userId)
    {
        PlaybackSummary::updateOrCreate(
            ['user_id' => $userId],
            ['song' => 'Unavailable', 'artist' => 'Unavailable', 'playback_status' => 'Unavailable']
        );
    }

    /**
     * Undocumented function
     *
     * @param integer $userId
     * @return PlaybackSummary
     */
    public static function getByUserId(int $userId)
    {
        return PlaybackSummary::whereUserId($userId)->first();
    }

    /**
     * Undocumented function
     *
     * @param PlaybackSummary $summary
     * @param string $artist
     * @param string $song
     * @return bool
     */
    public static function updateCurrentSong(PlaybackSummary $summary, string $artist, string $song)
    {
        if ($summary->song === $song && $summary->artist === $artist) {
            return false;
        }
        
        return $summary->update(['song' => $song, 'artist' => $artist]);
    }

    /**
     * Undocumented function
     *
     * @param PlaybackSummary $summary
     * @param string $newPlaybackStatus
     * @return bool
     */
    public static function updatePlaybackStatus(PlaybackSummary $summary, string $newPlaybackStatus)
    {
        return $summary->update(['playback_status' => $newPlaybackStatus]);
    }
}
