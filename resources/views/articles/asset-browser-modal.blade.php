<div
    id="assetBrowserModalOverlay"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 px-4 py-6"
>
    <div class="flex max-h-[90vh] w-full max-w-6xl flex-col overflow-hidden rounded-[2rem] bg-white shadow-2xl">
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

        <div class="flex-1 overflow-y-auto px-6 py-6">
            @if ($mediaLibrary->isEmpty())
                <div class="rounded-3xl border border-dashed border-slate-200 bg-slate-50 px-6 py-12 text-center text-sm text-slate-500">
                    No uploaded assets available yet.
                </div>
            @else
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach ($mediaLibrary as $mediaItem)
                        <button
                            type="button"
                            class="asset-browser-item overflow-hidden rounded-3xl border border-slate-200 bg-white text-left shadow-sm transition hover:-translate-y-0.5 hover:border-blue-300 hover:shadow-md"
                            data-media-id="{{ $mediaItem->id }}"
                            data-media-name="{{ $mediaItem->file_name }}"
                            data-media-url="{{ $mediaItem->public_url }}"
                        >
                            <div class="overflow-hidden border-b border-slate-200 bg-slate-50">
                                <img
                                    src="{{ $mediaItem->public_url }}"
                                    alt="{{ $mediaItem->file_name }}"
                                    class="h-44 w-full object-cover"
                                    loading="lazy"
                                >
                            </div>

                            <div class="px-4 py-4">
                                <p class="truncate text-sm font-semibold text-slate-900" title="{{ $mediaItem->file_name }}">
                                    {{ $mediaItem->file_name }}
                                </p>
                                <p class="mt-1 truncate text-xs text-slate-500">
                                    {{ $mediaItem->file_type }}
                                </p>
                            </div>
                        </button>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (() => {
            const assetBrowserModalOverlay = document.getElementById('assetBrowserModalOverlay');
            const openAssetBrowserModal = document.getElementById('openAssetBrowserModal');
            const closeAssetBrowserModal = document.getElementById('closeAssetBrowserModal');
            const assetBrowserItems = document.querySelectorAll('.asset-browser-item');

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

            assetBrowserItems.forEach((item) => {
                item.addEventListener('click', () => {
                    document.dispatchEvent(new CustomEvent('article:featured-image-selected', {
                        detail: {
                            mediaId: item.dataset.mediaId,
                            mediaName: item.dataset.mediaName,
                            mediaUrl: item.dataset.mediaUrl,
                        },
                    }));

                    hideAssetBrowserModal();
                });
            });
        })();
    </script>
@endpush