<?php

namespace App\Services\Article;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Support\Collection;

class PublicArticleService
{
    protected const FILTERABLE_COLUMNS = [
        'type',
    ];

    public function getPublishedArticles(?string $search = null, array $filters = []): Collection
    {
        $filters = $this->normalizeFilters($filters);

        $query = Article::query()
            ->with(['category', 'featuredImage', 'authorUser'])
            ->where('status', 'published')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('title', 'ilike', "%{$search}%")
                        ->orWhere('slug', 'ilike', "%{$search}%")
                        ->orWhere('type', 'ilike', "%{$search}%")
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
                ->map(fn ($article) => $this->formatArticle($article, false));
    }

    public function getPublishedArticleBySlug(string $slug): ?array
    {
        $article = Article::query()
            ->with(['category', 'featuredImage', 'ogImage', 'authorUser'])
            ->where('status', 'published')
            ->where('slug', $slug)
            ->first();

        if (! $article) {
            return null;
        }

        return $this->formatArticle($article, true);
    }

    private function formatArticle(Article $article, bool $includeContent = true): array
    {
        return [
            'id' => $article->id,
            'title' => $article->title,
            'slug' => $article->slug,
            'overview' => $article->overview,
            'content' => $includeContent ? $article->content : null,
            'category' => $article->category?->name,
            'published_at' => optional($article->published_at)->format('M Y'),
            'reading_time' => $this->calculateReadingTime($article->content),
            'author' => $article->authorUser?->name,
            'featured_image' => $article->featuredImage
                ? asset('storage/' . $article->featuredImage->file_path)
                : null,

            'seo' => [
                'meta_title' => $article->meta_title ?: $article->title,
                'meta_description' => $article->meta_description ?: $article->overview,
                'meta_keywords' => $article->meta_keywords,
                'canonical_url' => $article->canonical_url,
                'og_title' => $article->og_title ?: ($article->meta_title ?: $article->title),
                'og_description' => $article->og_description ?: ($article->meta_description ?: $article->overview),
                'og_image' => $article->ogImage
                    ? asset('storage/' . $article->ogImage->file_path)
                    : null,
                'no_index' => $article->no_index,
            ],
        ];
    }

    public function getFilterOptions(): array
    {
        return [
            'categories' => ArticleCategory::query()
                ->orderBy('name')
                ->get(['id', 'name', 'slug'])
                ->map(fn ($category) => ['value' => $category->slug, 'label' => $category->name])
                ->values()
                ->all(),
            'types' => $this->mapOptions(Article::TYPES),
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

    private function calculateReadingTime(?array $content): string
    {
        if (empty($content)) {
            return '1 min read';
        }

        $text = collect($content)
            ->flatten()
            ->filter(fn ($value) => is_string($value))
            ->implode(' ');

        $wordCount = str_word_count(strip_tags($text));

        $minutes = max(1, ceil($wordCount / 200));

        return $minutes . ' min read';
    }
}