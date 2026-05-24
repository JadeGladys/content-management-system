<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\StoreUserRequest;
use App\Services\UserService;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function store(StoreUserRequest $request, UserService $userService)
    {
        $actor = auth()->user();

        $userService->createUser($request->validated(), $actor);

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully.');
    }
}