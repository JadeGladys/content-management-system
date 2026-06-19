@extends('layouts.admin', ['title' => 'Users'])

@section('content')
    <div class="my-6 px-4 md:px-8">
        <div class="mx-auto max-w-7xl min-w-0">
            <div class="mb-8 rounded-[2rem] border border-slate-200 bg-gradient-to-br from-white via-slate-50 to-blue-50/70 px-6 py-6 shadow-sm md:px-8">
                <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_15rem] lg:items-center">
                    <div class="max-w-4xl">
                        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-blue-600">Users Directory</p>
                        <h1 class="mt-3 text-3xl leading-tight font-semibold tracking-tight text-slate-900 md:text-[3.15rem]">Manage every CMS user from one view</h1>
                        <p class="mt-3 max-w-[56rem] text-sm leading-6 text-slate-600 md:text-base">
                            Review account details, track invitation state, and create new users without leaving the listing page.
                        </p>
                    </div>

                    <div class="rounded-3xl border border-white/80 bg-white/80 px-5 py-4 shadow-sm backdrop-blur">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-400">Current inventory</p>
                        <p class="mt-2 text-3xl font-semibold text-slate-900">{{ $users->total() }}</p>
                        <p class="mt-1 text-sm text-slate-500">total user{{ $users->total() === 1 ? '' : 's' }}</p>
                    </div>
                </div>
            </div>

            <div class="mb-6 flex flex-wrap items-center gap-6">
                <form method="GET" action="{{ route('users.index') }}" class="w-full max-w-sm" role="search">
                    @foreach (($filters['roles'] ?? []) as $role)
                        <input type="hidden" name="roles[]" value="{{ $role }}">
                    @endforeach

                    @foreach (($filters['access_statuses'] ?? []) as $status)
                        <input type="hidden" name="access_statuses[]" value="{{ $status }}">
                    @endforeach
                    <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm transition focus-within:border-blue-500 focus-within:ring-4 focus-within:ring-blue-100">
                        <button type="submit" class="shrink-0 text-slate-400 transition hover:text-blue-600">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192.904 192.904" class="size-5 fill-current" aria-hidden="true">
                                <path d="m190.707 180.101-47.078-47.077c11.702-14.072 18.752-32.142 18.752-51.831C162.381 36.423 125.959 0 81.191 0 36.422 0 0 36.423 0 81.193c0 44.767 36.422 81.187 81.191 81.187 19.688 0 37.759-7.049 51.831-18.751l47.079 47.078a7.474 7.474 0 0 0 5.303 2.197 7.498 7.498 0 0 0 5.303-12.803zM15 81.193C15 44.694 44.693 15 81.191 15c36.497 0 66.189 29.694 66.189 66.193 0 36.496-29.692 66.187-66.189 66.187C44.693 147.38 15 117.689 15 81.193z" />
                            </svg>
                        </button>

                        <label for="search" class="sr-only">Search</label>

                        <input
                            type="search"
                            id="search"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Search..."
                            class="w-full text-sm text-slate-900 outline-none placeholder:text-slate-400"
                        />

                        @if ($search)
                            <button
                                type="button"
                                id="clear-search"
                                class="shrink-0 text-slate-400 transition hover:text-blue-600"
                                aria-label="Clear search"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        @endif
                    </div>
                </form>

                <div class="ml-auto flex flex-wrap gap-4">
                    <button
                        type="button"
                        id="openUserFilterPanel"
                        class="{{ $hasActiveFilters ? 'border-blue-600 bg-blue-600 text-white hover:bg-blue-700' : 'border-slate-200 bg-white text-slate-900 hover:bg-slate-50' }} flex items-center gap-2 rounded-2xl border px-4 py-3 text-sm font-semibold shadow-sm transition hover:-translate-y-0.5 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 fill-current" viewBox="0 0 64 64" aria-hidden="true">
                            <path d="M26.55 61.295a2.18 2.18 0 0 1-2.18-2.18v-20.96L4.161 15.928A6.115 6.115 0 0 1 8.685 5.705h46.63a6.115 6.115 0 0 1 4.524 10.224L39.63 38.154v12.241a2.18 2.18 0 0 1-.817 1.7l-10.9 8.72a2.18 2.18 0 0 1-1.363.48M8.685 10.065a1.755 1.755 0 0 0-1.297 2.932l20.775 22.89a2.18 2.18 0 0 1 .567 1.428v17.266l6.54-5.276v-11.99a2.18 2.18 0 0 1 .567-1.472l20.775-22.89a1.755 1.755 0 0 0-1.297-2.888z" />
                        </svg>
                        Filter
                        @if (! empty($filters['roles']) || ! empty($filters['access_statuses']))
                            <span class="{{ $hasActiveFilters ? 'bg-white/20 text-white' : 'bg-blue-50 text-blue-700' }} inline-flex h-5 min-w-5 items-center justify-center rounded-full px-1.5 text-[11px] font-bold">
                                {{ count($filters['roles']) + count($filters['access_statuses']) }}
                            </span>
                        @endif
                    </button>

                    <button
                        type="button"
                        class="flex items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-900 shadow-sm transition hover:-translate-y-0.5 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 fill-current" viewBox="0 0 32 32" aria-hidden="true">
                            <path d="M9.24 17.56a1.24 1.24 0 0 1 0-1.75 1.22 1.22 0 0 1 1.73 0l3.78 3.81V3.21a1.25 1.25 0 0 1 2.5 0v16.41L21 15.81a1.22 1.22 0 0 1 1.73 0 1.24 1.24 0 0 1 0 1.75l-5.89 6a1.21 1.21 0 0 1-1.74 0zm19.53 2.16a1.23 1.23 0 0 0-1.23 1.22v5.88a.73.73 0 0 1-.73.73H5.19a.73.73 0 0 1-.73-.73v-5.88a1.23 1.23 0 0 0-2.46 0v5.88A3.19 3.19 0 0 0 5.19 30h21.62A3.19 3.19 0 0 0 30 26.82v-5.88a1.23 1.23 0 0 0-1.23-1.22" />
                        </svg>
                        Export
                    </button>

                    <button
                        type="button"
                        id="openCreateUserModal"
                        class="flex items-center gap-2 rounded-2xl border border-blue-600 bg-blue-600 px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 fill-current" viewBox="0 0 512 512" aria-hidden="true">
                            <g>
                                <path d="M256 494.08a23.25 23.25 0 0 1-23.25-23.25V41.17a23.25 23.25 0 0 1 46.5 0v429.66A23.25 23.25 0 0 1 256 494.08" />
                                <path d="M470.83 279.25H41.17a23.25 23.25 0 0 1 0-46.5h429.66a23.25 23.25 0 0 1 0 46.5" />
                            </g>
                        </svg>
                        Add user
                    </button>
                </div>
            </div>

            <div class="rounded-[2rem] border border-slate-200 bg-white shadow-sm">
                <div class="max-w-full overflow-x-auto">
                    <table class="min-w-[1120px] w-full table-fixed">
                        <colgroup>
                            <col class="w-[4%]">
                            <col class="w-[22%]">
                            <col class="w-[22%]">
                            <col class="w-[10%]">
                            <col class="w-[14%]">
                            <col class="w-[10%]">
                            <col class="w-[16%]">
                        </colgroup>
                        <thead class="bg-slate-50 text-left text-[13px] font-semibold text-slate-900">
                            <tr>
                                <th scope="col" class="w-8 py-5 pl-4">
                                    <label class="group inline-block">
                                        <input type="checkbox" class="sr-only" id="master-checkbox" />
                                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border border-slate-300 bg-white group-has-[input:checked]:border-blue-600 group-has-[input:checked]:bg-blue-600 group-focus-within:ring-2 group-focus-within:ring-blue-500" aria-hidden="true">
                                            <svg class="size-3.5 text-white opacity-0 group-has-[input:checked]:opacity-100" viewBox="0 0 12 10" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 5l3 3 7-7" />
                                            </svg>
                                        </span>
                                    </label>
                                </th>
                                <th scope="col" class="px-4 py-5 whitespace-nowrap">Users</th>
                                <th scope="col" class="px-4 py-5 whitespace-nowrap">Email</th>
                                <th scope="col" class="px-4 py-5 whitespace-nowrap">Role</th>
                                <th scope="col" class="px-4 py-5 whitespace-nowrap">Access status</th>
                                <th scope="col" class="px-4 py-5 leading-tight">
                                    <span class="block">Updated at</span>
                                </th>
                                <th scope="col" class="px-4 py-5 whitespace-nowrap">Action</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-200 text-[13px]">
                            @forelse ($users as $listedUser)
                                <tr class="transition hover:bg-slate-50/80 has-[:checked]:bg-blue-50/50">
                                    <td class="w-8 py-5 pl-4 align-middle">
                                        <label class="group inline-block">
                                            <input type="checkbox" class="sr-only row-checkbox" />
                                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg border border-slate-300 bg-white group-has-[input:checked]:border-blue-600 group-has-[input:checked]:bg-blue-600 group-focus-within:ring-2 group-focus-within:ring-blue-500" aria-hidden="true">
                                                <svg class="size-3.5 text-white opacity-0 group-has-[input:checked]:opacity-100" viewBox="0 0 12 10" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M1 5l3 3 7-7" />
                                                </svg>
                                            </span>
                                        </label>
                                    </td>

                                    <td class="px-4 py-5 font-medium text-slate-900">
                                        <div class="flex min-w-0 items-center gap-3">
                                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-sm font-semibold text-blue-700">
                                                {{ \Illuminate\Support\Str::of($listedUser->name)->trim()->explode(' ')->take(2)->map(fn ($part) => \Illuminate\Support\Str::substr($part, 0, 1))->implode('') }}
                                            </div>
                                            <div class="min-w-0">
                                                <span class="block truncate font-semibold leading-relaxed" title="{{ $listedUser->name }}">{{ $listedUser->name }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-5 text-slate-500">
                                        <span class="block truncate whitespace-nowrap" title="{{ $listedUser->email }}">{{ $listedUser->email }}</span>
                                    </td>

                                    <td class="px-4 py-5 text-slate-500 whitespace-nowrap">
                                        <span class="inline-flex rounded-xl bg-slate-100 px-3 py-2 text-[12px] font-semibold tracking-wide text-slate-600">
                                            {{ ucfirst($listedUser->role) }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-5 text-slate-500 whitespace-nowrap">
                                        <span class="inline-flex w-max items-center gap-2 rounded-xl border px-3 py-2 text-[12px] font-semibold {{ $listedUser->must_set_password ? 'border-amber-200 bg-amber-50 text-amber-700' : 'border-emerald-200 bg-emerald-50 text-emerald-700' }}">
                                            <span class="h-2.5 w-2.5 rounded-full {{ $listedUser->must_set_password ? 'bg-amber-500' : 'bg-emerald-500' }}"></span>
                                            {{ $listedUser->must_set_password ? 'Pending setup' : 'Active' }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-5 text-slate-500">
                                        <span class="block leading-6">
                                            {{ $listedUser->updated_at->format('d M Y,') }}<br>
                                        </span>
                                    </td>

                                    <td class="px-4 py-5 text-slate-500">
                                        @if ($listedUser->must_set_password)
                                            <form method="POST" action="{{ route('users.password-setup.resend', $listedUser) }}">
                                                @csrf
                                                <button
                                                    type="submit"
                                                    class="inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-3 py-2 text-[12px] font-semibold text-amber-700 transition hover:bg-amber-100"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992V4.356m-.58 4.992A9 9 0 1 0 6.5 18.5" />
                                                    </svg>
                                                    Resend setup
                                                </button>
                                            </form>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-sm text-slate-500">
                                        No users found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mx-auto mt-6 flex flex-wrap items-center justify-between gap-6">
                <div class="text-sm text-slate-600">
                    Showing
                    <span class="mx-1 font-medium">{{ $users->firstItem() ?? 0 }}</span>
                    to
                    <span class="mx-1 font-medium">{{ $users->lastItem() ?? 0 }}</span>
                    of
                    <span class="mx-1 font-medium">{{ $users->total() }}</span>
                    results
                </div>

                @if ($users->hasPages())
                    <nav aria-label="Pagination" class="flex w-max items-center overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm divide-x divide-slate-200">
                        @if ($users->onFirstPage())
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center text-slate-300">
                                ‹
                            </span>
                        @else
                            <a href="{{ $users->previousPageUrl() }}" class="flex h-12 w-12 shrink-0 items-center justify-center hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                ‹
                            </a>
                        @endif

                        @foreach (range(1, $users->lastPage()) as $page)
                            @if ($page >= max(1, $users->currentPage() - 2) && $page <= min($users->lastPage(), $users->currentPage() + 2))
                                @if ($page === $users->currentPage())
                                    <span class="flex h-12 w-12 shrink-0 items-center justify-center bg-blue-600 text-sm font-semibold text-white">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $users->url($page) }}" class="flex h-12 w-12 shrink-0 items-center justify-center text-sm font-semibold text-slate-900 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endif
                        @endforeach

                        @if ($users->hasMorePages())
                            <a href="{{ $users->nextPageUrl() }}" class="flex h-12 w-12 shrink-0 items-center justify-center hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500">
                                ›
                            </a>
                        @else
                            <span class="flex h-12 w-12 shrink-0 items-center justify-center text-slate-300">
                                ›
                            </span>
                        @endif
                    </nav>
                @endif
            </div>
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

    <x-filter-panel
        panel-id="userFilterPanel"
        open-button-id="openUserFilterPanel"
        :action="route('users.index')"
        :search="$search"
        :fields="[
            [
                'key' => 'roles',
                'label' => 'Role',
                'placeholder' => 'Select role',
                'options' => [
                    ['value' => 'admin', 'label' => 'Admin'],
                    ['value' => 'editor', 'label' => 'Editor'],
                ],
                'selected' => $filters['roles'] ?? [],
            ],
            [
                'key' => 'access_statuses',
                'label' => 'Status',
                'placeholder' => 'Select status',
                'options' => [
                    ['value' => 'pending', 'label' => 'Pending setup'],
                    ['value' => 'active', 'label' => 'Active'],
                ],
                'selected' => $filters['access_statuses'] ?? [],
            ],
        ]"
    />

@endsection

@push('scripts')
    <script>
        const createUserModalOverlay = document.getElementById('createUserModal');
        const openCreateUserModalButton = document.getElementById('openCreateUserModal');
        const createUserModalCloseButtons = document.querySelectorAll('[data-modal-close="createUserModal"]');
        const masterCheckbox = document.getElementById('master-checkbox');
        const rowCheckboxes = document.querySelectorAll('.row-checkbox');

        const showCreateUserModal = () => {
            createUserModalOverlay?.classList.remove('hidden');
            createUserModalOverlay?.classList.add('flex');
        };

        const hideCreateUserModal = () => {
            createUserModalOverlay?.classList.add('hidden');
            createUserModalOverlay?.classList.remove('flex');
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

        masterCheckbox?.addEventListener('change', (event) => {
            rowCheckboxes.forEach((checkbox) => {
                checkbox.checked = event.target.checked;
            });
        });

        @if ($errors->any())
            showCreateUserModal();
        @endif

        const clearSearchButton = document.getElementById('clear-search');
        const searchInput = document.getElementById('search');
        const searchForm = searchInput?.closest('form');

        clearSearchButton?.addEventListener('click', () => {
            if (!searchInput || !searchForm) return;

            searchInput.value = '';
            searchForm.submit();
        });

    </script>
@endpush
