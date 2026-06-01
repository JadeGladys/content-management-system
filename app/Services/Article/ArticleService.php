<?php

namespace App\Services\Article;

use App\Models\Article;
use App\Models\User;
use App\Services\Media\MediaService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Throwable;

class ArticleService
{
    public function __construct(
        protected MediaService $mediaService
    ) {
    }

    public function getPaginatedArticles(?string $search, User $actor): LengthAwarePaginator
    {
        return Article::query()
            ->with([
                'authorUser:id,name',
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
                        ->orWhere('category', 'ilike', "%{$search}%")
                        ->orWhere('tags', 'ilike', "%{$search}")
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
                'category' => $data['category'],
                'tags' => [],
                'overview' => null,
                'content' => null,
                'featured_image_id' => null,
                'status' => 'draft',
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
            $isPublishing = ($data['action'] ?? 'save') === 'publish';

            if ($featuredImageUpload) {
                $media = $this->mediaService->storeMediaUpload($actor, $featuredImageUpload);
                $featuredImageId = $media->id;
                $reusedExistingFeaturedImage = ! $media->wasRecentlyCreated;
            }

            $article->update([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'category' => $data['category'],
                'tags' => $this->normalizeTags($data['tags'] ?? null),
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

            Log::info('Article updated.', [
                'actor_id' => $actor->id,
                'article_id' => $article->id,
                'title' => $article->title,
                'status' => $isPublishing ? 'published' : 'saved',
                'featured_image_reused' => $reusedExistingFeaturedImage,
            ]);

            return [
                'article' => $article->fresh(),
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

    protected function normalizeTags(?string $tags): array
    {
        if (blank($tags)) {
            return [];
        }

        return collect(explode(',', $tags))
            ->map(fn (string $tag) => trim($tag))
            ->filter()
            ->values()
            ->all();
    }
}
