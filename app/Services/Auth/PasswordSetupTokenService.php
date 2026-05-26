<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\UserToken;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PasswordSetupTokenService
{
    public function createForUser(User $user, ?User $actor = null): array
    {
        //invalidate old unused tokens so the new token remain valid and log it
        $invalidatedCount = UserToken::query()
            ->where('user_id', $user->id)
            ->where('type', 'password_setup')
            ->whereNull('used_at')
            ->update([
                'used_at' => now(),
            ]);

        if ($invalidatedCount > 0) {
            Log::info('Existing password setup tokens invalidated.', [
                'actor_id' => $actor?->id,
                'target_id' => $user->id,
                'user_id' => $user->id,
                'status' => 'success',
            ]);
        }

        $plainToken = Str::random(64);

        $userToken = UserToken::create([
            'user_id' => $user->id,
            'type' => 'password_setup',
            'token_hash' => hash('sha256', $plainToken),
            'expires_at' => Carbon::now()->addHours(24),
            'used_at' => null,
            'created_by' => $actor?->id,
        ]);

        Log::info('Password setup token created.', [
            'actor_id' => $actor?->id,
            'target_id' => $user->id,
            'user_id' => $user->id,
            'status' => 'success',
        ]);

        return [
            'plain_token' => $plainToken,
            'user_token' => $userToken,
        ];
    }
}