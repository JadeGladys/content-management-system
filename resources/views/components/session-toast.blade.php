@if (session('success') || session('error') || $errors->any())
    <div
        id="flash-toast-stack"
        class="pointer-events-none fixed right-4 top-4 z-[100] flex w-full max-w-xs flex-col gap-2"
    >
        @if (session('success'))
            <div
                class="flash-toast pointer-events-auto overflow-hidden rounded-2xl border border-slate-200 bg-white text-slate-900 shadow-[0_14px_35px_-18px_rgba(15,23,42,0.3)]"
                data-toast
            >
                <div class="flex items-center gap-3 p-4">
                    <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-emerald-400 text-emerald-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium leading-5 text-slate-900">
                            {{ session('success') }}
                        </p>
                    </div>

                    <button
                        type="button"
                        class="flash-toast-close inline-flex shrink-0 rounded-lg p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                        aria-label="Dismiss notification"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div
                class="flash-toast pointer-events-auto overflow-hidden rounded-2xl border border-slate-200 bg-white text-slate-900 shadow-[0_14px_35px_-18px_rgba(15,23,42,0.3)]"
                data-toast
            >
                <div class="flex items-center gap-3 p-4">
                    <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center text-rose-500">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium leading-5 text-slate-900">
                            {{ session('error') }}
                        </p>
                    </div>

                    <button
                        type="button"
                        class="flash-toast-close inline-flex shrink-0 rounded-lg p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                        aria-label="Dismiss notification"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        @elseif ($errors->any())
            <div
                class="flash-toast pointer-events-auto overflow-hidden rounded-2xl border border-slate-200 bg-white text-slate-900 shadow-[0_14px_35px_-18px_rgba(15,23,42,0.3)]"
                data-toast
            >
                <div class="flex items-center gap-3 p-4">
                    <div class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full border border-amber-400 text-amber-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008Zm9-3.758a9 9 0 1 1-18 0a9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium leading-5 text-slate-900">
                            Some fields need attention
                        </p>
                    </div>

                    <button
                        type="button"
                        class="flash-toast-close inline-flex shrink-0 rounded-lg p-1 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                        aria-label="Dismiss notification"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        @endif
    </div>

    <script>
        (() => {
            const toasts = document.querySelectorAll('[data-toast]');

            const dismissToast = (toast) => {
                if (!toast || toast.dataset.closing === 'true') {
                    return;
                }

                toast.dataset.closing = 'true';
                toast.classList.add('opacity-0', 'translate-y-2');

                setTimeout(() => {
                    toast.remove();
                }, 250);
            };

            toasts.forEach((toast) => {
                toast.classList.add('transition', 'duration-200');

                const closeButton = toast.querySelector('.flash-toast-close');

                closeButton?.addEventListener('click', () => {
                    dismissToast(toast);
                });

                setTimeout(() => {
                    dismissToast(toast);
                }, 4000);
            });
        })();
    </script>
@endif