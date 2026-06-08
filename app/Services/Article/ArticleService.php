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

    public function getPaginatedArticles(?string $search, User $actor): LengthAwarePaginator
    {
        return Article::query()
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
            })
            ->latest('updated_at')
            ->paginate(8)
            ->withQueryString();
    }

    public function createArticle(array $data, User $actor): array
    {
        try {
            $article = Article::create([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'article_category_id' => $this->resolveCategoryId($data['category'], $actor),
                'overview' => null,
                'content' => null,
                'featured_image_id' => null,
                'status' => 'draft',

                'meta_title' => null,
                'meta_description' => null,
                'meta_keywords' => null,
                'canonical_url' => null,
                'og_title' => null,
                'og_description' => null,
                'og_image_id' => null,
                'no_index' => false,

                'author' => $actor->id,
                'updated_by' => $actor->id,
                'published_at' => null,
                'archived_at' => null,
            ]);

            Log::info('Article created.', [
                'actor_id' => $actor->id,
                'article_id' => $article->id,
                'title' => $article->title,
                'status' => 'success',
            ]);

            return [
                'article' => $article,
                'reused_existing_featured_image' => false,
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
                'author' => $article->author,
                'updated_by' => $actor->id,
                'published_at' => $isPublishing ? ($article->published_at ?? now()) : $article->published_at,
                'archived_at' => $article->archived_at,
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

            $this->articleSeoService->logGeneratedFields(
                $article,
                $actor,
                $seoResult['generated_fields'],
                $isGeneratingSeo ? 'generated' : 'updated'
            );

            $article->tags()->sync($resolvedTagIds);

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
