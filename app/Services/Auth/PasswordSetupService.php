<?php

namespace App\Services\Auth;

use App\Models\UserToken;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PasswordSetupService
{
    public function validateToken(string $plainToken, string $email): ?UserToken
    {
        $tokenRecord = UserToken::query()
            ->where('type', 'password_setup')
            ->where('token_hash', hash('sha256', $plainToken))
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->whereHas('user', fn ($query) => $query->where('email', $email))
            ->with('user')
            ->first();

        return $tokenRecord;
    }

    public function setPassword(string $plainToken, string $email, string $password): void
    {
        $tokenRecord = $this->validateToken($plainToken, $email);

        if (! $tokenRecord) {
            Log::warning('Password setup denied because token is invalid or expired.', [
                'status' => 'failed',
                'reason' => 'invalid_or_expired_password_setup_token',
            ]);

            throw ValidationException::withMessages([
                'email' => 'This password setup link is invalid or has expired.',
            ]);
        }

        $user = $tokenRecord->user;

        $user->update([
            'password' => $password,
            'must_set_password' => false,
        ]);

        $tokenRecord->update([
            'used_at' => now(),
        ]);

        Log::info('Password setup completed.', [
            'target_id' => $user->id,
            'user_id' => $user->id,
            'status' => 'success',
        ]);
    }
}