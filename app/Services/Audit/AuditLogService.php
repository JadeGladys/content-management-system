<?php

namespace App\Services\Audit;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\AuditLog;
use App\Models\Career;
use App\Models\CareerCategory;
use App\Models\Media;
use App\Models\Tag;
use App\Models\User;
use App\Models\ThemeSetting;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AuditLogService
{
    public const AUDITABLE_TYPES = [
        'article' => Article::class,
        'career' => Career::class,
        'media' => Media::class,
        'tag' => Tag::class,
        'article_category' => ArticleCategory::class,
        'career_category' => CareerCategory::class,
        'user' => User::class,
        'theme_setting' => ThemeSetting::class,
    ];

    public const RESTORABLE_TYPES = [
        Article::class,
        Career::class,
        Tag::class,
        ArticleCategory::class,
        CareerCategory::class,
    ];

    protected const CATEGORY_DEPENDENCIES = [
        Article::class => ['column' => 'article_category_id', 'model' => ArticleCategory::class],
        Career::class => ['column' => 'career_category_id', 'model' => CareerCategory::class],
    ];

    public function getPaginatedAuditLogs(?string $search, array $filters): LengthAwarePaginator
    {
        $createdFrom = $filters['created_from'] ?? null;
        $createdTo = $filters['created_to'] ?? null;
        unset($filters['created_from'], $filters['created_to']);

        $filters = $this->normalizeFilters($filters);

        $query = AuditLog::query()
            ->with(['user:id,name', 'auditable'])
            ->when($search, function ($query) use ($search) {
                $actionSearch = str_replace(' ', '_', $search);
                $typeSearch = str_replace(' ', '', $search);

                $query->where(function ($innerQuery) use ($search, $actionSearch, $typeSearch) {
                    $innerQuery
                        ->where('action', 'ilike', "%{$actionSearch}%")
                        ->orWhere('auditable_type', 'ilike', "%{$typeSearch}%")
                        ->orWhereRaw("new_values->>'email' ilike ?", ["%{$search}%"])
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'ilike', "%{$search}%");
                        });
                });
            });

        if (! empty($filters['action'])) {
            $query->whereIn('action', $filters['action']);
        }

        if (! empty($filters['type'])) {
            $query->whereIn('auditable_type', collect($filters['type'])
                ->map(fn ($type) => self::AUDITABLE_TYPES[$type] ?? null)
                ->filter()
                ->values()
                ->all());
        }

        if (! empty($filters['user'])) {
            $query->whereIn('user_id', $filters['user']);
        }

        if (! empty($createdFrom)) {
            $query->whereDate('created_at', '>=', $createdFrom);
        }

        if (! empty($createdTo)) {
            $query->whereDate('created_at', '<=', $createdTo);
        }

        return $query
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();
    }

    public function getStats(): array
    {
        return [
            'events_today' => AuditLog::query()
                ->whereDate('created_at', today())
                ->count(),
            'events_this_week' => AuditLog::query()
                ->where('created_at', '>=', now()->startOfWeek())
                ->count(),
            'failed_logins_last_week' => AuditLog::query()
                ->where('action', 'login_failed')
                ->where('created_at', '>=', now()->subDays(7))
                ->count(),
        ];
    }

    protected function normalizeFilters(array $filters): array
    {
        return collect($filters)
            ->mapWithKeys(fn ($values, $key) => [
                $key => collect((array) $values)->filter()->values()->all(),
            ])
            ->all();
    }

    public function restore(AuditLog $log): Model
    {
        if ($log->action !== 'deleted') {
            throw new \InvalidArgumentException('Only deleted records can be restored.');
        }

        if (! in_array($log->auditable_type, self::RESTORABLE_TYPES, true)) {
            throw new \InvalidArgumentException('This record type cannot be restored.');
        }

        if (empty($log->old_values)) {
            throw new \InvalidArgumentException('This audit entry has no recoverable data.');
        }

        $modelClass = $log->auditable_type;

        if ($modelClass::query()->whereKey($log->old_values['id'] ?? null)->exists()) {
            throw new \InvalidArgumentException('This record already exists — it may have been restored already.');
        }

        $dependency = self::CATEGORY_DEPENDENCIES[$log->auditable_type] ?? null;

        if ($dependency) {
            $categoryId = $log->old_values[$dependency['column']] ?? null;

            if ($categoryId && ! $dependency['model']::query()->whereKey($categoryId)->exists()) {
                $categoryName = AuditLog::query()
                    ->where('action', 'deleted')
                    ->where('auditable_type', $dependency['model'])
                    ->where('auditable_id', $categoryId)
                    ->latest('created_at')
                    ->first()
                    ?->old_values['name'] ?? 'its category';

                throw new \InvalidArgumentException(
                    "This record's category was also deleted. Restore the \"{$categoryName}\" category first, then restore this record."
                );
            }
        }

        return DB::transaction(function () use ($log, $modelClass) {
            $record = AuditLog::withoutRecording(function () use ($log, $modelClass) {
                $record = new $modelClass;
                $record->forceFill($log->old_values);
                $record->save();

                return $record;
            });
            $record->writeAudit('restored', [], $log->old_values);
            return $record;
        });

    }
}
