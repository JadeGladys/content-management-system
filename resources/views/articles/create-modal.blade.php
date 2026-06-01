<div
    id="articleCreateModalOverlay"
    class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-950/50 px-4 py-6"
>
    <div class="w-full max-w-xl rounded-[2rem] bg-white shadow-2xl">
        <div class="border-b border-slate-200 px-6 py-5">
            <p class="text-sm font-semibold uppercase tracking-[0.18em] text-blue-600">Articles</p>
            <h2 class="mt-2 text-2xl font-semibold text-slate-900">Create a new article draft</h2>
            <p class="mt-2 text-sm leading-6 text-slate-500">
                Start with the core fields, then open the full editor from the list.
            </p>
        </div>

        <form method="POST" action="{{ route('articles.store') }}" class="space-y-5 px-6 py-6">
            @csrf

            <div>
                <label for="draft_title" class="mb-2 block text-sm font-medium text-slate-700">
                    Title <span class="text-rose-600">*</span>
                </label>
                <input
                    type="text"
                    id="draft_title"
                    name="title"
                    value="{{ old('title') }}"
                    class="w-full rounded-2xl border border-slate-300 px-4 py-3 text-sm text-slate-900 focus:border-blue-500 focus:outline-none focus:ring-4 focus:ring-blue-100"
                >
                @error('title')
                    <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <x-category-picker
                    picker-id="article-create-category-picker"
                    name="category"
                    label="Category"
                    :required="true"
                    :value="old('category')"
                    :options="$categories"
                    placeholder="Select category"
                />
            </div>

            <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-5">
                <button
                    type="button"
                    id="closeArticleCreateModal"
                    class="inline-flex min-w-28 items-center justify-center rounded-2xl border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-50"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="inline-flex min-w-32 items-center justify-center rounded-2xl bg-blue-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700"
                >
                    Save
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        (() => {
            const modalOverlay = document.getElementById('articleCreateModalOverlay');
            const openButton = document.getElementById('openArticleCreateModal');
            const closeButton = document.getElementById('closeArticleCreateModal');
            const hasCreateErrors = @json($errors->hasAny(['title', 'category']));

            const showModal = () => {
                modalOverlay?.classList.remove('hidden');
                modalOverlay?.classList.add('flex');
            };

            const hideModal = () => {
                modalOverlay?.classList.add('hidden');
                modalOverlay?.classList.remove('flex');
            };

            openButton?.addEventListener('click', showModal);
            closeButton?.addEventListener('click', hideModal);

            modalOverlay?.addEventListener('click', (event) => {
                if (event.target === modalOverlay) {
                    hideModal();
                }
            });

            if (hasCreateErrors) {
                showModal();
            }
        })();
    </script>
@endpush
