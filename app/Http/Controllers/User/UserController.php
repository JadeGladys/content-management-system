<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Models\User;
use App\Services\User\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $filters = [
            'roles' => collect((array) $request->input('roles', []))
                ->filter()
                ->values()
                ->all(),
            'access_statuses' => collect((array) $request->input('access_statuses', []))
                ->filter()
                ->values()
                ->all(),
        ];

        return view('users.index', [
            'users' => $this->userService->getPaginatedUsers($search, $filters),
            'search' => $search,
            'filters' => $filters,
            'hasActiveFilters' => ! empty($filters['roles']) || ! empty($filters['access_statuses']),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $actor = $request->user();

        $this->userService->createUser($request->validated(), $actor);

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function resendPasswordSetup(User $user, Request $request): RedirectResponse
    {
        try {
            $this->userService->resendPasswordSetup($user, $request->user());
        } catch (ValidationException $exception) {
            return redirect()
                ->route('users.index')
                ->with('error', $exception->errors()['user'][0] ?? 'Unable to resend password setup link.');
        }

        return redirect()
            ->route('users.index')
            ->with('success', 'Password setup link sent successfully.');
    }
}
