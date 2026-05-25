<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class BootstrapAdminSeeder extends Seeder
{
    public function run(): void
    {
        $name = env('BOOTSTRAP_ADMIN_NAME', 'System Admin');
        $email = env('BOOTSTRAP_ADMIN_EMAIL');
        $password = env('BOOTSTRAP_ADMIN_PASSWORD');

        if (! $email || ! $password) {
            Log::error('Bootstrap admin seeder failed because required environment variables are missing.', [
                'status' => 'failed',
                'reason' => 'missing_environment_variables',
            ]);

            throw new RuntimeException(
                'BOOTSTRAP_ADMIN_EMAIL and BOOTSTRAP_ADMIN_PASSWORD must be set before running the bootstrap admin seeder.'
            );
        }

        $user = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => $password,
                'role' => 'admin',
                'must_set_password' => false,
                'created_by' => null,
                'updated_by' => null,
            ]
        );

        if ($user->wasRecentlyCreated) {
            Log::info('Bootstrap admin created.', [
                'target_id' => $user->id,
                'status' => 'success',
            ]);
        } else {
            Log::info('Bootstrap admin already exists.', [
                'target_id' => $user->id,
                'status' => 'skipped',
            ]);
        }
    }
}
