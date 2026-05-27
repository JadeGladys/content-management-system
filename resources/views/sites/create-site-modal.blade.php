<x-modal
    id="createSiteModal"
    title="Create Site"
    description="Add a new site with its main identity details."
>
    @if ($errors->any())
        <div class="mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            <p class="font-semibold">Please fix the following:</p>
            <ul class="mt-2 list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="space-y-5" method="POST" action="{{ route('sites.store') }}">
        @csrf

        <div>
            <label for="name" class="mb-2 inline-block text-sm font-semibold text-slate-900">Site Name</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name') }}"
                placeholder="Jade Portfolio"
                required
                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-inner shadow-slate-100 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100"
            />
        </div>

        <div>
            <label for="domain" class="mb-2 inline-block text-sm font-semibold text-slate-900">Domain</label>
            <input
                type="text"
                id="domain"
                name="domain"
                value="{{ old('domain') }}"
                placeholder="jadegladys.netlify.app"
                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-inner shadow-slate-100 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100"
            />
            <p class="mt-2 text-xs text-slate-500">Use the domain only, without `https://` or trailing slashes.</p>
        </div>

        <div>
            <label for="status" class="mb-2 inline-block text-sm font-semibold text-slate-900">Status</label>
            <select
                id="status"
                name="status"
                required
                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-inner shadow-slate-100 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100"
            >
                <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
            </select>
        </div>

        <div class="flex gap-3 pt-2">
            <button
                type="button"
                data-modal-close="createSiteModal"
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
            >
                Cancel
            </button>

            <button
                type="submit"
                class="w-full rounded-2xl border border-blue-600 bg-blue-600 px-4 py-3 text-sm font-semibold tracking-wide text-white transition hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
            >
                Create Site
            </button>
        </div>
    </form>
</x-modal>
