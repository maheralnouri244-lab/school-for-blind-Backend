<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم غير موثق.'
            ], 401);
        }
$isMuted = !(bool) ($user->notifications_enabled ?? true);
        $notifications = Notification::where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->latest()
            ->get();

        $unreadCount = $notifications->whereNull('read_at')->count();

        $formattedNotifications = $notifications->map(function ($notification) {
            $data = $notification->data;
            if (is_string($data)) {
                $data = json_decode($data, true);
            }
            if (is_string($data)) {
                $data = json_decode($data, true);
            }

            return [
                'id'         => $notification->id,
                'title'      => $notification->title,
                'body'       => $notification->body,
                'data'       => $data,
                'is_read'    => !is_null($notification->read_at),
                'read_at'    => $notification->read_at ? $notification->read_at->toIso8601String() : null,
                'created_at' => $notification->created_at ? $notification->created_at->diffForHumans() : null,
                'timestamp'  => $notification->created_at ? $notification->created_at->toIso8601String() : null,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'تم جلب الإشعارات بنجاح.',
            'data'    => [
                'unread_count'  => $unreadCount,
                'is_muted'      => $isMuted,
                'total_count'   => $notifications->count(),
                'notifications' => $formattedNotifications,
            ]
        ], 200);
    }

   
    public function toggleMute(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم غير موثق.'
            ], 401);
        }

        $currentState = (bool) ($user->notifications_enabled ?? true);
        $newState = !$currentState;

        $user->notifications_enabled = $newState;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => $newState ? 'تم تفعيل الإشعارات بنجاح.' : 'تم كتم الإشعارات بنجاح.',
            'data'    => [
                'notifications_enabled' => $newState
            ]
        ], 200);
    }
    
    public function getMuteStatus(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'المستخدم غير موثق.'
            ], 401);
        }

        $isEnabled = (bool) ($user->notifications_enabled ?? true);

        return response()->json([
            'success' => true,
            'data'    => [
                'notifications_enabled' => $isEnabled,
                'status'                => $isEnabled ? 'enabled' : 'muted',
            ]
        ], 200);
    }
    
    }