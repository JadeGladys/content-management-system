<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ForgotPasswordRequest;
use App\Http\Requests\Auth\PasswordActionRequest;
use App\Services\Auth\PasswordActionService;
use App\Services\Auth\PasswordResetService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PasswordResetController extends Controller
{
    public function __construct(
        protected PasswordResetService $passwordResetService,
        protected PasswordActionService $passwordActionService,
    ) {
    }

    public function create(): View
    {
        return view('auth.forgot-password');
    }

    public function store(ForgotPasswordRequest $request): RedirectResponse
    {
        $this->passwordResetService->sendResetLink(
            $request->validated('email')
        );

        return back()->with('success', 'If the email is registered, a password reset link has been sent.');
    }

    public function edit(Request $request): View|Response
    {
        $tokenRecord = $this->passwordActionService->validateToken(
            $request->string('token')->toString(),
            $request->string('email')->toString(),
            'password_reset',
        );

        if (! $tokenRecord) {
            Log::warning('Password reset page denied because token is invalid or expired.', [
                'status' => 'failed',
                'reason' => 'invalid_or_expired_password_reset_token',
            ]);

            return response()->view('errors.password-link', [
                'code' => '410',
                'title' => 'Password reset link expired',
                'message' => 'This password reset link is invalid or has expired. Request a new reset link and try again.',
                'primaryActionLabel' => 'Go to login',
                'primaryActionUrl' => route('login'),
                'secondaryActionLabel' => 'Request new link',
                'secondaryActionUrl' => route('password.request'),
            ], 410);
        }

        return view('auth.password-action', [
            'token' => $request->string('token')->toString(),
            'email' => $request->string('email')->toString(),
            'title' => 'Reset Password',
            'description' => 'Create a new password for your ISCO CMS account.',
            'submitLabel' => 'Reset Password',
            'formAction' => route('password.update'),
        ]);
    }

    public function update(PasswordActionRequest $request): RedirectResponse
    {
        $this->passwordActionService->setPassword(
            $request->validated('token'),
            $request->validated('email'),
            $request->validated('password'),
            'password_reset',
        );

        return redirect()
            ->route('login')
            ->with('success', 'Your password has been reset. You can now sign in.');
    }
}