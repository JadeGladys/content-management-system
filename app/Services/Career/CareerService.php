<?php

namespace App\Services\Career;

use App\Models\Career;
use App\Models\CareerCategory;
use App\Models\User;
use App\Services\Career\CareerSeoService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class CareerService
{
    public function __construct(
        protected CareerSeoService $careerSeoService
    ) {
    }

    protected const FILTERABLE_COLUMNS = [
        'type',
        'employment_type',
        'work_mode',
        'location',
        'meta_title',
        'meta_keywords',
    ];

    public function getPaginatedCareers(?string $search, array $filters, User $actor): LengthAwarePaginator
    {
        $filters = $this->normalizeFilters($filters);

        $query = Career::query()
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
                        ->orWhere('meta_title', 'ilike', "%{$search}%")
                        ->orWhere('type', 'ilike', "%{$search}%")
                        ->orWhere('employment_type', 'ilike', "%{$search}%")
                        ->orWhere('work_mode', 'ilike', "%{$search}%")
                        ->orWhere('location', 'ilike', "%{$search}%")
                        ->orWhereHas('category', function ($categoryQuery) use ($search) {
                            $categoryQuery->where('name', 'ilike', "%{$search}%");
                        })
                        ->orWhereHas('createdBy', function ($createdByQuery) use ($search) {
                            $createdByQuery->where('name', 'ilike', "%{$search}%");
                        });
                });
            });

        foreach (self::FILTERABLE_COLUMNS as $column) {
            if (! empty($filters[$column])) {
                $query->whereIn($column, $filters[$column]);
            }
        }

        if (! empty($filters['category'])) {
            $query->whereHas('category', function ($categoryQuery) use ($filters) {
                $categoryQuery->whereIn('slug', $filters['category']);
            });
        }

        return $query
            ->latest('updated_at')
            ->paginate(8)
            ->withQueryString();
    }

    protected function normalizeFilters(array $filters): array
    {
        return collect($filters)
            ->mapWithKeys(fn ($values, $key) => [
                $key => collect((array) $values)->filter()->values()->all(),
            ])
            ->all();
    }

    public function createCareer(array $data, User $actor): Career
    {
        $this->ensureCanPublish($data);
        try {
            $action = $data['action'] ?? 'save';
            $isPublishing = $action === 'publish';
            $isGeneratingSeo = $action === 'generate_seo';

            $career = Career::create([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'type' => $data['type'] ?? null,
                'career_category_id' => $this->resolveCategoryId($data['category'], $actor),
                'employment_type' => $data['employment_type'] ?? null,
                'work_mode' => $data['work_mode'] ?? null,
                'application_url' => $data['application_url'] ?? null,
                'location' => $data['location'] ?? null,
                'overview' => filled($data['overview'] ?? null)
                    ? json_decode($data['overview'], true)
                    : null,
                'description' => filled($data['description'] ?? null)
                    ? json_decode($data['description'], true)
                    : null,
                'requirements' => filled($data['requirements'] ?? null)
                    ? json_decode($data['requirements'], true)
                    : null,
                'deadline' => $data['deadline'] ?? null,
                'status' => $isPublishing ? 'published' : 'draft',

                'meta_title' => null,
                'meta_description' => null,
                'meta_keywords' => null,
                'canonical_url' => null,
                'og_title' => null,
                'og_description' => null,
                'no_index' => (bool) ($data['no_index'] ?? false),

                'created_by' => $actor->id,
                'updated_by' => $actor->id,
                'published_at' => $isPublishing ? now() : null,
                'closed_at' => null,
            ]);

            $career->load('category');

            $seoResult = $this->careerSeoService->buildPayload(
                $data,
                $career,
                $isGeneratingSeo
            );

            $career->update($seoResult['payload']);

            $this->careerSeoService->logGeneratedFields(
                $career,
                $actor,
                $seoResult['generated_fields'],
                $isGeneratingSeo ? 'generated' : 'created'
            );

            Log::info('Career created.', [
                'actor_id' => $actor->id,
                'career_id' => $career->id,
                'title' => $career->title,
                'status' => $isPublishing ? 'published' : 'success',
            ]);

            return $career->fresh(['category']);
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
        $this->ensureCanPublish($data);
        try {
            $action = $data['action'] ?? 'save';
            $isPublishing = $action === 'publish';
            $isGeneratingSeo = $action === 'generate_seo';

            $career->update([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'type' => $data['type'] ?? null,
                'career_category_id' => $this->resolveCategoryId($data['category'], $actor),
                'employment_type' => $data['employment_type'] ?? null,
                'work_mode' => $data['work_mode'] ?? null,
                'application_url' => $data['application_url'] ?? null,
                'location' => $data['location'] ?? null,
                'overview' => filled($data['overview'] ?? null)
                    ? json_decode($data['overview'], true)
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

            $career->load('category');

            $seoResult = $this->careerSeoService->buildPayload(
                $data,
                $career,
                $isGeneratingSeo
            );

            $career->update($seoResult['payload']);

            $this->careerSeoService->logGeneratedFields(
                $career,
                $actor,
                $seoResult['generated_fields'],
                $isGeneratingSeo ? 'generated' : 'updated'
            );

            Log::info('Career updated.', [
                'actor_id' => $actor->id,
                'career_id' => $career->id,
                'title' => $career->title,
                'status' => $isPublishing ? 'published' : 'saved',
            ]);

            return $career->fresh(['category']);
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

    public function deleteCareer(Career $career, User $actor): void
    {
        try {
            $careerId = $career->id;
            $careerTitle = $career->title;

            $career->delete();

            Log::info('Career deleted.', [
                'actor_id' => $actor->id,
                'career_id' => $careerId,
                'title' => $careerTitle,
                'status' => 'success',
            ]);
        } catch (Throwable $exception) {
            Log::error('Career deletion failed.', [
                'actor_id' => $actor->id,
                'career_id' => $career->id,
                'title' => $career->title,
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

    private function ensureCanPublish(array $data): void
    {
        if (($data['action'] ?? null) !== 'publish') {
            return;
        }

        if (!empty($data['deadline']) && now()->greaterThan($data['deadline'])) {
            Log::warning('Career publish blocked due to deadline', [
                'career_id' => $career->id ?? null,
                'deadline' => $career->deadline ?? null,
            ]);
            throw new \DomainException(
                'Cannot publish a career with an expired deadline.'
            );
        }
    }
}
