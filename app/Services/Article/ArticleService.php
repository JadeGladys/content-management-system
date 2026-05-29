<?php

namespace App\Services\Article;

use App\Models\Article;
use App\Models\Media;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ArticleService
{
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

    public function createArticle(array $data, User $actor, ?UploadedFile $featuredImageUpload = null): Article
    {
        try {
            $featuredImageId = $data['featured_image_id'] ?? null;

            if ($featuredImageUpload) {
                $media = $this->storeFeaturedImage($featuredImageUpload, $actor);
                $featuredImageId = $media->id;
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
            ]);

            return $article;
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

    protected function storeFeaturedImage(UploadedFile $file, User $actor): Media
    {
        $path = $file->store('media/articles', 'public');

        return Media::create([
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getMimeType() ?? $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => $actor->id,
            'updated_by' => $actor->id,
        ]);
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
