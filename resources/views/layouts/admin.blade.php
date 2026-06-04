<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'CMS' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @media (min-width: 1024px) {
            .app-sidebar {
                width: 17.5rem;
            }

            .app-main {
                padding-left: 17.5rem;
            }
        }
    </style>
</head>
<body class="bg-[#eef2f7] text-slate-900 antialiased">
    @php
        $user = auth()->user();
        $userName = $user?->name ?? 'CMS User';
        $userInitials = collect(explode(' ', trim($userName)))
            ->filter()
            ->take(2)
            ->map(fn ($segment) => strtoupper(mb_substr($segment, 0, 1)))
            ->implode('');
    @endphp

    <div class="min-h-screen bg-white">
        <x-sidebar />

        <div class="app-main min-h-screen bg-[#f8fafc]">
            <x-session-toast />

            <header class="relative z-30 overflow-visible border-b border-slate-200 bg-white/95 px-5 py-4 backdrop-blur md:px-8 md:py-5">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:gap-6">
                    <div class="relative w-full min-w-0 flex-1 lg:max-w-none">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.85-5.15a7 7 0 1 1-14 0a7 7 0 0 1 14 0Z" />
                            </svg>
                        </span>

                        <input
                            type="search"
                            placeholder="Search"
                            class="w-full rounded-2xl border border-slate-200 bg-slate-50 py-3.5 pl-12 pr-4 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100"
                        >
                    </div>

                    <div class="flex shrink-0 items-center justify-end gap-3 md:gap-5">
                        <button
                            type="button"
                            class="inline-flex size-11 items-center justify-center rounded-2xl border border-slate-200 bg-white text-slate-500 transition hover:-translate-y-0.5 hover:text-slate-800"
                            aria-label="Notifications"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75v-.7V9a6 6 0 1 0-12 0v.05c0 .236 0 .474-.003.712A8.967 8.967 0 0 1 3.69 15.77a23.847 23.847 0 0 0 5.454 1.31m5.713 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                            </svg>
                        </button>

                        <div class="hidden h-10 w-px bg-slate-200 md:block"></div>

                        <div id="admin-profile-menu" class="relative">
                            <button
                                type="button"
                                id="admin-profile-trigger"
                                class="inline-flex items-center gap-3 rounded-2xl border border-transparent bg-white px-2 py-1.5 text-left transition hover:bg-slate-100"
                                aria-haspopup="menu"
                                aria-expanded="false"
                            >
                                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-slate-900 via-slate-700 to-slate-500 text-sm font-semibold text-white shadow-sm">
                                    {{ $userInitials ?: 'CU' }}
                                </span>

                                <span class="hidden min-w-0 sm:block">
                                    <span class="block truncate text-base font-semibold text-slate-900">{{ $userName }}</span>
                                    <span class="block truncate text-sm text-slate-500">{{ ucfirst($user?->role ?? 'member') }}</span>
                                </span>

                                <svg xmlns="http://www.w3.org/2000/svg" class="hidden size-5 text-slate-400 sm:block" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>

                            <div
                                id="admin-profile-dropdown"
                                class="absolute right-0 top-[calc(100%+0.75rem)] z-[70] hidden min-w-56 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_24px_60px_-24px_rgba(15,23,42,0.35)]"
                                role="menu"
                                aria-labelledby="admin-profile-trigger"
                            >
                                <div class="border-b border-slate-100 px-4 py-3">
                                    <p class="truncate text-sm font-semibold text-slate-900">{{ $userName }}</p>
                                </div>

                                <button
                                    type="button"
                                    class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm font-medium text-slate-400"
                                    role="menuitem"
                                    disabled
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9m-9 6h9m-9 6h9M4.5 6h.008v.008H4.5V6Zm0 6h.008v.008H4.5V12Zm0 6h.008v.008H4.5V18Z" />
                                    </svg>
                                    <span class="flex-1">Settings</span>
                                    <span class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-300">Soon</span>
                                </button>

                                <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="flex w-full items-center gap-3 px-4 py-3 text-left text-sm font-medium text-slate-700 transition hover:bg-slate-50 hover:text-slate-900"
                                        role="menuitem"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-7.5a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 6 21h7.5a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                                        </svg>
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <main class="min-w-0 overflow-x-hidden px-3 py-4 md:px-5 md:py-5 lg:px-6 lg:py-6">
                @yield('content')
            </main>
        </div>
    </div>

    @stack('scripts')
    <script>
        (() => {
            const menu = document.getElementById('admin-profile-menu');
            const trigger = document.getElementById('admin-profile-trigger');
            const dropdown = document.getElementById('admin-profile-dropdown');

            if (!menu || !trigger || !dropdown) {
                return;
            }

            const setOpen = (isOpen) => {
                dropdown.classList.toggle('hidden', !isOpen);
                trigger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
            };

            trigger.addEventListener('click', () => {
                setOpen(dropdown.classList.contains('hidden'));
            });

            document.addEventListener('click', (event) => {
                if (!menu.contains(event.target)) {
                    setOpen(false);
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    setOpen(false);
                }
            });
        })();
    </script>
</body>
</html>
