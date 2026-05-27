@extends('layouts.admin', ['title' => 'Sites'])

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white p-8 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Sites</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                    Create a new site record for the CMS. Sites start as active or inactive and can be connected to users and content in later workflows.
                </p>
            </div>

            <button
                type="button"
                id="openCreateSiteModal"
                class="inline-flex items-center justify-center rounded-xl border border-blue-600 bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
            >
                Create Site
            </button>
        </div>

        @if (session('success'))
            <div class="mt-6 rounded-2xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mt-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-medium">Please fix the following:</p>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mt-6 rounded-3xl border border-dashed border-slate-300 bg-slate-50 p-10 text-center">
            <h2 class="text-xl font-semibold text-slate-900">Site management</h2>
            <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-slate-600">
                Use the button above to open the modal and create a new site for the CMS.
            </p>
        </div>
    </div>

    <x-modal
        id="createSiteModal"
        title="Create Site"
        description="Add a new site with its main identity details."
    >
        <form class="space-y-4" method="POST" action="{{ route('sites.store') }}">
            @csrf

            <div>
                <label for="name" class="mb-2 inline-block text-sm font-medium text-slate-900">Site Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="ISCO Careers"
                    required
                    class="w-full rounded-md bg-white px-3 py-2.5 text-sm text-slate-900 outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600"
                />
            </div>

            <div>
                <label for="domain" class="mb-2 inline-block text-sm font-medium text-slate-900">Domain</label>
                <input
                    type="text"
                    id="domain"
                    name="domain"
                    value="{{ old('domain') }}"
                    placeholder="careers.isco.local"
                    class="w-full rounded-md bg-white px-3 py-2.5 text-sm text-slate-900 outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600"
                />
            </div>

            <div>
                <label for="status" class="mb-2 inline-block text-sm font-medium text-slate-900">Status</label>
                <select
                    id="status"
                    name="status"
                    required
                    class="w-full rounded-md bg-white px-3 py-2.5 text-sm text-slate-900 outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600"
                >
                    <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                    <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <button
                    type="button"
                    data-modal-close="createSiteModal"
                    class="w-full rounded-md border border-slate-300 px-3.5 py-2 text-sm font-medium text-slate-700 transition-all hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="w-full rounded-md border border-blue-600 bg-blue-600 px-3.5 py-2 text-sm font-semibold tracking-wide text-white transition-all hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                >
                    Create Site
                </button>
            </div>
        </form>
    </x-modal>
@endsection

@push('scripts')
    <script>
        const createSiteModalOverlay = document.getElementById('createSiteModal');
        const openCreateSiteModalButton = document.getElementById('openCreateSiteModal');
        const createSiteModalCloseButtons = document.querySelectorAll('[data-modal-close="createSiteModal"]');

        const showCreateSiteModal = () => {
            createSiteModalOverlay.classList.remove('hidden');
            createSiteModalOverlay.classList.add('flex');
        };

        const hideCreateSiteModal = () => {
            createSiteModalOverlay.classList.add('hidden');
            createSiteModalOverlay.classList.remove('flex');
        };

        openCreateSiteModalButton?.addEventListener('click', showCreateSiteModal);
        createSiteModalCloseButtons.forEach((button) => {
            button.addEventListener('click', hideCreateSiteModal);
        });

        createSiteModalOverlay?.addEventListener('click', (event) => {
            if (event.target === createSiteModalOverlay) {
                hideCreateSiteModal();
            }
        });

        @if ($errors->any())
            showCreateSiteModal();
        @endif
    </script>
@endpush
