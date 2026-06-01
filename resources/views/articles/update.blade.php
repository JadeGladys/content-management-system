@extends('layouts.admin', ['title' => $pageTitle])

@section('content')
    @php
        $selectedFeaturedImage = old('featured_image_id')
            ? $mediaLibrary->firstWhere('id', old('featured_image_id'))
            : $article->featuredImage;
    @endphp

    <div class="my-6 px-4 md:px-8">
        <div class="mx-auto max-w-7xl min-w-0">
            <div class="mb-8">
                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-blue-600">Articles</p>
                <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-900 md:text-[3rem]">{{ $pageHeading }}</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 md:text-base">
                    {{ $pageDescription }}
                </p>
            </div>

            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ $formAction }}" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method($formMethod)

                <div class="grid gap-8 xl:grid-cols-[minmax(0,1fr)_20rem]">
                    <div class="space-y-8">
                        <section class="border-t border-slate-200 pt-8">
                            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                                <div class="border-b border-slate-200 px-6 py-5">
                                    <h2 class="text-xl font-semibold tracking-tight text-slate-900">Cover photo</h2>
                                    <p class="mt-1 text-sm leading-6 text-slate-500">
                                        Upload a featured image for this article or browse uploaded assets later.
                                    </p>
                                </div>

                                <div class="flex flex-col gap-4 border-b border-slate-200 p-4 md:flex-row md:items-center">
                                    <button
                                        type="button"
                                        id="openAssetBrowserModal"
                                        class="inline-flex items-center justify-center gap-2 rounded-2xl border border-slate-300 bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5A2.5 2.5 0 0 1 5.5 5H9l1.5 2H18.5A2.5 2.5 0 0 1 21 9.5v7A2.5 2.5 0 0 1 18.5 19h-13A2.5 2.5 0 0 1 3 16.5z" />
                                        </svg>
                                        Browse uploaded assets
                                    </button>

                                    <label
                                        for="featured_image_upload"
                                        class="flex min-h-16 flex-1 cursor-pointer items-center gap-3 rounded-2xl border border-dashed border-slate-300 px-4 py-4 text-sm text-slate-600 transition hover:border-blue-400 hover:bg-blue-50/40"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-6 shrink-0 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16V6m0 0-3.5 3.5M12 6l3.5 3.5" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 16.5A3.5 3.5 0 0 0 16.5 13H16a5 5 0 1 0-9.8 1.5A3 3 0 0 0 7 20h10a3 3 0 0 0 3-3.5" />
                                        </svg>

                                        <span>
                                            Drag &amp; drop here or <span class="font-semibold text-blue-600 underline underline-offset-2">choose a file</span>.
                                            <span id="upload-selection-text" class="ml-1 text-slate-500">No file selected</span>
                                        </span>
                                    </label>

                                    <input
                                        type="file"
                                        id="featured_image_upload"
                                        name="featured_image_upload"
                                        accept=".png,.jpg,.jpeg,.gif,.webp"
                                        class="sr-only"
                                    >
                                </div>

                                <div class="p-6">
                                    <input type="hidden" id="featured_image_id" name="featured_image_id" value="{{ old('featured_image_id', $article->featured_image_id) }}">

                                    @error('featured_image_upload')
                                        <p class="mb-4 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror

                                    @error('featured_image_id')
                                        <p class="mb-4 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror

                                    <div
                                        id="featured-image-preview-card"
                                        class="@if (! $selectedFeaturedImage) hidden @endif max-w-xs overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                                    >
                                        <img
                                            id="featured-image-preview-tag"
                                            src="{{ $selectedFeaturedImage?->public_url }}"
                                            alt="Selected featured image preview"
                                            class="h-44 w-full object-cover"
                                        >

                                        <div class="flex items-center justify-between gap-3 px-4 py-3">
                                            <p
                                                id="featured-image-preview-name"
                                                class="truncate text-sm font-medium text-slate-700"
                                            >{{ $selectedFeaturedImage?->file_name }}</p>

                                            <button
                                                type="button"
                                                id="clearFeaturedImage"
                                                class="rounded-xl border border-slate-300 px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50"
                                            >
                                                Remove
                                            </button>
                                        </div>
                                    </div>

                                    <div
                                        id="featured-image-empty-state"
                                        class="@if ($selectedFeaturedImage) hidden @endif rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-6 py-10 text-sm text-slate-500"
                                    >
                                        No featured image selected yet.
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="border-t border-slate-200 pt-8">
                            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                                <div class="border-b border-slate-200 px-6 py-5">
                                    <h2 class="text-xl font-semibold tracking-tight text-slate-900">Article Information</h2>
                                    <p class="mt-1 text-sm leading-6 text-slate-500">
                                        Update the core details and refine the article content before publishing.
                                    </p>
                                </div>

                                <div class="grid gap-5 p-6 md:grid-cols-2">
                                    <div class="md:col-span-2">
                                        <label for="title" class="mb-2 block text-sm font-medium text-slate-700">
                                            Title <span class="text-rose-600">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            id="title"
                                            name="title"
                                            value="{{ old('title', $article->title) }}"
                                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                        >
                                        @error('title')
                                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="md:col-span-2">
                                        <label for="category" class="mb-2 block text-sm font-medium text-slate-700">
                                            Category <span class="text-rose-600">*</span>
                                        </label>
                                        <select
                                            id="category"
                                            name="category"
                                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                        >
                                            <option value="">Select category</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category }}" @selected(old('category', $article->category) === $category)>
                                                    {{ $category }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category')
                                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="md:col-span-2">
                                        <label for="overview" class="mb-2 block text-sm font-medium text-slate-700">
                                            Overview
                                        </label>
                                        <textarea
                                            id="overview"
                                            name="overview"
                                            rows="4"
                                            class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                        >{{ old('overview', $article->overview) }}</textarea>
                                        @error('overview')
                                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="border-t border-slate-200 pt-8">
                            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                                <div class="border-b border-slate-200 px-6 py-5">
                                    <h2 class="text-xl font-semibold tracking-tight text-slate-900">Article Content</h2>
                                    <p class="mt-1 text-sm leading-6 text-slate-500">
                                        Write and revise the article body using the editor below.
                                    </p>
                                </div>

                                <div class="p-6">
                                    @include('articles.editor', [
                                        'field' => 'content',
                                        'label' => 'Content',
                                        'editorId' => 'article-content-editor',
                                        'toolbarId' => 'article-content-toolbar',
                                        'value' => old('content', filled($article->content) ? json_encode($article->content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : ''),
                                    ])
                                </div>
                            </div>
                        </section>
                    </div>

                    <aside class="space-y-6 border-t border-slate-200 pt-8 xl:border-t-0 xl:pt-0">
                        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-200 px-6 py-5">
                                <h2 class="text-lg font-semibold tracking-tight text-slate-900">Publishing Details</h2>
                                <p class="mt-1 text-sm leading-6 text-slate-500">
                                    Control the stable URL slug and tags from here.
                                </p>
                            </div>

                            <div class="space-y-5 p-6">
                                <div>
                                    <label for="slug" class="mb-2 block text-sm font-medium text-slate-700">Slug</label>
                                    <input
                                        type="text"
                                        id="slug"
                                        name="slug"
                                        value="{{ old('slug', $article->slug) }}"
                                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                    >
                                    <p class="mt-2 text-xs leading-5 text-slate-500">
                                        This is generated once from the title and kept stable unless you edit or clear it manually.
                                    </p>
                                    @error('slug')
                                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="tags" class="mb-2 block text-sm font-medium text-slate-700">Tags</label>
                                    <input
                                        type="text"
                                        id="tags"
                                        name="tags"
                                        value="{{ old('tags', filled($article->tags) ? implode(', ', $article->tags) : '') }}"
                                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                    >
                                    @error('tags')
                                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
                    <a
                        href="{{ route('articles.index') }}"
                        class="inline-flex min-w-28 items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                    >
                        Cancel
                    </a>

                    <button
                        type="submit"
                        name="action"
                        value="save"
                        class="inline-flex min-w-28 items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                    >
                        Save
                    </button>

                    <button
                        type="submit"
                        name="action"
                        value="publish"
                        class="inline-flex min-w-28 items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700"
                    >
                        Publish
                    </button>
                </div>
            </form>
        </div>
    </div>

    @include('articles.asset-browser-modal')
@endsection

@push('scripts')
    <script>
        (() => {
            const featuredImageUploadInput = document.getElementById('featured_image_upload');
            const featuredImageIdInput = document.getElementById('featured_image_id');
            const featuredImagePreviewCard = document.getElementById('featured-image-preview-card');
            const featuredImagePreviewTag = document.getElementById('featured-image-preview-tag');
            const featuredImagePreviewName = document.getElementById('featured-image-preview-name');
            const featuredImageEmptyState = document.getElementById('featured-image-empty-state');
            const uploadSelectionText = document.getElementById('upload-selection-text');
            const clearFeaturedImageButton = document.getElementById('clearFeaturedImage');

            const showFeaturedImagePreview = (src, name) => {
                featuredImagePreviewTag.src = src;
                featuredImagePreviewName.textContent = name;
                featuredImagePreviewCard.classList.remove('hidden');
                featuredImageEmptyState.classList.add('hidden');
            };

            const resetFeaturedImagePreview = () => {
                featuredImagePreviewTag.src = '';
                featuredImagePreviewName.textContent = '';
                featuredImagePreviewCard.classList.add('hidden');
                featuredImageEmptyState.classList.remove('hidden');
                uploadSelectionText.textContent = 'No file selected';

                if (featuredImageIdInput) {
                    featuredImageIdInput.value = '';
                }
            };

            featuredImageUploadInput?.addEventListener('change', (event) => {
                const [file] = event.target.files ?? [];

                if (!file) {
                    resetFeaturedImagePreview();
                    return;
                }

                uploadSelectionText.textContent = '1 file selected';

                const reader = new FileReader();

                reader.onload = (loadEvent) => {
                    showFeaturedImagePreview(loadEvent.target?.result ?? '', file.name);

                    if (featuredImageIdInput) {
                        featuredImageIdInput.value = '';
                    }
                };

                reader.readAsDataURL(file);
            });

            clearFeaturedImageButton?.addEventListener('click', () => {
                if (featuredImageUploadInput) {
                    featuredImageUploadInput.value = '';
                }

                resetFeaturedImagePreview();
            });

            document.addEventListener('article:featured-image-selected', (event) => {
                const { mediaId, mediaName, mediaUrl } = event.detail;

                if (featuredImageIdInput) {
                    featuredImageIdInput.value = mediaId;
                }

                if (featuredImageUploadInput) {
                    featuredImageUploadInput.value = '';
                }

                uploadSelectionText.textContent = 'No file selected';
                showFeaturedImagePreview(mediaUrl, mediaName);
            });
        })();
    </script>
@endpush
