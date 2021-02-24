<?php

namespace App\Repositories;

use App\SpotifyUser;
use App\User;
use Carbon\Carbon;
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
     * @param string $refreshToken
     * @param string $profilePicture
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

        SummaryRepository::empty($user->id);
        PreferenceRepository::empty($user->id);

        return $user;
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
     * @return void
     */
    public static function update(
        int $id,
        string $name, 
        string $login, 
        string $email, 
        string $country,
        string $refreshToken,
        string $profilePicture
    ) {
        $user = User::find($id)->update([
            'name' => $name,
            'email' => $email,
        ]);

        SpotifyUser::whereUserId($id)->update([
            'login' => $login,
            'country' => $country,
            'refresh_token' => $refreshToken,
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
        return User::chunk(1000, $f);
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
