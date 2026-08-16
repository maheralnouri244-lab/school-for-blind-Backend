<?php

namespace App\Services;

use App\Models\Caregiver;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use App\Models\Notification;
use App\Models\Student;
use App\Models\Teacher;

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

    public function sendNotification($target, string $title, string $body, ?array $data = [], $notifiable = null): bool
    {
        try {
            $fcmToken = null;

            if (is_object($target)) {
                $notifiable = $target;
                $fcmToken = $target->fcm_token ?? null;
            } elseif (is_string($target)) {
                $fcmToken = $target;

                if (!$notifiable) {
                    $notifiable = Student::where('fcm_token', $fcmToken)->first()
                        ?? Teacher::where('fcm_token', $fcmToken)->first()
                        ?? Caregiver::where('fcm_token', $fcmToken)->first();
                }
            }

            if ($notifiable && is_object($notifiable)) {
                try {
                    Notification::create([
                        'notifiable_type' => get_class($notifiable),
                        'notifiable_id'   => $notifiable->id,
                        'title'           => $title,
                        'body'            => $body,
                        'data'            => !empty($data) ? json_encode($data) : null,
                    ]);
                    Log::info("Notification saved successfully for {$notifiable->id}");
                } catch (\Exception $dbEx) {
                    Log::error('DB Notification Save Failed: ' . $dbEx->getMessage());
                }
            } else {
                Log::warning('Notification sent via FCM only (User not found for token: ' . $fcmToken . ')');
            }

            if (empty($fcmToken)) {
                Log::warning('FCM Warning: Empty FCM token provided.');
                return false;
            }
$isMuted = false;
            if ($notifiable && isset($notifiable->notifications_enabled) && !$notifiable->notifications_enabled) {
                $isMuted = true;
                Log::info("User ID: {$notifiable->id} has muted sound - sending silent push.");
            }
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
$message = [
                "token" => $fcmToken,
                "notification" => [
                    "title" => $title,
                    "body"  => $body,
                ],
                "android" => [
                    "notification" => [
                        "default_sound"         => !$isMuted,
                        "default_vibrate_timings" => !$isMuted,
                        "notification_priority" => $isMuted ? "PRIORITY_LOW" : "PRIORITY_HIGH",
                    ]
                ],
                "apns" => [
                    "payload" => [
                        "aps" => [
                            "sound" => $isMuted ? null : "default",
                        ]
                    ]
                ]
            ];
            if (!empty($data)) {
                $formattedData = array_map(function ($value) {
                    return (string) $value;
                }, $data);

                $message["data"] = $formattedData;
            }

            $response = Http::withToken($accessToken)->post($url, ["message" => $message]);

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