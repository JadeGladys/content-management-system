@extends('layouts.admin', ['title' => 'Users'])

@section('content')
    <div class="mx-auto max-w-5xl">
        <div class="flex flex-col gap-4 rounded-3xl border border-slate-200 bg-white p-8 shadow-sm sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-slate-900">Users</h1>
                <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">
                    Manage CMS access from one place. Create a new user to give them a role and start the password setup flow.
                </p>
            </div>

            <button
                type="button"
                id="openCreateUserModal"
                class="inline-flex items-center justify-center rounded-xl border border-blue-600 bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
            >
                Create User
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
            <h2 class="text-xl font-semibold text-slate-900">User management</h2>
            <p class="mx-auto mt-3 max-w-xl text-sm leading-6 text-slate-600">
                Use the button above to open the modal and create a new CMS user.
            </p>
        </div>
    </div>

    <x-modal
        id="createUserModal"
        title="Create User"
        description="Add a new CMS user and assign a role. The account will be created with password setup pending."
    >
        <form class="space-y-4" method="POST" action="{{ route('users.store') }}">
            @csrf

            <div>
                <label for="name" class="mb-2 inline-block text-sm font-medium text-slate-900">Full Name</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Lily Grant" required
                    class="w-full rounded-md bg-white px-3 py-2.5 text-sm text-slate-900 outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600" />
            </div>

            <div>
                <label for="email" class="mb-2 inline-block text-sm font-medium text-slate-900">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="l.grant@isco.local"
                    required
                    class="w-full rounded-md bg-white px-3 py-2.5 text-sm text-slate-900 outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600" />
            </div>

            <div>
                <label for="role" class="mb-2 inline-block text-sm font-medium text-slate-900">Role</label>
                <select id="role" name="role" required
                    class="w-full rounded-md bg-white px-3 py-2.5 text-sm text-slate-900 outline-1 -outline-offset-1 outline-slate-300 focus:outline-2 focus:-outline-offset-2 focus:outline-blue-600">
                    <option value="">Select a role</option>
                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                    <option value="editor" @selected(old('role') === 'editor')>Editor</option>
                </select>
            </div>

            <div class="flex gap-3 pt-2">
                <button
                    type="button"
                    data-modal-close="createUserModal"
                    class="w-full rounded-md border border-slate-300 px-3.5 py-2 text-sm font-medium text-slate-700 transition-all hover:bg-slate-100 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="w-full rounded-md border border-blue-600 bg-blue-600 px-3.5 py-2 text-sm font-semibold tracking-wide text-white transition-all hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                >
                    Create User
                </button>
            </div>
        </form>
    </x-modal>
@endsection

@push('scripts')
    <script>
        const createUserModalOverlay = document.getElementById('createUserModal');
        const openCreateUserModalButton = document.getElementById('openCreateUserModal');
        const createUserModalCloseButtons = document.querySelectorAll('[data-modal-close="createUserModal"]');

        const showCreateUserModal = () => {
            createUserModalOverlay.classList.remove('hidden');
            createUserModalOverlay.classList.add('flex');
        };

        const hideCreateUserModal = () => {
            createUserModalOverlay.classList.add('hidden');
            createUserModalOverlay.classList.remove('flex');
        };

        openCreateUserModalButton?.addEventListener('click', showCreateUserModal);
        createUserModalCloseButtons.forEach((button) => {
            button.addEventListener('click', hideCreateUserModal);
        });

        createUserModalOverlay?.addEventListener('click', (event) => {
            if (event.target === createUserModalOverlay) {
                hideCreateUserModal();
            }
        });

        @if ($errors->any())
            showCreateUserModal();
        @endif
    </script>
@endpush
