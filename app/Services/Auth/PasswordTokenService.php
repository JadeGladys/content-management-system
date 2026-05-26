<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\UserToken;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PasswordTokenService
{
    public function createForUser(
        User $user,
        string $type,
        int $expiresInHours,
        ?User $actor = null
    ): array{
        //invalidate old unused tokens so the new token remain valid and log it
        $invalidatedCount = UserToken::query()
            ->where('user_id', $user->id)
            ->where('type', $type)
            ->whereNull('used_at')
            ->update([
                'used_at' => now(),
            ]);

        if ($invalidatedCount > 0) {
            Log::info('Existing password tokens invalidated.', [
                'actor_id' => $actor?->id,
                'target_id' => $user->id,
                'user_id' => $user->id,
                'token_type' => $type,
                'status' => 'success',
            ]);
        }

        $plainToken = Str::random(64);

        $userToken = UserToken::create([
            'user_id' => $user->id,
            'type' => $type,
            'token_hash' => hash('sha256', $plainToken),
            'expires_at' => Carbon::now()->addHours($expiresInHours),
            'used_at' => null,
            'created_by' => $actor?->id,
        ]);

        Log::info('Password token created.', [
            'actor_id' => $actor?->id,
            'target_id' => $user->id,
            'user_id' => $user->id,
            'token_type' => $type,
            'status' => 'success',
        ]);

        return [
            'plain_token' => $plainToken,
            'user_token' => $userToken,
        ];
    }
}