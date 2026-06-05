@props([
    'search' => '',
    'filters' => [
        'roles' => [],
        'access_statuses' => [],
    ],
])

<div
    id="userFilterPanel"
    class="fixed inset-0 z-[1000] hidden"
    aria-hidden="true"
>
    <div id="userFilterBackdrop" class="absolute inset-0 bg-[rgba(15,23,42,0.28)]"></div>

    <div
        role="dialog"
        aria-modal="true"
        aria-labelledby="user-filter-title"
        class="absolute inset-x-4 top-24 flex h-[min(28rem,calc(100vh-7rem))] w-auto flex-col overflow-hidden rounded-[2rem] border border-slate-200 bg-white shadow-[0_32px_80px_-32px_rgba(15,23,42,0.45)] sm:left-auto sm:right-6 sm:w-[28rem] lg:top-[18.75rem] lg:right-8 lg:h-[30rem] lg:w-[30rem]"
    >
        <form method="GET" action="{{ route('users.index') }}" class="flex h-full flex-col">
            <input type="hidden" name="search" value="{{ $search }}">

            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">
                <h2 id="user-filter-title" class="text-lg font-semibold text-slate-900">Filter</h2>

                <button
                    type="button"
                    id="clearUserFilters"
                    class="text-sm font-semibold text-red-500 transition hover:text-red-600"
                >
                    Clear all
                </button>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto px-6 py-6">
                <div class="space-y-8">
                    <fieldset>
                        <legend class="text-sm font-semibold text-slate-900">Role</legend>

                        <div class="mt-3">
                            <label for="roleSelect" class="sr-only">Select role</label>
                            <select
                                id="roleSelect"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            >
                                <option value="">Select role</option>
                                <option value="admin">Admin</option>
                                <option value="editor">Editor</option>
                            </select>
                        </div>

                        <div id="selectedRoles" class="mt-3 flex flex-wrap gap-2">
                            @foreach (($filters['roles'] ?? []) as $role)
                                <button
                                    type="button"
                                    class="filter-chip inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700"
                                    data-value="{{ $role }}"
                                    data-type="roles"
                                >
                                    {{ ucfirst($role) }}
                                    <span aria-hidden="true">×</span>
                                </button>
                                <input type="hidden" name="roles[]" value="{{ $role }}">
                            @endforeach
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend class="text-sm font-semibold text-slate-900">Status</legend>

                        <div class="mt-3">
                            <label for="statusSelect" class="sr-only">Select status</label>
                            <select
                                id="statusSelect"
                                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-4 focus:ring-blue-100"
                            >
                                <option value="">Select status</option>
                                <option value="pending">Pending setup</option>
                                <option value="active">Active</option>
                            </select>
                        </div>

                        <div id="selectedStatuses" class="mt-3 flex flex-wrap gap-2">
                            @foreach (($filters['access_statuses'] ?? []) as $status)
                                <button
                                    type="button"
                                    class="filter-chip inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700"
                                    data-value="{{ $status }}"
                                    data-type="access_statuses"
                                >
                                    {{ $status === 'pending' ? 'Pending setup' : 'Active' }}
                                    <span aria-hidden="true">×</span>
                                </button>
                                <input type="hidden" name="access_statuses[]" value="{{ $status }}">
                            @endforeach
                        </div>
                    </fieldset>
                </div>
            </div>

            <div class="flex shrink-0 gap-3 border-t border-slate-200 bg-white px-6 py-4">
                <button
                    type="submit"
                    class="w-full rounded-xl border border-blue-600 bg-blue-600 px-4 py-3 text-sm font-semibold text-white transition hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                >
                    Apply
                </button>

                <button
                    type="button"
                    id="closeUserFilterPanel"
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                >
                    Close
                </button>
            </div>
        </form>
    </div>
</div>

@once
    @push('scripts')
        <script>
            (() => {
                const userFilterPanel = document.getElementById('userFilterPanel');
                const userFilterBackdrop = document.getElementById('userFilterBackdrop');
                const openUserFilterPanelButton = document.getElementById('openUserFilterPanel');
                const closeUserFilterPanelButton = document.getElementById('closeUserFilterPanel');
                const clearUserFiltersButton = document.getElementById('clearUserFilters');
                const roleSelect = document.getElementById('roleSelect');
                const statusSelect = document.getElementById('statusSelect');
                const selectedRolesContainer = document.getElementById('selectedRoles');
                const selectedStatusesContainer = document.getElementById('selectedStatuses');

                const openUserFilterPanel = () => {
                    userFilterPanel?.classList.remove('hidden');
                    userFilterPanel?.classList.add('flex');
                };

                const closeUserFilterPanel = () => {
                    userFilterPanel?.classList.add('hidden');
                    userFilterPanel?.classList.remove('flex');
                };

                const makeLabel = (type, value) => {
                    if (type === 'roles') {
                        return value.charAt(0).toUpperCase() + value.slice(1);
                    }

                    if (type === 'access_statuses') {
                        return value === 'pending' ? 'Pending setup' : 'Active';
                    }

                    return value;
                };

                const makeChipClass = (type) => {
                    return type === 'roles'
                        ? 'filter-chip inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1.5 text-xs font-semibold text-blue-700'
                        : 'filter-chip inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-700';
                };

                const addFilterValue = (type, value, container) => {
                    if (!value || !container) {
                        return;
                    }

                    const existingValues = Array.from(
                        container.querySelectorAll(`input[name="${type}[]"]`)
                    ).map((input) => input.value);

                    if (existingValues.includes(value)) {
                        return;
                    }

                    const chip = document.createElement('button');
                    chip.type = 'button';
                    chip.className = makeChipClass(type);
                    chip.dataset.type = type;
                    chip.dataset.value = value;
                    chip.innerHTML = `${makeLabel(type, value)} <span aria-hidden="true">×</span>`;

                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = `${type}[]`;
                    hiddenInput.value = value;

                    container.appendChild(chip);
                    container.appendChild(hiddenInput);
                };

                const removeFilterValue = (type, value, container) => {
                    if (!container) {
                        return;
                    }

                    const chip = container.querySelector(`.filter-chip[data-type="${type}"][data-value="${value}"]`);
                    const input = container.querySelector(`input[name="${type}[]"][value="${value}"]`);

                    chip?.remove();
                    input?.remove();
                };

                roleSelect?.addEventListener('change', (event) => {
                    const value = event.target.value;
                    addFilterValue('roles', value, selectedRolesContainer);
                    event.target.value = '';
                });

                statusSelect?.addEventListener('change', (event) => {
                    const value = event.target.value;
                    addFilterValue('access_statuses', value, selectedStatusesContainer);
                    event.target.value = '';
                });

                selectedRolesContainer?.addEventListener('click', (event) => {
                    const chip = event.target.closest('.filter-chip');

                    if (!chip) {
                        return;
                    }

                    removeFilterValue(chip.dataset.type, chip.dataset.value, selectedRolesContainer);
                });

                selectedStatusesContainer?.addEventListener('click', (event) => {
                    const chip = event.target.closest('.filter-chip');

                    if (!chip) {
                        return;
                    }

                    removeFilterValue(chip.dataset.type, chip.dataset.value, selectedStatusesContainer);
                });

                clearUserFiltersButton?.addEventListener('click', () => {
                    selectedRolesContainer?.querySelectorAll('.filter-chip, input[type="hidden"]').forEach((element) => element.remove());
                    selectedStatusesContainer?.querySelectorAll('.filter-chip, input[type="hidden"]').forEach((element) => element.remove());
                });

                openUserFilterPanelButton?.addEventListener('click', openUserFilterPanel);
                closeUserFilterPanelButton?.addEventListener('click', closeUserFilterPanel);
                userFilterBackdrop?.addEventListener('click', closeUserFilterPanel);

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        closeUserFilterPanel();
                    }
                });
            })();
        </script>
    @endpush
@endonce
