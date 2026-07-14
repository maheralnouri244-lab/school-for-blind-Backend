<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Client as GoogleClient; 

class NotificationController extends Controller
{
    public function sendNotification(string $fcmToken, string $title, string $body, ?array $data = [])
    {
        require_once app_path('GoogleAPI/vendor/autoload.php');
        $projectId = "schoolforblindapp"; 
        
        $credentialsFilePath = storage_path('app/firebase/fcmkey.json'); 

        $client = new GoogleClient();
        $client->setAuthConfig($credentialsFilePath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();

        $access_token = $token['access_token'];

        $headers = [
            "Authorization: Bearer $access_token",
            'Content-Type: application/json'
        ];

        $dataPayload = [
            "message" => [
                "token" => $fcmToken,
                "notification" => [
                    "title" => $title,
                    "body" => $body,
                ],
                "data" => !empty($data) ? $data : null 
            ]
        ];
        
        $payload = json_encode($dataPayload);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/v1/projects/'.$projectId.'/messages:send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return response()->json([
                'success' => false,
                'message' => 'Curl Error: ' . $err
            ], 500);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'Notification has been sent',
                'response' => json_decode($response, true)
            ]);
        }
    }

    public function testSend(Request $request)
    {
        $testToken = "fANvh4r-gDoOakItxr3GAS:APA91bFCmUliI9oqPMwv_HxfnR0d6U4_sfKZwp8DDWPrRjq1HNkmOGbcANw0gi9qlX-adHuvrF6_gv2_imSQg2DVFHXbl0jmobUdkvy5WtWWBaWoxWDGzp4"; 
        
        $title = "مرحباً بك في مدرسة المكفوفين";
        $body = "هذا إشعار تجريبي للتأكد من عمل النظام بنجاح.";

        return $this->sendNotification($testToken, $title, $body);
    }
}