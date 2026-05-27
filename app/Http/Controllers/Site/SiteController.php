<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\StoreSiteRequest;
use App\Services\Site\SiteService;
use Illuminate\Contracts\View\View;

class SiteController extends Controller
{
    public function create(): View
    {
        return view('sites.create');
    }

    public function store(StoreSiteRequest $request, SiteService $siteService)
    {
        $actor = $request->user();

        $siteService->createSite($request->validated(), $actor);

        return redirect()
            ->route('sites.create')
            ->with('success', 'Site created successfully.');
    }
}
