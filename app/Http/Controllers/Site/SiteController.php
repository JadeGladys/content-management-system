<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\StoreSiteRequest;
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
        ]);
    }

    public function store(StoreSiteRequest $request): RedirectResponse
    {
        $this->siteService->createSite($request->validated(), $request->user());

        return redirect()
            ->route('sites.index')
            ->with('success', 'Site created successfully.');
    }
}
