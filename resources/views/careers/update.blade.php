@extends('layouts.admin', ['title' => $pageTitle])

@section('content')
    @php
        $editorValue = function ($value) {
            return filled($value)
                ? json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                : '';
        };
    @endphp

    <div class="my-6 px-4 md:px-8">
        <div class="mx-auto max-w-7xl min-w-0">
            <div class="mb-8">
                <h1 class="max-w-full font-serif text-[38px] font-bold leading-tight text-[#111827]">{{ $pageHeading }}</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 md:text-base">
                    {{ $pageDescription }}
                </p>
            </div>

            @if ($career->exists && $career->status === 'draft')
                <form
                    id="career-delete-form"
                    method="POST"
                    action="{{ route('careers.destroy', $career) }}"
                    class="hidden"
                    onsubmit="return confirm('Delete this career draft? This action cannot be undone.');"
                >
                    @csrf
                    @method('DELETE')
                </form>
            @endif

            <form method="POST" action="{{ $formAction }}" class="space-y-8">
                @csrf
                @if (strtoupper($formMethod) !== 'POST')
                    @method($formMethod)
                @endif

                <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-400 pb-5">
                    <div class="flex items-center gap-6">
                        <button
                            type="button"
                            class="career-content-tab border-b-2 border-slate-900 pb-2 text-sm font-semibold text-slate-900"
                            data-content-target="main"
                        >
                            Main
                        </button>

                        <button
                            type="button"
                            class="career-content-tab border-b-2 border-transparent pb-2 text-sm font-semibold text-slate-500 transition hover:text-slate-700"
                            data-content-target="seo"
                        >
                            SEO
                        </button>
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-3">
                        @if ($career->exists && $career->status === 'draft')
                            <button
                                type="submit"
                                form="career-delete-form"
                                class="inline-flex items-center gap-2 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-700 shadow-sm transition hover:-translate-y-0.5 hover:border-rose-300 hover:bg-rose-100"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor" class="size-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.11 0 0 0-7.5 0" />
                                </svg>
                                Delete
                            </button>
                        @endif

                        <div id="career-submit-menu" class="relative">
                            <input
                                type="hidden"
                                name="action"
                                id="career-submit-action"
                                value="{{ old('action', 'save') }}"
                            >

                            <div class="flex overflow-hidden rounded-2xl shadow-sm">
                                <button
                                    type="submit"
                                    id="career-submit-primary"
                                    class="inline-flex min-w-44 items-center justify-center bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                                >
                                    Save
                                </button>

                                <button
                                    type="button"
                                    id="career-submit-toggle"
                                    class="inline-flex items-center justify-center border-l border-blue-500 bg-blue-600 px-4 py-3 text-white transition hover:bg-blue-700"
                                    aria-expanded="false"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>

                            <div
                                id="career-submit-options"
                                class="absolute right-0 top-[calc(100%+0.75rem)] z-30 hidden min-w-56 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_18px_40px_-20px_rgba(15,23,42,0.35)]"
                            >
                                <button
                                    type="button"
                                    class="career-submit-option flex w-full items-center justify-between px-4 py-3 text-left text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                                    data-submit-action="save"
                                    data-submit-label="Save"
                                >
                                    Save
                                </button>

                                <button
                                    type="button"
                                    class="career-submit-option flex w-full items-center justify-between border-t border-slate-200 px-4 py-3 text-left text-sm font-medium text-slate-700 transition hover:bg-slate-50"
                                    data-submit-action="publish"
                                    data-submit-label="Save & Publish"
                                >
                                    Save &amp; Publish
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_20rem]">
                    <div class="space-y-8">
                        <div id="career-content-panel-main" class="space-y-8">
                            <section class="pt-8">
                                <div class="overflow-visible rounded-3xl border border-slate-200 bg-white shadow-sm">
                                    <div class="border-b border-slate-200 px-6 py-5">
                                        <h2 class="text-xl font-semibold tracking-tight text-slate-900">Career Information</h2>
                                        <p class="mt-1 text-sm leading-6 text-slate-500">
                                            Update the main job details and keep the draft aligned with the role you want to publish.
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
                                                value="{{ old('title', $career->title) }}"
                                                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                            >
                                            @error('title')
                                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div class="md:col-span-2">
                                            <x-category-picker
                                                picker-id="career-update-category-picker"
                                                name="category"
                                                label="Category"
                                                :required="true"
                                                :value="old('category', $career->category?->name)"
                                                :options="$categories"
                                                placeholder="Select category"
                                                :panel-bleed="true"
                                            />
                                        </div>

                                        <div>
                                            <label for="department" class="mb-2 block text-sm font-medium text-slate-700">
                                                Department <span class="text-rose-600">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                id="department"
                                                name="department"
                                                value="{{ old('department', $career->department) }}"
                                                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                            >
                                            @error('department')
                                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="location" class="mb-2 block text-sm font-medium text-slate-700">
                                                Location
                                            </label>
                                            <input
                                                type="text"
                                                id="location"
                                                name="location"
                                                list="career-location-suggestions"
                                                value="{{ old('location', $career->location) }}"
                                                placeholder="Start typing a location"
                                                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                            >
                                            <datalist id="career-location-suggestions">
                                                @foreach ($locationSuggestions as $location)
                                                    <option value="{{ $location }}"></option>
                                                @endforeach
                                            </datalist>
                                            @error('location')
                                                <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <section class="border-t border-slate-200 pt-8">
                                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                                    <div class="border-b border-slate-200 px-6 py-5">
                                        <h2 class="text-xl font-semibold tracking-tight text-slate-900">Career Content</h2>
                                        <p class="mt-1 text-sm leading-6 text-slate-500">
                                            Complete the full job content here. Save when you are still refining it, or publish when it is ready.
                                        </p>
                                    </div>

                                    <div class="space-y-5 p-6">
                                        <x-tiptap-editor
                                            field="about"
                                            label="About"
                                            :required="false"
                                            :value="old('about', $editorValue($career->about))"
                                        />

                                        <x-tiptap-editor
                                            field="description"
                                            label="Description"
                                            :required="false"
                                            :value="old('description', $editorValue($career->description))"
                                        />

                                        <x-tiptap-editor
                                            field="requirements"
                                            label="Requirements"
                                            :required="false"
                                            :value="old('requirements', $editorValue($career->requirements))"
                                        />
                                    </div>
                                </div>
                            </section>
                        </div>

                        <div id="career-content-panel-seo" class="hidden space-y-8">
                            <section class="border-t border-slate-200 pt-8">
                                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                                    <div class="border-b border-slate-200 px-6 py-5">
                                        <h2 class="text-xl font-semibold tracking-tight text-slate-900">Search Visibility</h2>
                                    </div>

                                    <div class="p-6">
                                        <label class="flex items-start justify-between gap-4 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4">
                                            <div>
                                                <p class="text-sm font-semibold text-slate-900">Hide from search engines</p>
                                            </div>

                                            <input
                                                type="checkbox"
                                                name="no_index"
                                                value="1"
                                                @checked(old('no_index', $career->no_index))
                                                class="mt-1 size-5 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                            >
                                        </label>
                                    </div>
                                </div>
                            </section>

                            <section class="border-t border-slate-200 pt-8">
                                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                                    <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-200 px-6 py-5">
                                        <div>
                                            <h2 class="text-xl font-semibold tracking-tight text-slate-900">Meta Data</h2>
                                            <p class="mt-1 text-sm leading-6 text-slate-500">
                                                Leave fields blank if you want the system defaults to be used.
                                            </p>
                                        </div>

                                        <button
                                            type="button"
                                            id="career-generate-seo"
                                            class="inline-flex items-center justify-center rounded-2xl border border-slate-300 bg-blue-600 px-4 py-3 text-sm font-semibold text-slate-100 shadow-sm transition hover:bg-blue-700"
                                        >
                                            Generate SEO
                                        </button>
                                    </div>

                                    <div class="space-y-5 p-6">
                                        <div>
                                            <label for="meta_title" class="mb-2 block text-sm font-medium text-slate-700">Meta title</label>
                                            <input
                                                type="text"
                                                id="meta_title"
                                                name="meta_title"
                                                value="{{ old('meta_title', $career->meta_title) }}"
                                                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                            >
                                            @error('meta_title')
                                                <p data-error-field="meta_title" class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="meta_description" class="mb-2 block text-sm font-medium text-slate-700">Meta description</label>
                                            <textarea
                                                id="meta_description"
                                                name="meta_description"
                                                rows="4"
                                                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                            >{{ old('meta_description', $career->meta_description) }}</textarea>
                                            @error('meta_description')
                                                <p data-error-field="meta_description" class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="meta_keywords" class="mb-2 block text-sm font-medium text-slate-700">Meta keywords</label>
                                            <input
                                                type="text"
                                                id="meta_keywords"
                                                name="meta_keywords"
                                                value="{{ old('meta_keywords', $career->meta_keywords) }}"
                                                placeholder="engineering, backend, remote"
                                                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                            >
                                            @error('meta_keywords')
                                                <p data-error-field="meta_keywords" class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="canonical_url" class="mb-2 block text-sm font-medium text-slate-700">Canonical URL</label>
                                            <input
                                                type="url"
                                                id="canonical_url"
                                                name="canonical_url"
                                                value="{{ old('canonical_url', $career->canonical_url) }}"
                                                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                            >
                                            <p class="mt-2 text-xs leading-5 text-green-300">
                                                Keep this blank until the final public website URL is confirmed, or set it manually if you already know it.
                                            </p>
                                            @error('canonical_url')
                                                <p data-error-field="canonical_url" class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <section class="border-t border-slate-200 pt-8">
                                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                                    <div class="border-b border-slate-200 px-6 py-5">
                                        <h2 class="text-xl font-semibold tracking-tight text-slate-900">Social Sharing</h2>
                                        <p class="mt-1 text-sm leading-6 text-slate-500">
                                            These values are used for Open Graph sharing metadata.
                                        </p>
                                    </div>

                                    <div class="space-y-5 p-6">
                                        <div>
                                            <label for="og_title" class="mb-2 block text-sm font-medium text-slate-700">OG title</label>
                                            <input
                                                type="text"
                                                id="og_title"
                                                name="og_title"
                                                value="{{ old('og_title', $career->og_title) }}"
                                                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                            >
                                            @error('og_title')
                                                <p data-error-field="og_title" class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <div>
                                            <label for="og_description" class="mb-2 block text-sm font-medium text-slate-700">OG description</label>
                                            <textarea
                                                id="og_description"
                                                name="og_description"
                                                rows="4"
                                                class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                            >{{ old('og_description', $career->og_description) }}</textarea>
                                            @error('og_description')
                                                <p data-error-field="og_description" class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>

                    <aside class="space-y-6 border-t border-slate-200 pt-8 lg:self-start lg:border-t-0 lg:pt-8">
                        <div class="rounded-3xl border border-slate-200 bg-white shadow-sm lg:sticky lg:top-6">
                            <div class="border-b border-slate-200 px-6 py-5">
                                <h2 class="text-lg font-semibold tracking-tight text-slate-900">Publishing Details</h2>
                                <p class="mt-1 text-sm leading-6 text-slate-500">
                                    Control the stable URL slug and application deadline from here.
                                </p>
                            </div>

                            <div class="space-y-5 p-6">
                                <div>
                                    <label for="slug" class="mb-2 block text-sm font-medium text-slate-700">Slug</label>
                                    <input
                                        type="text"
                                        id="slug"
                                        name="slug"
                                        value="{{ old('slug', $career->slug) }}"
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
                                    <label for="deadline" class="mb-2 block text-sm font-medium text-slate-700">Deadline</label>
                                    <input
                                        type="date"
                                        id="deadline"
                                        name="deadline"
                                        value="{{ old('deadline', optional($career->deadline)->format('Y-m-d')) }}"
                                        class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                    >
                                    @error('deadline')
                                        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="border-t border-slate-200 pt-5">
                                    <a
                                        href="{{ route('careers.index') }}"
                                        class="inline-flex w-full items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                                    >
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        (() => {
            const careerContentTabs = document.querySelectorAll('.career-content-tab');
            const careerContentPanels = {
                main: document.getElementById('career-content-panel-main'),
                seo: document.getElementById('career-content-panel-seo'),
            };

            const actionInput = document.getElementById('career-submit-action');
            const submitPrimary = document.getElementById('career-submit-primary');
            const submitToggle = document.getElementById('career-submit-toggle');
            const submitOptions = document.getElementById('career-submit-options');
            const submitMenu = document.getElementById('career-submit-menu');
            const submitOptionButtons = document.querySelectorAll('.career-submit-option');
            const generateSeoButton = document.getElementById('career-generate-seo');

            const activateContentPanel = (target) => {
                Object.entries(careerContentPanels).forEach(([panelKey, panel]) => {
                    panel?.classList.toggle('hidden', panelKey !== target);
                });

                careerContentTabs.forEach((tab) => {
                    const isActive = tab.dataset.contentTarget === target;
                    tab.classList.toggle('border-slate-900', isActive);
                    tab.classList.toggle('text-slate-900', isActive);
                    tab.classList.toggle('border-transparent', !isActive);
                    tab.classList.toggle('text-slate-500', !isActive);
                });
            };

            careerContentTabs.forEach((tab) => {
                tab.addEventListener('click', () => {
                    activateContentPanel(tab.dataset.contentTarget);
                });
            });

            const setSubmitAction = (action, label) => {
                if (actionInput) {
                    actionInput.value = action;
                }

                if (submitPrimary) {
                    submitPrimary.textContent = label;
                    submitPrimary.classList.toggle('bg-emerald-600', action === 'publish');
                    submitPrimary.classList.toggle('hover:bg-emerald-700', action === 'publish');
                    submitPrimary.classList.toggle('bg-blue-600', action !== 'publish');
                    submitPrimary.classList.toggle('hover:bg-blue-700', action !== 'publish');
                }

                if (submitToggle) {
                    submitToggle.classList.toggle('bg-emerald-600', action === 'publish');
                    submitToggle.classList.toggle('hover:bg-emerald-700', action === 'publish');
                    submitToggle.classList.toggle('border-emerald-500', action === 'publish');
                    submitToggle.classList.toggle('bg-blue-600', action !== 'publish');
                    submitToggle.classList.toggle('hover:bg-blue-700', action !== 'publish');
                    submitToggle.classList.toggle('border-blue-500', action !== 'publish');
                }
            };

            submitToggle?.addEventListener('click', () => {
                submitOptions?.classList.toggle('hidden');
            });

            submitOptionButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    setSubmitAction(button.dataset.submitAction, button.dataset.submitLabel);
                    submitOptions?.classList.add('hidden');
                });
            });

            generateSeoButton?.addEventListener('click', () => {
                setSubmitAction('generate_seo', 'Save');
                generateSeoButton.form?.requestSubmit();
            });

            document.addEventListener('click', (event) => {
                if (!submitMenu?.contains(event.target)) {
                    submitOptions?.classList.add('hidden');
                }
            });

            const seoFields = [
                'meta_title',
                'meta_description',
                'meta_keywords',
                'canonical_url',
                'og_title',
                'og_description',
            ];

            const hasSeoErrors = seoFields.some((field) => {
                return Boolean(document.querySelector(`[data-error-field="${field}"]`));
            });
            const requestedTab = new URLSearchParams(window.location.search).get('tab');

            setSubmitAction(
                actionInput?.value === 'publish' ? 'publish' : 'save',
                actionInput?.value === 'publish' ? 'Save & Publish' : 'Save'
            );

            activateContentPanel(hasSeoErrors || requestedTab === 'seo' ? 'seo' : 'main');
        })();
    </script>
@endpush
