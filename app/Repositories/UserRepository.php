<?php

namespace App\Repositories;

use App\SpotifyUser;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

final class UserRepository
{
    /**
     * Undocumented function
     *
     * @param string $name
     * @param string $login
     * @param string $email
     * @param string $country
     * @return User
     */
    public static function insert(
        string $name, 
        string $login, 
        string $email, 
        string $country
    ) {
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make(''),
            'email_verified_at' => Carbon::now(),
        ]);

        SpotifyUser::create([
            'user_id' => $user->id,
            'login' => $login,
            'country' => $country
        ]);

        PlaybackSummaryRepository::empty($user->id);
        PreferenceRepository::empty($user->id);
        IntegrationRepository::empty($user->id);
        ConfigRepository::empty($user->id);
        WidgetRepository::empty($user->id);

        NotificationRepository::sendToUserId(
            $user->id, 
            "Welcome to Spotify-Nightbot! Now, make sure you go to the configurations page and setup your integrations.",
            "info"
        );

        return $user;
    }

    /**
     * Undocumented function
     *
     * @param integer $userId
     * @return User|null
     */
    public static function findUserByIdForWidgetBox(int $userId)
    {
        return User::with(['summary', 'preferences', 'widget'])->find($userId);
    }

    /**
     * Undocumented function
     *
     * @param integer $id
     * @param string $name
     * @param string $login
     * @param string $email
     * @param string $country
     * @param string $profilePicture
     * @return bool
     */
    public static function update(
        int $id,
        string $name, 
        string $login, 
        string $email, 
        string $country,
        string $profilePicture
    ) {
        $user = User::find($id)->update([
            'name' => $name,
            'email' => $email,
        ]);

        SpotifyUser::whereUserId($id)->update([
            'login' => $login,
            'country' => $country,
            'profile_picture' => $profilePicture,
        ]);

        return $user;
    }

    /**
     * Undocumented function
     *
     * @return Collection
     */
    public static function chunkEnableds($f)
    {
        return User::with(['config', 'preferences', 'spotify', 'integration'])
        ->whereHas('config', function(Builder $query) {
            $query->where('spotify_polling_enabled', true);
        })
        ->chunk(1000, $f);
    }

    /**
     * Find active user by ID
     *
     * @param integer $id
     * @return User|null
     */
    public static function findBySpotifyLogin(string $login)
    {
        $spotifyUser = SpotifyUser::whereLogin($login)->first();

        if (!$spotifyUser)  {
            return null;
        }

        return $spotifyUser->user;
    }
}
