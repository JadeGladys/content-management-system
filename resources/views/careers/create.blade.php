@extends('layouts.admin', ['title' => 'Create Career'])

@section('content')
    <div class="my-6 px-4 md:px-8">
        <div class="mx-auto max-w-7xl min-w-0">
            <div class="mb-8">
                <p class="text-sm font-semibold uppercase tracking-[0.22em] text-blue-600">Careers</p>
                <h1 class="mt-3 text-3xl font-semibold tracking-tight text-slate-900 md:text-[3rem]">Create a new career</h1>
                <p class="mt-3 max-w-3xl text-sm leading-6 text-slate-600 md:text-base">
                    Add a new job entry to the CMS. The slug will be generated automatically from the title and kept unique.
                </p>
            </div>

            @if (session('error'))
                <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('careers.store') }}" class="space-y-10">
                @csrf

                <section class="grid gap-6 border-t border-slate-200 pt-8 lg:grid-cols-[240px_minmax(0,1fr)]">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">Career Information</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Fill in the main details that define the career posting.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="grid gap-5 md:grid-cols-2">
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
                                <label for="department" class="mb-2 block text-sm font-medium text-slate-700">
                                    Department <span class="text-rose-600">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="department"
                                    name="department"
                                    value="{{ old('department') }}"
                                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                >
                                @error('department')
                                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="location" class="mb-2 block text-sm font-medium text-slate-700">
                                    Location <span class="text-rose-600">*</span>
                                </label>
                                <input
                                    type="text"
                                    id="location"
                                    name="location"
                                    list="career-location-suggestions"
                                    value="{{ old('location') }}"
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

                            <div>
                                <label for="deadline" class="mb-2 block text-sm font-medium text-slate-700">
                                    Deadline
                                </label>
                                <input
                                    type="date"
                                    id="deadline"
                                    name="deadline"
                                    value="{{ old('deadline') }}"
                                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                >
                                @error('deadline')
                                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </section>

                <section class="grid gap-6 border-t border-slate-200 pt-8 lg:grid-cols-[240px_minmax(0,1fr)]">
                    <div>
                        <h2 class="text-sm font-semibold text-slate-900">Career Content</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500">
                            Add the summary and detailed information that candidates will read.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <div class="grid gap-5">
                            <div>
                                <label for="about" class="mb-2 block text-sm font-medium text-slate-700">
                                    About
                                </label>
                                <textarea
                                    id="about"
                                    name="about"
                                    rows="4"
                                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                >{{ old('about') }}</textarea>
                                @error('about')
                                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="description" class="mb-2 block text-sm font-medium text-slate-700">
                                    Description
                                </label>
                                <textarea
                                    id="description"
                                    name="description"
                                    rows="6"
                                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                >{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="requirements" class="mb-2 block text-sm font-medium text-slate-700">
                                    Requirements
                                </label>
                                <textarea
                                    id="requirements"
                                    name="requirements"
                                    rows="6"
                                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                                >{{ old('requirements') }}</textarea>
                                @error('requirements')
                                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-3 border-t border-slate-200 pt-6">
                            <a
                                href="{{ route('careers.index') }}"
                                class="rounded-2xl border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                            >
                                Cancel
                            </a>

                            <button
                                type="submit"
                                class="rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700"
                            >
                                Save
                            </button>

                        </div>
                    </div>
                </section>
            </form>
        </div>
    </div>
@endsection
