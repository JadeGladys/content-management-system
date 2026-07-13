<?php

namespace App\Services\Article;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Tag;
use App\Models\User;
use App\Services\Article\ArticleSeoService;
use App\Services\Media\MediaService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ArticleService
{
    public function __construct(
        protected MediaService $mediaService,
        protected ArticleSeoService $articleSeoService
    ) {
    }

    protected const FILTERABLE_COLUMNS = [
        'type',
        'status',
        'author',
    ];

    public function getPaginatedArticles(?string $search, array $filters, User $actor): LengthAwarePaginator
    {
        $publishedFrom = $filters['published_from'] ?? null;
        $publishedTo = $filters['published_to'] ?? null;
        unset($filters['published_from'], $filters['published_to']);

        $filters = $this->normalizeFilters($filters);

        $query = Article::query()
            ->with([
                'authorUser:id,name',
                'category:id,name,slug',
                'tags:id,name,slug',
            ])
            ->when($actor->role === 'admin', function ($query) use ($actor) {
                $query->where(function ($visibilityQuery) use ($actor) {
                    $visibilityQuery
                        ->whereIn('status', ['published', 'archived'])
                        ->orWhere(function ($draftQuery) use ($actor) {
                            $draftQuery
                                ->where('status', 'draft')
                                ->where('author', $actor->id);
                        });
                });
            })
            ->when($actor->role === 'editor', function ($query) use ($actor) {
                $query->where('author', $actor->id);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('title', 'ilike', "%{$search}%")
                        ->orWhere('slug', 'ilike', "%{$search}%")
                        ->orWhere('type', 'ilike', "%{$search}%")
                        ->orWhere('meta_title', 'ilike', "%{$search}%")
                        ->orWhereHas('category', function ($categoryQuery) use ($search) {
                            $categoryQuery->where('name', 'ilike', "%{$search}%");
                        })
                        ->orWhereHas('tags', function ($tagQuery) use ($search) {
                            $tagQuery->where('name', 'ilike', "%{$search}%");
                        })
                        ->orWhereHas('authorUser', function ($authorQuery) use ($search) {
                            $authorQuery->where('name', 'ilike', "%{$search}%");
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

        if (! empty($filters['tag'])) {
            $query->whereHas('tags', function ($tagQuery) use ($filters) {
                $tagQuery->whereIn('slug', $filters['tag']);
            });
        }

        if (! empty($publishedFrom)) {
            $query->whereDate('published_at', '>=', $publishedFrom);
        }

        if (! empty($publishedTo)) {
            $query->whereDate('published_at', '<=', $publishedTo);
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

    public function createArticle(array $data, User $actor, ?UploadedFile $featuredImageUpload = null): array
    {
        try {
            $featuredImageId = $data['featured_image_id'] ?? null;
            $reusedExistingFeaturedImage = false;
            $action = $data['action'] ?? 'save';
            $isPublishing = $action === 'publish';
            $isGeneratingSeo = $action === 'generate_seo';

            if ($featuredImageUpload) {
                $media = $this->mediaService->storeMediaUpload($actor, $featuredImageUpload);
                $featuredImageId = $media->id;
                $reusedExistingFeaturedImage = ! $media->wasRecentlyCreated;
            }

            $resolvedTagIds = $this->resolveTagIds(
                $data['tag_ids'] ?? [],
                $data['new_tags'] ?? [],
                $actor
            );

            $resolvedTagNames = Tag::query()
                ->whereIn('id', $resolvedTagIds)
                ->pluck('name')
                ->all();

            $article = Article::create([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'article_category_id' => $this->resolveCategoryId($data['category'], $actor),
                'overview' => $data['overview'] ?? null,
                'content' => filled($data['content'] ?? null)
                    ? json_decode($data['content'], true)
                    : null,
                'featured_image_id' => $featuredImageId,
                'status' => $isPublishing ? 'published' : 'draft',
                'type' => $data['type'] ?? 'article',
                'is_featured' => (bool) ($data['is_featured'] ?? false),

                'meta_title' => null,
                'meta_description' => null,
                'meta_keywords' => null,
                'canonical_url' => null,
                'og_title' => null,
                'og_description' => null,
                'og_image_id' => $data['og_image_id'] ?? $featuredImageId,
                'no_index' => (bool) ($data['no_index'] ?? false),

                'author' => $actor->id,
                'updated_by' => $actor->id,
                'published_at' => $isPublishing ? now() : null,
                'archived_at' => null,
            ]);

            $article->load('category');

            $seoResult = $this->articleSeoService->buildPayload(
                $data,
                $article,
                $resolvedTagNames,
                $featuredImageId,
                $isGeneratingSeo
            );

            $article->update($seoResult['payload']);
            
            $article->tags()->sync($resolvedTagIds);
            if (! empty($resolvedTagNames)) {
                $article->writeAudit('updated', ['tags' => []], ['tags' => $resolvedTagNames]);
            }

            $this->articleSeoService->logGeneratedFields(
                $article,
                $actor,
                $seoResult['generated_fields'],
                $isGeneratingSeo ? 'generated' : 'created'
            );

            if ($isPublishing) {
                $article->auditAction('published');
            }
            
            Log::info('Article created.', [
                'actor_id' => $actor->id,
                'article_id' => $article->id,
                'title' => $article->title,
                'status' => $isPublishing ? 'published' : 'success',
                'featured_image_reused' => $reusedExistingFeaturedImage,
            ]);

            return [
                'article' => $article->fresh(['category', 'tags', 'featuredImage']),
                'reused_existing_featured_image' => $reusedExistingFeaturedImage,
            ];
        } catch (Throwable $exception) {
            Log::error('Article creation failed.', [
                'actor_id' => $actor->id,
                'title' => $data['title'] ?? null,
                'status' => 'failed',
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function updateArticle(array $data, Article $article, User $actor, ?UploadedFile $featuredImageUpload = null): array
    {
        try {
            $featuredImageId = $data['featured_image_id'] ?? $article->featured_image_id;
            $reusedExistingFeaturedImage = false;
            $action = $data['action'] ?? 'save';
            $isPublishing = $action === 'publish';
            $isGeneratingSeo = $action === 'generate_seo';

            if ($featuredImageUpload) {
                $media = $this->mediaService->storeMediaUpload($actor, $featuredImageUpload);
                $featuredImageId = $media->id;
                $reusedExistingFeaturedImage = ! $media->wasRecentlyCreated;
            }

            $resolvedTagIds = $this->resolveTagIds(
                $data['tag_ids'] ?? [],
                $data['new_tags'] ?? [],
                $actor
            );

            $resolvedTagNames = Tag::query()
                ->whereIn('id', $resolvedTagIds)
                ->pluck('name')
                ->all();

            $article->update([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'article_category_id' => $this->resolveCategoryId($data['category'], $actor),
                'overview' => $data['overview'] ?? null,
                'content' => filled($data['content'] ?? null)
                    ? json_decode($data['content'], true)
                    : null,
                'featured_image_id' => $featuredImageId,
                'status' => $isPublishing ? 'published' : $article->status,
                'type' => $data['type'] ?? 'article',
                'is_featured' => (bool) ($data['is_featured'] ?? false),
                'author' => $article->author,
                'updated_by' => $actor->id,
                'published_at' => $isPublishing ? ($article->published_at ?? now()) : $article->published_at,
                'archived_at' => $article->archived_at,
            ]);

            if ($article->wasChanged('status') && $article->status === 'published') {
                $article->auditAction('published');
            }

            $article->load('category');

            $seoResult = $this->articleSeoService->buildPayload(
                $data,
                $article,
                $resolvedTagNames,
                $featuredImageId,
                $isGeneratingSeo
            );

            $article->update($seoResult['payload']);

            $this->articleSeoService->logGeneratedFields(
                $article,
                $actor,
                $seoResult['generated_fields'],
                $isGeneratingSeo ? 'generated' : 'updated'
            );

            $oldTags = $article->tags()->orderBy('name')->pluck('name')->all();

            $article->tags()->sync($resolvedTagIds);

            $newTags = $article->tags()->orderBy('name')->pluck('name')->all();

            if ($oldTags !== $newTags) {
                $article->writeAudit('updated', ['tags' => $oldTags], ['tags' => $newTags]);
            }

            Log::info('Article updated.', [
                'actor_id' => $actor->id,
                'article_id' => $article->id,
                'title' => $article->title,
                'status' => $isPublishing ? 'published' : 'saved',
                'featured_image_reused' => $reusedExistingFeaturedImage,
            ]);

            return [
                'article' => $article->fresh(['category', 'tags']),
                'reused_existing_featured_image' => $reusedExistingFeaturedImage,
            ];
        } catch (Throwable $exception) {
            Log::error('Article update failed.', [
                'actor_id' => $actor->id,
                'article_id' => $article->id,
                'title' => $data['title'] ?? null,
                'status' => 'failed',
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function deleteArticle(Article $article, User $actor): void
    {
        try {
            $articleId = $article->id;
            $articleTitle = $article->title;

            $article->delete();

            Log::info('Article deleted.', [
                'actor_id' => $actor->id,
                'article_id' => $articleId,
                'title' => $articleTitle,
                'status' => 'success',
            ]);
        } catch (Throwable $exception) {
            Log::error('Article deletion failed.', [
                'actor_id' => $actor->id,
                'article_id' => $article->id,
                'title' => $article->title,
                'status' => 'failed',
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }
    }

    public function canTransitionStatus(Article $article, string $targetStatus): bool
    {
        return match ($article->status) {
            'published' => in_array($targetStatus, ['draft', 'archived'], true),
            'archived' => $targetStatus === 'draft',
            default => false,
        };
    }

    public function transitionStatus(Article $article, string $targetStatus, User $actor): Article
    {
        if (! $this->canTransitionStatus($article, $targetStatus)) {
            throw new \InvalidArgumentException('Invalid article status transition.');
        }

        $article->update([
            'status' => $targetStatus,
            'published_at' => $targetStatus === 'draft' ? null : $article->published_at,
            'archived_at' => $targetStatus === 'archived' ? now() : null,
            'updated_by' => $actor->id,
        ]);

        $article->auditAction(match ($targetStatus) {
            'archived' => 'archived',
            'draft' => 'restored to draft',
            default => $targetStatus,
        });

        Log::info('Article status updated.', [
            'actor_id' => $actor->id,
            'article_id' => $article->id,
            'from_status' => $article->getOriginal('status'),
            'to_status' => $targetStatus,
            'status' => 'success',
        ]);

        return $article->fresh(['authorUser', 'category', 'featuredImage', 'tags']);
    }

    protected function resolveTagIds(array $tagIds, array $newTags, User $actor): array
    {
        $existingTagIds = collect($tagIds)
            ->filter()
            ->unique()
            ->values();

        $newTagIds = collect($newTags)
            ->map(fn ($tag) => trim((string) $tag))
            ->filter()
            ->unique(fn ($tag) => Str::slug($tag))
            ->map(function (string $tag) use ($actor) {
                $tagSlug = Str::slug($tag);

                $tagModel = Tag::query()->firstOrCreate(
                    ['slug' => $tagSlug],
                    [
                        'name' => $tag,
                        'created_by' => $actor->id,
                    ]
                );

                return $tagModel->id;
            });

        return $existingTagIds
            ->merge($newTagIds)
            ->unique()
            ->values()
            ->all();
    }

    protected function resolveCategoryId(string $categoryName, User $actor): string
    {
        $normalizedCategoryName = trim($categoryName);
        $slug = Str::slug($normalizedCategoryName) ?: 'article-category';

        $category = ArticleCategory::query()->firstOrCreate(
            ['slug' => $slug],
            [
                'name' => $normalizedCategoryName,
                'created_by' => $actor->id,
            ]
        );

        return $category->id;
    }
}
