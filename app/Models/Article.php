<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Article extends Model
{
    use HasUlids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'title',
        'slug',
        'article_category_id',
        'overview',
        'content',
        'featured_image_id',
        'status',
        'author',
        'updated_by',
        'published_at',
        'archived_at',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'published_at' => 'datetime',
            'archived_at' => 'datetime',
        ];
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_id');
    }
    
    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_image_id');
    }

    public function authorUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
