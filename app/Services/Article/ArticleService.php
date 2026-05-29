<?php

namespace App\Services\Article;

use App\Models\Article;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

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
}
