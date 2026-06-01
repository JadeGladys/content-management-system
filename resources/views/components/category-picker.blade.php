@props([
    'pickerId',
    'name' => 'category',
    'label' => 'Category',
    'required' => false,
    'value' => null,
    'options' => collect(),
    'placeholder' => 'Select category',
    'panelBleed' => false,
])

@php
    $optionItems = collect($options)
        ->map(function ($option) {
            if (is_string($option)) {
                return ['name' => $option];
            }

            return ['name' => $option->name];
        })
        ->filter(fn ($option) => filled($option['name'] ?? null))
        ->values()
        ->all();

    $currentValue = old($name, $value);
@endphp

<div
    id="{{ $pickerId }}"
    class="relative"
    data-options='@json($optionItems)'
    data-initial-value='@json($currentValue)'
>
    <label for="{{ $pickerId }}-trigger" class="mb-2 block text-sm font-medium text-slate-700">
        {{ $label }}
        @if ($required)
            <span class="text-rose-600">*</span>
        @endif
    </label>

    <input type="hidden" id="{{ $pickerId }}-value" name="{{ $name }}" value="{{ $currentValue }}">

    <button
        type="button"
        id="{{ $pickerId }}-trigger"
        aria-expanded="false"
        class="flex w-full items-center justify-between gap-3 rounded-2xl border border-slate-300 bg-white px-4 py-3 text-left text-sm text-slate-900 transition hover:border-slate-400 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
    >
        <span id="{{ $pickerId }}-summary" class="block truncate text-sm font-medium text-slate-900">
            {{ filled($currentValue) ? $currentValue : $placeholder }}
        </span>

        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0 text-slate-400 transition" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.5a.75.75 0 0 1-1.08 0l-4.25-4.5a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
        </svg>
    </button>

    <div
        id="{{ $pickerId }}-panel"
        class="absolute top-[calc(100%+0.75rem)] z-30 hidden overflow-hidden rounded-[1.75rem] border border-slate-200 bg-white shadow-[0_28px_55px_-24px_rgba(15,23,42,0.35)] {{ $panelBleed ? '-left-3 -right-3' : 'left-0 right-0' }}"
    >
        <div class="border-b border-slate-200 p-4">
            <div class="relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-slate-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.472 9.765l2.63 2.63a.75.75 0 1 0 1.06-1.06l-2.629-2.63A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0a4 4 0 0 1-8 0Z" clip-rule="evenodd" />
                </svg>

                <input
                    type="text"
                    id="{{ $pickerId }}-search"
                    autocomplete="off"
                    placeholder="Search categories or add one"
                    class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-11 py-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-blue-100"
                >
            </div>
        </div>

        <div id="{{ $pickerId }}-options" class="max-h-56 space-y-1 overflow-y-auto px-3 py-3"></div>
    </div>

    @error($name)
        <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
    @enderror
</div>

@push('scripts')
    <script>
        (() => {
            const root = document.getElementById(@json($pickerId));

            if (!root) {
                return;
            }

            const trigger = document.getElementById(@json("{$pickerId}-trigger"));
            const panel = document.getElementById(@json("{$pickerId}-panel"));
            const search = document.getElementById(@json("{$pickerId}-search"));
            const optionsContainer = document.getElementById(@json("{$pickerId}-options"));
            const summary = document.getElementById(@json("{$pickerId}-summary"));
            const hiddenInput = document.getElementById(@json("{$pickerId}-value"));
            const options = JSON.parse(root.dataset.options ?? '[]');
            const initialValue = JSON.parse(root.dataset.initialValue ?? 'null');
            let selectedValue = typeof initialValue === 'string' ? initialValue.trim() : '';

            const normalizeValue = (value) => value.trim();
            const slugify = (value) => normalizeValue(value)
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');

            const updateSummary = () => {
                summary.textContent = selectedValue || @json($placeholder);
                hiddenInput.value = selectedValue;
            };

            const closePanel = () => {
                panel.classList.add('hidden');
                trigger.setAttribute('aria-expanded', 'false');
            };

            const openPanel = () => {
                panel.classList.remove('hidden');
                trigger.setAttribute('aria-expanded', 'true');
                search.value = '';
                renderOptions();
                search.focus();
            };

            const renderOptions = () => {
                const query = search.value.trim().toLowerCase();
                const filteredOptions = options.filter((option) => option.name.toLowerCase().includes(query));

                optionsContainer.innerHTML = '';

                filteredOptions.forEach((option) => {
                    const isSelected = normalizeValue(option.name) === normalizeValue(selectedValue);
                    const optionButton = document.createElement('button');
                    optionButton.type = 'button';
                    optionButton.className = `flex w-full items-center rounded-2xl px-4 py-3 text-left text-sm transition ${
                        isSelected
                            ? 'bg-blue-600 text-white shadow-sm'
                            : 'bg-white text-slate-500 hover:bg-slate-100 hover:text-slate-800'
                    }`;
                    optionButton.textContent = option.name;

                    optionButton.addEventListener('click', () => {
                        selectedValue = option.name;
                        updateSummary();
                        closePanel();
                    });

                    optionsContainer.appendChild(optionButton);
                });

                const normalizedQuery = normalizeValue(search.value);
                const querySlug = slugify(normalizedQuery);
                const exactMatchExists = options.some((option) => slugify(option.name) === querySlug);

                if (normalizedQuery && querySlug && !exactMatchExists) {
                    const createButton = document.createElement('button');
                    createButton.type = 'button';
                    createButton.className = 'mt-2 flex w-full items-center rounded-2xl border border-blue-200 bg-blue-50/80 px-4 py-3 text-left text-sm font-medium text-blue-700 transition hover:border-blue-300 hover:bg-blue-100';
                    createButton.textContent = `Use "${normalizedQuery}"`;

                    createButton.addEventListener('click', () => {
                        selectedValue = normalizedQuery;
                        updateSummary();
                        closePanel();
                    });

                    optionsContainer.appendChild(createButton);
                }

                if (optionsContainer.children.length === 0) {
                    const emptyState = document.createElement('div');
                    emptyState.className = 'rounded-2xl border border-dashed border-slate-200 px-4 py-5 text-sm text-slate-500';
                    emptyState.textContent = 'No matching categories found.';
                    optionsContainer.appendChild(emptyState);
                }
            };

            trigger?.addEventListener('click', () => {
                if (panel.classList.contains('hidden')) {
                    openPanel();
                    return;
                }

                closePanel();
            });

            search?.addEventListener('input', renderOptions);

            document.addEventListener('click', (event) => {
                if (!root.contains(event.target)) {
                    closePanel();
                }
            });

            updateSummary();
        })();
    </script>
@endpush
