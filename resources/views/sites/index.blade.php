@extends('layouts.admin', ['title' => 'Sites'])

@section('content')
    <div class="my-6 px-4 md:px-8">
        <div class="mx-auto max-w-7xl min-w-0">
            @if (session('success'))
                <div class="mb-6 rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-8 rounded-[2rem] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-blue-50/70 px-6 py-6 shadow-sm md:px-8">
                <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_15rem] lg:items-center">
                    <div class="max-w-4xl">
                        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-blue-600">Sites Directory</p>
                        <h1 class="mt-3 text-3xl font-semibold tracking-tight leading-tight text-slate-900 md:text-[3.15rem]">Manage every CMS site from one view</h1>
                        <p class="mt-3 max-w-[56rem] text-sm leading-6 text-slate-600 md:text-base">
                            Review site identity details, check assignment coverage, and create new sites without leaving the listing page.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-white/80 bg-white/80 px-5 py-4 shadow-sm backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Current inventory</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $sites->total() }}</p>
                        <p class="mt-1 text-sm text-slate-500">total site{{ $sites->total() === 1 ? '' : 's' }}</p>
                    </div>
                </div>
            </div>

            <div class="mb-6 flex flex-wrap items-center gap-6">
                <form method="GET" action="{{ route('sites.index') }}" class="w-full max-w-sm" role="search">
                    <div class="flex items-center gap-2.5 rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm transition focus-within:border-blue-500 focus-within:ring-4 focus-within:ring-blue-100">
                        <label for="search" class="sr-only">Search</label>
                        <input
                            type="search"
                            id="search"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Search..."
                            class="w-full text-sm text-slate-900 outline-none placeholder:text-slate-400"
                        />

                        <button type="submit" class="ml-auto text-slate-400 transition hover:text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192.904 192.904" class="size-5 fill-current" aria-hidden="true">
                                <path d="m190.707 180.101-47.078-47.077c11.702-14.072 18.752-32.142 18.752-51.831C162.381 36.423 125.959 0 81.191 0 36.422 0 0 36.423 0 81.193c0 44.767 36.422 81.187 81.191 81.187 19.688 0 37.759-7.049 51.831-18.751l47.079 47.078a7.474 7.474 0 0 0 5.303 2.197 7.498 7.498 0 0 0 5.303-12.803zM15 81.193C15 44.694 44.693 15 81.191 15c36.497 0 66.189 29.694 66.189 66.193 0 36.496-29.692 66.187-66.189 66.187C44.693 147.38 15 117.689 15 81.193z" />
                            </svg>
                        </button>
                    </div>
                </form>

                <div class="flex flex-wrap gap-4 ml-auto">
                    <button
                        type="button"
                        class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 shadow-sm transition hover:-translate-y-0.5 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 fill-current" viewBox="0 0 64 64" aria-hidden="true">
                            <path d="M26.55 61.295a2.18 2.18 0 0 1-2.18-2.18v-20.96L4.161 15.928A6.115 6.115 0 0 1 8.685 5.705h46.63a6.115 6.115 0 0 1 4.524 10.224L39.63 38.154v12.241a2.18 2.18 0 0 1-.817 1.7l-10.9 8.72a2.18 2.18 0 0 1-1.363.48M8.685 10.065a1.755 1.755 0 0 0-1.297 2.932l20.775 22.89a2.18 2.18 0 0 1 .567 1.428v17.266l6.54-5.276v-11.99a2.18 2.18 0 0 1 .567-1.472l20.775-22.89a1.755 1.755 0 0 0-1.297-2.888z" />
                        </svg>
                        Filter
                    </button>

                    <button
                        type="button"
                        class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 shadow-sm transition hover:-translate-y-0.5 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 fill-current" viewBox="0 0 32 32" aria-hidden="true">
                            <path d="M9.24 17.56a1.24 1.24 0 0 1 0-1.75 1.22 1.22 0 0 1 1.73 0l3.78 3.81V3.21a1.25 1.25 0 0 1 2.5 0v16.41L21 15.81a1.22 1.22 0 0 1 1.73 0 1.24 1.24 0 0 1 0 1.75l-5.89 6a1.21 1.21 0 0 1-1.74 0zm19.53 2.16a1.23 1.23 0 0 0-1.23 1.22v5.88a.73.73 0 0 1-.73.73H5.19a.73.73 0 0 1-.73-.73v-5.88a1.23 1.23 0 0 0-2.46 0v5.88A3.19 3.19 0 0 0 5.19 30h21.62A3.19 3.19 0 0 0 30 26.82v-5.88a1.23 1.23 0 0 0-1.23-1.22" />
                        </svg>
                        Export
                    </button>

                    <button
                        type="button"
                        id="openCreateSiteModal"
                        class="flex items-center gap-2 rounded-2xl border border-blue-600 bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 fill-current" viewBox="0 0 512 512" aria-hidden="true">
                            <g>
                                <path d="M256 494.08a23.25 23.25 0 0 1-23.25-23.25V41.17a23.25 23.25 0 0 1 46.5 0v429.66A23.25 23.25 0 0 1 256 494.08" />
                                <path d="M470.83 279.25H41.17a23.25 23.25 0 0 1 0-46.5h429.66a23.25 23.25 0 0 1 0 46.5" />
                            </g>
                        </svg>
                        Add site
                    </button>
                </div>
            </div>

            <div class="rounded-[2rem] border border-slate-200 bg-white shadow-sm">
                <div class="max-w-full overflow-x-auto">
                        <table class="min-w-[1200px] w-full table-fixed">
                            <colgroup>
                                <col class="w-[4%]">
                                <col class="w-[13%]">
                                <col class="w-[12%]">
                                <col class="w-[12%]">
                                <col class="w-[12%]">
                                <col class="w-[10%]">
                                <col class="w-[12%]">
                                <col class="w-[12%]">
                            </colgroup>
                            <thead class="bg-slate-50 text-left text-[13px] font-semibold text-slate-900">
                            <tr>
                                <th scope="col" class="w-8 pl-4 py-5">
                                    <label class="group inline-block">
                                        <input type="checkbox" class="sr-only" id="master-checkbox" />
                                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border border-slate-300 bg-white group-has-[input:checked]:border-blue-600 group-has-[input:checked]:bg-blue-600 group-focus-within:ring-2 group-focus-within:ring-blue-500" aria-hidden="true">
                                            <svg class="size-3.5 text-white opacity-0 group-has-[input:checked]:opacity-100" viewBox="0 0 12 10" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 5l3 3 7-7" />
                                            </svg>
                                        </span>
                                    </label>
                                </th>

                                <th scope="col" class="px-2 py-5 whitespace-nowrap">Sites</th>
                                <th scope="col" class="px-2 py-5 leading-tight">
                                    <span class="block">Assigned users</span>
                                </th>
                                <th scope="col" class="px-2 py-5 whitespace-nowrap">Slug</th>
                                <th scope="col" class="px-2 py-5 whitespace-nowrap">Domain</th>
                                <th scope="col" class="px-2 py-5 whitespace-nowrap">Status</th>
                                <th scope="col" class="px-2 py-5 leading-tight">
                                    <span class="block">Updated at</span>
                                </th>
                                <th scope="col" class="px-2 py-5 whitespace-nowrap">Actions</th>
                            </tr>
                            </thead>

                            <tbody class="text-[13px] divide-y divide-slate-200">
                            @forelse ($sites as $site)
                                <tr class="transition hover:bg-slate-50/80 has-[:checked]:bg-blue-50/50">
                                    <td class="w-8 pl-4 py-5 align-center">
                                        <label class="group inline-block">
                                            <input type="checkbox" class="sr-only row-checkbox" />
                                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border border-slate-300 bg-white group-has-[input:checked]:border-blue-600 group-has-[input:checked]:bg-blue-600 group-focus-within:ring-2 group-focus-within:ring-blue-500" aria-hidden="true">
                                                <svg class="size-3.5 text-white opacity-0 group-has-[input:checked]:opacity-100" viewBox="0 0 12 10" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M1 5l3 3 7-7" />
                                                </svg>
                                            </span>
                                        </label>
                                    </td>

                                    <td class="px-2 py-5 font-medium text-slate-900">
                                        <div class="flex min-w-0 items-center gap-3">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                                                </svg>

                                            </div>
                                            <div class="min-w-0">
                                                <span class="block truncate font-semibold leading-relaxed" title="{{ $site->name }}">{{ $site->name }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-2 py-5 text-slate-500 text-left whitespace-nowrap">
                                        @if ($site->assigned_users_count > 0)
                                            <span class="inline-flex h-10 min-w-10 items-center justify-center rounded-full bg-slate-100 px-3 font-semibold text-blue-700">
                                                {{ $site->assigned_users_count }}
                                            </span>
                                        @else
                                            <span class="inline-flex h-10 min-w-10 items-center justify-center rounded-full bg-slate-100 px-3 font-semibold text-slate-400">—</span>
                                        @endif
                                    </td>

                                    <td class="px-2 py-5 text-slate-500">
                                        <span class="inline-flex max-w-full truncate rounded-xl bg-slate-100 px-3 py-2 text-[12px] font-semibold tracking-wide text-slate-600" title="{{ $site->slug }}">
                                            {{ $site->slug }}
                                        </span>
                                    </td>

                                    <td class="px-2 py-5 text-slate-500">
                                        @if ($site->domain)
                                            <span class="block truncate whitespace-nowrap" title="{{ $site->domain }}">{{ $site->domain }}</span>
                                        @else
                                            —
                                        @endif
                                    </td>

                                    <td class="px-2 py-5 text-slate-500 whitespace-nowrap">
                                        <span class="inline-flex w-max items-center gap-2 rounded-xl border px-3 py-2 text-[12px] font-semibold {{ $site->status === 'active' ? 'border-emerald-200 bg-emerald-50 text-emerald-700' : 'border-rose-200 bg-rose-50 text-rose-700' }}">
                                            <span class="h-2.5 w-2.5 rounded-full {{ $site->status === 'active' ? 'bg-emerald-500' : 'bg-rose-500' }}"></span>
                                            {{ ucfirst($site->status) }}
                                        </span>
                                    </td>

                                    <td class="px-2 py-5 text-slate-500">
                                        <span class="block leading-6">
                                            {{ $site->updated_at->format('d M Y,') }}<br>
                                            {{ $site->updated_at->format('g:i a') }}
                                        </span>
                                    </td>

                                    <td class="px-2 py-5 whitespace-nowrap">
                                        <div class="flex items-center gap-1.5">
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1 rounded-xl border border-blue-200 bg-blue-50 px-2.5 py-2 text-xs font-semibold text-blue-700 transition hover:bg-blue-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-4" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                            Edit
                                        </button>

                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1 rounded-xl border border-rose-200 bg-rose-50 px-2.5 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-rose-500"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="size-4" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                            Delete
                                        </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-16 text-center text-sm text-slate-500">
                                        No sites found yet.
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                </div>
            </div>

            <div class="flex items-center justify-between flex-wrap gap-6 mx-auto mt-6">
                <div class="text-sm text-slate-600">
                    Showing
                    <span class="font-medium mx-1">{{ $sites->firstItem() ?? 0 }}</span>
                    to
                    <span class="font-medium mx-1">{{ $sites->lastItem() ?? 0 }}</span>
                    of
                    <span class="font-medium mx-1">{{ $sites->total() }}</span>
                    results
                </div>

                @if ($sites->hasPages())
                    <nav aria-label="Pagination" class="flex items-center w-max overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm divide-x divide-slate-200">
                        @if ($sites->onFirstPage())
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center text-slate-300">
                                ‹
                            </span>
                        @else
                            <a href="{{ $sites->previousPageUrl() }}" class="flex h-12 w-12 shrink-0 items-center justify-center hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                ‹
                            </a>
                        @endif

                        @foreach (range(1, $sites->lastPage()) as $page)
                            @if ($page >= max(1, $sites->currentPage() - 2) && $page <= min($sites->lastPage(), $sites->currentPage() + 2))
                                @if ($page === $sites->currentPage())
                                    <span class="flex items-center justify-center shrink-0 text-sm font-semibold text-white w-12 h-12 bg-blue-600">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $sites->url($page) }}" class="flex items-center justify-center shrink-0 text-sm font-semibold text-slate-900 w-12 h-12 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endif
                        @endforeach

                        @if ($sites->hasMorePages())
                            <a href="{{ $sites->nextPageUrl() }}" class="flex items-center justify-center shrink-0 w-12 h-12 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                ›
                            </a>
                        @else
                            <span class="flex items-center justify-center shrink-0 w-12 h-12 text-slate-300">
                                ›
                            </span>
                        @endif
                    </nav>
                @endif
            </div>
        </div>
    </div>

    @include('sites.create-site-modal')
@endsection

@push('scripts')
    <script>
        const createSiteModalOverlay = document.getElementById('createSiteModal');
        const openCreateSiteModalButton = document.getElementById('openCreateSiteModal');
        const createSiteModalCloseButtons = document.querySelectorAll('[data-modal-close="createSiteModal"]');
        const masterCheckbox = document.getElementById('master-checkbox');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');

        const showCreateSiteModal = () => {
            createSiteModalOverlay?.classList.remove('hidden');
            createSiteModalOverlay?.classList.add('flex');
        };

        const hideCreateSiteModal = () => {
            createSiteModalOverlay?.classList.add('hidden');
            createSiteModalOverlay?.classList.remove('flex');
        };

        openCreateSiteModalButton?.addEventListener('click', showCreateSiteModal);

        createSiteModalCloseButtons.forEach((button) => {
            button.addEventListener('click', hideCreateSiteModal);
        });

        createSiteModalOverlay?.addEventListener('click', (event) => {
            if (event.target === createSiteModalOverlay) {
                hideCreateSiteModal();
            }
        });

        masterCheckbox?.addEventListener('change', (event) => {
            rowCheckboxes.forEach((checkbox) => {
                checkbox.checked = event.target.checked;
            });
        });

        @if ($errors->any())
            showCreateSiteModal();
        @endif
    </script>
@endpush
