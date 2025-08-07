<?php

namespace App\Services\Amadius;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class AmadeusAuthService
{
    protected string $clientId;
    protected string $clientSecret;

    public function __construct()
    {
        $this->clientId = env('AMADIUS_API_KEY');
        $this->clientSecret = env('AMADIUS_API_SECRET');
    }

    public function getAccessToken(): ?string
    {
        // Cache the token for reuse (expires_in = 1800s)
        return Cache::remember('amadeus_access_token', 1700, function () {
            $response = Http::asForm()->post('https://test.api.amadeus.com/v1/security/oauth2/token', [
                'grant_type' => 'client_credentials',
                'client_id' => $this->clientId,
                'client_secret' => $this->clientSecret,
            ]);

            if (!$response->ok()) {
                \Log::error('Failed to fetch Amadeus token', ['response' => $response->body()]);
                return null;
            }

            return $response->json()['access_token'] ?? null;
        });
    }
}
