<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http as HttpClient;
use Illuminate\Support\Str;

class SsoTokenService
{
    public function store(Request $request, array $tokenData): void
    {
        $expiresIn = (int) ($tokenData['expires_in'] ?? 0);

        $payload = [
            'access_token' => $tokenData['access_token'] ?? null,
            'refresh_token' => $tokenData['refresh_token'] ?? null,
            'id_token' => $tokenData['id_token'] ?? null,
            'expires_at' => $expiresIn > 0 ? now()->addSeconds(max(30, $expiresIn - 60))->timestamp : null,
            'stored_at' => now()->timestamp,
        ];

        $request->session()->put('sso_tokens', Crypt::encryptString(json_encode($payload)));
        $request->session()->forget(['sso_access_token', 'sso_refresh_token', 'sso_id_token']);
    }

    public function get(Request $request): array
    {
        $encrypted = $request->session()->get('sso_tokens');
        if (! $encrypted) {
            return [];
        }

        try {
            return json_decode(Crypt::decryptString($encrypted), true) ?: [];
        } catch (\Throwable $e) {
            $this->clear($request);
            return [];
        }
    }

    public function clear(Request $request): void
    {
        $request->session()->forget(['sso_tokens', 'sso_access_token', 'sso_refresh_token', 'sso_id_token', 'sso_state']);
    }

    public function refresh(Request $request, array $config): bool
    {
        $tokens = $this->get($request);
        $refreshToken = $tokens['refresh_token'] ?? null;

        if (! $refreshToken) {
            return false;
        }

        $tokenUrl = $this->baseUrl($config) . '/realms/' . ($config['realm'] ?? '') . '/protocol/openid-connect/token';
        $response = HttpClient::asForm()->post($tokenUrl, [
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => $config['client_id'] ?? null,
            'client_secret' => $config['client_secret'] ?? null,
        ]);

        if ($response->failed() || empty($response->json('access_token'))) {
            $this->clear($request);
            return false;
        }

        $data = $response->json();
        if (empty($data['refresh_token'])) {
            $data['refresh_token'] = $refreshToken;
        }
        if (empty($data['id_token']) && ! empty($tokens['id_token'])) {
            $data['id_token'] = $tokens['id_token'];
        }

        $this->store($request, $data);
        return true;
    }

    public function shouldRefresh(Request $request): bool
    {
        $tokens = $this->get($request);
        return ! empty($tokens['refresh_token']) && ! empty($tokens['expires_at']) && now()->timestamp >= (int) $tokens['expires_at'];
    }

    private function baseUrl(array $config): string
    {
        $url = rtrim($config['url'] ?? '', '/');
        if (! Str::endsWith($url, '/auth')) {
            $url .= '/auth';
        }
        return $url;
    }
}
