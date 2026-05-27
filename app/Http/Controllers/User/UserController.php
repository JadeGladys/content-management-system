<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Services\UserService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        return view('users.index', [
            'users' => $this->userService->getPaginatedUsers($search),
            'search' => $search,
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
}
