<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SendFcmNotificationJob;

class NotificationController extends Controller
{
  public function testSend(Request $request)
{
    $testToken = $request->input('token'); 
    $title = "مرحباً بك في مدرسة المكفوفين";
    $body = "هذا إشعار تجريبي للتأكد من عمل النظام بنجاح.";

    SendFcmNotificationJob::dispatch($testToken, $title, $body);

    return response()->json([
        'success' => true,
        'message' => 'تمت إضافة الإشعار لطابور الإرسال بنجاح'
    ]);
}
}