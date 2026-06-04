<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Career;
use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getDashboardData(User $actor): array
    {
        $articleQuery = $this->articleBaseQuery($actor);
        $careerQuery = $this->careerBaseQuery($actor);
        $mediaQuery = $this->mediaBaseQuery();

        $totalArticles = (clone $articleQuery)->count();
        $totalCareers = (clone $careerQuery)->count();
        $totalMedia = (clone $mediaQuery)->count();
        $totalUsers = $this->canViewUserStats($actor) ? User::query()->count() : null;
        $pendingUserSetups = $this->canViewUserStats($actor)
            ? User::query()->where('must_set_password', true)->count()
            : 0;

        $articlePublishingTrend = $this->trendSummary(
            $this->articleBaseQuery($actor),
            'published_at'
        );

        $careerPublishingTrend = $this->trendSummary(
            $this->careerBaseQuery($actor),
            'published_at'
        );

        $mediaUploadTrend = $this->trendSummary(
            $this->mediaBaseQuery(),
            'created_at'
        );

        return [
            'stat_cards' => $this->buildStatCards(
                $actor,
                $totalArticles,
                $totalCareers,
                $totalMedia,
                $totalUsers,
                $pendingUserSetups,
                $articlePublishingTrend,
                $careerPublishingTrend,
                $mediaUploadTrend
            ),

            'stats' => [
                'total_articles' => $totalArticles,
                'published_articles' => (clone $articleQuery)->where('status', 'published')->count(),
                'archived_articles' => (clone $articleQuery)->where('status', 'archived')->count(),

                'total_careers' => $totalCareers,
                'published_careers' => (clone $careerQuery)->where('status', 'published')->count(),
                'closed_careers' => (clone $careerQuery)->where('status', 'closed')->count(),

                'total_users' => $this->canViewUserStats($actor) ? User::query()->count() : 0,
                'pending_user_setups' => $pendingUserSetups,

                'total_media' => $totalMedia,
            ],

            'article_status_chart' => [
                'labels' => ['Published', 'Draft', 'Archived'],
                'series' => $this->statusBreakdown((clone $articleQuery), [
                    'published',
                    'draft',
                    'archived',
                ]),
            ],

            'career_status_chart' => [
                'labels' => ['Published', 'Closed', 'Draft'],
                'series' => $this->statusBreakdown((clone $careerQuery), [
                    'published',
                    'closed',
                    'draft',
                ]),
            ],

            'monthly_published_content_chart' => $this->monthlyPublishedContent($actor),
            'monthly_archived_content_chart' => $this->monthlyArchivedContent($actor),
        ];
    }

    protected function buildStatCards(
        User $actor,
        int $totalArticles,
        int $totalCareers,
        int $totalMedia,
        ?int $totalUsers,
        int $pendingUserSetups,
        array $articlePublishingTrend,
        array $careerPublishingTrend,
        array $mediaUploadTrend
    ): array {
        $cards = [
            [
                'label' => 'Total Articles',
                'value' => $totalArticles,
                'context' => 'Published ' . number_format($articlePublishingTrend['current_total']),
                'pill_value' => $articlePublishingTrend['display'],
                'pill_label' => 'Published',
                'pill_tone' => $articlePublishingTrend['tone'],
                'pill_icon' => $articlePublishingTrend['icon'],
            ],
            [
                'label' => 'Total Careers',
                'value' => $totalCareers,
                'context' => 'Published ' . number_format($careerPublishingTrend['current_total']),
                'pill_value' => $careerPublishingTrend['display'],
                'pill_label' => 'Published',
                'pill_tone' => $careerPublishingTrend['tone'],
                'pill_icon' => $careerPublishingTrend['icon'],
            ],
            [
                'label' => 'Total Media',
                'value' => $totalMedia,
                'context' => 'Global library',
                'pill_value' => $mediaUploadTrend['display'],
                'pill_label' => 'Uploads',
                'pill_tone' => $mediaUploadTrend['tone'],
                'pill_icon' => $mediaUploadTrend['icon'],
            ],
        ];

        if ($this->canViewUserStats($actor)) {
            array_splice($cards, 2, 0, [[
                'label' => 'Total Users',
                'value' => $totalUsers ?? 0,
                'context' => 'Pending setup ' . number_format($pendingUserSetups),
                'pill_value' => number_format($pendingUserSetups),
                'pill_label' => 'Pending',
                'pill_tone' => 'warning',
                'pill_icon' => 'flat',
            ]]);
        }

        return $cards;
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
