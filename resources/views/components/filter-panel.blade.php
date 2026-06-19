@props([
    'panelId',
    'openButtonId',
    'action',
    'search' => '',
    'fields' => [],
])

<div
    id="{{ $panelId }}"
    class="fixed inset-0 z-[1000] hidden items-center justify-end p-4"
    aria-hidden="true"
    data-filter-panel
    data-open-trigger="{{ $openButtonId }}"
>
    <div class="absolute inset-0 bg-[rgba(15,23,42,0.28)]" data-filter-backdrop></div>

    <div
        role="dialog"
        aria-modal="true"
        aria-labelledby="{{ $panelId }}-title"
        class="relative z-10 w-full max-w-md overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_32px_80px_-32px_rgba(15,23,42,0.45)]"
    >
        <form method="GET" action="{{ $action }}" class="flex max-h-[min(34rem,calc(100vh-2rem))] flex-col">
            <input type="hidden" name="search" value="{{ $search }}">

            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-5">
                <h2 id="{{ $panelId }}-title" class="text-xs font-semibold text-slate-900">Filter</h2>

                <button
                    type="button"
                    class="text-xs font-semibold text-red-500 transition hover:text-red-600"
                    data-filter-clear
                >
                    Clear all
                </button>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto px-4 py-4">
                <div class="space-y-4">
                    @foreach ($fields as $field)
                        @php
                            $chipsId = "{$panelId}-chips-{$field['key']}";
                            $selectId = "{$panelId}-select-{$field['key']}";
                            $placeholder = $field['placeholder'] ?? "Select {$field['label']}";
                        @endphp

                        <fieldset>
                            <legend class="text-xs font-semibold text-slate-900">{{ $field['label'] }}</legend>

                            <div class="mt-3">
                                <label for="{{ $selectId }}" class="sr-only">{{ $placeholder }}</label>
                                <select
                                    id="{{ $selectId }}"
                                    class="w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                                    data-filter-key="{{ $field['key'] }}"
                                    data-chip-target="{{ $chipsId }}"
                                >
                                    <option value="">{{ $placeholder }}</option>
                                    @foreach ($field['options'] as $option)
                                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div id="{{ $chipsId }}" class="mt-3 flex flex-wrap gap-2" data-filter-key="{{ $field['key'] }}">
                                @foreach (($field['selected'] ?? []) as $value)
                                    @php
                                        $optionLabel = collect($field['options'])->firstWhere('value', $value)['label'] ?? $value;
                                    @endphp
                                    <button
                                        type="button"
                                        class="filter-chip inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700"
                                        data-value="{{ $value }}"
                                    >
                                        {{ $optionLabel }}
                                        <span aria-hidden="true">×</span>
                                    </button>
                                    <input type="hidden" name="{{ $field['key'] }}[]" value="{{ $value }}">
                                @endforeach
                            </div>
                        </fieldset>
                    @endforeach
                </div>
            </div>

            <div class="flex shrink-0 gap-3 border-t border-slate-200 bg-white px-4 py-4">
                <button
                    type="submit"
                    class="w-full rounded-xl border border-blue-600 bg-blue-600 px-3 py-2 text-xs font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                >
                    Apply
                </button>

                <button
                    type="button"
                    class="w-full rounded-xl border border-slate-300 bg-white px-3 py-2 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    data-filter-close
                >
                    Close
                </button>
            </div>
        </form>
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.querySelectorAll('[data-filter-panel]').forEach((panel) => {
                const backdrop = panel.querySelector('[data-filter-backdrop]');
                const closeButton = panel.querySelector('[data-filter-close]');
                const clearButton = panel.querySelector('[data-filter-clear]');
                const openButton = document.getElementById(panel.dataset.openTrigger);

                const openPanel = () => {
                    panel.classList.remove('hidden');
                    panel.classList.add('flex');
                };

                const closePanel = () => {
                    panel.classList.add('hidden');
                    panel.classList.remove('flex');
                };

                openButton?.addEventListener('click', openPanel);
                closeButton?.addEventListener('click', closePanel);
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

                const addFilterValue = (key, value, label, container) => {
                    if (!value || !container) {
                        return;
                    }

                    const existingValues = Array.from(
                        container.querySelectorAll(`input[name="${key}[]"]`)
                    ).map((input) => input.value);

                    if (existingValues.includes(value)) {
                        return;
                    }

                    const chip = document.createElement('button');
                    chip.type = 'button';
                    chip.className = 'filter-chip inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700';
                    chip.dataset.value = value;
                    chip.innerHTML = `${label} <span aria-hidden="true">×</span>`;

                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = `${key}[]`;
                    hiddenInput.value = value;

                    container.appendChild(chip);
                    container.appendChild(hiddenInput);
                };

                const removeFilterValue = (key, value, container) => {
                    const chip = container.querySelector(`.filter-chip[data-value="${value}"]`);
                    const input = container.querySelector(`input[name="${key}[]"][value="${value}"]`);

                    chip?.remove();
                    input?.remove();
                };

                panel.querySelectorAll('select[data-filter-key]').forEach((select) => {
                    select.addEventListener('change', (event) => {
                        const value = event.target.value;

                        if (!value) {
                            return;
                        }

                        const label = event.target.options[event.target.selectedIndex].text;
                        const container = document.getElementById(event.target.dataset.chipTarget);

                        addFilterValue(event.target.dataset.filterKey, value, label, container);
                        event.target.value = '';
                    });
                });

                panel.querySelectorAll('[data-filter-key]').forEach((container) => {
                    if (container.tagName === 'SELECT') {
                        return;
                    }

                    container.addEventListener('click', (event) => {
                        const chip = event.target.closest('.filter-chip');

                        if (!chip) {
                            return;
                        }

                        removeFilterValue(container.dataset.filterKey, chip.dataset.value, container);
                    });
                });

                clearButton?.addEventListener('click', () => {
                    panel.querySelectorAll('.filter-chip, input[type="hidden"][name$="[]"]').forEach((element) => element.remove());
                });
            });
        </script>
    @endpush
@endonce
