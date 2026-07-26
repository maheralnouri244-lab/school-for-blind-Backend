<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FcmService
{
    public function sendNotification(string $fcmToken, string $title, string $body, ?array $data = [])
    {
        try {
            $client = new GoogleClient();
            $client->setAuthConfig(storage_path('app/firebase/fcmkey.json'));
            $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
            $client->refreshTokenWithAssertion();
            $token = $client->getAccessToken();

            $access_token = $token['access_token'];
            $projectId = env('FCM_PROJECT_ID');
            $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

            $payload = [
                "message" => [
                    "token" => $fcmToken,
                    "notification" => [
                        "title" => $title,
                        "body" => $body,
                    ],
                    "data" => !empty($data) ? $data : null
                ]
            ];

            $response = Http::withToken($access_token)->post($url, $payload);

            if ($response->successful()) {
                return true;
            }

            Log::error('FCM Send Error: ' . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error('FCM Exception: ' . $e->getMessage());
            return false;
        }
    }
}