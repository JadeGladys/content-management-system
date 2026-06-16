@extends('layouts.admin', ['title' => $pageTitle])

@section('content')
    @php
        $canManageCareer = auth()->user()->role === 'admin' || $career->created_by === auth()->id();
        $publishedLabel = $career->published_at
            ? 'Published ' . $career->published_at->format('d.m.Y') . ' by ' . ($career->createdBy?->name ?? 'Unknown')
            : 'By ' . ($career->createdBy?->name ?? 'Unknown');
    @endphp

    <div class="my-4 px-4 md:px-6">
        <div class="mx-auto max-w-5xl min-w-0">
            <div class="mb-6 rounded-2xl border border-slate-200 bg-white px-4 py-4 shadow-sm md:px-8">
                <div class="flex items-center justify-between gap-5">
                    <div class="min-w-0">
                        <a
                            href="{{ route('careers.index') }}"
                            class="inline-flex items-center gap-2 text-xs font-semibold text-slate-500 transition hover:text-slate-800"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                            </svg>
                            Back to careers
                        </a>
                    </div>

                    <div class="flex shrink-0 flex-wrap items-center justify-end gap-3">
                        @if ($career->status === 'draft' && $career->created_by === auth()->id())
                            <a
                                href="{{ route('careers.edit', $career) }}"
                                class="inline-flex items-center gap-2 rounded-xl border border-blue-600 bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-blue-700"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                </svg>
                                Edit Draft
                            </a>
                        @endif

                        @if ($canManageCareer && $career->status === 'published')
                            <form method="POST" action="{{ route('careers.status.update', $career) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="target_status" value="draft">
                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-sm font-semibold text-amber-700 shadow-sm transition hover:-translate-y-0.5 hover:border-amber-300 hover:bg-amber-100"
                                >
                                    Unpublish to Draft
                                </button>
                            </form>

                            <form method="POST" action="{{ route('careers.status.update', $career) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="target_status" value="closed">
                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-2 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-sm font-semibold text-rose-700 shadow-sm transition hover:-translate-y-0.5 hover:border-rose-300 hover:bg-rose-100"
                                >
                                    Close Career
                                </button>
                            </form>
                        @endif

                        @if ($canManageCareer && $career->status === 'closed')
                            <form method="POST" action="{{ route('careers.status.update', $career) }}">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="target_status" value="draft">
                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 shadow-sm transition hover:-translate-y-0.5 hover:border-blue-300 hover:bg-blue-100"
                                >
                                    Set to Draft
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            <article class="overflow-hidden rounded-2xl bg-white shadow-sm">
                <div class="px-5 py-7 md:px-8">
                    <div class="mx-auto max-w-3xl">
                        <h1 class="font-serif text-[25px] font-bold leading-tight tracking-tight text-slate-950 md:text-[30px]">
                            {{ $career->title }}
                        </h1>

                        <div class="mt-4 flex flex-wrap gap-2">
                            @if ($career->category)
                                <span class="inline-flex items-center rounded-full bg-white px-2 py-1 text-[11px] font-semibold text-slate-600 shadow-sm ring-1 ring-slate-200">
                                    {{ $career->category->name }}
                                </span>
                            @endif

                            @if ($career->type)
                                <span class="inline-flex items-center rounded-full bg-white px-2 py-1 text-[11px] font-semibold text-slate-600 shadow-sm ring-1 ring-slate-200">
                                    {{ $career->type }}
                                </span>
                            @endif

                            @if ($career->location)
                                <span class="inline-flex items-center rounded-full bg-white px-2 py-1 text-[11px] font-semibold text-slate-600 shadow-sm ring-1 ring-slate-200">
                                    {{ $career->location }}
                                </span>
                            @endif

                            @if ($career->deadline)
                                <span class="inline-flex items-center rounded-full bg-white px-2 py-1 text-[11px] font-semibold text-slate-600 shadow-sm ring-1 ring-slate-200">
                                    Deadline {{ $career->deadline->format('d M Y') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="px-5 py-4 md:px-8">
                    <div class="mx-auto max-w-2xl">
                        <div class="space-y-8">
                            @if (filled($career->overview))
                                <section>
                                    <h2 class="text-lg font-semibold text-slate-900">Overview</h2>
                                    <div class="prose prose-xs prose-slate mt-3 max-w-none">
                                        {!! $renderedOverview !!}
                                    </div>
                                </section>
                            @endif

                            @if (filled($career->description))
                                <section>
                                    <h2 class="text-lg font-semibold text-slate-900">Description</h2>
                                    <div class="prose prose-xs prose-slate mt-3 max-w-none">
                                        {!! $renderedDescription !!}
                                    </div>
                                </section>
                            @endif

                            @if (filled($career->requirements))
                                <section>
                                    <h2 class="text-lg font-semibold text-slate-900">Requirements</h2>
                                    <div class="prose prose-xs prose-slate mt-3 max-w-none">
                                        {!! $renderedRequirements !!}
                                    </div>
                                </section>
                            @endif
                        </div>

                        <div class="mt-6 border-t border-slate-200 pt-5">
                            <div class="flex items-center gap-4">
                                <span class="flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-slate-100 ring-1 ring-slate-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0 0 12 15.75a7.488 7.488 0 0 0-5.982 2.975m11.963 0a9 9 0 1 0-11.963 0m11.963 0A8.966 8.966 0 0 1 12 21a8.966 8.966 0 0 1-5.982-2.275" />
                                    </svg>
                                </span>

                                <p class="text-xs font-light tracking-tight text-slate-800 md:text-xs">
                                    {{ $publishedLabel }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </div>
    </div>
@endsection