<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Services\UserService;
use Illuminate\Contracts\View\View;

class UserController extends Controller
{
    public function create(): View
    {
        return view('users.create');
    }

    public function store(StoreUserRequest $request, UserService $userService)
    {
        $actor = $request->user();

        $userService->createUser($request->validated(), $actor);

        return redirect()
            ->route('users.create')
            ->with('success', 'User created successfully.');
    }
}
