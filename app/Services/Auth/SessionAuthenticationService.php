<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class SessionAuthenticationService
{
    public function attemptLogin(array $credentials, bool $remember, Session $session): array
    {
        $user = User::query()
            ->where('email', $credentials['email'])
            ->first();

        if ($user && ($user->must_set_password || empty($user->password))) {
            Log::warning('Login denied because password setup is incomplete.', [
                'target_id' => $user->id,
                'status' => 'failed',
                'reason' => 'password_setup_incomplete',
            ]);

            return [
                'success' => false,
                'reason' => 'password_setup_incomplete',
            ];
        }

        if (! Auth::attempt($credentials, $remember)) {
            Log::warning('Login failed.', [
                'status' => 'failed',
                'reason' => 'invalid_credentials',
            ]);

            return [
                'success' => false,
                'reason' => 'invalid_credentials',
            ];
        }

        $session->regenerate();

        /** @var User $user */
        $user = Auth::user();

        Log::info('User logged in.', [
            'target_id' => $user->id,
            'status' => 'success',
        ]);

        return [
            'success' => true,
            'user' => $user,
        ];
    }

    public function logout(?User $user, Session $session): void
    {
        Auth::logout();

        $session->invalidate();
        $session->regenerateToken();

        Log::info('User logged out.', [
            'target_id' => $user?->id,
            'status' => 'success',
        ]);
    }
}
