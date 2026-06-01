@extends('layouts.admin', ['title' => 'Media'])

@section('content')
    @php
        $formatFileSize = function (int $bytes): string {
            if ($bytes >= 1024 * 1024) {
                return number_format($bytes / (1024 * 1024), $bytes >= 10 * 1024 * 1024 ? 0 : 1) . ' MB';
            }

            if ($bytes >= 1024) {
                return number_format($bytes / 1024, $bytes >= 100 * 1024 ? 0 : 1) . ' KB';
            }

            return $bytes . ' B';
        };
    @endphp

    <div class="my-6 px-4 md:px-8">
        <div class="mx-auto max-w-7xl min-w-0">
            @if (session('success'))
                <div class="mb-6 rounded-3xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm text-emerald-700 shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-3xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700 shadow-sm">
                    {{ session('error') }}
                </div>
            @endif

            @error('media_upload')
                <div class="mb-6 rounded-3xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-700 shadow-sm">
                    {{ $message }}
                </div>
            @enderror

            <div class="mb-8 rounded-[2rem] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-blue-50/70 px-6 py-6 shadow-sm md:px-8">
                <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_15rem] lg:items-center">
                    <div class="max-w-4xl">
                        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-blue-600">Media Directory</p>
                        <h1 class="mt-3 text-3xl leading-tight font-semibold tracking-tight text-slate-900 md:text-[3.15rem]">Manage every CMS media from one view</h1>
                        <p class="mt-3 max-w-[56rem] text-sm leading-6 text-slate-600 md:text-base">
                            Upload new assets, review existing files, and switch between table and block views without leaving the media library.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-white/80 bg-white/80 px-5 py-4 shadow-sm backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Current inventory</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $media->total() }}</p>
                        <p class="mt-1 text-sm text-slate-500">total media{{ $media->total() === 1 ? '' : 's' }}</p>
                    </div>
                </div>
            </div>

            <div class="mb-6 flex flex-wrap items-center gap-4">
                <form method="GET" action="{{ route('media.index') }}" class="w-full max-w-sm" role="search">
                    <input type="hidden" name="view" value="{{ $viewMode }}">

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

                <button
                    type="button"
                    class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 shadow-sm transition hover:-translate-y-0.5 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4 fill-current" viewBox="0 0 64 64" aria-hidden="true">
                        <path d="M26.55 61.295a2.18 2.18 0 0 1-2.18-2.18v-20.96L4.161 15.928A6.115 6.115 0 0 1 8.685 5.705h46.63a6.115 6.115 0 0 1 4.524 10.224L39.63 38.154v12.241a2.18 2.18 0 0 1-.817 1.7l-10.9 8.72a2.18 2.18 0 0 1-1.363.48M8.685 10.065a1.755 1.755 0 0 0-1.297 2.932l20.775 22.89a2.18 2.18 0 0 1 .567 1.428v17.266l6.54-5.276v-11.99a2.18 2.18 0 0 1 .567-1.472l20.775-22.89a1.755 1.755 0 0 0-1.297-2.888z" />
                    </svg>
                    Filters
                </button>

                <div class="ml-auto flex flex-wrap items-center gap-4">
                    <form id="media-upload-form" method="POST" action="{{ route('media.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="search" value="{{ $search }}">
                        <input type="hidden" name="view" value="{{ $viewMode }}">

                        <label
                            for="media_upload"
                            class="inline-flex cursor-pointer items-center gap-2 rounded-2xl border border-blue-600 bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-blue-700 focus-within:outline-none focus-within:ring-2 focus-within:ring-blue-500"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 fill-current" viewBox="0 0 32 32" aria-hidden="true">
                                <path d="M16 21a1.24 1.24 0 0 1-1.24-1.24v-9.4L11.7 13.4a1.24 1.24 0 1 1-1.76-1.76l5.18-5.18a1.24 1.24 0 0 1 1.76 0l5.18 5.18a1.24 1.24 0 1 1-1.76 1.76l-3.06-3.05v9.4A1.24 1.24 0 0 1 16 21" />
                                <path d="M23.44 27.5H8.56A4.56 4.56 0 0 1 4 22.94v-2.38a1.24 1.24 0 0 1 2.48 0v2.38A2.08 2.08 0 0 0 8.56 25h14.88a2.08 2.08 0 0 0 2.08-2.08v-2.38a1.24 1.24 0 1 1 2.48 0v2.38a4.56 4.56 0 0 1-4.56 4.58" />
                            </svg>
                            Upload
                        </label>

                        <input
                            type="file"
                            id="media_upload"
                            name="media_upload"
                            accept=".png,.jpg,.jpeg,.gif,.webp"
                            class="sr-only"
                        >
                    </form>

                    <div class="inline-flex overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <a
                            href="{{ route('media.index', array_filter(['search' => $search, 'view' => 'grid'])) }}"
                            class="flex h-12 w-12 items-center justify-center transition {{ $viewMode === 'grid' ? 'bg-blue-600 text-white' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}"
                            aria-label="Grid view"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M4 4h6v6H4zm10 0h6v6h-6zM4 14h6v6H4zm10 0h6v6h-6z" />
                            </svg>
                        </a>

                        <a
                            href="{{ route('media.index', array_filter(['search' => $search, 'view' => 'list'])) }}"
                            class="flex h-12 w-12 items-center justify-center border-l border-slate-200 transition {{ $viewMode === 'list' ? 'bg-blue-600 text-white border-blue-700' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}"
                            aria-label="List view"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5 fill-current" viewBox="0 0 24 24" aria-hidden="true">
                                <path d="M4 6h16v2H4zm0 5h16v2H4zm0 5h16v2H4z" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            @if ($viewMode === 'grid')
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm">
                    @if ($media->count())
                        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5">
                            @foreach ($media as $listedMedia)
                                <article class="overflow-hidden rounded-3xl border border-slate-200 bg-slate-50/60 p-4 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-200 hover:bg-white">
                                    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                                        <img
                                            src="{{ $listedMedia->public_url }}"
                                            alt="{{ $listedMedia->file_name }}"
                                            class="h-44 w-full object-cover"
                                            loading="lazy"
                                        >
                                    </div>

                                    <div class="mt-4">
                                        <h2 class="truncate text-base font-semibold text-slate-900" title="{{ $listedMedia->file_name }}">
                                            {{ $listedMedia->file_name }}
                                        </h2>
                                        <p class="mt-2 text-sm text-slate-500">
                                            {{ $formatFileSize($listedMedia->file_size) }}
                                        </p>
                                        <a
                                            href="{{ $listedMedia->public_url }}"
                                            target="_blank"
                                            rel="noreferrer"
                                            class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white my-2 px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                                        >
                                            Open
                                        </a>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-3xl border border-dashed border-slate-200 bg-slate-50 px-6 py-12 text-center text-sm text-slate-500">
                            No media found yet.
                        </div>
                    @endif
                </div>
            @else
                <div class="rounded-[2rem] border border-slate-200 bg-white shadow-sm">
                    <div class="max-w-full overflow-x-auto">
                        <table class="min-w-[980px] w-full table-fixed">
                            <colgroup>
                                <col class="w-[34%]">
                                <col class="w-[14%]">
                                <col class="w-[14%]">
                                <col class="w-[16%]">
                                <col class="w-[12%]">
                                <col class="w-[10%]">
                            </colgroup>

                            <thead class="bg-slate-50 text-left text-[13px] font-semibold text-slate-900">
                                <tr>
                                    <th scope="col" class="px-4 py-5 whitespace-nowrap">File</th>
                                    <th scope="col" class="px-4 py-5 whitespace-nowrap">Type</th>
                                    <th scope="col" class="px-4 py-5 whitespace-nowrap">Size</th>
                                    <th scope="col" class="px-4 py-5 whitespace-nowrap">Uploaded by</th>
                                    <th scope="col" class="px-4 py-5 whitespace-nowrap">Updated at</th>
                                    <th scope="col" class="px-4 py-5 whitespace-nowrap">Preview</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-200 text-[13px]">
                                @forelse ($media as $listedMedia)
                                    <tr class="transition hover:bg-slate-50/80">
                                        <td class="px-4 py-5 text-slate-900">
                                            <div class="flex items-center gap-4 min-w-0">
                                                <div class="h-14 w-14 overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 shrink-0">
                                                    <img
                                                        src="{{ $listedMedia->public_url }}"
                                                        alt="{{ $listedMedia->file_name }}"
                                                        class="h-full w-full object-cover"
                                                        loading="lazy"
                                                    >
                                                </div>

                                                <div class="min-w-0">
                                                    <span class="block truncate font-semibold leading-relaxed" title="{{ $listedMedia->file_name }}">
                                                        {{ $listedMedia->file_name }}
                                                    </span>
                                                    <span class="mt-1 block truncate text-slate-500" title="{{ $listedMedia->file_path }}">
                                                        {{ $listedMedia->file_path }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="px-4 py-5 text-slate-500">
                                            <span class="block truncate whitespace-nowrap" title="{{ $listedMedia->file_type }}">
                                                {{ $listedMedia->file_type }}
                                            </span>
                                        </td>

                                        <td class="px-4 py-5 text-slate-500">
                                            {{ $formatFileSize($listedMedia->file_size) }}
                                        </td>

                                        <td class="px-4 py-5 text-slate-500">
                                            <span class="block truncate whitespace-nowrap" title="{{ $listedMedia->uploadedBy?->name ?? '—' }}">
                                                {{ $listedMedia->uploadedBy?->name ?? '—' }}
                                            </span>
                                        </td>

                                        <td class="px-4 py-5 text-slate-500">
                                            <span class="block whitespace-nowrap leading-6">
                                                {{ $listedMedia->updated_at->format('d M Y') }}
                                            </span>
                                        </td>

                                        <td class="px-4 py-5">
                                            <a
                                                href="{{ $listedMedia->public_url }}"
                                                target="_blank"
                                                rel="noreferrer"
                                                class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                                            >
                                                Open
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-8 text-center text-sm text-slate-500">
                                            No media found yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="mx-auto mt-6 flex flex-wrap items-center justify-between gap-6">
                <div class="text-sm text-slate-600">
                    Showing
                    <span class="mx-1 font-medium">{{ $media->firstItem() ?? 0 }}</span>
                    to
                    <span class="mx-1 font-medium">{{ $media->lastItem() ?? 0 }}</span>
                    of
                    <span class="mx-1 font-medium">{{ $media->total() }}</span>
                    results
                </div>

                @if ($media->hasPages())
                    <nav aria-label="Pagination" class="flex w-max items-center overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm divide-x divide-slate-200">
                        @if ($media->onFirstPage())
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center text-slate-300">
                                ‹
                            </span>
                        @else
                            <a href="{{ $media->previousPageUrl() }}" class="flex h-12 w-12 shrink-0 items-center justify-center hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                ‹
                            </a>
                        @endif

                        @foreach (range(1, $media->lastPage()) as $page)
                            @if ($page >= max(1, $media->currentPage() - 2) && $page <= min($media->lastPage(), $media->currentPage() + 2))
                                @if ($page === $media->currentPage())
                                    <span class="flex h-12 w-12 shrink-0 items-center justify-center bg-blue-600 text-sm font-semibold text-white">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $media->url($page) }}" class="flex h-12 w-12 shrink-0 items-center justify-center text-sm font-semibold text-slate-900 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endif
                        @endforeach

                        @if ($media->hasMorePages())
                            <a href="{{ $media->nextPageUrl() }}" class="flex h-12 w-12 shrink-0 items-center justify-center hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                ›
                            </a>
                        @else
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center text-slate-300">
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
        const mediaUploadInput = document.getElementById('media_upload');
        const mediaUploadForm = document.getElementById('media-upload-form');

        mediaUploadInput?.addEventListener('change', (event) => {
            if ((event.target.files ?? []).length > 0) {
                mediaUploadForm?.submit();
            }
        });
    </script>
@endpush
