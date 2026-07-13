<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\Auditable;

class Media extends Model
{
    use Auditable;
    
    use HasUlids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'file_name',
        'file_hash',
        'file_path',
        'file_type',
        'file_size',
        'uploaded_by',
        'updated_by',
    ];

    public function getPublicUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function featuredInArticles(): HasMany
    {
        return $this->hasMany(Article::class, 'featured_image_id');
    }
}
