<aside class="fixed top-0 left-0 flex h-screen w-full max-w-[264px] flex-col border-r border-slate-200 bg-white px-4 py-6 shadow-sm">
    <div class="flex items-center gap-3 px-2">
        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-600 text-sm font-bold text-white">
            IC
        </div>
        <div>
            <p class="text-sm font-semibold text-slate-900">CMS</p>
            <p class="text-xs text-slate-500">Admin workspace</p>
        </div>
    </div>

    <nav class="mt-8 flex-1 space-y-2" aria-label="Primary sidebar navigation">

        <nav aria-label="Primary sidebar navigation">
            <ul class="space-y-2 text-sm text-slate-800 dark:text-slate-400 font-medium">
                <li>
                    <a href="{{ url('/') }}" aria-current="page"
                    class="flex items-center gap-2.5 hover:text-slate-900 dark:hover:text-slate-50 hover:bg-slate-200 dark:hover:bg-neutral-800 rounded-md px-3 py-2 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-[18px] fill-current overflow-visible"
                        viewBox="0 0 512 512" aria-hidden="true">
                        <path
                            d="M426 495.983H86c-25.364 0-46-20.635-46-46v-242.02c0-8.836 7.163-16 16-16s16 7.164 16 16v242.02c0 7.72 6.28 14 14 14h340c7.72 0 14-6.28 14-14v-242.02c0-8.836 7.163-16 16-16s16 7.164 16 16v242.02c0 25.364-20.635 46-46 46"
                            data-original="#000000" />
                        <path
                            d="M496 263.958a15.95 15.95 0 0 1-11.313-4.687L285.698 60.284c-16.375-16.376-43.02-16.376-59.396 0L27.314 259.272c-6.248 6.249-16.379 6.249-22.627 0-6.249-6.248-6.249-16.379 0-22.627L203.675 37.656c28.852-28.852 75.799-28.852 104.65 0l198.988 198.988c6.249 6.249 6.249 16.379 0 22.627A15.94 15.94 0 0 1 496 263.958M320 495.983H192c-8.837 0-16-7.164-16-16v-142c0-27.57 22.43-50 50-50h60c27.57 0 50 22.43 50 50v142c0 8.836-7.163 16-16 16m-112-32h96v-126c0-9.925-8.075-18-18-18h-60c-9.925 0-18 8.075-18 18z"
                            data-original="#000000" />
                    </svg>
                    Dashboard
                    </a>
                </li>
            </ul>
        </nav>

        <a
            href="{{ route('users.create') }}"
            class="block rounded-xl px-4 py-3 text-sm font-medium text-slate-700 transition hover:bg-slate-100 hover:text-slate-900"
        >
            Users
        </a>
    </nav>

    <div class="border-t border-slate-200 pt-4">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button
                type="submit"
                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-100"
            >
                Logout
            </button>
        </form>
    </div>
</aside>
