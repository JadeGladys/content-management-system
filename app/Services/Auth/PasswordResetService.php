<?php

namespace App\Services\Auth;

use App\Mail\PasswordActionMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class PasswordResetService
{
    public function __construct(
        protected PasswordTokenService $passwordTokenService
    ) {
    }

    public function sendResetLink(string $email): void
    {
        $user = User::query()->where('email', $email)->first();

        if (! $user) {
            Log::warning('Password reset requested for unknown email.', [
                'status' => 'failed',
                'reason' => 'user_not_found',
            ]);

            return;
        }

        $tokenData = $this->passwordTokenService->createForUser(
            $user,
            'password_reset',
            1,
        );

        $resetUrl = route('password.reset', [
            'token' => $tokenData['plain_token'],
            'email' => $user->email,
        ]);

        try {
            Mail::to($user->email)->send(
                new PasswordActionMail(
                    $user,
                    $resetUrl,
                    'Reset your CMS password',
                    'We received a request to reset your CMS password.',
                    'Reset password',
                    'This link will expire in 1 hour.',
                )
            );

            Log::info('Password reset email sent.', [
                'target_id' => $user->id,
                'user_id' => $user->id,
                'status' => 'success',
            ]);
        } catch (Throwable $exception) {
            Log::error('Password reset email failed to send.', [
                'target_id' => $user->id,
                'user_id' => $user->id,
                'status' => 'failed',
                'reason' => 'mail_send_failed',
            ]);

            throw $exception;
        }
    }
}