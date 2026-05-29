<?php

namespace App\Services\Career;

use App\Models\Career;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class CareerService
{
    public function getPaginatedCareers(?string $search, User $actor): LengthAwarePaginator
    {
        return Career::query()
            ->with([
                'createdBy:id,name',
            ])
            ->when($actor->role === 'editor', function ($query) use ($actor) {
                $query->where('created_by', $actor->id);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('title', 'ilike', "%{$search}%")
                        ->orWhere('slug', 'ilike', "%{$search}%")
                        ->orWhere('category', 'ilike', "%{$search}%")
                        ->orWhere('department', 'ilike', "%{$search}%")
                        ->orWhere('location', 'ilike', "%{$search}%");
                });
            })
            ->latest('updated_at')
            ->paginate(8)
            ->withQueryString();
    }

    public function createCareer(array $data, User $actor): Career
    {
        try {
            $career = Career::create([
                'title' => $data['title'],
                'slug' => $this->generateUniqueSlug($data['title']),
                'category' => $data['category'],
                'location' => $data['location'],
                'department' => $data['department'],
                'about' => $data['about'],
                'description' => $data['description'],
                'requirements' => $data['requirements'],
                'deadline' => $data['deadline'] ?? null,
                'status' => 'draft',
                'created_by' => $actor->id,
                'updated_by' => $actor->id,
                'published_at' => null,
                'closed_at' => null,
            ]);

            Log::info('Career created.', [
                'actor_id' => $actor->id,
                'career_id' => $career->id,
                'title' => $career->title,
                'status' => 'success',
            ]);

            return $career;
        } catch (Throwable $exception) {
            Log::error('Career creation failed.', [
                'actor_id' => $actor->id,
                'title' => $data['title'] ?? null,
                'status' => 'failed',
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    protected function generateUniqueSlug(string $title): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 2;

        while (Career::query()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
