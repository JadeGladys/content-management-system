<x-modal
    id="editSiteModal"
    title="Edit Site"
    description="Update the site's details and assigned editors."
>
    <form id="editSiteForm" class="space-y-5" method="POST">
        @csrf
        @method('PATCH')
        <input type="hidden" id="edit_site_id" name="site_id" />

        @if ($errors->updateSite->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <p class="font-semibold">Please fix the following:</p>
                <ul class="mt-2 list-disc pl-5">
                    @foreach ($errors->updateSite->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div>
            <label for="edit_name" class="mb-2 inline-block text-sm font-semibold text-slate-900">Site Name</label>
            <input
                type="text"
                id="edit_name"
                name="name"
                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-inner shadow-slate-100 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100"
                required
            />
        </div>

        <div>
            <label for="edit_domain" class="mb-2 inline-block text-sm font-semibold text-slate-900">Domain</label>
            <input
                type="text"
                id="edit_domain"
                name="domain"
                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-inner shadow-slate-100 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100"
            />
            <p class="mt-2 text-xs text-slate-500">Use the domain only, without `https://` or trailing slashes.</p>
        </div>

        <div>
            <label for="edit_status" class="mb-2 inline-block text-sm font-semibold text-slate-900">Status</label>
            <select
                id="edit_status"
                name="status"
                required
                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 shadow-inner shadow-slate-100 outline-none transition focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-100"
            >
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <div>
            <label for="editorSearch" class="mb-2 inline-block text-sm font-semibold text-slate-900">Assigned Editors</label>

            <div class="relative">
                <div
                    id="editorSelectField"
                    class="flex min-h-[3.5rem] w-full flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-3 py-2 shadow-inner shadow-slate-100 transition focus-within:border-blue-500 focus-within:bg-white focus-within:ring-4 focus-within:ring-blue-100"
                >
                    <div id="selectedEditorsChips" class="flex flex-wrap items-center gap-2"></div>

                    <input
                        type="text"
                        id="editorSearch"
                        placeholder="Search editors by name or email"
                        class="min-w-[12rem] flex-1 bg-transparent text-sm text-slate-900 outline-none placeholder:text-slate-400"
                        autocomplete="off"
                    />
                </div>

                <div
                    id="editorDropdown"
                    class="absolute left-0 right-0 z-20 mt-2 hidden overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl"
                >
                    <div class="max-h-56 overflow-y-auto">
                        @foreach ($editors as $editor)
                            <button
                                type="button"
                                class="editor-option flex w-full items-start justify-between gap-3 border-b border-slate-100 px-4 py-3 text-left transition hover:bg-slate-50 last:border-b-0"
                                data-editor-id="{{ $editor->id }}"
                                data-editor-name="{{ $editor->name }}"
                                data-editor-email="{{ $editor->email }}"
                                data-editor-search="{{ strtolower($editor->name.' '.$editor->email) }}"
                            >
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-900">{{ $editor->name }}</p>
                                    <p class="truncate text-xs text-slate-500">{{ $editor->email }}</p>
                                </div>

                                <span class="text-xs font-semibold text-blue-600">Add</span>
                            </button>
                        @endforeach

                        <div id="editorDropdownEmpty" class="hidden px-4 py-3 text-sm text-slate-500">
                            No editors found.
                        </div>
                    </div>
                </div>
            </div>

            <div id="assignedEditorsHiddenInputs"></div>
        </div>

        <div class="flex gap-3 pt-2">
            <button
                type="button"
                data-modal-close="editSiteModal"
                class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
            >
                Cancel
            </button>

            <button
                type="submit"
                class="w-full rounded-2xl border border-blue-600 bg-blue-600 px-4 py-3 text-sm font-semibold tracking-wide text-white transition hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
            >
                Save Changes
            </button>
        </div>
    </form>
</x-modal>
