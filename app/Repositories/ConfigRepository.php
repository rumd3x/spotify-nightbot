<?php

namespace App\Repositories;

use App\Configuration;

final class ConfigRepository
{

    /**
     * Undocumented function
     *
     * @param integer $userId
     * @return Configuration
     */
    public static function empty(int $userId)
    {
        return Configuration::updateOrCreate(
            ['user_id' => $userId],
            ['spotify_polling_enabled' => true, 'nightbot_alerts_enabled' => true]
        );
    }

    /**
     * Undocumented function
     *
     * @param integer $userId
     * @param boolean $spotifyPolling
     * @param boolean $nightbotAlerts
     * @return Configuration|null
     */
    public static function findByUserId(int $userId) 
    {
        return Configuration::whereUserId($userId)->first();
    }

    /**
     * Undocumented function
     *
     * @param integer $userId
     * @param boolean $spotifyPolling
     * @param boolean $nightbotAlerts
     * @return bool
     */
    public static function edit(
        int $userId,
        bool $spotifyPolling,
        bool $nightbotAlerts
    ) {
        return Configuration::whereUserId($userId)->update([
            'spotify_polling_enabled' => $spotifyPolling, 
            'nightbot_alerts_enabled' => $nightbotAlerts,
        ]);
    }
}
