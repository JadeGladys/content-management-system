<?php

namespace App\Services\Career;

use App\Models\Career;
use App\Models\CareerCategory;
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
                'category:id,name,slug',
                'createdBy:id,name',
            ])
            ->when($actor->role === 'admin', function ($query) use ($actor) {
                $query->where(function ($visibilityQuery) use ($actor) {
                    $visibilityQuery
                        ->whereIn('status', ['published', 'closed'])
                        ->orWhere(function ($draftQuery) use ($actor) {
                            $draftQuery
                                ->where('status', 'draft')
                                ->where('created_by', $actor->id);
                        });
                });
            })
            ->when($actor->role === 'editor', function ($query) use ($actor) {
                $query->where('created_by', $actor->id);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('title', 'ilike', "%{$search}%")
                        ->orWhere('slug', 'ilike', "%{$search}%")
                        ->orWhereHas('category', function ($categoryQuery) use ($search) {
                            $categoryQuery->where('name', 'ilike', "%{$search}%");
                        })
                        ->orWhere('department', 'ilike', "%{$search}%")
                        ->orWhere('location', 'ilike', "%{$search}%")
                        ->orWhereHas('createdBy', function ($createdByQuery) use ($search) {
                            $createdByQuery->where('name', 'ilike', "%{$search}%");
                        });
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
                'slug' => $data['slug'],
                'career_category_id' => $this->resolveCategoryId($data['category'], $actor),
                'location' => null,
                'department' => $data['department'],
                'about' => null,
                'description' => null,
                'requirements' => null,
                'deadline' => null,
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

    public function updateCareer(array $data, Career $career, User $actor): Career
    {
        try {
            $isPublishing = ($data['action'] ?? 'save') === 'publish';

            $career->update([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'career_category_id' => $this->resolveCategoryId($data['category'], $actor),
                'location' => $data['location'] ?? null,
                'department' => $data['department'],
                'about' => filled($data['about'] ?? null)
                    ? json_decode($data['about'], true)
                    : null,
                'description' => filled($data['description'] ?? null)
                    ? json_decode($data['description'], true)
                    : null,
                'requirements' => filled($data['requirements'] ?? null)
                    ? json_decode($data['requirements'], true)
                    : null,
                'deadline' => $data['deadline'] ?? null,
                'status' => $isPublishing
                    ? 'published'
                    : $career->status,
                'updated_by' => $actor->id,
                'published_at' => $isPublishing
                    ? ($career->published_at ?? now())
                    : $career->published_at,
            ]);

            Log::info('Career updated.', [
                'actor_id' => $actor->id,
                'career_id' => $career->id,
                'title' => $career->title,
                'status' => $isPublishing ? 'published' : 'saved',
            ]);

            return $career->fresh();
        } catch (Throwable $exception) {
            Log::error('Career update failed.', [
                'actor_id' => $actor->id,
                'career_id' => $career->id,
                'title' => $data['title'] ?? null,
                'status' => 'failed',
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function canTransitionStatus(Career $career, string $targetStatus): bool
    {
        return match ($career->status) {
            'published' => in_array($targetStatus, ['draft', 'closed'], true),
            'closed' => $targetStatus === 'draft',
            default => false,
        };
    }

    public function transitionStatus(Career $career, string $targetStatus, User $actor): Career
    {
        if (! $this->canTransitionStatus($career, $targetStatus)) {
            throw new \InvalidArgumentException('Invalid career status transition.');
        }

        $career->update([
            'status' => $targetStatus,
            'published_at' => $targetStatus === 'draft' ? null : $career->published_at,
            'closed_at' => $targetStatus === 'closed' ? now() : null,
            'updated_by' => $actor->id,
        ]);

        Log::info('Career status updated.', [
            'actor_id' => $actor->id,
            'career_id' => $career->id,
            'from_status' => $career->getOriginal('status'),
            'to_status' => $targetStatus,
            'status' => 'success',
        ]);

        return $career->fresh(['createdBy', 'category']);
    }

    protected function resolveCategoryId(string $categoryName, User $actor): string
    {
        $normalizedCategoryName = trim($categoryName);
        $slug = Str::slug($normalizedCategoryName) ?: 'career-category';

        $category = CareerCategory::query()->firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $normalizedCategoryName,
                'created_by' => $actor->id,
            ]
        );

        return $category->id;
    }
}
