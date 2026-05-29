<div
    id="assetBrowserModalOverlay"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 px-4 py-6"
>
    <div class="w-full max-w-5xl rounded-[2rem] bg-white shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.18em] text-blue-600">Uploaded Assets</p>
                <h2 class="mt-2 text-2xl font-semibold text-slate-900">Browse uploaded assets</h2>
            </div>

            <button
                type="button"
                id="closeAssetBrowserModal"
                class="rounded-full p-2 text-slate-500 transition hover:bg-slate-100 hover:text-slate-900"
            >
                <span class="sr-only">Close</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="px-6 py-10 text-sm text-slate-500">
            Media browser will be added in the media tasks.
        </div>
    </div>
</div>

@push('scripts')
    <script>
        const assetBrowserModalOverlay = document.getElementById('assetBrowserModalOverlay');
        const openAssetBrowserModal = document.getElementById('openAssetBrowserModal');
        const closeAssetBrowserModal = document.getElementById('closeAssetBrowserModal');

        const showAssetBrowserModal = () => {
            assetBrowserModalOverlay?.classList.remove('hidden');
            assetBrowserModalOverlay?.classList.add('flex');
        };

        const hideAssetBrowserModal = () => {
            assetBrowserModalOverlay?.classList.add('hidden');
            assetBrowserModalOverlay?.classList.remove('flex');
        };

        openAssetBrowserModal?.addEventListener('click', showAssetBrowserModal);
        closeAssetBrowserModal?.addEventListener('click', hideAssetBrowserModal);

        assetBrowserModalOverlay?.addEventListener('click', (event) => {
            if (event.target === assetBrowserModalOverlay) {
                hideAssetBrowserModal();
            }
        });
    </script>
@endpush
