<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 'admin') {
            Log::warning('Admin-only action denied.', [
                'actor_id' => $user?->id,
                'status' => 'failed',
                'reason' => 'permission_denied',
            ]);

            abort(403);
        }

        return $next($request);
    }
}
