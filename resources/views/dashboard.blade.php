@extends('layouts.admin', ['title' => 'Dashboard'])

@section('content')
    @php
        $monthlyChart = $monthly_published_content_chart;
        $monthlyArchivedChart = $monthly_archived_content_chart;
        $articlePublishedTotal = collect($monthlyChart['articles'])->sum();
        $careerPublishedTotal = collect($monthlyChart['careers'])->sum();
        $combinedPublishedTotal = $articlePublishedTotal + $careerPublishedTotal;
        $articleArchivedTotal = collect($monthlyArchivedChart['articles'])->sum();
        $careerClosedTotal = collect($monthlyArchivedChart['careers'])->sum();
        $combinedArchivedTotal = $articleArchivedTotal + $careerClosedTotal;
        $articleStatusTotal = collect($article_status_chart['series'])->sum();
        $careerStatusTotal = collect($career_status_chart['series'])->sum();
    @endphp

    <div class="overflow-hidden rounded-[28px] border border-slate-700 bg-[#182231] shadow-sm">
        <div class="grid grid-cols-1 divide-y divide-slate-700 md:grid-cols-2 md:divide-y-0 xl:divide-x {{ count($stat_cards) === 4 ? 'xl:grid-cols-4' : 'xl:grid-cols-3' }}">
            @foreach ($stat_cards as $card)
                @php
                    $pillClasses = match ($card['pill_tone']) {
                        'success' => 'bg-emerald-500/15 text-emerald-400',
                        'danger' => 'bg-rose-500/15 text-rose-400',
                        'warning' => 'bg-amber-500/15 text-amber-300',
                        default => 'bg-slate-500/15 text-slate-300',
                    };
                @endphp

                <div class="flex items-center justify-between gap-4 px-8 py-10">
                    <div>
                        <p class="text-[18px] font-medium tracking-[-0.02em] text-slate-100">
                            {{ $card['label'] }}
                        </p>

                        <div class="mt-1 flex items-end gap-3">
                            <h3 class="text-4xl font-semibold tracking-[-0.04em] text-[#7c83ff]">
                                {{ number_format($card['value']) }}
                            </h3>
                        </div>

                        <div class="mt-1 flex items-end gap-3">
                            <p class="mb-1 text-lg text-slate-400">
                                {{ $card['context'] }}
                            </p>
                        </div>

                        <span class="mt-3 inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm font-semibold {{ $pillClasses }}">
                            @if ($card['pill_icon'] === 'up')
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0 6 6m-6-6-6 6" />
                                </svg>
                            @elseif ($card['pill_icon'] === 'down')
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14m0 0 6-6m-6 6-6-6" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                </svg>
                            @endif

                            {{ $card['pill_value'] }} {{ $card['pill_label'] }}
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="mt-6 grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1.45fr)_minmax(340px,0.85fr)]">
        <div class="flex flex-col gap-6">
            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm md:p-6">
                <div class="mb-4 flex flex-col gap-4 border-b border-slate-200 pb-4 md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full border border-slate-200 bg-slate-100 text-slate-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7.5 15.75 11.25 12l2.25 2.25L18 8.25" />
                            </svg>
                        </div>

                        <div>
                            <h3 class="text-2xl font-semibold text-slate-900">{{ $combinedPublishedTotal }}</h3>
                            <p class="text-sm text-slate-500">
                                {{ auth()->user()->role === 'editor' ? 'Your published content this year' : 'Published content this year' }}
                            </p>
                        </div>
                    </div>

                    <div class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700">
                        Articles published: {{ $articlePublishedTotal }} | Careers published: {{ $careerPublishedTotal }}
                    </div>
                </div>

                <div id="monthly-published-content-chart" class="mt-6 h-[320px]"></div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm md:p-6">
                <div class="mb-4 flex flex-col gap-4 border-b border-slate-200 pb-4 md:flex-row md:items-center md:justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-full border border-slate-200 bg-slate-100 text-slate-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 4.5h12M6.75 8.25h10.5m-9.75 4.5h9m-8.25 4.5h7.5" />
                            </svg>
                        </div>

                        <div>
                            <h3 class="text-2xl font-semibold text-slate-900">{{ $combinedArchivedTotal }}</h3>
                            <p class="text-sm text-slate-500">
                                {{ auth()->user()->role === 'editor' ? 'Your archived content this year' : 'Archived and closed content this year' }}
                            </p>
                        </div>
                    </div>

                    <div class="inline-flex items-center rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700">
                        Articles archived: {{ $articleArchivedTotal }} | Careers closed: {{ $careerClosedTotal }}
                    </div>
                </div>

                <div id="monthly-archived-content-chart" class="mt-6 h-[320px]"></div>
            </div>
        </div>

        <div class="flex flex-col gap-6">
            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm md:p-6">
                <div class="flex items-start justify-between w-full">
                    <div class="flex-col items-center">
                        <div class="mb-1 flex items-center">
                            <h5 class="me-1 text-xl font-semibold text-slate-900">Articles by Status</h5>
                        </div>

                        <div class="flex items-center gap-3">
                            <p class="text-sm text-slate-500">Current article distribution</p>
                            <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-medium text-slate-700">
                                {{ $articleStatusTotal }} total
                            </span>
                        </div>
                    </div>
                </div>

                <div class="py-6" id="articles-status-pie-chart"></div>

                <div class="grid grid-cols-1 gap-2 border-t border-slate-200 pt-4 text-sm sm:grid-cols-3">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-[#4f46e5]"></span>
                        <span class="text-slate-600">Published: {{ $article_status_chart['series'][0] }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-[#f59e0b]"></span>
                        <span class="text-slate-600">Draft: {{ $article_status_chart['series'][1] }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-[#ef4444]"></span>
                        <span class="text-slate-600">Archived: {{ $article_status_chart['series'][2] }}</span>
                    </div>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-sm md:p-6">
                <div class="flex items-start justify-between w-full">
                    <div class="flex-col items-center">
                        <div class="mb-1 flex items-center">
                            <h5 class="me-1 text-xl font-semibold text-slate-900">Careers by Status</h5>
                        </div>

                        <div class="flex items-center gap-3">
                            <p class="text-sm text-slate-500">Current career distribution</p>
                            <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-medium text-slate-700">
                                {{ $careerStatusTotal }} total
                            </span>
                        </div>
                    </div>
                </div>

                <div class="py-6" id="careers-status-pie-chart"></div>

                <div class="grid grid-cols-1 gap-2 border-t border-slate-200 pt-4 text-sm sm:grid-cols-3">
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-[#0f766e]"></span>
                        <span class="text-slate-600">Published: {{ $career_status_chart['series'][0] }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-[#dc2626]"></span>
                        <span class="text-slate-600">Closed: {{ $career_status_chart['series'][1] }}</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="h-2.5 w-2.5 rounded-full bg-[#f59e0b]"></span>
                        <span class="text-slate-600">Draft: {{ $career_status_chart['series'][2] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script type="module">
        const monthlyPublishedContentChart = @json($monthly_published_content_chart);
        const monthlyArchivedContentChart = @json($monthly_archived_content_chart);
        const articleStatusChart = @json($article_status_chart);
        const careerStatusChart = @json($career_status_chart);

        const monthlyPublishedContentOptions = {
            chart: {
                type: 'bar',
                height: 320,
                toolbar: {
                    show: false,
                },
                fontFamily: 'inherit',
            },
            series: [
                {
                    name: 'Articles',
                    data: monthlyPublishedContentChart.articles,
                },
                {
                    name: 'Careers',
                    data: monthlyPublishedContentChart.careers,
                },
            ],
            colors: ['#4f46e5', '#0f766e'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '42%',
                    borderRadius: 8,
                    borderRadiusApplication: 'end',
                },
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                show: false,
            },
            grid: {
                borderColor: '#e2e8f0',
                strokeDashArray: 4,
            },
            xaxis: {
                categories: monthlyPublishedContentChart.labels,
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '12px',
                    },
                },
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false,
                },
            },
            yaxis: {
                min: 0,
                forceNiceScale: true,
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '12px',
                    },
                },
            },
            legend: {
                position: 'top',
                horizontalAlign: 'left',
                labels: {
                    colors: '#334155',
                },
            },
            tooltip: {
                shared: true,
                intersect: false,
            },
        };

        const monthlyPublishedContentElement = document.querySelector('#monthly-published-content-chart');

        if (monthlyPublishedContentElement) {
            const monthlyPublishedContentInstance = new ApexCharts(
                monthlyPublishedContentElement,
                monthlyPublishedContentOptions
            );

            monthlyPublishedContentInstance.render();
        }

        const monthlyArchivedContentOptions = {
            chart: {
                type: 'bar',
                height: 320,
                toolbar: {
                    show: false,
                },
                fontFamily: 'inherit',
            },
            series: [
                {
                    name: 'Archived Articles',
                    data: monthlyArchivedContentChart.articles,
                },
                {
                    name: 'Closed Careers',
                    data: monthlyArchivedContentChart.careers,
                },
            ],
            colors: ['#ef4444', '#f59e0b'],
            plotOptions: {
                bar: {
                    horizontal: false,
                    columnWidth: '42%',
                    borderRadius: 8,
                    borderRadiusApplication: 'end',
                },
            },
            dataLabels: {
                enabled: false,
            },
            stroke: {
                show: false,
            },
            grid: {
                borderColor: '#e2e8f0',
                strokeDashArray: 4,
            },
            xaxis: {
                categories: monthlyArchivedContentChart.labels,
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '12px',
                    },
                },
                axisBorder: {
                    show: false,
                },
                axisTicks: {
                    show: false,
                },
            },
            yaxis: {
                min: 0,
                forceNiceScale: true,
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '12px',
                    },
                },
            },
            legend: {
                position: 'top',
                horizontalAlign: 'left',
                labels: {
                    colors: '#334155',
                },
            },
            tooltip: {
                shared: true,
                intersect: false,
            },
        };

        const monthlyArchivedContentElement = document.querySelector('#monthly-archived-content-chart');

        if (monthlyArchivedContentElement) {
            const monthlyArchivedContentInstance = new ApexCharts(
                monthlyArchivedContentElement,
                monthlyArchivedContentOptions
            );

            monthlyArchivedContentInstance.render();
        }

        const sharedPieOptions = {
            chart: {
                type: 'pie',
                height: 320,
                fontFamily: 'inherit',
                toolbar: {
                    show: false,
                },
            },
            legend: {
                position: 'bottom',
                fontSize: '13px',
                labels: {
                    colors: '#475569',
                },
            },
            dataLabels: {
                enabled: true,
                style: {
                    fontSize: '12px',
                    fontWeight: 600,
                },
                dropShadow: {
                    enabled: false,
                },
            },
            stroke: {
                width: 2,
                colors: ['#ffffff'],
            },
            tooltip: {
                y: {
                    formatter: (value) => `${value}`,
                },
            },
        };

        const articleOptions = {
            ...sharedPieOptions,
            series: articleStatusChart.series,
            labels: articleStatusChart.labels,
            colors: ['#4f46e5', '#f59e0b', '#ef4444'],
        };

        const careerOptions = {
            ...sharedPieOptions,
            series: careerStatusChart.series,
            labels: careerStatusChart.labels,
            colors: ['#0f766e', '#dc2626', '#f59e0b'],
        };

        const articleChartElement = document.querySelector('#articles-status-pie-chart');
        const careerChartElement = document.querySelector('#careers-status-pie-chart');

        if (articleChartElement) {
            new ApexCharts(articleChartElement, articleOptions).render();
        }

        if (careerChartElement) {
            new ApexCharts(careerChartElement, careerOptions).render();
        }
    </script>
@endpush
