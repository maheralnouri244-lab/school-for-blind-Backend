<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FcmTokenController extends Controller
{
    public function updateFcmToken(Request $request): JsonResponse
    {
        $request->validate([
            'fcm_token' => ['required', 'string'],
        ]);

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'العنصر المطلوب غير موجود في قاعدة البيانات أو المستخدم غير موثق.'
            ], 404);
        }

        $user->fcm_token = $request->fcm_token;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'تم تحديث التوكن بنجاح.',
            'data'    => [
                'user_id'   => $user->id,
                'user_type' => class_basename($user), 
                'fcm_token' => $user->fcm_token,
            ]
        ], 200);
    }

    
    public function logout(Request $request): JsonResponse
{
    $user = $request->user();

    if ($user) {
        $user->timestamps = false;
        $user->update(['fcm_token' => null]);

        if (method_exists($user, 'currentAccessToken') && $user->currentAccessToken()) {
            $user->currentAccessToken()->delete();
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'تم تسجيل الخروج بنجاح.'
    ], 200);
}}