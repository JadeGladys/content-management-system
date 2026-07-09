<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Career;
use App\Models\Media;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDashboardData(User $actor): array
    {
        return [
            'kpi_cards' => $this->buildKpiCards($actor),
            'publishing_activity' => $this->publishingActivity($actor),
            'status_chart' => $this->combinedStatusChart($actor),
            'recent_content' => $this->recentContent($actor),
            'careers_closing_soon' => $this->careersClosingSoon($actor),
            'stale_drafts' => $this->staleDrafts($actor),
            'top_categories' => $this->topCategories($actor),
            'popular_tags' => $this->popularTags(),
            'media_breakdown' => $this->mediaBreakdown(),
            'team_activity' => $this->canViewUserStats($actor) ? $this->teamActivity() : null,
        ];
    }

    protected function buildKpiCards(User $actor): array
    {
        $isAdmin = $this->canViewUserStats($actor);

        $articleTrend = $this->trendSummary($this->articleBaseQuery($actor), 'published_at');
        $careerTrend = $this->trendSummary($this->careerBaseQuery($actor), 'published_at');

        $cards = [
            [
                'label' => 'Total Articles',
                'value' => $this->articleBaseQuery($actor)->count(),
                'pill' => $articleTrend['display'],
                'pill_tone' => $articleTrend['tone'],
                'sparkline' => $this->monthlySeries($this->articleBaseQuery($actor), 'created_at'),
                'color' => '#6366f1',
            ],
            [
                'label' => 'Open Careers',
                'value' => $this->careerBaseQuery($actor)->where('status', 'published')->count(),
                'pill' => $careerTrend['display'],
                'pill_tone' => $careerTrend['tone'],
                'sparkline' => $this->monthlySeries($this->careerBaseQuery($actor), 'created_at'),
                'color' => '#0d9488',
            ],
            [
                'label' => 'Media Files',
                'value' => $this->mediaBaseQuery()->count(),
                'pill' => $this->formatBytes((int) $this->mediaBaseQuery()->sum('file_size')),
                'pill_tone' => 'neutral',
                'sparkline' => $this->monthlySeries($this->mediaBaseQuery(), 'created_at'),
                'color' => '#d97706',
            ],
            [
                'label' => 'Drafts Pending',
                'value' => $this->articleBaseQuery($actor)->where('status', 'draft')->count()
                    + $this->careerBaseQuery($actor)->where('status', 'draft')->count(),
                'pill' => 'needs review',
                'pill_tone' => 'warning',
                'sparkline' => $this->monthlySeries(
                    $this->articleBaseQuery($actor)->where('status', 'draft'),
                    'created_at'
                ),
                'color' => '#e11d48',
            ],
        ];

        if ($isAdmin) {
            array_splice($cards, 3, 0, [
                [
                    'label' => 'Team Members',
                    'value' => User::query()->count(),
                    'pill' => $this->onlineUsersCount() . ' online',
                    'pill_tone' => 'success',
                    'sparkline' => $this->monthlySeries(User::query(), 'created_at'),
                    'color' => '#0284c7',
                ],
                [
                    'label' => 'Tags',
                    'value' => Tag::query()->count(),
                    'pill' => '+' . $this->trendSummary(Tag::query(), 'created_at')['current_total'],
                    'pill_tone' => 'success',
                    'sparkline' => $this->monthlySeries(Tag::query(), 'created_at'),
                    'color' => '#7c3aed',
                ],
            ]);
        }

        return $cards;
    }

    protected function publishingActivity(User $actor): array
    {
        $published = $this->monthlyPublishedContent($actor);
        $archived = $this->monthlyArchivedContent($actor);

        return [
            'labels' => $published['labels'],
            'articles' => $published['articles'],
            'careers' => $published['careers'],
            'archived' => collect($archived['articles'])
                ->zip($archived['careers'])
                ->map(fn ($pair) => $pair->sum())
                ->all(),
        ];
    }

    protected function combinedStatusChart(User $actor): array
    {
        $articles = $this->statusBreakdown($this->articleBaseQuery($actor), ['published', 'draft', 'archived']);
        $careers = $this->statusBreakdown($this->careerBaseQuery($actor), ['published', 'draft', 'closed']);

        return [
            'labels' => ['Published', 'Draft', 'Archived', 'Closed careers'],
            'series' => [
                $articles[0] + $careers[0],
                $articles[1] + $careers[1],
                $articles[2],
                $careers[2],
            ],
        ];
    }

    protected function recentContent(User $actor, int $limit = 5): array
    {
        $articles = $this->articleBaseQuery($actor)
            ->with(['category', 'authorUser'])
            ->latest('updated_at')
            ->limit($limit)
            ->get()
            ->map(fn (Article $article) => [
                'title' => $article->title,
                'type' => 'Article',
                'category' => $article->category?->name,
                'author' => $article->authorUser?->name,
                'status' => $article->status,
                'updated_at' => $article->updated_at,
            ]);

        $careers = $this->careerBaseQuery($actor)
            ->with(['category', 'createdBy'])
            ->latest('updated_at')
            ->limit($limit)
            ->get()
            ->map(fn (Career $career) => [
                'title' => $career->title,
                'type' => 'Career',
                'category' => $career->category?->name,
                'author' => $career->createdBy?->name,
                'status' => $career->status,
                'updated_at' => $career->updated_at,
            ]);

        return $articles->concat($careers)
            ->sortByDesc('updated_at')
            ->take($limit)
            ->map(fn (array $item) => [
                ...$item,
                'author_initials' => $this->initials($item['author']),
                'updated_human' => $item['updated_at']?->diffForHumans() ?? '—',
            ])
            ->values()
            ->all();
    }

    protected function careersClosingSoon(User $actor, int $limit = 4): array
    {
        return $this->careerBaseQuery($actor)
            ->where('status', 'published')
            ->whereNotNull('deadline')
            ->whereBetween('deadline', [now()->startOfDay(), now()->addDays(14)->endOfDay()])
            ->orderBy('deadline')
            ->limit($limit)
            ->get()
            ->map(fn (Career $career) => [
                'title' => $career->title,
                'location' => $career->location,
                'employment_type' => $career->employment_type,
                'work_mode' => $career->work_mode,
                'days_left' => (int) now()->startOfDay()->diffInDays($career->deadline),
            ])
            ->all();
    }

    protected function staleDrafts(User $actor, int $limit = 5): array
    {
        $threshold = now()->subDays(14);

        $articles = $this->articleBaseQuery($actor)
            ->where('status', 'draft')
            ->where('updated_at', '<', $threshold)
            ->get(['title', 'updated_at']);

        $careers = $this->careerBaseQuery($actor)
            ->where('status', 'draft')
            ->where('updated_at', '<', $threshold)
            ->get(['title', 'updated_at']);

        return $articles->concat($careers)
            ->sortBy('updated_at')
            ->take($limit)
            ->map(fn ($item) => [
                'title' => $item->title,
                'days_stale' => (int) $item->updated_at->diffInDays(now()),
            ])
            ->values()
            ->all();
    }

    protected function topCategories(User $actor, int $limit = 5): array
    {
        $categories = ArticleCategory::query()
            ->withCount([
                'articles' => fn ($query) => $query->when(
                    $actor->role === 'editor',
                    fn ($q) => $q->where('author', $actor->id)
                ),
            ])
            ->orderByDesc('articles_count')
            ->limit($limit)
            ->get();

        return [
            'labels' => $categories->pluck('name')->all(),
            'series' => $categories->pluck('articles_count')->map(fn ($count) => (int) $count)->all(),
        ];
    }

    protected function popularTags(int $limit = 10): array
    {
        return Tag::query()
            ->withCount('articles')
            ->orderByDesc('articles_count')
            ->limit($limit)
            ->get()
            ->map(fn (Tag $tag) => ['name' => $tag->name, 'count' => (int) $tag->articles_count])
            ->all();
    }

    protected function mediaBreakdown(): array
    {
        $counts = $this->mediaBaseQuery()
            ->selectRaw('file_type, COUNT(*) as aggregate')
            ->groupBy('file_type')
            ->pluck('aggregate', 'file_type');

        $buckets = ['Images' => 0, 'Documents' => 0, 'Other' => 0];

        foreach ($counts as $mime => $count) {
            $bucket = match (true) {
                str_starts_with((string) $mime, 'image/') => 'Images',
                str_contains((string) $mime, 'pdf'),
                str_contains((string) $mime, 'word'),
                str_contains((string) $mime, 'spreadsheet'),
                str_starts_with((string) $mime, 'text/') => 'Documents',
                default => 'Other',
            };

            $buckets[$bucket] += $count;
        }

        return [
            'labels' => array_keys($buckets),
            'series' => array_values($buckets),
            'total_files' => array_sum($buckets),
            'total_size' => $this->formatBytes((int) $this->mediaBaseQuery()->sum('file_size')),
        ];
    }

    protected function teamActivity(int $limit = 4): array
    {
        $articleCounts = Article::query()
            ->selectRaw('author as user_id, COUNT(*) as aggregate')
            ->groupBy('author')
            ->pluck('aggregate', 'user_id');

        $careerCounts = Career::query()
            ->selectRaw('created_by as user_id, COUNT(*) as aggregate')
            ->groupBy('created_by')
            ->pluck('aggregate', 'user_id');

        $totals = $articleCounts->keys()
            ->merge($careerCounts->keys())
            ->unique()
            ->mapWithKeys(fn ($id) => [
                $id => (int) ($articleCounts[$id] ?? 0) + (int) ($careerCounts[$id] ?? 0),
            ])
            ->sortDesc()
            ->take($limit);

        $users = User::query()
            ->whereIn('id', $totals->keys())
            ->get(['id', 'name', 'role'])
            ->keyBy('id');

        $max = max($totals->max() ?? 0, 1);

        return $totals
            ->map(fn (int $count, string $userId) => [
                'name' => $users[$userId]->name ?? 'Unknown',
                'role' => $users[$userId]->role ?? null,
                'initials' => $this->initials($users[$userId]->name ?? null),
                'count' => $count,
                'percent' => (int) round(($count / $max) * 100),
            ])
            ->values()
            ->all();
    }

    protected function onlineUsersCount(): int
    {
        return DB::table('sessions')
            ->whereNotNull('user_id')
            ->where('last_activity', '>=', now()->subMinutes(5)->getTimestamp())
            ->distinct()
            ->count('user_id');
    }

    protected function monthlySeries(Builder $query, string $dateColumn, int $months = 8): array
    {
        $start = now()->subMonths($months - 1)->startOfMonth();

        $counts = $query
            ->whereNotNull($dateColumn)
            ->where($dateColumn, '>=', $start)
            ->selectRaw("to_char({$dateColumn}, 'YYYY-MM') as month_key, COUNT(*) as aggregate")
            ->groupBy('month_key')
            ->pluck('aggregate', 'month_key');

        return collect(range($months - 1, 0))
            ->map(fn (int $offset) => (int) ($counts[now()->subMonths($offset)->format('Y-m')] ?? 0))
            ->all();
    }

    protected function initials(?string $name): string
    {
        return collect(explode(' ', (string) $name))
            ->filter()
            ->take(2)
            ->map(fn (string $part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');
    }

    protected function formatBytes(int $bytes): string
    {
        return match (true) {
            $bytes >= 1073741824 => round($bytes / 1073741824, 1) . ' GB',
            $bytes >= 1048576 => round($bytes / 1048576, 1) . ' MB',
            $bytes >= 1024 => round($bytes / 1024, 1) . ' KB',
            default => $bytes . ' B',
        };
    }

    protected function articleBaseQuery(User $actor): Builder
    {
        return Article::query()
            ->when($actor->role === 'editor', function (Builder $query) use ($actor) {
                $query->where('author', $actor->id);
            });
    }

    protected function careerBaseQuery(User $actor): Builder
    {
        return Career::query()
            ->when($actor->role === 'editor', function (Builder $query) use ($actor) {
                $query->where('created_by', $actor->id);
            });
    }

    protected function mediaBaseQuery(): Builder
    {
        return Media::query();
    }

    protected function canViewUserStats(User $actor): bool
    {
        return $actor->role === 'admin';
    }

    protected function statusBreakdown(Builder $query, array $statuses): array
    {
        $counts = $query
            ->select('status', DB::raw('COUNT(*) as aggregate'))
            ->whereIn('status', $statuses)
            ->groupBy('status')
            ->pluck('aggregate', 'status');

        return collect($statuses)
            ->map(fn (string $status) => (int) ($counts[$status] ?? 0))
            ->all();
    }

    protected function monthlyPublishedContent(User $actor): array
    {
        $year = now()->year;

        $articleCounts = $this->articleBaseQuery($actor)
            ->whereNotNull('published_at')
            ->whereYear('published_at', $year)
            ->selectRaw('EXTRACT(MONTH FROM published_at)::int as month_number, COUNT(*) as aggregate')
            ->groupBy('month_number')
            ->orderBy('month_number')
            ->pluck('aggregate', 'month_number');

        $careerCounts = $this->careerBaseQuery($actor)
            ->whereNotNull('published_at')
            ->whereYear('published_at', $year)
            ->selectRaw('EXTRACT(MONTH FROM published_at)::int as month_number, COUNT(*) as aggregate')
            ->groupBy('month_number')
            ->orderBy('month_number')
            ->pluck('aggregate', 'month_number');

        $labels = collect(range(1, 12))
            ->map(fn (int $month) => now()->startOfYear()->month($month)->format('M'))
            ->all();

        return [
            'labels' => $labels,
            'articles' => collect(range(1, 12))
                ->map(fn (int $month) => (int) ($articleCounts[$month] ?? 0))
                ->all(),
            'careers' => collect(range(1, 12))
                ->map(fn (int $month) => (int) ($careerCounts[$month] ?? 0))
                ->all(),
        ];
    }

    protected function monthlyArchivedContent(User $actor): array
    {
        $year = now()->year;

        $articleCounts = $this->articleBaseQuery($actor)
            ->whereNotNull('archived_at')
            ->whereYear('archived_at', $year)
            ->selectRaw('EXTRACT(MONTH FROM archived_at)::int as month_number, COUNT(*) as aggregate')
            ->groupBy('month_number')
            ->orderBy('month_number')
            ->pluck('aggregate', 'month_number');

        $careerCounts = $this->careerBaseQuery($actor)
            ->whereNotNull('closed_at')
            ->whereYear('closed_at', $year)
            ->selectRaw('EXTRACT(MONTH FROM closed_at)::int as month_number, COUNT(*) as aggregate')
            ->groupBy('month_number')
            ->orderBy('month_number')
            ->pluck('aggregate', 'month_number');

        $labels = collect(range(1, 12))
            ->map(fn (int $month) => now()->startOfYear()->month($month)->format('M'))
            ->all();

        return [
            'labels' => $labels,
            'articles' => collect(range(1, 12))
                ->map(fn (int $month) => (int) ($articleCounts[$month] ?? 0))
                ->all(),
            'careers' => collect(range(1, 12))
                ->map(fn (int $month) => (int) ($careerCounts[$month] ?? 0))
                ->all(),
        ];
    }

    protected function trendSummary(Builder $query, string $dateColumn): array
    {
        $currentStart = now()->subDays(30)->startOfDay();
        $previousStart = now()->subDays(60)->startOfDay();
        $previousEnd = now()->subDays(30)->endOfDay();

        $currentTotal = (clone $query)
            ->whereNotNull($dateColumn)
            ->where($dateColumn, '>=', $currentStart)
            ->count();

        $previousTotal = (clone $query)
            ->whereNotNull($dateColumn)
            ->whereBetween($dateColumn, [$previousStart, $previousEnd])
            ->count();

        $percentage = $this->calculatePercentageChange($previousTotal, $currentTotal);

        return [
            'current_total' => $currentTotal,
            'previous_total' => $previousTotal,
            'percentage' => $percentage,
            'display' => $this->formatPercentage($percentage),
            'tone' => $percentage > 0 ? 'success' : ($percentage < 0 ? 'danger' : 'neutral'),
            'icon' => $percentage > 0 ? 'up' : ($percentage < 0 ? 'down' : 'flat'),
        ];
    }

    protected function calculatePercentageChange(int $previousTotal, int $currentTotal): float
    {
        if ($previousTotal === 0) {
            return $currentTotal > 0 ? 100.0 : 0.0;
        }

        return round((($currentTotal - $previousTotal) / $previousTotal) * 100, 1);
    }

    protected function formatPercentage(float $percentage): string
    {
        $formatted = number_format(abs($percentage), 1);

        if (str_ends_with($formatted, '.0')) {
            $formatted = (string) (int) $formatted;
        }

        return $formatted . '%';
    }
}
