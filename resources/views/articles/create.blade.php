@extends('layouts.admin', ['title' => 'Create Article'])

@section('content')
    <div class="my-6 px-4 md:px-8">
        <div class="mx-auto max-w-7xl min-w-0">
            <div class="mb-8">
                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-blue-600">Articles</p>
                <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-900 md:text-[3rem]">Create a new article</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 md:text-base">
                    Add a new article entry to the CMS. The slug will be generated automatically from the title and kept unique.
                </p>
            </div>

            @if (session('error'))
                <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('articles.store') }}" enctype="multipart/form-data" class="space-y-10">
                @csrf

                <section class="border-t border-slate-200 pt-8">
                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-200 px-6 py-5">
                            <div class="mt-2 flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
                                <div>
                                    <h2 class="text-xl font-semibold tracking-tight text-slate-900">Cover photo</h2>
                                    <p class="mt-1 text-sm leading-6 text-slate-500">
                                        Upload a featured image for this article or browse uploaded assets later.
                                    </p>
                                </div>
                            </div>
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
                            <input type="hidden" id="featured_image_id" name="featured_image_id" value="{{ old('featured_image_id') }}">

                            @error('featured_image_upload')
                                <p class="mb-4 text-sm text-rose-600">{{ $message }}</p>
                            @enderror

                            @error('featured_image_id')
                                <p class="mb-4 text-sm text-rose-600">{{ $message }}</p>
                            @enderror

                            <div
                                id="featured-image-preview-card"
                                class="hidden max-w-xs overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
                            >
                                <img
                                    id="featured-image-preview-tag"
                                    src=""
                                    alt="Selected featured image preview"
                                    class="h-44 w-full object-cover"
                                >

                                <div class="flex items-center justify-between gap-3 px-4 py-3">
                                    <p
                                        id="featured-image-preview-name"
                                        class="truncate text-sm font-medium text-slate-700"
                                    ></p>

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
                                class="rounded-2xl border border-dashed border-slate-200 bg-slate-50 px-6 py-10 text-sm text-slate-500"
                            >
                                No featured image selected yet.
                            </div>
                        </div>
                    </div>
                </section>

                <section class="border-t border-slate-200 pt-8">
                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-200 px-6 py-5">
                            <h2 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Article Information</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                Fill in the core details that define the article before it is published.
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
                                    value="{{ old('title') }}"
                                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                >
                                @error('title')
                                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
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
                                        <option value="{{ $category }}" @selected(old('category') === $category)>
                                            {{ $category }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('category')
                                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="tags" class="mb-2 block text-sm font-medium text-slate-700">
                                    Tags
                                </label>
                                <input
                                    type="text"
                                    id="tags"
                                    name="tags"
                                    value="{{ old('tags') }}"
                                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                >
                                @error('tags')
                                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </section>

                <section class="border-t border-slate-200 pt-8">
                    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                        <div class="border-b border-slate-200 px-6 py-5">
                            <h2 class="mt-2 text-xl font-semibold tracking-tight text-slate-900">Article Content</h2>
                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                Write the article summary and main body using the editor below.
                            </p>
                        </div>

                        <div class="p-6">
                            <div class="grid gap-5">
                            <div>
                                <label for="overview" class="mb-2 block text-sm font-medium text-slate-700">
                                    Overview
                                </label>
                                <textarea
                                    id="overview"
                                    name="overview"
                                    rows="4"
                                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                >{{ old('overview') }}</textarea>
                                @error('overview')
                                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="content" class="mb-2 block text-sm font-medium text-slate-700">
                                    Content
                                </label>
                                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                                    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 px-4 py-3">
                                        <button
                                            type="button"
                                            data-editor="h2"
                                            class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                                <path fill-rule="evenodd" d="M2.75 4a.75.75 0 0 1 .75.75v4.5h5v-4.5a.75.75 0 0 1 1.5 0v10.5a.75.75 0 0 1-1.5 0v-4.5h-5v4.5a.75.75 0 0 1-1.5 0V4.75A.75.75 0 0 1 2.75 4ZM15 9.5c-.729 0-1.445.051-2.146.15a.75.75 0 0 1-.208-1.486 16.887 16.887 0 0 1 3.824-.1c.855.074 1.512.78 1.527 1.637a17.476 17.476 0 0 1-.009.931 1.713 1.713 0 0 1-1.18 1.556l-2.453.818a1.25 1.25 0 0 0-.855 1.185v.309h3.75a.75.75 0 0 1 0 1.5h-4.5a.75.75 0 0 1-.75-.75v-1.059a2.75 2.75 0 0 1 1.88-2.608l2.454-.818c.102-.034.153-.117.155-.188a15.556 15.556 0 0 0 .009-.85.171.171 0 0 0-.158-.169A15.458 15.458 0 0 0 15 9.5Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        <button
                                            type="button"
                                            data-editor="h3"
                                            class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                                <path fill-rule="evenodd" d="M2.75 4a.75.75 0 0 1 .75.75v4.5h5v-4.5a.75.75 0 0 1 1.5 0v10.5a.75.75 0 0 1-1.5 0v-4.5h-5v4.5a.75.75 0 0 1-1.5 0V4.75A.75.75 0 0 1 2.75 4ZM15 9.5c-.73 0-1.448.051-2.15.15a.75.75 0 1 1-.209-1.485 16.886 16.886 0 0 1 3.476-.128c.985.065 1.878.837 1.883 1.932V10a6.75 6.75 0 0 1-.301 2A6.75 6.75 0 0 1 18 14v.031c-.005 1.095-.898 1.867-1.883 1.932a17.018 17.018 0 0 1-3.467-.127.75.75 0 0 1 .209-1.485 15.377 15.377 0 0 0 3.16.115c.308-.02.48-.24.48-.441L16.5 14c0-.431-.052-.85-.15-1.25h-2.6a.75.75 0 0 1 0-1.5h2.6c.098-.4.15-.818.15-1.25v-.024c-.001-.201-.173-.422-.481-.443A15.485 15.485 0 0 0 15 9.5Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        <button
                                            type="button"
                                            data-editor="bold"
                                            class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-bold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                                <path fill-rule="evenodd" d="M4 3a1 1 0 0 1 1-1h6a4.5 4.5 0 0 1 3.274 7.587A4.75 4.75 0 0 1 11.25 18H5a1 1 0 0 1-1-1V3Zm2.5 5.5v-4H11a2 2 0 1 1 0 4H6.5Zm0 2.5v4.5h4.75a2.25 2.25 0 0 0 0-4.5H6.5Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        <button
                                            type="button"
                                            data-editor="italic"
                                            class="rounded-xl border border-slate-300 px-3 py-2 text-sm italic text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                                <path fill-rule="evenodd" d="M8 2.75A.75.75 0 0 1 8.75 2h7.5a.75.75 0 0 1 0 1.5h-3.215l-4.483 13h2.698a.75.75 0 0 1 0 1.5h-7.5a.75.75 0 0 1 0-1.5h3.215l4.483-13H8.75A.75.75 0 0 1 8 2.75Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        <button
                                            type="button"
                                            data-editor="bulletList"
                                            class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                                <path fill-rule="evenodd" d="M6 4.75A.75.75 0 0 1 6.75 4h10.5a.75.75 0 0 1 0 1.5H6.75A.75.75 0 0 1 6 4.75ZM6 10a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H6.75A.75.75 0 0 1 6 10Zm0 5.25a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H6.75a.75.75 0 0 1-.75-.75ZM1.99 4.75a1 1 0 0 1 1-1H3a1 1 0 0 1 1 1v.01a1 1 0 0 1-1 1h-.01a1 1 0 0 1-1-1v-.01ZM1.99 15.25a1 1 0 0 1 1-1H3a1 1 0 0 1 1 1v.01a1 1 0 0 1-1 1h-.01a1 1 0 0 1-1-1v-.01ZM1.99 10a1 1 0 0 1 1-1H3a1 1 0 0 1 1 1v.01a1 1 0 0 1-1 1h-.01a1 1 0 0 1-1-1V10Z" clip-rule="evenodd" />
                                            </svg>
                                        </button>

                                        <button
                                            type="button"
                                            data-editor="orderedList"
                                            class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                                                <path d="M3 1.25a.75.75 0 0 0 0 1.5h.25v2.5a.75.75 0 0 0 1.5 0V2A.75.75 0 0 0 4 1.25H3ZM2.97 8.654a3.5 3.5 0 0 1 1.524-.12.034.034 0 0 1-.012.012L2.415 9.579A.75.75 0 0 0 2 10.25v1c0 .414.336.75.75.75h2.5a.75.75 0 0 0 0-1.5H3.927l1.225-.613c.52-.26.848-.79.848-1.371 0-.647-.429-1.327-1.193-1.451a5.03 5.03 0 0 0-2.277.155.75.75 0 0 0 .44 1.434ZM7.75 3a.75.75 0 0 0 0 1.5h9.5a.75.75 0 0 0 0-1.5h-9.5ZM7.75 9.25a.75.75 0 0 0 0 1.5h9.5a.75.75 0 0 0 0-1.5h-9.5ZM7.75 15.5a.75.75 0 0 0 0 1.5h9.5a.75.75 0 0 0 0-1.5h-9.5ZM2.625 13.875a.75.75 0 0 0 0 1.5h1.5a.125.125 0 0 1 0 .25H3.5a.75.75 0 0 0 0 1.5h.625a.125.125 0 0 1 0 .25h-1.5a.75.75 0 0 0 0 1.5h1.5a1.625 1.625 0 0 0 1.37-2.5 1.625 1.625 0 0 0-1.37-2.5h-1.5Z" />
                                            </svg>
                                        </button>

                                        <button
                                            type="button"
                                            data-editor="quote"
                                            class="rounded-xl border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 transition hover:border-blue-300 hover:bg-blue-50 hover:text-blue-700"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                                <path fill="none" stroke="#000000" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m9.937 10.453l-.01.13c0 3.35-2.038 5.115-4.63 6.058m4.64-6.188a3.093 3.093 0 1 1-6.187.001a3.093 3.093 0 0 1 6.187-.001m10.313 0l-.01.13c0 3.35-2.038 5.115-4.63 6.058m4.64-6.188a3.093 3.093 0 1 1-6.187 0a3.093 3.093 0 0 1 6.187 0"/>
                                            </svg>
                                        </button>
                                    </div>

                                    <div
                                        id="article-content-editor"
                                        class="min-h-80 px-5 py-4 text-base leading-8 text-slate-900 focus:outline-none [&_.ProseMirror]:min-h-80 [&_.ProseMirror]:outline-none [&_.ProseMirror_blockquote]:border-l-4 [&_.ProseMirror_blockquote]:border-blue-200 [&_.ProseMirror_blockquote]:pl-4 [&_.ProseMirror_blockquote]:italic [&_.ProseMirror_h2]:mb-3 [&_.ProseMirror_h2]:mt-6 [&_.ProseMirror_h2]:text-2xl [&_.ProseMirror_h2]:font-semibold [&_.ProseMirror_h3]:mb-3 [&_.ProseMirror_h3]:mt-5 [&_.ProseMirror_h3]:text-xl [&_.ProseMirror_h3]:font-semibold [&_.ProseMirror_ol]:my-4 [&_.ProseMirror_ol]:list-decimal [&_.ProseMirror_ol]:pl-6 [&_.ProseMirror_p]:mb-4 [&_.ProseMirror_ul]:my-4 [&_.ProseMirror_ul]:list-disc [&_.ProseMirror_ul]:pl-6"
                                    ></div>
                                    @error('content')
                                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <textarea id="content" name="content" class="hidden">{{ old('content') }}</textarea>
                            </div>
                        </div>
                        </div>

                        <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50/60 px-6 py-5">
                            <a
                                href="{{ route('articles.index') }}"
                                class="inline-flex min-w-28 items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                            >
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="inline-flex min-w-28 items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                            >
                                Save
                            </button>
                        </div>
                    </div>
                </section>
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
