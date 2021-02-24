<?php

namespace App\Repositories;

use App\SongHistory;
use Carbon\Carbon;

final class SongHistoryRepository
{

    /**
     * Undocumented function
     *
     * @param integer $userId
     * @return SongHistory
     */
    public static function add(
        int $userId,
        string $artist,
        string $song,
        Carbon $time
    ) {
        return SongHistory::create([
            'user_id' => $userId,
            'artist' => $artist,
            'song' => $song,
            'time' => $time,
        ]);
    }

    public static function getUserLast50(int $userId)
    {
        return SongHistory::whereUserId($userId)->orderByDesc('time')->limit(50)->get();
    }
}
