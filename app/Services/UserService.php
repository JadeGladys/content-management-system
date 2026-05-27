<?php

namespace App\Services;

use App\Models\User;
use App\Services\Auth\PasswordTokenService;
use App\Mail\PasswordActionMail;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class UserService
{
    public function __construct(
        protected PasswordTokenService $passwordTokenService
    ) {
    }

    public function getPaginatedUsers(?string $search = null): LengthAwarePaginator
    {
        return User::query()
            ->withCount('assignedSites')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('name', 'ilike', "%{$search}%")
                        ->orWhere('email', 'ilike', "%{$search}%")
                        ->orWhere('role', 'ilike', "%{$search}%");
                });
            })
            ->latest('updated_at')
            ->paginate(8)
            ->withQueryString();
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

        $tokenData = $this->passwordTokenService->createForUser(
            $user,
            'password_setup',
            24,
            $actor,
        );

        $setupUrl = route('password.setup', [
            'token' => $tokenData['plain_token'],
            'email' => $user->email,
        ]);

        try {
            Mail::to($user->email)->send(
                new PasswordActionMail(
                    $user,
                    $setupUrl,
                    'Set up your CMS password',
                    'Your CMS account has been created.',
                    'Set up password',
                    'This link will expire in 24 hours.',
                )
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
