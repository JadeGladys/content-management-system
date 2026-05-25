<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserService
{
    public function createUser(array $data, User $actor): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => null,
            'role' => $data['role'],
            'must_set_password' => true,
            'created_by' => $actor->id,
            'updated_by' => $actor->id,
        ]);

        Log::info('User created.', [
            'actor_id' => $actor->id,
            'target_id' => $user->id,
            'user_id' => $user->id,
            'status' => 'success',
        ]);

        return $user;
    }
}