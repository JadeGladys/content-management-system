<?php

namespace App\Services\Auth;

use App\Models\UserToken;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PasswordActionService
{
    public function validateToken(string $plainToken, string $email, string $type): ?UserToken
    {
        $tokenRecord = UserToken::query()
            ->where('type', $type)
            ->where('token_hash', hash('sha256', $plainToken))
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->whereHas('user', fn ($query) => $query->where('email', $email))
            ->with('user')
            ->first();

        return $tokenRecord;
    }

    public function setPassword(
        string $plainToken, 
        string $email, 
        string $password,
        string $type,
        bool $clearMustSetPassword = false,
    ): void{
        $tokenRecord = $this->validateToken($plainToken, $email, $type);

        if (! $tokenRecord) {
            Log::warning('Password action denied because token is invalid or expired.', [
                'status' => 'failed',
                'reason' => 'invalid_or_expired_password_token',
                'token_type' => $type,

            ]);

            throw ValidationException::withMessages([
                'email' => 'This password link is invalid or has expired.',
            ]);
        }

        $user = $tokenRecord->user;

        if (Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'password' => 'Your new password must be different from your current password.',
            ]);
        }

        $updates = [
            'password' => $password,
        ];

        if ($clearMustSetPassword) {
            $updates['must_set_password'] = false;
        }

        $user->update($updates);

        $tokenRecord->update([
            'used_at' => now(),
        ]);

        Log::info('Password action completed.', [
            'target_id' => $user->id,
            'user_id' => $user->id,
            'token_type' => $type,
            'status' => 'success',
        ]);
    }
}