<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PostController extends Controller
{
    public function store()
    {
        $url = config('services.api_free.url');

        $this->resolveAutorization();

        $response = Http::withOptions([
            'verify' => false
        ])->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . auth()->user()->accessToken->access_token
        ])->post($url.'/v1/posts', [
            'name' => 'Post de prueba',
            'slug' => 'post-de-prueba-2',
            'extract' => 'Este es un post de prueba',
            'body' => 'Contenido del primer post de prueba',
            'category_id' => 1
        ]);

        return $response->json();
    }
}
