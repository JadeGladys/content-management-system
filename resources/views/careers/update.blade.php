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
                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-blue-600">Careers</p>
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

            <form method="POST" action="{{ $formAction }}" class="space-y-8">
                @csrf
                @method($formMethod)

                <div class="grid gap-8 xl:grid-cols-[minmax(0,1fr)_20rem]">
                    <div class="space-y-8">
                        <section class="border-t border-slate-200 pt-8">
                            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
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
                                                <option value="{{ $category }}" @selected(old('category', $career->category) === $category)>
                                                    {{ $category }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('category')
                                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                        @enderror
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

                                    <div class="md:col-span-2">
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
                                    @include('careers.editor', ['field' => 'about', 'label' => 'About', 'required' => true, 'value' => old('about', $editorValue($career->about))])
                                    @include('careers.editor', ['field' => 'description', 'label' => 'Description', 'required' => false, 'value' => old('description', $editorValue($career->description))])
                                    @include('careers.editor', ['field' => 'requirements', 'label' => 'Requirements', 'required' => false, 'value' => old('requirements', $editorValue($career->requirements))])
                                </div>
                            </div>
                        </section>
                    </div>

                    <aside class="space-y-6 border-t border-slate-200 pt-8 xl:border-t-0 xl:pt-0">
                        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                            <div class="border-b border-slate-200 px-6 py-5">
                                <h2 class="text-lg font-semibold tracking-tight text-slate-900">Publishing Details</h2>
                                <p class="mt-1 text-sm leading-6 text-slate-500">
                                    Control the stable URL slug and deadline from here.
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
                            </div>
                        </div>
                    </aside>
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
                    <a
                        href="{{ route('careers.index') }}"
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
@endsection
