<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\StoreSiteRequest;
use App\Http\Requests\Site\UpdateSiteRequest;
use App\Models\Site;
use App\Models\User;
use App\Services\Site\SiteService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function __construct(
        protected SiteService $siteService
    ) {
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        return view('sites.index', [
            'sites' => $this->siteService->getPaginatedSites($search),
            'search' => $search,
            'editors' => User::query()
                ->where('role', 'editor')
                ->orderBy('name')
                ->get(['id', 'name', 'email']),
        ]);
    }

    public function store(StoreSiteRequest $request): RedirectResponse
    {
        $this->siteService->createSite($request->validated(), $request->user());

        return redirect()
            ->route('sites.index')
            ->with('success', 'Site created successfully.');
    }

    public function update(UpdateSiteRequest $request, Site $site): RedirectResponse
    {
        $this->siteService->updateSite($site, $request->validated(), $request->user());

        return redirect()
            ->route('sites.index')
            ->with('success', 'Site updated successfully.');
    }

    public function toggleStatus(Request $request, Site $site): RedirectResponse
    {
        $this->siteService->toggleStatus($site, $request->user());

        return redirect()
            ->route('sites.index')
            ->with('success', 'Site status updated successfully.');
    }
}
