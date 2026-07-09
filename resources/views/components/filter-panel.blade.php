@props([
    'panelId',
    'openButtonId',
    'action',
    'search' => '',
    'fields' => [],
])

<div
    id="{{ $panelId }}"
    class="pointer-events-none fixed inset-0 z-[1000]"
    aria-hidden="true"
    data-filter-panel
    data-open-trigger="{{ $openButtonId }}"
>
    <div class="absolute inset-0 bg-[rgba(15,23,42,0.28)] opacity-0 transition-opacity duration-300" data-filter-backdrop></div>

    <aside
        role="dialog"
        aria-modal="true"
        aria-labelledby="{{ $panelId }}-title"
        class="absolute right-0 top-0 flex h-full w-full max-w-md translate-x-full flex-col border-l border-slate-200 bg-white shadow-[0_32px_80px_-32px_rgba(15,23,42,0.45)] transition-transform duration-300"
        data-filter-dialog
    >
        <form method="GET" action="{{ $action }}" class="flex min-h-0 flex-1 flex-col">
            <input type="hidden" name="search" value="{{ $search }}">

            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <div>
                    <h2 id="{{ $panelId }}-title" class="text-base font-bold text-slate-900">Filters</h2>
                    <p class="text-xs text-slate-500"><span data-filter-active-count>0</span> active</p>
                </div>

                <div class="flex items-center gap-3">
                    <button
                        type="button"
                        class="text-xs font-semibold text-red-500 transition hover:text-red-600"
                        data-filter-clear
                    >
                        Clear all
                    </button>

                    <button
                        type="button"
                        class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700"
                        aria-label="Close filters"
                        data-filter-close
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto">
                @foreach ($fields as $field)
                    @php
                        $fieldType = $field['type'] ?? 'options';
                        $sectionId = "{$panelId}-section-{$field['key']}";
                        $searchable = $fieldType === 'options' && count($field['options']) > 8;
                    @endphp

                    <fieldset class="border-b border-slate-100 px-6 py-5" data-filter-section>
                        <legend class="sr-only">{{ $field['label'] }}</legend>

                        <button
                            type="button"
                            class="flex w-full items-center justify-between"
                            aria-expanded="true"
                            aria-controls="{{ $sectionId }}"
                            data-section-toggle
                        >
                            <span class="text-xs font-bold uppercase tracking-wide text-slate-400">{{ $field['label'] }}</span>

                            <span class="flex items-center gap-2">
                                <span class="hidden rounded-full bg-blue-600 px-2 py-0.5 text-[10px] font-bold text-white" data-section-count></span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 text-slate-400 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true" data-section-chevron>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                                </svg>
                            </span>
                        </button>

                        <div id="{{ $sectionId }}" class="mt-3" data-section-body>
                            @if ($fieldType === 'date_range')
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label for="{{ $sectionId }}-from" class="text-[11px] font-medium text-slate-400">From</label>
                                        <input
                                            type="date"
                                            id="{{ $sectionId }}-from"
                                            name="{{ $field['key'] }}_from"
                                            value="{{ $field['from'] ?? '' }}"
                                            class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-xs text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                            data-filter-date
                                        >
                                    </div>

                                    <div>
                                        <label for="{{ $sectionId }}-to" class="text-[11px] font-medium text-slate-400">To</label>
                                        <input
                                            type="date"
                                            id="{{ $sectionId }}-to"
                                            name="{{ $field['key'] }}_to"
                                            value="{{ $field['to'] ?? '' }}"
                                            class="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2 text-xs text-slate-700 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                            data-filter-date
                                        >
                                    </div>
                                </div>
                            @else
                                @if ($searchable)
                                    <div class="mb-3 flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 transition focus-within:border-blue-500 focus-within:bg-white focus-within:ring-4 focus-within:ring-blue-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5 shrink-0 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                            <path stroke-linecap="round" d="m21 21-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" />
                                        </svg>

                                        <label for="{{ $sectionId }}-search" class="sr-only">Search {{ $field['label'] }}</label>
                                        <input
                                            type="text"
                                            id="{{ $sectionId }}-search"
                                            placeholder="Search {{ strtolower($field['label']) }}…"
                                            autocomplete="off"
                                            class="w-full bg-transparent text-xs text-slate-900 outline-none placeholder:text-slate-400"
                                            data-option-search
                                        >
                                    </div>
                                @endif

                                <div class="flex flex-wrap gap-2" data-option-list>
                                    @foreach ($field['options'] as $option)
                                        <label class="group cursor-pointer" data-option-label="{{ strtolower($option['label']) }}">
                                            <input
                                                type="checkbox"
                                                name="{{ $field['key'] }}[]"
                                                value="{{ $option['value'] }}"
                                                @checked(in_array($option['value'], $field['selected'] ?? []))
                                                class="peer sr-only"
                                                data-filter-option
                                            >
                                            <span class="inline-flex items-center rounded-full border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 transition group-hover:border-slate-300 peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:font-semibold peer-checked:text-blue-700 peer-focus-visible:ring-2 peer-focus-visible:ring-blue-500">
                                                {{ $option['label'] }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </fieldset>
                @endforeach
            </div>

            <div class="flex shrink-0 gap-3 border-t border-slate-200 bg-white px-6 py-4">
                <button
                    type="submit"
                    class="flex-1 rounded-xl border border-blue-600 bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                >
                    Apply filters
                </button>

                <button
                    type="button"
                    class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    data-filter-close
                >
                    Cancel
                </button>
            </div>
        </form>
    </aside>
</div>

@once
    @push('scripts')
        <script>
            document.querySelectorAll('[data-filter-panel]').forEach((panel) => {
                const backdrop = panel.querySelector('[data-filter-backdrop]');
                const dialog = panel.querySelector('[data-filter-dialog]');
                const closeButtons = panel.querySelectorAll('[data-filter-close]');
                const clearButton = panel.querySelector('[data-filter-clear]');
                const openButton = document.getElementById(panel.dataset.openTrigger);
                const activeCount = panel.querySelector('[data-filter-active-count]');

                const refreshCounts = () => {
                    let total = 0;

                    panel.querySelectorAll('[data-filter-section]').forEach((section) => {
                        const badge = section.querySelector('[data-section-count]');
                        const checked = section.querySelectorAll('[data-filter-option]:checked').length;
                        const dates = Array.from(section.querySelectorAll('[data-filter-date]'))
                            .filter((input) => input.value !== '').length;
                        const count = checked + dates;

                        total += count;

                        if (badge) {
                            badge.textContent = count;
                            badge.classList.toggle('hidden', count === 0);
                        }
                    });

                    if (activeCount) {
                        activeCount.textContent = total;
                    }
                };

                const openPanel = () => {
                    panel.classList.remove('pointer-events-none');
                    panel.setAttribute('aria-hidden', 'false');

                    requestAnimationFrame(() => {
                        backdrop?.classList.remove('opacity-0');
                        dialog?.classList.remove('translate-x-full');
                    });
                };

                const closePanel = () => {
                    backdrop?.classList.add('opacity-0');
                    dialog?.classList.add('translate-x-full');
                    panel.setAttribute('aria-hidden', 'true');

                    setTimeout(() => panel.classList.add('pointer-events-none'), 300);
                };

                openButton?.addEventListener('click', openPanel);
                closeButtons.forEach((button) => button.addEventListener('click', closePanel));
                backdrop?.addEventListener('click', closePanel);

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        closePanel();
                    }
                });

                const form = panel.querySelector('form');

                form?.addEventListener('keydown', (event) => {
                    if (event.key !== 'Enter' || event.target.tagName === 'BUTTON') {
                        return;
                    }

                    event.preventDefault();
                    form.requestSubmit();
                });

                form?.addEventListener('change', refreshCounts);

                panel.querySelectorAll('[data-section-toggle]').forEach((toggle) => {
                    toggle.addEventListener('click', () => {
                        const body = toggle.parentElement.querySelector('[data-section-body]');
                        const chevron = toggle.querySelector('[data-section-chevron]');
                        const expanded = toggle.getAttribute('aria-expanded') === 'true';

                        body?.classList.toggle('hidden', expanded);
                        chevron?.classList.toggle('-rotate-90', expanded);
                        toggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
                    });
                });

                panel.querySelectorAll('[data-option-search]').forEach((input) => {
                    input.addEventListener('input', (event) => {
                        const query = event.target.value.toLowerCase();
                        const list = event.target.closest('[data-section-body]').querySelector('[data-option-list]');

                        list?.querySelectorAll('[data-option-label]').forEach((label) => {
                            label.classList.toggle('hidden', ! label.dataset.optionLabel.includes(query));
                        });
                    });
                });

                clearButton?.addEventListener('click', () => {
                    panel.querySelectorAll('[data-filter-option]:checked').forEach((option) => {
                        option.checked = false;
                    });

                    panel.querySelectorAll('[data-filter-date]').forEach((input) => {
                        input.value = '';
                    });

                    refreshCounts();
                });

                refreshCounts();
            });
        </script>
    @endpush
@endonce
