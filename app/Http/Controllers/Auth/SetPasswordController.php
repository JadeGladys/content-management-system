<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordActionRequest;
use App\Services\Auth\PasswordActionService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SetPasswordController extends Controller
{
    public function __construct(
        protected PasswordActionService $passwordActionService
    ) {
    }

    public function edit(Request $request): View|Response
    {
        $tokenRecord = $this->passwordActionService->validateToken(
            $request->string('token')->toString(),
            $request->string('email')->toString(),
            'password_setup',
        );

        if (! $tokenRecord) {
            Log::warning('Password setup page denied because token is invalid or expired.', [
                'status' => 'failed',
                'reason' => 'invalid_or_expired_password_setup_token',
            ]);

            return response()->view('errors.password-link', [
                'code' => '410',
                'title' => 'Password setup link expired',
                'message' => 'This password setup link is invalid or has expired. Ask an administrator to send you a new one.',
                'primaryActionLabel' => 'Go to login',
                'primaryActionUrl' => route('login'),
                'secondaryActionLabel' => null,
                'secondaryActionUrl' => null,
            ], 410);
        }

        return view('auth.password-action', [
            'token' => $request->string('token')->toString(),
            'email' => $request->string('email')->toString(),
        ]);
    }

    public function update(PasswordActionRequest $request): RedirectResponse
    {
        $this->passwordActionService->setPassword(
            $request->validated('token'),
            $request->validated('email'),
            $request->validated('password'),
            'password_setup',
            true,
        );

        return redirect()
            ->route('login')
            ->with('success', 'Your password has been set. You can now sign in.');
    }
}