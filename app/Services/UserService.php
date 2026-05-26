<?php

namespace App\Services;

use App\Models\User;
use App\Services\Auth\PasswordSetupTokenService;
use App\Mail\PasswordSetupMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class UserService
{
    public function __construct(
        protected PasswordSetupTokenService $passwordSetupTokenService
    ) {
    }

    public function createUser(array $data, User $actor): array
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => null,
            'role' => $data['role'],
            'must_set_password' => true,
            'created_by' => $actor->id,
            'updated_by' => $actor->id,
        ]);

        Log::info('User created.', [
            'actor_id' => $actor->id,
            'target_id' => $user->id,
            'user_id' => $user->id,
            'status' => 'success',
        ]);

        $tokenData = $this->passwordSetupTokenService->createForUser($user, $actor);

        $setupUrl = route('password.setup', [
            'token' => $tokenData['plain_token'],
            'email' => $user->email,
        ]);

        try {
            Mail::to($user->email)->send(
                new PasswordSetupMail($user, $setupUrl)
            );

            Log::info('Password setup email sent.', [
                'actor_id' => $actor->id,
                'target_id' => $user->id,
                'user_id' => $user->id,
                'status' => 'success',
            ]);
        } catch (Throwable $exception) {
            Log::error('Password setup email failed to send.', [
                'actor_id' => $actor->id,
                'target_id' => $user->id,
                'user_id' => $user->id,
                'status' => 'failed',
                'reason' => 'mail_send_failed',
            ]);

            throw $exception;
        }

        return [
            'user' => $user,
            'password_setup_token' => $tokenData['plain_token'],
            'user_token' => $tokenData['user_token'],
        ];
    }
}