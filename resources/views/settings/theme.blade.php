@extends('layouts.admin', ['title' => 'Theme'])

@section('content')
    <div class="my-4 px-4 md:px-6">
        <div class="mx-auto max-w-7xl min-w-0">
            <form action="{{ route('theme.update') }}" method="POST" id="themeForm">
                @csrf

                <input
                    type="hidden"
                    name="theme"
                    id="selectedTheme"
                    value="{{ $activeTheme }}"
                >

                <div class="mb-8 flex items-center rounded-xl border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-blue-50/70 px-4 py-4 shadow-sm md:px-6">
                    <div class="mr-3 h-3 w-3 rounded-full bg-emerald-500"></div>

                    <p class="font-semibold leading-tight text-slate-900">
                        Active theme:
                        <span class="font-semibold text-slate-600">
                            {{ $themes[$activeTheme]['label'] }}
                        </span>
                    </p>

                    <span id="pendingSelection" class="ml-3 hidden text-sm font-medium text-amber-600"></span>
                </div>

                @php
                    $colors = [
                        'default' => ['#DC2626','#1F1238','#FFFFFF'],
                        'kwibuka' => ['#374151', '#6B21A8', '#D1D5DB'],
                        'umuganura' => ['#7D4F20', '#CF8C38', '#ECC66D'],
                        'christmas' => ['#356A35', '#8F1A12', '#F5F2EB'],
                        'new_year' => ['#a58c00', '#ffe600', '#E8DFC8'],
                    ];
                @endphp

                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($themes as $key => $theme)
                        <div
                            class="theme-card cursor-pointer overflow-hidden rounded-2xl border bg-white shadow-sm transition-all duration-200 {{ $activeTheme == $key ? 'border-blue-500 ring-2 ring-blue-500' : 'border-slate-200 hover:border-slate-300' }}"
                            data-theme="{{ $key }}"
                            data-label="{{ $theme['label'] }}"
                        >
                            <div class="grid h-24 grid-cols-3">
                                @foreach ($colors[$key] as $color)
                                    <div style="background: {{ $color }}"></div>
                                @endforeach
                            </div>

                            <div class="p-5">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-slate-900">
                                            {{ $theme['label'] }}
                                        </h3>

                                        <p class="mt-1 text-sm text-slate-500">
                                            {{ $theme['description'] }}
                                        </p>
                                    </div>

                                    @if ($activeTheme == $key)
                                        <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-600">
                                            <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                            Active
                                        </span>
                                    @endif
                                </div>

                                <button
                                    type="button"
                                    class="theme-button mt-6 w-full rounded-xl border py-2.5 text-sm font-semibold transition {{ $activeTheme == $key ? 'border-blue-500 bg-blue-50 text-blue-700' : 'border-slate-200 text-slate-700 hover:border-blue-500 hover:text-blue-600' }}"
                                >
                                    {{ $activeTheme == $key ? 'Currently Active' : 'Select Theme' }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-10 flex items-center gap-4">
                    <button
                        type="submit"
                        class="rounded-xl border border-blue-600 bg-blue-600 px-8 py-3 font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    >
                        Save Theme
                    </button>

                    <a
                        href="{{ route('theme.index') }}"
                        class="rounded-xl border border-slate-300 bg-white px-8 py-3 font-semibold text-slate-700 shadow-sm transition hover:-translate-y-0.5 hover:bg-slate-50"
                    >
                        Cancel
                    </a>

                    <span class="text-sm text-slate-500">
                        Theme changes will take effect after saving.
                    </span>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const themeCards = document.querySelectorAll('.theme-card');
        const selectedThemeInput = document.getElementById('selectedTheme');
        const pendingSelection = document.getElementById('pendingSelection');
        const activeTheme = @json($activeTheme);

        themeCards.forEach((card) => {
            card.addEventListener('click', () => {
                themeCards.forEach((otherCard) => {
                    otherCard.classList.remove('ring-2', 'ring-blue-500', 'border-blue-500');
                    otherCard.classList.add('border-slate-200');

                    const otherButton = otherCard.querySelector('.theme-button');
                    otherButton.classList.remove('border-blue-500', 'bg-blue-50', 'text-blue-700');
                    otherButton.classList.add('border-slate-200', 'text-slate-700');
                    otherButton.textContent = otherCard.dataset.theme === activeTheme ? 'Currently Active' : 'Select Theme';
                });

                card.classList.remove('border-slate-200');
                card.classList.add('ring-2', 'ring-blue-500', 'border-blue-500');

                const button = card.querySelector('.theme-button');
                button.classList.remove('border-slate-200', 'text-slate-700');
                button.classList.add('border-blue-500', 'bg-blue-50', 'text-blue-700');
                button.textContent = card.dataset.theme === activeTheme ? 'Currently Active' : 'Selected';

                selectedThemeInput.value = card.dataset.theme;

                if (card.dataset.theme === activeTheme) {
                    pendingSelection.classList.add('hidden');
                    pendingSelection.textContent = '';
                } else {
                    pendingSelection.classList.remove('hidden');
                    pendingSelection.textContent = '→ ' + card.dataset.label + ' selected — save to apply';
                }
            });
        });
    </script>
@endpush
