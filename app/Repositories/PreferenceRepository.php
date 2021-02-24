<?php

namespace App\Repositories;

use App\Preference;
use App\SpotifySummary;
use App\User;

final class PreferenceRepository
{

    /**
     * Undocumented function
     *
     * @param integer $userId
     * @return void
     */
    public static function empty(int $userId)
    {
        Preference::updateOrCreate(
            ['user_id' => $userId],
            ['preceding_label' => null, 'artist_song_order' => 'songNamePreceding']
        );
    }

    /**
     * Undocumented function
     *
     * @param User $user
     * @param string $precedingLabel
     * @param string $artistSongOrder
     * @return void
     */
    public static function edit(
        int $userId,
        string $precedingLabel,
        string $artistSongOrder
    ) {
        return Preference::whereUserId($userId)->update([
            'preceding_label' => $precedingLabel, 
            'artist_song_order' => $artistSongOrder
        ]);
    }
}
