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
     * @return bool
     */
    public static function updateSpotifyRefreshToken(
        int $userId,
        string $newRefreshToken
    ) {
        return Integration::whereUserId($userId)->update([
            'spotify_refresh_token' => $newRefreshToken,
        ]);
    }

    /**
     * Undocumented function
     *
     * @param integer $userId
     * @param string $newRefreshToken
     * @return bool
     */
    public static function updateNightbotRefreshToken(
        int $userId,
        string $newRefreshToken
    ) {
        return Integration::whereUserId($userId)->update([
            'nightbot_refresh_token' => $newRefreshToken,
        ]);
    }

    /**
     * Undocumented function
     *
     * @param integer $userId
     * @return Integration|null
     */
    public static function getFromUserId(int $userId) {
        return Integration::whereUserId($userId)->first();
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public static function getIntegrationsWithSpotify()
    {
        return Integration::where('spotify_refresh_token', '<>', '')->get();
    }
}
