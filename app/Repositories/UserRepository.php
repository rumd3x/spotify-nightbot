<?php

namespace App\Repositories;

use App\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

final class UserRepository
{
    /**
     * Inserts a new User
     *
     * @param string $name
     * @param string $login
     * @param string $email
     * @param string $password
     * @return User
     */
    public static function insert(string $name, string $login, string $email, string $password, string $country)
    {
        return User::create([
            'name' => $name,
            'login' => $login,
            'email' => $email,
            'country' => $country,
            'password' => Hash::make($password),
            'email_verified_at' => Carbon::now(),
        ]);
    }

    /**
     * Find active user by ID
     *
     * @param integer $id
     * @return User|null
     */
    public static function findById(int $id)
    {
        return User::find($id);
    }
}
