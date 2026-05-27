<?php

namespace App\Services\Site;

use App\Models\Site;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SiteService
{
    public function getPaginatedSites(?string $search = null): LengthAwarePaginator
    {
        return Site::query()
            ->withCount('assignedUsers')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('name', 'ilike', "%{$search}%")
                        ->orWhere('slug', 'ilike', "%{$search}%")
                        ->orWhere('domain', 'ilike', "%{$search}%");
                });
            })
            ->latest('updated_at')
            ->paginate(8)
            ->withQueryString();
    }
    
    public function createSite(array $data, User $actor): Site
    {
        $baseSlug = Str::slug($data['name']);
        $slug = $this->generateUniqueSlug($baseSlug);

        $site = Site::create([
            'name' => $data['name'],
            'slug' => $slug,
            'domain' => $data['domain'] ?? null,
            'status' => $data['status'],
            'created_by' => $actor->id,
            'updated_by' => $actor->id,
        ]);

        Log::info('Site created.', [
            'actor_id' => $actor->id,
            'target_id' => $site->id,
            'site_id' => $site->id,
            'status' => 'success',
        ]);

        return $site;
    }

    protected function generateUniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug;
        $counter = 2;

        while (Site::query()->where('slug', $slug)->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
