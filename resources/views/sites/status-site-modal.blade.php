<x-modal
    id="statusSiteModal"
    title="Update Site Status"
    description="Confirm whether this site should remain active in the CMS."
>
    <div class="space-y-5">
        <p class="text-sm leading-6 text-slate-600">
            <span id="statusSiteAction" class="font-semibold text-slate-900">Deactivate</span>
            <span id="statusSiteName" class="font-semibold text-slate-900"></span>?
        </p>

        <p id="statusSiteDescription" class="text-sm leading-6 text-slate-600">
            This will prevent this site from accessing the CMS until it is activated again.
        </p>

        <form id="statusSiteForm" method="POST">
            @csrf
            @method('PATCH')

            <div class="flex gap-3 pt-2">
                <button
                    type="button"
                    data-modal-close="statusSiteModal"
                    class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="w-full rounded-2xl border border-blue-600 bg-blue-600 px-4 py-3 text-sm font-semibold tracking-wide text-white transition hover:bg-blue-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-500"
                >
                    <span id="statusSiteSubmitLabel">Deactivate</span> Site
                </button>
            </div>
        </form>
    </div>
</x-modal>
