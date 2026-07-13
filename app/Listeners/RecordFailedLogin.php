<?php

namespace App\Listeners;

use App\Models\AuditLog;
use Illuminate\Auth\Events\Failed;

class RecordFailedLogin
{
    public function handle(Failed $event): void
    {
        if (! AuditLog::isRecording()) {
            return;
        }

        AuditLog::create([
            'user_id' => $event->user?->getAuthIdentifier(),
            'action' => 'login_failed',
            'new_values' => ['email' => $event->credentials['email'] ?? null],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}