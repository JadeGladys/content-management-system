<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\Auditable;

class Career extends Model
{
    use Auditable;
    
    use HasUlids;

    public $incrementing = false;

    protected $keyType = 'string';

    public const TYPES = [
        'security' => 'Security',
        'corporate' => 'Corporate',
        'technology' => 'Technology',
    ];

    public const EMPLOYMENT_TYPES = [
        'full_time' => 'Full-time',
        'part_time' => 'Part-time',
        'contract' => 'Contract',
        'internship' => 'Internship',
        'temporary' => 'Temporary',
    ];

    public const WORK_MODES = [
        'onsite' => 'On-site',
        'remote' => 'Remote',
        'hybrid' => 'Hybrid',
    ];

    protected $fillable = [
        'title',
        'slug',
        'type',
        'career_category_id',
        'location',
        'employment_type',
        'work_mode',
        'overview',
        'description',
        'requirements',
        'application_url',
        'deadline',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'canonical_url',
        'og_title',
        'og_description',
        'no_index',
        'created_by',
        'updated_by',
        'published_at',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'overview' => 'array',
            'description' => 'array',
            'requirements' => 'array',
            'deadline' => 'datetime',
            'no_index' => 'boolean',
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
