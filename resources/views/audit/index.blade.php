@extends('layouts.admin', ['title' => 'Audit'])

@section('content')
    @php
        $flattenAuditValue = function ($value) {
            $normalized = is_array($value) ? $value : (json_decode((string) $value, true) ?? $value);

            return is_array($normalized)
                ? collect($normalized)->flatten()->filter(fn ($item) => is_string($item) && strlen($item) > 12)->implode(' ')
                : (string) $normalized;
        };
    @endphp

    <div class="my-4 px-4 md:px-6">
        <div class="mx-auto max-w-7xl min-w-0">
            <div class="mb-8 rounded-xl border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-blue-50/70 px-4 py-4 shadow-sm md:px-6">
                <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_auto] lg:items-center">
                    <div class="max-w-4xl">
                        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-blue-600">Audit Log</p>
                        <h1 class="mt-3 text-3xl leading-tight font-semibold tracking-tight text-slate-900 md:text-[2.35rem]">Every change, tracked and traceable</h1>
                        <p class="mt-2 max-w-[46rem] text-sm leading-6 text-slate-600">
                            Review who created, changed, published, or deleted content — with full before and after detail.
                        </p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <div class="rounded-2xl border border-white/80 bg-white/80 px-4 py-3 shadow-sm backdrop-blur">
                            <p class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">Events today</p>
                            <p class="mt-1 text-xl font-semibold text-slate-900">{{ number_format($stats['events_today']) }}</p>
                        </div>
                        <div class="rounded-2xl border border-white/80 bg-white/80 px-4 py-3 shadow-sm backdrop-blur">
                            <p class="text-xs font-semibold uppercase tracking-[0.1em] text-slate-400">This week</p>
                            <p class="mt-1 text-xl font-semibold text-slate-900">{{ number_format($stats['events_this_week']) }}</p>
                        </div>
                        <div class="rounded-2xl border border-rose-200 bg-rose-50/80 px-4 py-3 shadow-sm backdrop-blur">
                            <p class="text-xs font-semibold uppercase tracking-[0.1em] text-rose-400">Failed logins (7d)</p>
                            <p class="mt-1 text-xl font-semibold text-rose-500">{{ number_format($stats['failed_logins_last_week']) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-5 flex items-center gap-4">
                <form method="GET" action="{{ route('audit.index') }}" class="min-w-0 flex-1 max-w-sm" role="search">
                    @foreach (($filters['action'] ?? []) as $action)
                        <input type="hidden" name="action[]" value="{{ $action }}">
                    @endforeach

                    @foreach (($filters['type'] ?? []) as $type)
                        <input type="hidden" name="type[]" value="{{ $type }}">
                    @endforeach

                    @foreach (($filters['user'] ?? []) as $user)
                        <input type="hidden" name="user[]" value="{{ $user }}">
                    @endforeach

                    @if (! empty($filters['created_from']))
                        <input type="hidden" name="created_from" value="{{ $filters['created_from'] }}">
                    @endif

                    @if (! empty($filters['created_to']))
                        <input type="hidden" name="created_to" value="{{ $filters['created_to'] }}">
                    @endif

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
                            placeholder="Search users, actions, emails..."
                            class="w-full text-sm text-slate-900 outline-none placeholder:text-slate-400"
                            list="audit-search-suggestions"
                            autocomplete="off"
                        />

                        <datalist id="audit-search-suggestions">
                            @foreach ($searchSuggestions as $suggestion)
                                <option value="{{ $suggestion }}"></option>
                            @endforeach
                        </datalist>

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

                <div class="ml-auto flex flex-wrap gap-3">
                    <button
                        type="button"
                        id="openAuditFilterPanel"
                        class="{{ $hasActiveFilters ? 'border-blue-600 bg-blue-600 text-white hover:bg-blue-700' : 'border-slate-200 bg-white text-slate-900 hover:bg-slate-50' }} flex items-center gap-2 rounded-xl border px-3 py-2 text-sm font-semibold shadow-sm transition hover:-translate-y-0.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 fill-current" viewBox="0 0 64 64" aria-hidden="true">
                            <path d="M26.55 61.295a2.18 2.18 0 0 1-2.18-2.18v-20.96L4.161 15.928A6.115 6.115 0 0 1 8.685 5.705h46.63a6.115 6.115 0 0 1 4.524 10.224L39.63 38.154v12.241a2.18 2.18 0 0 1-.817 1.7l-10.9 8.72a2.18 2.18 0 0 1-1.363.48M8.685 10.065a1.755 1.755 0 0 0-1.297 2.932l20.775 22.89a2.18 2.18 0 0 1 .567 1.428v17.266l6.54-5.276v-11.99a2.18 2.18 0 0 1 .567-1.472l20.775-22.89a1.755 1.755 0 0 0-1.297-2.888z" />
                        </svg>
                        Filter
                        @if ($hasActiveFilters)
                            <span class="{{ $hasActiveFilters ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-700' }} inline-flex h-5 min-w-5 items-center justify-center rounded-full px-1.5 text-[11px] font-bold">
                                {{ collect($filters)->flatten()->filter()->count() }}
                            </span>
                        @endif
                    </button>
                    <button type="button" class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-900 shadow-sm transition hover:-translate-y-0.5 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 fill-current" viewBox="0 0 32 32" aria-hidden="true">
                            <path d="M9.24 17.56a1.24 1.24 0 0 1 0-1.75 1.22 1.22 0 0 1 1.73 0l3.78 3.81V3.21a1.25 1.25 0 0 1 2.5 0v16.41L21 15.81a1.22 1.22 0 0 1 1.73 0 1.24 1.24 0 0 1 0 1.75l-5.89 6a1.21 1.21 0 0 1-1.74 0zm19.53 2.16a1.23 1.23 0 0 0-1.23 1.22v5.88a.73.73 0 0 1-.73.73H5.19a.73.73 0 0 1-.73-.73v-5.88a1.23 1.23 0 0 0-2.46 0v5.88A3.19 3.19 0 0 0 5.19 30h21.62A3.19 3.19 0 0 0 30 26.82v-5.88a1.23 1.23 0 0 0-1.23-1.22"></path>
                        </svg>
                        Export
                    </button>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="max-w-full overflow-x-auto">
                    <table class="min-w-[950px] w-full table-fixed">
                        <colgroup>
                            <col class="w-[12%]">
                            <col class="w-[17%]">
                            <col class="w-[13%]">
                            <col class="w-[40%]">
                            <col class="w-[12%]">
                            <col class="w-[7%]">
                        </colgroup>
                        <thead class="bg-slate-50 text-left text-[13px] font-semibold text-slate-900">
                            <tr>
                                <th scope="col" class="px-4 py-4">When</th>
                                <th scope="col" class="px-3 py-4">Who</th>
                                <th scope="col" class="px-3 py-4">Action</th>
                                <th scope="col" class="px-3 py-4">Record</th>
                                <th scope="col" class="px-3 py-4">Summary</th>
                                <th scope="col" class="px-3 py-4 text-center">Detail</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 text-[13px]">
                            @forelse ($auditLogs as $log)
                                @php
                                    $badgeClasses = match ($log->action) {
                                        'created' => 'border-teal-200 bg-teal-50 text-teal-700',
                                        'updated' => 'border-blue-200 bg-blue-50 text-blue-700',
                                        'deleted', 'login_failed' => 'border-rose-200 bg-rose-50 text-rose-600',
                                        'published' => 'border-emerald-200 bg-emerald-50 text-emerald-700',
                                        'archived', 'closed' => 'border-amber-200 bg-amber-50 text-amber-700',
                                        'restored' => 'border-sky-200 bg-sky-50 text-sky-600',
                                        default => 'border-slate-200 bg-slate-50 text-slate-600',
                                    };

                                    $dotClasses = match ($log->action) {
                                        'created' => 'bg-teal-500',
                                        'updated' => 'bg-blue-500',
                                        'deleted', 'login_failed' => 'bg-rose-500',
                                        'published' => 'bg-emerald-500',
                                        'archived', 'closed' => 'bg-amber-500',
                                        'restored' => 'bg-sky-500',
                                        default => 'bg-slate-400',
                                    };

                                    $auditable = $log->auditable;
                                    $typeLabel = $log->auditable_type
                                        ? \Illuminate\Support\Str::headline(class_basename($log->auditable_type))
                                        : null;

                                    $recordLabel = $auditable?->title
                                        ?? $auditable?->name
                                        ?? $auditable?->file_name
                                        ?? $auditable?->key
                                        ?? ($log->old_values['title'] ?? $log->old_values['name'] ?? $log->old_values['file_name'] ?? $log->old_values['key'] ?? null);

                                    $recordUrl = match (true) {
                                        $auditable && $log->auditable_type === \App\Models\Article::class => route('articles.edit', $auditable),
                                        $auditable && $log->auditable_type === \App\Models\Career::class => route('careers.edit', $auditable),
                                        default => null,
                                    };

                                    $changedCount = count($log->new_values ?? []);
                                    $summary = match ($log->action) {
                                        'created' => 'New record',
                                        'updated' => $changedCount . ' field' . ($changedCount === 1 ? '' : 's') . ' changed',
                                        'deleted' => 'Record removed',
                                        'login' => 'Signed in',
                                        'login_failed' => 'Attempted: ' . ($log->new_values['email'] ?? 'unknown'),
                                        default => ucfirst(str_replace('_', ' ', $log->action)),
                                    };

                                    $hasDetail = ! empty($log->old_values) || (! empty($log->new_values) && $log->action !== 'login_failed');
                                    $detailId = "audit-detail-{$log->id}";
                                @endphp

                                <tr
                                    class="transition hover:bg-slate-50/80 {{ $hasDetail ? 'cursor-pointer' : '' }}"
                                    @if ($hasDetail) data-audit-toggle="{{ $detailId }}" @endif
                                >
                                    <td class="px-4 py-4 text-slate-500">
                                        <span class="block font-medium text-slate-700">{{ $log->created_at->format('H:i') }}</span>
                                        <span class="block text-xs text-slate-400">
                                            {{ $log->created_at->isToday() ? 'today' : ($log->created_at->isYesterday() ? 'yesterday' : $log->created_at->format('d M Y')) }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-4">
                                        @if ($log->user)
                                            <span class="flex items-center gap-2">
                                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-slate-800 text-[10px] font-bold text-white">
                                                    {{ \Illuminate\Support\Str::of($log->user->name)->trim()->explode(' ')->take(2)->map(fn ($part) => \Illuminate\Support\Str::substr($part, 0, 1))->implode('') }}
                                                </span>
                                                <span class="truncate font-medium text-slate-700">{{ $log->user->name }}</span>
                                            </span>
                                        @else
                                            <span class="flex items-center gap-2">
                                                <span class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full border border-dashed border-slate-300 bg-slate-50 text-[10px] font-bold text-slate-400">?</span>
                                                <span class="truncate italic text-slate-400">Unknown</span>
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-4">
                                        <span class="inline-flex w-max items-center gap-1 rounded-lg border px-2 py-1 text-[11px] font-semibold {{ $badgeClasses }}">
                                            <span class="h-2 w-2 rounded-full {{ $dotClasses }}"></span>
                                            {{ ucfirst(str_replace('_', ' ', $log->action)) }}
                                        </span>
                                    </td>

                                    <td class="px-3 py-4">
                                        @if ($typeLabel)
                                            <span class="mr-1.5 rounded bg-blue-50 px-1.5 py-0.5 text-[10px] font-bold uppercase tracking-wide text-blue-600">{{ $typeLabel }}</span>

                                            @if ($recordUrl)
                                                <a href="{{ $recordUrl }}" class="font-medium text-slate-800 hover:text-blue-600 hover:underline">{{ $recordLabel ?? '—' }}</a>
                                            @elseif ($log->action === 'deleted')
                                                <span class="font-medium text-slate-500 line-through">{{ $recordLabel ?? '—' }}</span>
                                            @else
                                                <span class="font-medium text-slate-800">{{ $recordLabel ?? '—' }}</span>
                                            @endif
                                        @else
                                            <span class="text-slate-400">—</span>
                                        @endif
                                    </td>

                                    <td class="px-3 py-4 text-slate-500">
                                        <span class="block truncate" title="{{ $summary }}">{{ $summary }}</span>
                                    </td>

                                    <td class="px-3 py-4 text-center">
                                        @if ($hasDetail)
                                            <svg id="chevron-{{ $detailId }}" xmlns="http://www.w3.org/2000/svg" class="mx-auto size-4 text-slate-400 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" d="m19 9-7 7-7-7" />
                                            </svg>
                                        @else
                                            <span class="text-slate-300">—</span>
                                        @endif
                                    </td>
                                </tr>

                                @if ($hasDetail)
                                    <tr id="{{ $detailId }}" class="hidden">
                                        <td colspan="6" class="bg-slate-50/70 px-6 pb-5 pt-1">
                                            <div class="grid gap-4 {{ ! empty($log->old_values) && ! empty($log->new_values) ? 'md:grid-cols-2' : '' }}">
                                                @if (! empty($log->old_values))
                                                    <div class="rounded-xl border border-rose-200 bg-white p-4">
                                                        <p class="mb-3 text-[11px] font-bold uppercase tracking-wide text-rose-400">
                                                            {{ $log->action === 'deleted' ? 'Record at deletion' : 'Before' }}
                                                        </p>
                                                        <div class="space-y-2 text-xs">
                                                            @foreach ($log->old_values as $field => $value)
                                                                @continue(in_array($field, ['id', 'created_by', 'updated_by'], true))

                                                                @php
                                                                    $displayValue = $flattenAuditValue($value);
                                                                @endphp
                                                                <div class="flex gap-2">
                                                                    <span class="w-32 shrink-0 font-mono text-slate-400">{{ $field }}</span>
                                                                    <div class="min-w-0">
                                                                        <span class="break-words rounded bg-rose-50 px-1.5 font-mono text-rose-600 {{ $log->action !== 'deleted' ? 'line-through' : '' }}">
                                                                            {{ \Illuminate\Support\Str::limit($displayValue, 240) ?: '—' }}
                                                                        </span>

                                                                        @if (\Illuminate\Support\Str::length($displayValue) > 240)
                                                                            <button
                                                                                type="button"
                                                                                class="mt-1.5 flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-1 text-[11px] font-semibold text-slate-600 shadow-sm transition hover:border-blue-500 hover:text-blue-600"
                                                                                data-diff-field="{{ $field }}"
                                                                                data-diff-old="{{ $flattenAuditValue($log->old_values[$field] ?? '') }}"
                                                                                data-diff-new="{{ $flattenAuditValue($log->new_values[$field] ?? '') }}"
                                                                                data-diff-context="{{ trim(($typeLabel ? $typeLabel . ' · ' : '') . ($recordLabel ?? '—')) }} · {{ $log->user?->name ?? 'Unknown' }} · {{ $log->created_at->format('d M Y, H:i') }}"
                                                                            >
                                                                                View full change
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                        @if ($log->action === 'deleted' && in_array($log->auditable_type, \App\Services\Audit\AuditLogService::RESTORABLE_TYPES, true))
                                                            <form method="POST" action="{{ route('audit.restore', $log) }}" class="mt-3">
                                                                @csrf
                                                                <button
                                                                    type="submit"
                                                                    class="inline-flex items-center gap-1 rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700 shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-300 hover:bg-emerald-100"
                                                                >
                                                                    Restore this record
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                @endif

                                                @if (! empty($log->new_values))
                                                    <div class="rounded-xl border border-emerald-200 bg-white p-4">
                                                        <p class="mb-3 text-[11px] font-bold uppercase tracking-wide text-emerald-500">
                                                            {{ $log->action === 'created' ? 'Initial values' : 'After' }}
                                                        </p>
                                                        <div class="space-y-2 text-xs">
                                                            @foreach ($log->new_values as $field => $value)
                                                            @continue(in_array($field, ['id', 'created_by', 'updated_by'], true))

                                                            @php
                                                                $displayValue = $flattenAuditValue($value);
                                                            @endphp
                                                            <div class="flex gap-2">
                                                                <span class="w-32 shrink-0 font-mono text-slate-400">{{ $field }}</span>
                                                                <div class="min-w-0">
                                                                    <span class="break-words rounded bg-emerald-50 px-1.5 font-mono text-emerald-700">
                                                                        {{ \Illuminate\Support\Str::limit($displayValue, 240) ?: '—' }}
                                                                    </span>

                                                                    @if (\Illuminate\Support\Str::length($displayValue) > 240)
                                                                        <button
                                                                            type="button"
                                                                            class="mt-1.5 flex items-center gap-1 rounded-lg border border-slate-200 bg-white px-2 py-1 text-[11px] font-semibold text-slate-600 shadow-sm transition hover:border-blue-500 hover:text-blue-600"
                                                                            data-diff-field="{{ $field }}"
                                                                            data-diff-old="{{ $flattenAuditValue($log->old_values[$field] ?? '') }}"
                                                                            data-diff-new="{{ $flattenAuditValue($log->new_values[$field] ?? '') }}"
                                                                            data-diff-context="{{ trim(($typeLabel ? $typeLabel . ' · ' : '') . ($recordLabel ?? '—')) }} · {{ $log->user?->name ?? 'Unknown' }} · {{ $log->created_at->format('d M Y, H:i') }}"
                                                                        >
                                                                            View full change
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                            <p class="mt-3 text-xs text-slate-400">
                                                IP {{ $log->ip_address ?? '—' }}{{ $log->user_agent ? ' · ' . \Illuminate\Support\Str::limit($log->user_agent, 90) : '' }}
                                            </p>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-xs text-slate-500">
                                        No audit events found.
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
                    <span class="mx-1 font-medium">{{ $auditLogs->firstItem() ?? 0 }}</span>
                    to
                    <span class="mx-1 font-medium">{{ $auditLogs->lastItem() ?? 0 }}</span>
                    of
                    <span class="mx-1 font-medium">{{ $auditLogs->total() }}</span>
                    events
                </div>

                @if ($auditLogs->hasPages())
                    <nav aria-label="Pagination" class="flex w-max items-center overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm divide-x divide-slate-200">
                        @if ($auditLogs->onFirstPage())
                            <span class="flex h-11 w-11 shrink-0 items-center justify-center text-slate-300">
                                ‹
                            </span>
                        @else
                            <a href="{{ $auditLogs->previousPageUrl() }}" class="flex h-11 w-11 shrink-0 items-center justify-center hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                ‹
                            </a>
                        @endif

                        @foreach (range(1, $auditLogs->lastPage()) as $page)
                            @if ($page >= max(1, $auditLogs->currentPage() - 2) && $page <= min($auditLogs->lastPage(), $auditLogs->currentPage() + 2))
                                @if ($page === $auditLogs->currentPage())
                                    <span class="flex h-11 w-11 shrink-0 items-center justify-center bg-blue-600 text-sm font-semibold text-white">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $auditLogs->url($page) }}" class="flex h-11 w-11 shrink-0 items-center justify-center text-sm font-semibold text-slate-900 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endif
                        @endforeach

                        @if ($auditLogs->hasMorePages())
                            <a href="{{ $auditLogs->nextPageUrl() }}" class="flex h-11 w-11 shrink-0 items-center justify-center hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
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

    <x-filter-panel
        panel-id="auditFilterPanel"
        open-button-id="openAuditFilterPanel"
        :action="route('audit.index')"
        :search="$search"
        :fields="$filterFields"
    />

    <div id="auditDiffModal" class="pointer-events-none fixed inset-0 z-[1000] flex items-center justify-center p-4" aria-hidden="true">
        <div id="auditDiffBackdrop" class="absolute inset-0 bg-[rgba(15,23,42,0.4)] opacity-0 transition-opacity duration-200"></div>

        <div
            id="auditDiffPanel"
            role="dialog"
            aria-modal="true"
            aria-labelledby="auditDiffTitle"
            class="relative z-10 w-full max-w-2xl scale-95 rounded-2xl border border-slate-200 bg-white opacity-0 shadow-2xl transition-all duration-200"
        >
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <div>
                    <h2 id="auditDiffTitle" class="text-base font-bold text-slate-900">
                        Full change — <span id="auditDiffField" class="font-mono text-sm font-semibold text-blue-600"></span>
                    </h2>
                    <p id="auditDiffContext" class="text-xs text-slate-500"></p>
                </div>

                <button
                    type="button"
                    class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                    aria-label="Close"
                    data-diff-close
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="max-h-[60vh] overflow-y-auto px-6 py-5">
                <div class="mb-3 flex items-center gap-4 text-[11px] font-semibold">
                    <span class="flex items-center gap-1.5 text-rose-600"><span class="inline-block h-3 w-6 rounded bg-rose-100"></span> removed</span>
                    <span class="flex items-center gap-1.5 text-emerald-700"><span class="inline-block h-3 w-6 rounded bg-emerald-100"></span> added</span>
                </div>

                <div id="auditDiffOutput" class="rounded-xl border border-slate-200 bg-slate-50/50 p-4 text-sm leading-7 text-slate-700"></div>
            </div>

            <div class="flex justify-end border-t border-slate-200 px-6 py-4">
                <button
                    type="button"
                    class="rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50"
                    data-diff-close
                >
                    Close
                </button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('[data-audit-toggle]').forEach((row) => {
            row.addEventListener('click', (event) => {
                if (event.target.closest('a')) {
                    return;
                }

                const panel = document.getElementById(row.dataset.auditToggle);
                const chevron = document.getElementById('chevron-' + row.dataset.auditToggle);

                panel?.classList.toggle('hidden');
                chevron?.classList.toggle('rotate-180');
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

        const diffModal = document.getElementById('auditDiffModal');
        const diffBackdrop = document.getElementById('auditDiffBackdrop');
        const diffPanel = document.getElementById('auditDiffPanel');

        const wordDiff = (oldStr, newStr) => {
            const a = oldStr.split(/\s+/).filter(Boolean);
            const b = newStr.split(/\s+/).filter(Boolean);
            const lcs = Array.from({ length: a.length + 1 }, () => new Array(b.length + 1).fill(0));

            for (let i = a.length - 1; i >= 0; i--) {
                for (let j = b.length - 1; j >= 0; j--) {
                    lcs[i][j] = a[i] === b[j] ? lcs[i + 1][j + 1] + 1 : Math.max(lcs[i + 1][j], lcs[i][j + 1]);
                }
            }

            const parts = [];
            let i = 0;
            let j = 0;

            while (i < a.length && j < b.length) {
                if (a[i] === b[j]) {
                    parts.push({ type: 'same', word: a[i] });
                    i++;
                    j++;
                } else if (lcs[i + 1][j] >= lcs[i][j + 1]) {
                    parts.push({ type: 'del', word: a[i] });
                    i++;
                } else {
                    parts.push({ type: 'ins', word: b[j] });
                    j++;
                }
            }

            while (i < a.length) parts.push({ type: 'del', word: a[i++] });
            while (j < b.length) parts.push({ type: 'ins', word: b[j++] });

            return parts;
        };

        const renderDiff = (parts) => parts.map(({ type, word }) => {
            const safe = word.replace(/&/g, '&amp;').replace(/</g, '&lt;');

            if (type === 'del') {
                return `<del class="rounded bg-rose-100 px-0.5 text-rose-700">${safe}</del>`;
            }

            if (type === 'ins') {
                return `<ins class="rounded bg-emerald-100 px-0.5 font-semibold text-emerald-700 no-underline">${safe}</ins>`;
            }

            return safe;
        }).join(' ');

        const openDiffModal = (button) => {
            document.getElementById('auditDiffField').textContent = button.dataset.diffField;
            document.getElementById('auditDiffContext').textContent = button.dataset.diffContext;
            document.getElementById('auditDiffOutput').innerHTML = renderDiff(
                wordDiff(button.dataset.diffOld || '', button.dataset.diffNew || '')
            );

            diffModal.classList.remove('pointer-events-none');
            diffModal.setAttribute('aria-hidden', 'false');

            requestAnimationFrame(() => {
                diffBackdrop.classList.remove('opacity-0');
                diffPanel.classList.remove('opacity-0', 'scale-95');
            });
        };

        const closeDiffModal = () => {
            diffBackdrop.classList.add('opacity-0');
            diffPanel.classList.add('opacity-0', 'scale-95');
            diffModal.setAttribute('aria-hidden', 'true');

            setTimeout(() => diffModal.classList.add('pointer-events-none'), 200);
        };

        document.querySelectorAll('[data-diff-field]').forEach((button) => {
            button.addEventListener('click', () => openDiffModal(button));
        });

        document.querySelectorAll('[data-diff-close]').forEach((button) => {
            button.addEventListener('click', closeDiffModal);
        });

        diffBackdrop.addEventListener('click', closeDiffModal);

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                closeDiffModal();
            }
        });
    </script>
@endpush
