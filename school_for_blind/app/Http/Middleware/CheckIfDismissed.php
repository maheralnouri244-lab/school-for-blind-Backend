<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class CheckIfDismissed
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user) {
            if (isset($user->status) && $user->status === 'dismissed') {
                if (method_exists($user, 'currentAccessToken') && $user->currentAccessToken()) {
                    $user->currentAccessToken()->delete();
                }

                return response()->json([
                    'success' => false,
                    'message' => 'تم فصل حسابك بشكل نهائي من النظام. يرجى مراجعة الإدارة.',
                    'is_dismissed' => true,
                    'expires_at' => null
                ], Response::HTTP_FORBIDDEN, [], JSON_UNESCAPED_UNICODE);
            }

            $dismissalRecord = DB::table('punishables')
                ->join('punishments', 'punishables.punishment_id', '=', 'punishments.id')
                ->where('punishables.punishable_id', $user->id)
                ->where('punishables.punishable_type', get_class($user))
                ->where('punishments.name', 'Dismissal')
                ->where(function ($query) {
                    $query->whereNull('punishables.expires_at')
                        ->orWhere('punishables.expires_at', '>', Carbon::now());
                })
                ->select('punishables.expires_at')
                ->first();

            if ($dismissalRecord) {
                if (method_exists($user, 'currentAccessToken') && $user->currentAccessToken()) {
                    $user->currentAccessToken()->delete();
                }

                $expiresAt = $dismissalRecord->expires_at;
                $message = 'تم تقييد حسابك وفصلك من النظام.';

                if (is_null($expiresAt)) {
                    $message = 'تم فصل حسابك بشكل نهائي من النظام. يرجى مراجعة الإدارة.';
                } else {
                    $timeLeft = Carbon::parse($expiresAt)->locale('ar')->diffForHumans();
                    $message = "حسابك مفصول مؤقتاً. ستنتهي العقوبة في: " . $timeLeft;
                }

                return response()->json([
                    'success' => false,
                    'message' => $message,
                    'is_dismissed' => true,
                    'expires_at' => $expiresAt
                ], Response::HTTP_FORBIDDEN, [], JSON_UNESCAPED_UNICODE);
            }
        }

        return $next($request);
    }
}