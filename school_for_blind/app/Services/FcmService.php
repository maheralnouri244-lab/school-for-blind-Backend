<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FcmService
{
    private function getAccessToken(): ?string
    {
        return Cache::remember('fcm_access_token', 3500, function () {
            try {
                $client = new GoogleClient();
                $client->setAuthConfig(storage_path('app/firebase/fcmkey.json'));
                $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
                $client->refreshTokenWithAssertion();
                $token = $client->getAccessToken();

                return $token['access_token'] ?? null;
            } catch (\Exception $e) {
                Log::error('FCM Token Fetch Error: ' . $e->getMessage());
                return null;
            }
        });
    }

    public function sendNotification(string $fcmToken, string $title, string $body, ?array $data = []): bool
    {
        try {
            $accessToken = $this->getAccessToken();

            if (!$accessToken) {
                Log::error('FCM Error: Could not retrieve Access Token.');
                return false;
            }

            $projectId = config('services.fcm.project_id', env('FCM_PROJECT_ID'));
            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            $message = [
                "token" => $fcmToken,
                "notification" => [
                    "title" => $title,
                    "body"  => $body,
                ],
            ];

            if (!empty($data)) {
                $formattedData = array_map(function ($value) {
                    return (string) $value;
                }, $data);

                $message["data"] = $formattedData;
            }

            $payload = [
                "message" => $message
            ];

            $response = Http::withToken($accessToken)->post($url, $payload);

            if ($response->successful()) {
                return true;
            }

            if ($response->status() === 401) {
                Cache::forget('fcm_access_token');
            }

            Log::error('FCM Send Error: ' . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error('FCM Exception: ' . $e->getMessage());
            return false;
        }
    }
}