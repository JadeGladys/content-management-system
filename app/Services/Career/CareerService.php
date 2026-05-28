<?php

namespace App\Services\Career;

use App\Models\Career;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CareerService
{
    public function getPaginatedCareers(?string $search, User $actor): LengthAwarePaginator
    {
        return Career::query()
            ->with([
                'createdBy:id,name',
            ])
            ->when($actor->role === 'editor', function ($query) use ($actor) {
                $query->where('created_by', $actor->id);
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($innerQuery) use ($search) {
                    $innerQuery
                        ->where('title', 'ilike', "%{$search}%")
                        ->orWhere('slug', 'ilike', "%{$search}%")
                        ->orWhere('category', 'ilike', "%{$search}%")
                        ->orWhere('department', 'ilike', "%{$search}%")
                        ->orWhere('location', 'ilike', "%{$search}%");
                });
            })
            ->latest('updated_at')
            ->paginate(8)
            ->withQueryString();
    }
}
