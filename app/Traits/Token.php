<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;

trait Token
{
    public function setAccessToken($user, $service)
    {
        $url = config('services.api_free.url');

        $response = Http::withOptions([
            'verify' => false
        ])->withHeaders([
            'Accept' => 'application/json'
        ])->post($url . '/oauth/token', [
            'grant_type' => 'password',
            'client_id' => config('services.api_free.client_id'),
            'client_secret' => config('services.api_free.client_secret'),
            'username' => request('email'),
            'password' => request('password'),
            'scope' => 'create-post read-post update-post delete-post'
        ]);

        $access_token = $response->json();

        $user->accessToken()->create([
            'service_id' => $service['data']['id'],
            'access_token' => $access_token['access_token'],
            'refresh_token' => $access_token['refresh_token'],
            'expires_at' => now()->addSecond($access_token['expires_in'])
        ]);
    }
}
