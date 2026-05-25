<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Auth\SessionAuthenticationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request, SessionAuthenticationService $authenticationService): RedirectResponse
    {
        $result = $authenticationService->attemptLogin(
            $request->validated(),
            $request->boolean('remember'),
            $request->session(),
        );

        if (! $result['success']) {
            $message = $result['reason'] === 'password_setup_incomplete'
                ? 'Your account setup is not complete yet.'
                : 'The provided credentials do not match our records.';

            return back()
                ->withErrors([
                    'email' => $message,
                ])
                ->onlyInput('email');
        }

        return redirect()->intended(route('users.create'));
    }

    public function destroy(SessionAuthenticationService $authenticationService): RedirectResponse
    {
        $user = Auth::user();

        $authenticationService->logout($user, request()->session());

        return redirect()->route('login');
    }
}
