<?php

namespace App\Services\Career;

use App\Models\Career;
use App\Models\CareerCategory;
use Illuminate\Support\Collection;

class PublicCareerService
{
    protected const FILTERABLE_COLUMNS = [
        'type',
        'employment_type',
        'work_mode',
        'location',
    ];

    public function getPublishedCareers(?string $search = null, array $filters = []): Collection
    {
        $filters = $this->normalizeFilters($filters);

        $query = Career::query()
            ->with(['category', 'createdBy'])
            ->where('status', 'published')
            ->where(function ($query) {
                $query
                    ->whereNull('deadline')
                    ->orWhere('deadline', '>', now());
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('title', 'ilike', "%{$search}%")
                        ->orWhere('slug', 'ilike', "%{$search}%")
                        ->orWhere('type', 'ilike', "%{$search}%")
                        ->orWhere('employment_type', 'ilike', "%{$search}%")
                        ->orWhere('work_mode', 'ilike', "%{$search}%")
                        ->orWhere('location', 'ilike', "%{$search}%")
                        ->orWhereHas('category', function ($categoryQuery) use ($search) {
                            $categoryQuery->where('name', 'ilike', "%{$search}%");
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
                ->latest('published_at')
                ->get()
                ->map(fn ($career) => $this->formatCareer($career, false));
    }

    public function getPublishedCareerBySlug(string $slug): ?array
    {
        $career = Career::query()
            ->with(['category', 'createdBy'])
            ->where('status', 'published')
            ->where('slug', $slug)
            ->first();

        if (! $career) {
            return null;
        }

        return $this->formatCareer($career, true);
    }

    private function formatCareer(Career $career, bool $includeContent = true): array
    {
        return [
            'id' => $career->id,
            'title' => $career->title,
            'slug' => $career->slug,
            'type' => $career->type,
            'employment_type' => $career->employment_type,
            'work_mode' => $career->work_mode,
            'overview' => $includeContent ? $career->overview : null,
            'description' => $includeContent ? $career->description : null,
            'requirements' => $includeContent ? $career->requirements : null,
            'category' => $career->category?->name,
            'application_url' => $career->application_url,
            'location' => $career->location,
            'deadline' => optional($career->deadline)->format('Y/m/d'),
            'published_at' => optional($career->published_at)->format('M Y'),

            'seo' => [
                'meta_title' => $career->meta_title ?: $career->title,
                'meta_description' => $career->meta_description ?: $career->overview,
                'canonical_url' => $career->canonical_url,
                'og_title' => $career->og_title ?: ($career->meta_title ?: $career->title),
                'og_description' => $career->og_description ?: ($career->meta_description ?: $career->overview),
                'no_index' => $career->no_index,
            ],
        ];
    }

    public function getFilterOptions(): array
    {
        return [
            'categories' => CareerCategory::query()
                ->orderBy('name')
                ->get(['id', 'name', 'slug'])
                ->map(fn ($category) => ['value' => $category->slug, 'label' => $category->name])
                ->values()
                ->all(),
            'types' => $this->mapOptions(Career::TYPES),
            'employment_types' => $this->mapOptions(Career::EMPLOYMENT_TYPES),
            'work_modes' => $this->mapOptions(Career::WORK_MODES),
            'locations' => Career::query()
                ->where('status', 'published')
                ->whereNotNull('location')
                ->select('location')
                ->distinct()
                ->orderBy('location')
                ->pluck('location')
                ->map(fn ($location) => ['value' => $location, 'label' => $location])
                ->values()
                ->all(),
        ];
    }

    protected function mapOptions(array $options): array
    {
        return collect($options)
            ->map(fn ($label, $value) => ['value' => $value, 'label' => $label])
            ->values()
            ->all();
    }

    protected function normalizeFilters(array $filters): array
    {
        return collect($filters)
            ->mapWithKeys(fn ($values, $key) => [
                $key => collect((array) $values)->filter()->values()->all(),
            ])
            ->all();
    }
}