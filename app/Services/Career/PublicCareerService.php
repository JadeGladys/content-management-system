<?php

namespace App\Services\Career;

use App\Models\Career;
use Illuminate\Support\Collection;

class PublicCareerService
{
    public function getPublishedCareers(): Collection
    {
        return Career::query()
            ->with(['category', 'createdBy'])
            ->where('status', 'published')
            ->where(function ($query) {
                $query
                    ->whereNull('deadline')
                    ->orWhere('deadline', '>', now());
            })
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
}