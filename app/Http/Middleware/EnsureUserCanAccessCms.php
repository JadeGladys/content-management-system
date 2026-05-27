<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserCanAccessCms
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, ['admin', 'editor'], true)) {
            Log::warning('CMS access denied.', [
                'actor_id' => $user?->id,
                'status' => 'failed',
                'reason' => 'permission_denied',
            ]);

            abort(403);
        }

        return $next($request);
    }
}