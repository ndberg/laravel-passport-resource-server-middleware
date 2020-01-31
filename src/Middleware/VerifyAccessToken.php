<?php

namespace Ndberg\LaravelPassportResourceServerMiddleware\Middleware;

use App\Models\User;
use Closure;
use Firebase\JWT\JWT;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Ndberg\LaravelPassportResourceServerMiddleware\Exceptions\InvalidAccessTokenException;

class VerifyAccessToken
{
    /**
     * Create a new middleware instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle an incoming request.
     *
     * try - catch in middleware not usable: https://github.com/laravel/framework/issues/14573
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @param  array  $scopes
     *
     * @return mixed
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Ndberg\LaravelPassportResourceServerMiddleware\Exceptions\InvalidAccessTokenException
     */
    public function handle($request, Closure $next, ...$scopes)
    {
        Log::debug('Bearer: '.$request->bearerToken());

        if (empty($request->bearerToken())) {
            Log::debug('No Access Token');
            abort(403, 'Access denied');
            throw new InvalidAccessTokenException('Invalid Access Token');
        }
        $accessToken = $request->bearerToken();
        $decodedAccessToken = $this->decodeAccessToken($accessToken);
        $this->getUserFromAuth($accessToken, $decodedAccessToken);
        return $next($request);
    }

    /**
     * @param  string|null  $accessToken
     * @return object|null
     * @throws InvalidAccessTokenException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function decodeAccessToken(string $accessToken): ?object
    {
        $key = File::get(storage_path('oauth-public.key'));
        $decoded = JWT::decode($accessToken, $key, ['RS256']);

        if (! $decoded->aud) {
            throw new InvalidAccessTokenException();
        }

        Log::debug('Access Token:', (array) $decoded);
        Log::debug('Scopes', $decoded->scopes);
        Log::debug('Scopes json '.json_encode($decoded->scopes));

        return $decoded;
    }

    /**
     * @param  string|null  $accessToken
     */
    protected function getUserFromAuth(string $accessToken, object $decodedAccessToken): void
    {
        $user = Cache::remember($accessToken, 1, function () use ($accessToken, $decodedAccessToken) {
            $client = new Client();
            $response = $client->get('http://auth.test/api/user', [
                'headers' => [
                    'Accept' => 'application/json', 'Authorization' => 'Bearer '.$accessToken,
                ],
            ]);
            $userFromAuth = json_decode((string) $response->getBody(), true);
            $user = User::find($userFromAuth['id']) ?? User::forceCreate([
                    'id' => $userFromAuth['id'], 'name' => $userFromAuth['name'], 'email' => $userFromAuth['email'],
                    'oauth_token' => $accessToken, 'scopes' => implode(',', $decodedAccessToken->scopes),
                ]);

            return $user;
        });

        Auth::login($user);
    }
}
