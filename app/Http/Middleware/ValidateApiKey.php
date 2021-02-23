<?php

namespace App\Http\Middleware;

use Closure;
use App\User;
use App\Repositories\UserRepository;

class ValidateApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $apiKey = $request->input('api_key', null);
        if (!$apiKey) {
            return response(['message' => 'Missing "api_key" on request Body'], 400);
        }

        $user = UserRepository::findByApiKey($apiKey);
        if (!$user) {
            return response(['message' => 'Invalid "api_key" on request Body'], 400);
        }

        app()->instance(User::class, $user);
        return $next($request);
    }
}
