@php
    $user = auth()->user();
    $mainItems = collect([
        [
            'label' => 'Dashboard',
            'route' => route('dashboard'),
            'active' => request()->routeIs('dashboard'),
            'icon' => 'home',
        ],
        $user?->role === 'admin'
            ? [
                'label' => 'Team',
                'route' => route('users.index'),
                'active' => request()->routeIs('users.*'),
                'icon' => 'team',
            ]
            : null,
        [
            'label' => 'Articles',
            'route' => route('articles.index'),
            'active' => request()->routeIs('articles.*'),
            'icon' => 'document',
        ],
        [
            'label' => 'Careers',
            'route' => route('careers.index'),
            'active' => request()->routeIs('careers.*'),
            'icon' => 'briefcase',
        ],
        [
            'label' => 'Media',
            'route' => route('media.index'),
            'active' => request()->routeIs('media.*'),
            'icon' => 'photo',
        ],
    ])->filter();

    $toolItems = collect([
        ['label' => 'Audit', 'icon' => 'shield', 'route' => null],
        ['label' => 'Analytics', 'icon' => 'chart', 'route' => null],
    ]);

    $renderSidebarIcon = function ($icon) {
        return match ($icon) {
            'home' => '<path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />',
            'team' => '<path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />',
            'briefcase' => '<path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 0 0 .75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 0 0-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0 1 12 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 0 1-.673-.38m0 0A2.18 2.18 0 0 1 3 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 0 1 3.413-.387m7.5 0V5.25A2.25 2.25 0 0 0 13.5 3h-3a2.25 2.25 0 0 0-2.25 2.25v.894m7.5 0a48.667 48.667 0 0 0-7.5 0M12 12.75h.008v.008H12v-.008Z" />',
            'document' => '<path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-8.625a2.625 2.625 0 0 0-2.625-2.625H6.375A2.625 2.625 0 0 0 3.75 5.625v12.75A2.625 2.625 0 0 0 6.375 21h11.25A2.625 2.625 0 0 0 20.25 18.375V16.5m-11.25-9h6m-6 4.5h6m-6 4.5h3" />',
            'photo' => '<path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />',
            'tools' => '<path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z" />',
            'shield' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m6 2.25c0 5.186-3.786 9.318-8.25 10.274C8.286 21.318 4.5 17.186 4.5 12V6.705c0-.664.432-1.25 1.061-1.45l6.75-2.143a2.25 2.25 0 0 1 1.378 0l6.75 2.143c.629.2 1.061.786 1.061 1.45V12Z" />',
            'chart' => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 3v18h18M7.5 15.75 11.25 12l2.25 2.25L18 8.25" />',
            default => '',
        };
    };
@endphp

<aside id="admin-sidebar" class="app-sidebar w-full shrink-0 border-b border-[#243042] bg-[#111827] text-slate-200 lg:fixed lg:inset-y-0 lg:left-0 lg:z-40 lg:border-b-0 lg:border-r lg:border-[#243042]">
    <div class="flex h-full flex-col px-4 py-5 md:px-5 lg:h-screen">
        <div data-sidebar-top class="flex items-start justify-between gap-3 px-1">
            <div class="flex min-w-0 items-center gap-3">
                <div class="shrink-0">
                    <img
                        src="{{ asset('favicon.ico') }}"
                        alt="CMS favicon"
                        class="size-9 rounded-md object-contain"
                    >
                </div>

                <div data-sidebar-brand-copy class="min-w-0">
                    <p class="text-sm font-semibold tracking-[0.16em] text-slate-100">CMS</p>
                    <p class="text-xs text-slate-400">Admin workspace</p>
                </div>
            </div>
        </div>

        <nav data-sidebar-nav class="mt-8 space-y-6" aria-label="Primary sidebar navigation">
            <div data-sidebar-group>
                <p data-sidebar-section class="px-4 text-xs font-semibold uppercase tracking-[0.2em] text-slate-500">Main</p>

                <div class="mt-2.5 space-y-1.5">
                    @foreach ($mainItems as $item)
                        <a
                            href="{{ $item['route'] }}"
                            title="{{ $item['label'] }}"
                            data-sidebar-link
                            class="{{ $item['active'] ? 'bg-white/[0.07] text-white shadow-[inset_0_1px_0_rgba(255,255,255,0.04)]' : 'text-slate-400 hover:bg-white/5 hover:text-white' }} flex items-center gap-3 rounded-xl px-4 py-3 text-sm font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-[#635bff]"
                        >
                            <span data-sidebar-link-icon class="{{ $item['active'] ? 'text-[#635bff]' : 'text-slate-400' }} shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    {!! $renderSidebarIcon($item['icon']) !!}
                                </svg>
                            </span>
                            <span data-sidebar-label>{{ $item['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            <details id="sidebar-tools-details" data-sidebar-group class="group" open>
                <summary
                    title="Tools"
                    data-sidebar-link
                    class="flex cursor-pointer list-none items-center justify-between rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-300 transition hover:bg-white/5 hover:text-white"
                >
                    <span class="flex items-center">
                        <span data-tools-trigger-label class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 group-hover:text-slate-400">Tools</span>
                    </span>

                    <svg data-tools-trigger-chevron xmlns="http://www.w3.org/2000/svg" class="size-4 text-slate-500 transition group-open:rotate-180" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                    </svg>
                </summary>

                <div data-tools-panel class="mt-1.5 space-y-1.5 pl-2">
                    @foreach ($toolItems as $item)
                        <button
                            type="button"
                            title="{{ $item['label'] }}"
                            data-sidebar-link
                            class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left text-sm font-semibold text-slate-500 transition hover:bg-white/5 hover:text-slate-300"
                        >
                            <span data-sidebar-link-icon class="shrink-0 text-slate-500">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    {!! $renderSidebarIcon($item['icon']) !!}
                                </svg>
                            </span>
                            <span data-sidebar-label>{{ $item['label'] }}</span>
                        </button>
                    @endforeach
                </div>
            </details>
        </nav>

        <div class="mt-auto pt-6">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button
                    type="submit"
                    title="Logout"
                    data-sidebar-link
                    class="flex w-full items-center gap-3 rounded-xl px-4 py-3 text-left text-sm font-semibold text-slate-300 transition hover:bg-white/5 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-[#635bff]"
                >
                    <span data-sidebar-link-icon class="shrink-0 text-slate-400">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75" />
                        </svg>
                    </span>
                    <span data-sidebar-label>Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>
