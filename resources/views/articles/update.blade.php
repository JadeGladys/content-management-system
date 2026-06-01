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

                <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_20rem]">
                    <div class="space-y-8">
                        <section class="border-t border-slate-200 pt-8">
                            <div class="overflow-visible rounded-3xl border border-slate-200 bg-white shadow-sm">
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
                            <div class="overflow-visible rounded-3xl border border-slate-200 bg-white shadow-sm">
                                <div class="border-b border-slate-200 px-6 py-5">
                                    <h2 class="text-xl font-semibold tracking-tight text-slate-900">Article Information</h2>
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
                                        <x-category-picker
                                            picker-id="article-update-category-picker"
                                            name="category"
                                            label="Category"
                                            :required="true"
                                            :value="old('category', $article->category?->name)"
                                            :options="$categories"
                                            placeholder="Select category"
                                            :panel-bleed="true"
                                        />
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

                    <aside class="space-y-6 border-t border-slate-200 pt-8 lg:self-start lg:border-t-0 lg:pt-8">
                        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm lg:sticky lg:top-6">
                            <div class="border-b border-slate-200 px-6 py-5">
                                <h2 class="text-lg font-semibold tracking-tight text-slate-900">Publishing Details</h2>
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
                                    @error('slug')
                                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                @php
                                    $selectedTagIds = collect(old('tag_ids', $article->tags->pluck('id')->all()))
                                        ->filter()
                                        ->map(fn ($id) => (string) $id)
                                        ->values()
                                        ->all();

                                    $selectedNewTags = collect(old('new_tags', []))
                                        ->map(fn ($tag) => trim((string) $tag))
                                        ->filter()
                                        ->values()
                                        ->all();

                                    $availableTagOptions = $availableTags
                                        ->map(fn ($tag) => [
                                            'id' => (string) $tag->id,
                                            'name' => $tag->name,
                                            'slug' => $tag->slug,
                                        ])
                                        ->values()
                                        ->all();
                                @endphp

                                <div>
                                    <label class="mb-2 block text-sm font-medium text-slate-700">Tags</label>

                                    <div
                                        id="article-tag-picker"
                                        class="relative rounded-[1.75rem] border border-slate-200 bg-slate-50/70 p-3"
                                        data-available-tags='@json($availableTagOptions)'
                                        data-selected-tag-ids='@json($selectedTagIds)'
                                        data-selected-new-tags='@json($selectedNewTags)'
                                    >
                                        <div id="article-tag-id-inputs"></div>
                                        <div id="article-new-tag-inputs"></div>

                                        <div class="relative">
                                            <button
                                                type="button"
                                                id="article-tags-trigger"
                                                aria-expanded="false"
                                                class="flex w-full items-center justify-between gap-3 rounded-2xl border border-slate-300 bg-white px-4 py-3 text-left shadow-sm transition hover:border-slate-400 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                            >
                                                <span id="article-tags-summary" class="block truncate text-sm font-semibold text-slate-900">
                                                    Tag picker
                                                </span>

                                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0 text-slate-400 transition" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                                                </svg>
                                            </button>

                                            <div
                                                id="article-tags-panel"
                                                class="absolute left-0 right-0 top-[calc(100%+0.75rem)] z-30 hidden overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-[0_28px_55px_-24px_rgba(15,23,42,0.35)] xl:right-[-1.5rem]"
                                            >
                                                <div class="border-b border-slate-200 p-4">
                                                    <div class="relative">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.472 9.765l2.63 2.63a.75.75 0 1 0 1.06-1.06l-2.629-2.63A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0a4 4 0 0 1-8 0Z" clip-rule="evenodd" />
                                                        </svg>

                                                        <input
                                                            type="text"
                                                            id="article-tags-search"
                                                            autocomplete="off"
                                                            placeholder="Search tags or add a new one"
                                                            class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-11 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100"
                                                        >
                                                    </div>
                                                </div>

                                                <div
                                                    id="article-tags-options"
                                                    class="max-h-72 space-y-1 overflow-y-auto px-3 py-1"
                                                ></div>
                                            </div>
                                        </div>

                                    @error('tag_ids')
                                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror

                                    @error('tag_ids.*')
                                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror

                                    @error('new_tags')
                                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror

                                    @error('new_tags.*')
                                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror

                                    <div
                                        id="article-selected-tags"
                                        class="mt-4 space-y-3"
                                        aria-live="polite"
                                    ></div>
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
            const articleTagPicker = document.getElementById('article-tag-picker');
            const articleTagsTrigger = document.getElementById('article-tags-trigger');
            const articleTagsPanel = document.getElementById('article-tags-panel');
            const articleTagsSearch = document.getElementById('article-tags-search');
            const articleTagsOptions = document.getElementById('article-tags-options');
            const articleTagsSummary = document.getElementById('article-tags-summary');
            const articleSelectedTags = document.getElementById('article-selected-tags');
            const articleTagIdInputs = document.getElementById('article-tag-id-inputs');
            const articleNewTagInputs = document.getElementById('article-new-tag-inputs');

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

            if (
                !articleTagPicker ||
                !articleTagsTrigger ||
                !articleTagsPanel ||
                !articleTagsSearch ||
                !articleTagsOptions ||
                !articleTagsSummary ||
                !articleSelectedTags ||
                !articleTagIdInputs ||
                !articleNewTagInputs
            ) {
                    return;
            }

            const availableTags = JSON.parse(articleTagPicker.dataset.availableTags ?? '[]');
            const initialSelectedTagIds = JSON.parse(articleTagPicker.dataset.selectedTagIds ?? '[]');
            const initialSelectedNewTags = JSON.parse(articleTagPicker.dataset.selectedNewTags ?? '[]');
            const selectedTagIds = new Set(initialSelectedTagIds);
            let selectedNewTags = [...new Set(initialSelectedNewTags.map((tag) => tag.trim()).filter(Boolean))];

            const normalizeTag = (value) => value.trim();
            const slugify = (value) => normalizeTag(value)
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');

            const syncHiddenInputs = () => {
                articleTagIdInputs.innerHTML = '';
                articleNewTagInputs.innerHTML = '';

                Array.from(selectedTagIds).forEach((tagId) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'tag_ids[]';
                    input.value = tagId;
                    articleTagIdInputs.appendChild(input);
                });

                selectedNewTags.forEach((tag) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'new_tags[]';
                    input.value = tag;
                    articleNewTagInputs.appendChild(input);
                });
            };

            const updateSummary = () => {
                articleTagsSummary.textContent = 'Tag picker';
            };

            const removeSelectedTag = (type, value) => {
                if (type === 'existing') {
                    selectedTagIds.delete(value);
                } else {
                    selectedNewTags = selectedNewTags.filter((tag) => tag !== value);
                }

                renderTagPicker();
            };

            const renderSelectedTags = () => {
                articleSelectedTags.innerHTML = '';

                const selectedExistingTags = availableTags.filter((tag) => selectedTagIds.has(tag.id));
                const selectedTagItems = [
                    ...selectedExistingTags.map((tag) => ({ type: 'existing', value: tag.id, label: tag.name })),
                    ...selectedNewTags.map((tag) => ({ type: 'new', value: tag, label: tag })),
                ];

                if (selectedTagItems.length === 0) {
                    const emptyState = document.createElement('div');
                    emptyState.className = 'rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-4 py-5 text-sm text-slate-500';
                    emptyState.textContent = 'No tags selected yet.';
                    articleSelectedTags.appendChild(emptyState);
                    return;
                }

                selectedTagItems.forEach((tag) => {
                    const item = document.createElement('div');
                    item.className = 'flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-1 shadow-sm';

                    const textWrap = document.createElement('div');
                    textWrap.className = 'min-w-0';

                    const name = document.createElement('p');
                    name.className = 'truncate text-sm font-medium text-slate-900';
                    name.textContent = tag.label;

                    textWrap.appendChild(name);

                    const removeButton = document.createElement('button');
                    removeButton.type = 'button';
                    removeButton.className = 'inline-flex size-7 items-center justify-center rounded-full border border-slate-200 text-base font-medium leading-none text-slate-400 transition hover:border-rose-200 hover:bg-rose-50 hover:text-rose-600';
                    removeButton.setAttribute('aria-label', `Remove ${tag.label}`);
                    removeButton.textContent = '×';
                    removeButton.addEventListener('click', () => {
                        removeSelectedTag(tag.type, tag.value);
                    });

                    item.appendChild(textWrap);
                    item.appendChild(removeButton);
                    articleSelectedTags.appendChild(item);
                });
            };

            const renderOptions = () => {
                const query = articleTagsSearch.value.trim().toLowerCase();
                const filteredTags = availableTags.filter((tag) => tag.name.toLowerCase().includes(query));

                articleTagsOptions.innerHTML = '';

                filteredTags.forEach((tag) => {
                    const option = document.createElement('button');
                    option.type = 'button';
                    option.className = `flex w-full items-center justify-between gap-3 rounded-2xl px-4 py-3 text-left text-sm transition ${
                        selectedTagIds.has(tag.id)
                            ? 'bg-blue-600 text-white shadow-sm'
                            : 'bg-white text-slate-500 hover:bg-slate-100 hover:text-slate-800'
                    }`;

                    const name = document.createElement('span');
                    name.className = 'truncate font-medium';
                    name.textContent = tag.name;

                    option.appendChild(name);

                    option.addEventListener('click', () => {
                        if (selectedTagIds.has(tag.id)) {
                            selectedTagIds.delete(tag.id);
                        } else {
                            selectedTagIds.add(tag.id);
                            selectedNewTags = selectedNewTags.filter((newTag) => slugify(newTag) !== tag.slug);
                        }

                        renderTagPicker();
                    });

                    articleTagsOptions.appendChild(option);
                });

                const normalizedQuery = normalizeTag(articleTagsSearch.value);
                const querySlug = slugify(normalizedQuery);

                const exactExistingMatch = availableTags.some((tag) => tag.slug === querySlug);
                const exactNewMatch = selectedNewTags.some((tag) => slugify(tag) === querySlug);

                if (normalizedQuery && querySlug && !exactExistingMatch && !exactNewMatch) {
                    const createOption = document.createElement('button');
                    createOption.type = 'button';
                    createOption.className = 'mt-2 flex w-full items-center justify-between gap-3 rounded-2xl border border-blue-200 bg-blue-50/80 px-4 py-3 text-left text-sm text-blue-700 transition hover:border-blue-300 hover:bg-blue-100';

                    const createText = document.createElement('span');
                    createText.className = 'truncate font-medium';
                    createText.textContent = `Add "${normalizedQuery}"`;

                    createOption.appendChild(createText);

                    createOption.addEventListener('click', () => {
                        selectedNewTags = [...selectedNewTags, normalizedQuery];
                        articleTagsSearch.value = '';
                        renderTagPicker();
                    });

                    articleTagsOptions.appendChild(createOption);
                }

                if (articleTagsOptions.children.length === 0) {
                    const emptyState = document.createElement('div');
                    emptyState.className = 'rounded-2xl border border-dashed border-slate-200 px-4 py-5 text-sm text-slate-500';
                    emptyState.textContent = 'No matching tags found.';
                    articleTagsOptions.appendChild(emptyState);
                }
            };

            const renderTagPicker = () => {
                syncHiddenInputs();
                updateSummary();
                renderOptions();
                renderSelectedTags();
            };

            const openTagPicker = () => {
                articleTagsPanel.classList.remove('hidden');
                articleTagsTrigger.setAttribute('aria-expanded', 'true');
                articleTagsSearch.focus();
            };

            const closeTagPicker = () => {
                articleTagsPanel.classList.add('hidden');
                articleTagsTrigger.setAttribute('aria-expanded', 'false');
            };

            articleTagsTrigger.addEventListener('click', () => {
                if (articleTagsPanel.classList.contains('hidden')) {
                    openTagPicker();
                    return;
                }

                closeTagPicker();
            });

            articleTagsSearch.addEventListener('input', renderOptions);

            document.addEventListener('click', (event) => {
                if (!articleTagPicker.contains(event.target)) {
                    closeTagPicker();
                }
            });

            renderTagPicker();

        })();
    </script>
@endpush
