<?php

namespace App\Services\Site;

use App\Models\Site;
use App\Models\SiteAssignment;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SiteService
{
    public function getPaginatedSites(?string $search = null): LengthAwarePaginator
    {
        return Site::query()
            ->with(['assignedUsers:id,name'])
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

    public function updateSite(Site $site, array $data, User $actor): Site
    {
        DB::transaction(function () use ($site, $data, $actor): void {
            $site->update([
                'name' => $data['name'],
                'domain' => $data['domain'] ?? null,
                'status' => $data['status'],
                'updated_by' => $actor->id,
            ]);

            $assignedUserIds = collect($data['assigned_user_ids'] ?? [])
                ->filter()
                ->unique()
                ->values();

            $existingAssignments = $site->siteAssignments()
                ->get()
                ->keyBy('user_id');

            $site->siteAssignments()
                ->whereNotIn('user_id', $assignedUserIds)
                ->delete();

            foreach ($assignedUserIds as $userId) {
                $assignment = $existingAssignments->get($userId);

                if ($assignment) {
                    $assignment->update([
                        'updated_by' => $actor->id,
                    ]);

                    continue;
                }

                SiteAssignment::create([
                    'user_id' => $userId,
                    'site_id' => $site->id,
                    'assigned_by' => $actor->id,
                    'updated_by' => $actor->id,
                ]);
            }
        });

        Log::info('Site updated.', [
            'actor_id' => $actor->id,
            'target_id' => $site->id,
            'site_id' => $site->id,
            'status' => 'success',
        ]);

        return $site->fresh(['assignedUsers']);
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

    public function toggleStatus(Site $site, User $actor): Site
    {
        $currentStatus = $site->status;
        $newStatus = $currentStatus === 'active' ? 'inactive' : 'active';

        $site->update([
            'status' => $newStatus,
            'updated_by' => $actor->id,
        ]);

        Log::info('Site status updated.', [
            'actor_id' => $actor->id,
            'target_id' => $site->id,
            'site_id' => $site->id,
            'old_status' => $currentStatus,
            'new_status' => $newStatus,
            'status' => 'success',
        ]);

        return $site->fresh();
    }
}
