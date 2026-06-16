@extends('layouts.admin', ['title' => 'Articles'])

@section('content')
    <div class="my-4 px-4 md:px-6">
        <div class="mx-auto max-w-7xl min-w-0">
            <div class="mb-8 rounded-xl border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-blue-50/70 px-4 py-4 shadow-sm md:px-6">
                <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_15rem] lg:items-center">
                    <div class="max-w-4xl">
                        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-blue-600">Articles Directory</p>
                        <h1 class="mt-3 text-3xl leading-tight font-semibold tracking-tight text-slate-900 md:text-[2.35rem]">Manage every CMS article from one view</h1>
                        <p class="mt-2 max-w-[46rem] text-sm leading-6 text-slate-600">
                            Review article details, monitor publishing status, and track ownership from one listing page.
                        </p>
                    </div>

                    <div class="rounded-2xl border border-white/80 bg-white/80 px-4 py-3 shadow-sm backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">Current inventory</p>
                        <p class="mt-1 text-xl font-semibold text-slate-900">{{ $articles->total() }}</p>
                        <p class="mt-1 text-sm text-slate-500">total article{{ $articles->total() === 1 ? '' : 's' }}</p>
                    </div>
                </div>
            </div>

            <div class="mb-5 flex items-center gap-4">
                <form method="GET" action="{{ route('articles.index') }}" class="min-w-0 flex-1 max-w-sm" role="search">
                    <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 shadow-sm transition focus-within:border-blue-500 focus-within:ring-4 focus-within:ring-blue-100">

                        <button type="submit" class="shrink-0 text-slate-400 transition hover:text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192.904 192.904" class="size-5 fill-current" aria-hidden="true">
                                <path d="m190.707 180.101-47.078-47.077c11.702-14.072 18.752-32.142 18.752-51.831C162.381 36.423 125.959 0 81.191 0 36.422 0 0 36.423 0 81.193c0 44.767 36.422 81.187 81.191 81.187 19.688 0 37.759-7.049 51.831-18.751l47.079 47.078a7.474 7.474 0 0 0 5.303 2.197 7.498 7.498 0 0 0 5.303-12.803zM15 81.193C15 44.694 44.693 15 81.191 15c36.497 0 66.189 29.694 66.189 66.193 0 36.496-29.692 66.187-66.189 66.187C44.693 147.38 15 117.689 15 81.193z" />
                            </svg>
                        </button>

                        <label for="search" class="sr-only">Search</label>

                        <input
                            type="search"
                            id="search"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Search..."
                            class="w-full text-sm text-slate-900 outline-none placeholder:text-slate-400"
                        />

                        @if ($search)
                            <button
                                type="button"
                                id="clear-search"
                                class="shrink-0 text-slate-400 transition hover:text-blue-600"
                                aria-label="Clear search"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </form>

                <div class="ml-auto flex shrink-0 gap-3">
                    <button
                        type="button"
                        class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm transition hover:-translate-y-0.5 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 fill-current" viewBox="0 0 64 64" aria-hidden="true">
                            <path d="M26.55 61.295a2.18 2.18 0 0 1-2.18-2.18v-20.96L4.161 15.928A6.115 6.115 0 0 1 8.685 5.705h46.63a6.115 6.115 0 0 1 4.524 10.224L39.63 38.154v12.241a2.18 2.18 0 0 1-.817 1.7l-10.9 8.72a2.18 2.18 0 0 1-1.363.48M8.685 10.065a1.755 1.755 0 0 0-1.297 2.932l20.775 22.89a2.18 2.18 0 0 1 .567 1.428v17.266l6.54-5.276v-11.99a2.18 2.18 0 0 1 .567-1.472l20.775-22.89a1.755 1.755 0 0 0-1.297-2.888z" />
                        </svg>
                        Filter
                    </button>

                    <button
                        type="button"
                        class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm transition hover:-translate-y-0.5 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 fill-current" viewBox="0 0 32 32" aria-hidden="true">
                            <path d="M9.24 17.56a1.24 1.24 0 0 1 0-1.75 1.22 1.22 0 0 1 1.73 0l3.78 3.81V3.21a1.25 1.25 0 0 1 2.5 0v16.41L21 15.81a1.22 1.22 0 0 1 1.73 0 1.24 1.24 0 0 1 0 1.75l-5.89 6a1.21 1.21 0 0 1-1.74 0zm19.53 2.16a1.23 1.23 0 0 0-1.23 1.22v5.88a.73.73 0 0 1-.73.73H5.19a.73.73 0 0 1-.73-.73v-5.88a1.23 1.23 0 0 0-2.46 0v5.88A3.19 3.19 0 0 0 5.19 30h21.62A3.19 3.19 0 0 0 30 26.82v-5.88a1.23 1.23 0 0 0-1.23-1.22" />
                        </svg>
                        Export
                    </button>

                    <a
                        href="{{ route('articles.create') }}"
                        class="flex items-center gap-2 rounded-xl border border-blue-600 bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 fill-current" viewBox="0 0 512 512" aria-hidden="true">
                            <g>
                                <path d="M256 494.08a23.25 23.25 0 0 1-23.25-23.25V41.17a23.25 23.25 0 0 1 46.5 0v429.66A23.25 23.25 0 0 1 256 494.08" />
                                <path d="M470.83 279.25H41.17a23.25 23.25 0 0 1 0-46.5h429.66a23.25 23.25 0 0 1 0 46.5" />
                            </g>
                        </svg>
                        Create Article
                    </a>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="max-w-full overflow-x-auto">
                    <table class="min-w-[950px] w-full table-fixed">
                        <colgroup>
                            <col class="w-[3.5rem]">
                            <col class="w-[20%]">
                            <col class="w-[16%]">
                            <col class="w-[14%]">
                            <col class="w-[14%]">
                            <col class="w-[12%]">
                            <col class="w-[12%]">
                            <col class="w-[12%]">
                        </colgroup>
                        <thead class="bg-slate-50 text-left text-[13px] font-semibold text-slate-900">
                            <tr>
                                <th scope="col" class="px-4 py-4 text-center">
                                    <label class="group inline-flex items-center justify-center">
                                        <input type="checkbox" class="sr-only" id="master-checkbox" />
                                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border border-slate-300 bg-white group-has-[input:checked]:border-blue-600 group-has-[input:checked]:bg-blue-600 group-focus-within:ring-2 group-focus-within:ring-blue-500" aria-hidden="true">
                                            <svg class="size-3.5 text-white opacity-0 group-has-[input:checked]:opacity-100" viewBox="0 0 12 10" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 5l3 3 7-7" />
                                            </svg>
                                        </span>
                                    </label>
                                </th>
                                <th scope="col" class="px-3 py-4">Article title</th>
                                <th scope="col" class="px-3 py-4">Category</th>
                                <th scope="col" class="px-3 py-4">Overview</th>
                                <th scope="col" class="px-3 py-4">Author</th>
                                <th scope="col" class="px-3 py-4">Status</th>
                                <th scope="col" class="px-3 py-4 leading-tight">
                                    <span class="block">Updated at</span>
                                </th>
                                <th scope="col" class="px-3 py-4 text-center">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 text-[13px]">
                            @forelse ($articles as $listedArticle)
                                <tr class="transition hover:bg-slate-50/80 has-[:checked]:bg-blue-50/50">
                                    <td class="px-4 py-5 text-center">
                                        <label class="group inline-flex items-center justify-center">
                                            <input type="checkbox" class="sr-only row-checkbox" />
                                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border border-slate-300 bg-white group-has-[input:checked]:border-blue-600 group-has-[input:checked]:bg-blue-600 group-focus-within:ring-2 group-focus-within:ring-blue-500" aria-hidden="true">
                                                <svg class="size-3.5 text-white opacity-0 group-has-[input:checked]:opacity-100" viewBox="0 0 12 10" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M1 5l3 3 7-7" />
                                                </svg>
                                            </span>
                                        </label>
                                    </td>

                                    <td class="px-4 py-5 font-medium text-slate-900">
                                        <div class="min-w-0 max-w-[16rem] xl:max-w-[18rem]">
                                            <span class="block truncate text-sm font-semibold leading-6" title="{{ $listedArticle->title }}">
                                                {{ $listedArticle->title }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-4 py-5 text-slate-500">
                                        <span class="block truncate" title="{{ $listedArticle->category?->name ?? '—' }}">
                                            {{ $listedArticle->category?->name ?? '—' }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-5 text-slate-500">
                                        <div class="min-w-0 max-w-[22rem]">
                                            <span class="block truncate" title="{{ $listedArticle->overview ?? '—' }}">
                                                {{ $listedArticle->overview ?? '—' }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-4 py-5 text-slate-500">
                                        <span class="block truncate" title="{{ $listedArticle->authorUser?->name ?? '—' }}">
                                            {{ $listedArticle->authorUser?->name ?? '—' }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-5 whitespace-nowrap text-slate-500">
                                        @php
                                            $statusClasses = match ($listedArticle->status) {
                                                'published' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                                                'archived' => 'border-rose-200 bg-rose-50 text-rose-700',
                                                default => 'border-amber-200 bg-amber-50 text-amber-700',
                                            };

                                            $dotClasses = match ($listedArticle->status) {
                                                'published' => 'bg-emerald-500',
                                                'archived' => 'bg-rose-500',
                                                default => 'bg-amber-500',
                                            };
                                        @endphp

                                        <span class="inline-flex w-max items-center gap-1 rounded-lg border px-2 py-1 text-[11px] font-semibold {{ $statusClasses }}">
                                            <span class="h-2 w-2 rounded-full {{ $dotClasses }}"></span>
                                            {{ ucfirst($listedArticle->status) }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-4 text-slate-500">
                                        <span class="block whitespace-nowrap leading-6">
                                            {{ $listedArticle->updated_at->format('d M Y') }}
                                        </span>
                                    </td>

                                    <td class="px-5 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            @if ($listedArticle->status === 'draft')
                                                <a
                                                    href="{{ route('articles.edit', $listedArticle) }}"
                                                    class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-300 hover:bg-emerald-100"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="size-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                    </svg>
                                                    Edit
                                                </a>
                                            @else
                                                <a
                                                    href="{{ route('articles.show', $listedArticle) }}"
                                                    class="inline-flex items-center gap-1 rounded-lg border border-amber-200 bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-700 shadow-sm transition hover:-translate-y-0.5 hover:border-amber-300 hover:bg-amber-100"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="size-3.5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.644C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.437 0 .644C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178Z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                    </svg>
                                                    View
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-6 text-center text-xs text-slate-500">
                                        No articles found yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mx-auto mt-4 flex flex-wrap items-center justify-between gap-4">
                <div class="text-xs text-slate-600">
                    Showing
                    <span class="mx-1 font-medium">{{ $articles->firstItem() ?? 0 }}</span>
                    to
                    <span class="mx-1 font-medium">{{ $articles->lastItem() ?? 0 }}</span>
                    of
                    <span class="mx-1 font-medium">{{ $articles->total() }}</span>
                    results
                </div>

                @if ($articles->hasPages())
                    <nav aria-label="Pagination" class="flex w-max items-center overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm divide-x divide-slate-200">
                        @if ($articles->onFirstPage())
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center text-slate-300">
                                ‹
                            </span>
                        @else
                            <a href="{{ $articles->previousPageUrl() }}" class="flex h-11 w-11 shrink-0 items-center justify-center hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                ‹
                            </a>
                        @endif

                        @foreach (range(1, $articles->lastPage()) as $page)
                            @if ($page >= max(1, $articles->currentPage() - 2) && $page <= min($articles->lastPage(), $articles->currentPage() + 2))
                                @if ($page === $articles->currentPage())
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center bg-blue-600 text-sm font-semibold text-white">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $articles->url($page) }}" class="flex h-11 w-11 shrink-0 items-center justify-center text-sm font-semibold text-slate-900 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endif
                        @endforeach

                        @if ($articles->hasMorePages())
                            <a href="{{ $articles->nextPageUrl() }}" class="flex h-11 w-11 shrink-0 items-center justify-center hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                ›
                            </a>
                        @else
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center text-slate-300">
                                ›
                            </span>
                        @endif
                    </nav>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const masterCheckbox = document.getElementById('master-checkbox');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');

        masterCheckbox?.addEventListener('change', (event) => {
            rowCheckboxes.forEach((checkbox) => {
                checkbox.checked = event.target.checked;
            });
        });
        document.getElementById('clear-search')?.addEventListener('click', () => {
            const searchInput = document.getElementById('search');

            if (!searchInput) {
                return;
            }

            searchInput.value = '';
            searchInput.form.submit();
        });
    </script>
@endpush
