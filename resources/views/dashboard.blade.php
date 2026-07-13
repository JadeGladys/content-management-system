@extends('layouts.admin', ['title' => 'Dashboard'])

@section('content')
    @php
        $pillClasses = [
            'success' => 'bg-emerald-50 text-emerald-600',
            'danger' => 'bg-rose-50 text-rose-500',
            'warning' => 'bg-amber-50 text-amber-600',
            'neutral' => 'bg-slate-100 text-slate-500',
        ];

        $statusClasses = [
            'published' => 'bg-emerald-50 text-emerald-600',
            'draft' => 'bg-amber-50 text-amber-600',
            'archived' => 'bg-rose-50 text-rose-500',
            'closed' => 'bg-slate-100 text-slate-500',
        ];

        $tagPalette = [
            'bg-indigo-50 text-indigo-600',
            'bg-teal-50 text-teal-600',
            'bg-violet-50 text-violet-600',
            'bg-amber-50 text-amber-600',
            'bg-sky-50 text-sky-600',
            'bg-rose-50 text-rose-500',
        ];

        $avatarPalette = ['bg-slate-800', 'bg-indigo-600', 'bg-teal-600', 'bg-rose-500', 'bg-amber-500', 'bg-sky-600'];
        $barPalette = ['bg-indigo-500', 'bg-teal-500', 'bg-violet-500', 'bg-amber-400'];
    @endphp

    {{-- Header: greeting + quick actions --}}
    <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div>
            <p class="text-sm font-medium text-slate-500">{{ now()->format('l, j F Y') }}</p>
            <h1 class="mt-1 text-2xl font-bold tracking-tight text-slate-900">
                Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ str(auth()->user()->name)->before(' ') }} 👋
            </h1>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('articles.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" d="M12 5v14M5 12h14" /></svg>
                New Article
            </a>
            <a href="{{ route('careers.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-teal-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-teal-500">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" d="M12 5v14M5 12h14" /></svg>
                New Career
            </a>
            <a href="{{ route('media.index') }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2A2.5 2.5 0 0 0 5.5 21h13a2.5 2.5 0 0 0 2.5-2.5v-2M12 3v13m0-13 4 4m-4-4-4 4" /></svg>
                Upload Media
            </a>
        </div>
    </div>

    {{-- KPI cards --}}
    <div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-3 {{ count($kpi_cards) === 6 ? 'xl:grid-cols-6' : 'xl:grid-cols-4' }}">
        @foreach ($kpi_cards as $card)
            <div class="rounded-[20px] border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background-color: {{ $card['color'] }}1a; color: {{ $card['color'] }}">
                        <svg class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" /></svg>
                    </span>
                    <span class="rounded-full px-2 py-0.5 text-xs font-semibold {{ $pillClasses[$card['pill_tone']] ?? $pillClasses['neutral'] }}">
                        {{ $card['pill'] }}
                    </span>
                </div>

                <p class="mt-3 text-2xl font-bold tracking-tight">{{ number_format($card['value']) }}</p>
                <p class="text-xs font-medium text-slate-500">{{ $card['label'] }}</p>

                <div class="kpi-spark mt-2 h-8" data-spark="{{ json_encode($card['sparkline']) }}" data-color="{{ $card['color'] }}"></div>
            </div>
        @endforeach
    </div>

    {{-- Publishing activity + status donut --}}
    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="rounded-[20px] border border-slate-200 bg-white p-6 shadow-sm xl:col-span-2">
            <div>
                <h3 class="text-base font-bold text-slate-900">Publishing Activity</h3>
                <p class="text-sm text-slate-500">Published vs archived content this year</p>
            </div>
            <div id="activity-chart" class="mt-4 h-[300px]"></div>
        </div>

        <div class="rounded-[20px] border border-slate-200 bg-white p-6 shadow-sm">
            <h3 class="text-base font-bold text-slate-900">Content by Status</h3>
            <p class="text-sm text-slate-500">Articles &amp; careers combined</p>
            <div id="status-donut" class="mx-auto mt-2 h-[220px]"></div>

            @php
                $statusTotal = max(array_sum($status_chart['series']), 1);
                $statusDotColors = ['bg-indigo-500', 'bg-amber-400', 'bg-rose-400', 'bg-slate-300'];
            @endphp
            <div class="mt-2 space-y-2.5 border-t border-slate-100 pt-4 text-sm">
                @foreach ($status_chart['labels'] as $index => $label)
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span class="h-2.5 w-2.5 rounded-full {{ $statusDotColors[$index] }}"></span>
                            {{ $label }}
                        </span>
                        <span class="font-semibold">
                            {{ number_format($status_chart['series'][$index]) }}
                            <span class="text-xs font-normal text-slate-400">({{ round($status_chart['series'][$index] / $statusTotal * 100) }}%)</span>
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Recent content + needs attention --}}
    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="overflow-hidden rounded-[20px] border border-slate-200 bg-white shadow-sm xl:col-span-2">
            <div class="flex items-center justify-between px-6 pt-6">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Recent Content</h3>
                </div>
            </div>

            <div class="max-w-full overflow-x-auto">
                <table class="mt-4 w-full min-w-[560px] table-fixed text-left text-sm">
                    <colgroup>
                        <col class="w-[28%]">
                        <col class="w-[11%]">
                        <col class="w-[16%]">
                        <col class="w-[19%]">
                        <col class="w-[13%]">
                        <col class="w-[14%]">
                    </colgroup>
                    <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-400">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Title</th>
                            <th class="px-3 py-3 font-semibold">Type</th>
                            <th class="px-3 py-3 font-semibold">Category</th>
                            <th class="px-3 py-3 font-semibold">Author</th>
                            <th class="px-3 py-3 font-semibold">Status</th>
                            <th class="px-3 py-3 font-semibold">Updated</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recent_content as $item)
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="px-4 py-3.5 font-medium text-slate-800">
                                    <span class="block truncate" title="{{ $item['title'] }}">{{ $item['title'] }}</span>
                                </td>
                                <td class="px-3 py-3.5">
                                    <span class="whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold {{ $item['type'] === 'Article' ? 'bg-indigo-50 text-indigo-600' : 'bg-teal-50 text-teal-600' }}">
                                        {{ $item['type'] }}
                                    </span>
                                </td>
                                <td class="px-3 py-3.5 text-slate-500">
                                    <span class="block truncate" title="{{ $item['category'] ?? '—' }}">{{ $item['category'] ?? '—' }}</span>
                                </td>
                                <td class="px-3 py-3.5">
                                    <span class="flex min-w-0 items-center gap-2">
                                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[10px] font-bold text-white {{ $avatarPalette[$loop->index % count($avatarPalette)] }}">
                                            {{ $item['author_initials'] ?: '?' }}
                                        </span>
                                        <span class="truncate text-slate-600">{{ $item['author'] ?? 'Unknown' }}</span>
                                    </span>
                                </td>
                                <td class="px-3 py-3.5">
                                    <span class="whitespace-nowrap rounded-full px-2.5 py-1 text-xs font-semibold capitalize {{ $statusClasses[$item['status']] ?? $statusClasses['closed'] }}">
                                        {{ $item['status'] }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-3.5 text-slate-400">{{ $item['updated_human'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-sm text-slate-500">No content yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex flex-col gap-6">
            {{-- Careers closing soon --}}
            <div class="rounded-[20px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">Careers Closing Soon</h3>
                    <span class="rounded-full bg-rose-50 px-2.5 py-1 text-xs font-bold text-rose-500">{{ count($careers_closing_soon) }}</span>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse ($careers_closing_soon as $career)
                        @php
                            [$boxClasses, $textClasses] = match (true) {
                                $career['days_left'] < 3 => ['border-rose-100 bg-rose-50/50', 'text-rose-500'],
                                $career['days_left'] < 7 => ['border-amber-100 bg-amber-50/50', 'text-amber-600'],
                                default => ['border-slate-100 bg-slate-50/60', 'text-slate-500'],
                            };
                        @endphp
                        <a href="{{ route('careers.index') }}" class="flex items-center justify-between rounded-xl border px-4 py-3 transition hover:-translate-y-0.5 {{ $boxClasses }}">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-slate-800">{{ $career['title'] }}</p>
                                <p class="truncate text-xs text-slate-500">
                                    {{ collect([$career['location'], $career['employment_type'], $career['work_mode']])->filter()->implode(' · ') }}
                                </p>
                            </div>
                            <span class="ml-3 shrink-0 text-xs font-bold {{ $textClasses }}">
                                {{ $career['days_left'] === 0 ? 'Today' : $career['days_left'] . ' day' . ($career['days_left'] === 1 ? '' : 's') . ' left' }}
                            </span>
                        </a>
                    @empty
                        <p class="text-sm text-slate-500">No deadlines in the next 14 days.</p>
                    @endforelse
                </div>
            </div>

            {{-- Stale drafts --}}
            <div class="rounded-[20px] border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">Stale Drafts</h3>
                    <span class="text-xs font-medium text-slate-400">untouched &gt; 14 days</span>
                </div>

                <div class="mt-4 space-y-3 text-sm">
                    @forelse ($stale_drafts as $draft)
                        <a href="{{ $draft['edit_url'] }}" class="group flex items-center justify-between">
                            <p class="truncate pr-3 font-medium text-slate-700 transition group-hover:text-blue-600">{{ $draft['title'] }}</p>
                            <span class="shrink-0 text-xs text-slate-400">{{ $draft['days_stale'] }} days</span>
                        </a>
                    @empty
                        <p class="text-sm text-slate-500">No stale drafts — nice work.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Categories + tags + media + team --}}
    <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-2 {{ $team_activity !== null ? 'xl:grid-cols-4' : 'xl:grid-cols-3' }}">
        <div class="rounded-[20px] border border-slate-200 bg-white p-4 shadow-sm">
            <h3 class="text-base font-bold text-slate-900">Top Categories</h3>
            <p class="text-sm text-slate-500">By article count</p>
            <div id="categories-bar" class="mt-2 h-[210px]"></div>
        </div>

        <div class="rounded-[20px] border border-slate-200 bg-white p-4 shadow-sm">
            <h3 class="text-base font-bold text-slate-900">Popular Tags</h3>
            <p class="text-sm text-slate-500">Most used across articles</p>

            <div class="mt-4 flex flex-wrap gap-2">
                @forelse ($popular_tags as $tag)
                    <span class="rounded-full px-3 py-1.5 text-xs font-semibold {{ $tagPalette[$loop->index % count($tagPalette)] }}">
                        {{ $tag['name'] }} <span class="opacity-60">{{ $tag['count'] }}</span>
                    </span>
                @empty
                    <p class="text-sm text-slate-500">No tags yet.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-[20px] border border-slate-200 bg-white p-4 shadow-sm">
            <h3 class="text-base font-bold text-slate-900">Media Library</h3>
            <p class="text-sm text-slate-500">{{ number_format($media_breakdown['total_files']) }} files · {{ $media_breakdown['total_size'] }} used</p>
            <div id="media-donut" class="mx-auto mt-2 h-[160px]"></div>

            @php $mediaDotColors = ['bg-indigo-500', 'bg-teal-500', 'bg-amber-400']; @endphp
            <div class="mt-1 space-y-2 text-sm">
                @foreach ($media_breakdown['labels'] as $index => $label)
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full {{ $mediaDotColors[$index] }}"></span>
                            {{ $label }}
                        </span>
                        <span class="font-semibold text-slate-700">{{ number_format($media_breakdown['series'][$index]) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        @if ($team_activity !== null)
            <div class="rounded-[20px] border border-slate-200 bg-white p-4 shadow-sm">
                <h3 class="text-base font-bold text-slate-900">Team Activity</h3>
                <p class="text-sm text-slate-500">Content by team member</p>

                <div class="mt-4 space-y-4">
                    @forelse ($team_activity as $member)
                        <div>
                            <div class="flex items-center justify-between text-sm">
                                <span class="flex min-w-0 items-center gap-2 font-medium text-slate-700">
                                    <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full text-[10px] font-bold text-white {{ $avatarPalette[$loop->index % count($avatarPalette)] }}">
                                        {{ $member['initials'] ?: '?' }}
                                    </span>
                                    <span class="truncate">{{ $member['name'] }}</span>
                                    @if ($member['role'])
                                        <span class="rounded bg-slate-100 px-1.5 text-[10px] font-semibold uppercase text-slate-500">{{ $member['role'] }}</span>
                                    @endif
                                </span>
                                <span class="font-semibold">{{ number_format($member['count']) }}</span>
                            </div>
                            <div class="mt-1.5 h-1.5 rounded-full bg-slate-100">
                                <div class="h-1.5 rounded-full {{ $barPalette[$loop->index % count($barPalette)] }}" style="width: {{ $member['percent'] }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">No content created yet.</p>
                    @endforelse
                </div>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script type="module">
        const publishingActivity = @json($publishing_activity);
        const statusChart = @json($status_chart);
        const topCategories = @json($top_categories);
        const mediaBreakdown = @json($media_breakdown);

        // Sparklines in KPI cards
        document.querySelectorAll('.kpi-spark').forEach((element) => {
            new ApexCharts(element, {
                chart: { type: 'area', height: 32, sparkline: { enabled: true } },
                series: [{ data: JSON.parse(element.dataset.spark) }],
                stroke: { curve: 'smooth', width: 2 },
                fill: { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0 } },
                colors: [element.dataset.color],
                tooltip: { enabled: false },
            }).render();
        });

        // Publishing activity (stacked area)
        const activityElement = document.querySelector('#activity-chart');

        if (activityElement) {
            new ApexCharts(activityElement, {
                chart: { type: 'area', height: 300, toolbar: { show: false }, fontFamily: 'inherit' },
                series: [
                    { name: 'Articles published', data: publishingActivity.articles },
                    { name: 'Careers published', data: publishingActivity.careers },
                    { name: 'Archived / closed', data: publishingActivity.archived },
                ],
                colors: ['#6366f1', '#14b8a6', '#f43f5e'],
                stroke: { curve: 'smooth', width: 2.5 },
                fill: { type: 'gradient', gradient: { opacityFrom: 0.25, opacityTo: 0.02 } },
                dataLabels: { enabled: false },
                grid: { borderColor: '#eef2f7', strokeDashArray: 4 },
                xaxis: {
                    categories: publishingActivity.labels,
                    labels: { style: { colors: '#94a3b8', fontSize: '12px' } },
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                },
                yaxis: {
                    min: 0,
                    forceNiceScale: true,
                    labels: { style: { colors: '#94a3b8', fontSize: '12px' } },
                },
                legend: { position: 'top', horizontalAlign: 'left', labels: { colors: '#475569' } },
                tooltip: { shared: true },
            }).render();
        }

        // Combined status donut
        const statusElement = document.querySelector('#status-donut');

        if (statusElement) {
            new ApexCharts(statusElement, {
                chart: { type: 'donut', height: 220, fontFamily: 'inherit' },
                series: statusChart.series,
                labels: statusChart.labels,
                colors: ['#6366f1', '#fbbf24', '#fb7185', '#cbd5e1'],
                legend: { show: false },
                dataLabels: { enabled: false },
                stroke: { width: 3, colors: ['#fff'] },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '74%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'Total',
                                    fontSize: '13px',
                                    color: '#64748b',
                                    formatter: (chart) => chart.globals.seriesTotals.reduce((a, b) => a + b, 0),
                                },
                                value: { fontSize: '26px', fontWeight: 700, color: '#0f172a' },
                            },
                        },
                    },
                },
            }).render();
        }

        // Top categories horizontal bar
        const categoriesElement = document.querySelector('#categories-bar');

        if (categoriesElement && topCategories.labels.length > 0) {
            new ApexCharts(categoriesElement, {
                chart: { type: 'bar', height: 210, toolbar: { show: false }, fontFamily: 'inherit' },
                series: [{ name: 'Articles', data: topCategories.series }],
                colors: ['#6366f1'],
                plotOptions: { bar: { horizontal: true, borderRadius: 6, barHeight: '55%' } },
                dataLabels: {
                    enabled: true,
                    formatter: (value) => Math.round(value),
                    style: { fontSize: '12px', fontWeight: 600 },
                    dropShadow: { enabled: false },
                },
                grid: { show: false },
                xaxis: {
                    categories: topCategories.labels,
                    labels: { show: false },
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                },
                yaxis: { labels: { style: { colors: '#64748b', fontSize: '12px' } } },
                tooltip: { enabled: false },
            }).render();
        }

        // Media type donut
        const mediaElement = document.querySelector('#media-donut');

        if (mediaElement) {
            new ApexCharts(mediaElement, {
                chart: { type: 'donut', height: 160, fontFamily: 'inherit' },
                series: mediaBreakdown.series,
                labels: mediaBreakdown.labels,
                colors: ['#6366f1', '#14b8a6', '#fbbf24'],
                legend: { show: false },
                dataLabels: { enabled: false },
                stroke: { width: 3, colors: ['#fff'] },
                plotOptions: { pie: { donut: { size: '72%' } } },
            }).render();
        }
    </script>
@endpush
