<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SetPasswordRequest;
use App\Services\Auth\PasswordSetupService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SetPasswordController extends Controller
{
    public function __construct(
        protected PasswordSetupService $passwordSetupService
    ) {
    }

    public function edit(Request $request): View
    {
        $tokenRecord = $this->passwordSetupService->validateToken(
            $request->string('token')->toString(),
            $request->string('email')->toString(),
        );

        if (! $tokenRecord) {
            Log::warning('Password setup page denied because token is invalid or expired.', [
                'status' => 'failed',
                'reason' => 'invalid_or_expired_password_setup_token',
            ]);

            abort(404);
        }

        return view('auth.set-password', [
            'token' => $request->string('token')->toString(),
            'email' => $request->string('email')->toString(),
        ]);
    }

    public function update(SetPasswordRequest $request): RedirectResponse
    {
        $this->passwordSetupService->setPassword(
            $request->validated('token'),
            $request->validated('email'),
            $request->validated('password'),
        );

        return redirect()
            ->route('login')
            ->with('success', 'Your password has been set. You can now sign in.');
    }
}