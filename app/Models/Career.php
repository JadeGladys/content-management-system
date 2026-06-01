<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Career extends Model
{
    use HasUlids;

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'title',
        'slug',
        'career_category_id',
        'location',
        'department',
        'about',
        'description',
        'requirements',
        'deadline',
        'status',
        'created_by',
        'updated_by',
        'published_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'about' => 'array',
            'description' => 'array',
            'requirements' => 'array',
            'deadline' => 'datetime',
            'published_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CareerCategory::class, 'career_category_id');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
