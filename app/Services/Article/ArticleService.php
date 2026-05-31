<?php

namespace App\Services\Article;

use App\Models\Article;
use App\Services\Media\MediaService;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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
            ->when($actor->role === 'editor', function ($query) use ($actor) {
                $query->where('author', $actor->id);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('title', 'ilike', "%{$search}%")
                        ->orWhere('slug', 'ilike', "%{$search}%")
                        ->orWhere('category', 'ilike', "%{$search}%")
                        ->orWhere('content', 'ilike', "%{$search}%")
                        ->orWhereHas('authorUser', function ($authorQuery) use ($search) {
                            $authorQuery->where('name', 'ilike', "%{$search}%");
                        });
                });
            })
            ->latest('updated_at')
            ->paginate(8)
            ->withQueryString();
    }

    public function createArticle(array $data, User $actor, ?UploadedFile $featuredImageUpload = null): array
    {
        try {
            $featuredImageId = $data['featured_image_id'] ?? null;
            $reusedExistingFeaturedImage = false;

            if ($featuredImageUpload) {
                $media = $this->mediaService->storeMediaUpload($actor, $featuredImageUpload);
                $featuredImageId = $media->id;
                $reusedExistingFeaturedImage = ! $media->wasRecentlyCreated;
            }

            $article = Article::create([
                'title' => $data['title'],
                'slug' => $this->generateUniqueSlug($data['title']),
                'category' => $data['category'],
                'tags' => $this->normalizeTags($data['tags'] ?? null),
                'overview' => $data['overview'] ?? null,
                'content' => isset($data['content']) 
                    ? json_decode($data['content'], true) 
                    : null,
                'featured_image_id' => $featuredImageId,
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
                'featured_image_id' => $featuredImageId,
                'status' => 'success',
                'featured_image_reused' => $reusedExistingFeaturedImage,
            ]);

            return [
                'article' => $article,
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

    protected function generateUniqueSlug(string $title): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 2;

        while (Article::query()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
