<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function resolveAutorization()
    {
        $url = config('services.api_free.url');

        if (auth()->user()->accessToken->expires_at <= now()) {
            $response = Http::withOptions([
                'verify' => false
            ])->withHeaders([
                'Accept' => 'application/json'
            ])->post($url . '/oauth/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => auth()->user()->accessToken->refresh_token,
                'client_id' => config('services.api_free.client_id'),
                'client_secret' => config('services.api_free.client_secret'),
            ]);

            $access_token = $response->json();

            auth()->user()->accessToken->update([
                'access_token' => $access_token['access_token'],
                'refresh_token' => $access_token['refresh_token'],
                'expires_at' => now()->addSecond($access_token['expires_in'])
            ]);
        }
    }
}
