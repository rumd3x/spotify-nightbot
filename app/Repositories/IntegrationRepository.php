<?php

namespace App\Repositories;

use App\Integration;

final class IntegrationRepository
{

    /**
     * Undocumented function
     *
     * @param integer $userId
     * @return void
     */
    public static function empty(int $userId)
    {
        Integration::updateOrCreate(
            ['user_id' => $userId],
            ['spotify_refresh_token' => null, 'nightbot_refresh_token' => null]
        );
    }

    /**
     * Undocumented function
     *
     * @param integer $userId
     * @param integer $newRefreshToken
     * @return void
     */
    public static function updateSpotifyRefreshToken(
        int $userId,
        string $newRefreshToken
    ) {
        return Integration::whereUserId($userId)->update([
            'spotify_refresh_token' => $newRefreshToken,
        ]);
    }

    public static function updateNightbotRefreshToken(
        int $userId,
        string $newRefreshToken
    ) {
        return Integration::whereUserId($userId)->update([
            'nightbot_refresh_token' => $newRefreshToken,
        ]);
    }
}
