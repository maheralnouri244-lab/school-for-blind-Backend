<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Caregiver;

class IsParent
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user() instanceof Caregiver) {
            return $next($request);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'غير مصرح لك بالوصول. هذا القسم مخصص لأولياء الأمور فقط.'
        ], 403);
    }
}