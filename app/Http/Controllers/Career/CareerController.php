<?php

namespace App\Http\Controllers\Career;

use App\Http\Controllers\Controller;
use App\Http\Requests\Career\StoreCareerRequest;
use App\Models\Career;
use App\Services\Career\CareerService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    public function __construct(
        protected CareerService $careerService
    ) {
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        return view('careers.index', [
            'careers' => $this->careerService->getPaginatedCareers($search, $request->user()),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('careers.create', [
        'categories' => config('careers.categories', []),
        'locationSuggestions' => Career::query()
            ->whereNotNull('location')
            ->select('location')
            ->distinct()
            ->orderBy('location')
            ->pluck('location'),
    ]);
    }

    public function store(StoreCareerRequest $request): RedirectResponse
    {
        try {
            $this->careerService->createCareer($request->validated(), $request->user());

            return redirect()
                ->route('careers.index')
                ->with('success', 'Career created successfully.');
        } catch (\Throwable $exception) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong while creating the career. Please try again.');
        }
    }
}
