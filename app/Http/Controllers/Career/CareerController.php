<?php

namespace App\Http\Controllers\Career;

use App\Http\Controllers\Controller;
use App\Http\Requests\Career\StoreCareerRequest;
use App\Http\Requests\Career\UpdateCareerRequest;
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
            'categories' => config('careers.categories', []),
        ]);
    }

    public function store(StoreCareerRequest $request): RedirectResponse
    {
        try {
            $this->careerService->createCareer($request->validated(), $request->user());

            return redirect()
                ->route('careers.index')
                ->with('success', 'Career draft created successfully.');
        } catch (\Throwable $exception) {
            return redirect()
                ->route('careers.index')
                ->withInput()
                ->with('error', 'Something went wrong while creating the career draft. Please try again.');
        }
    }

    public function edit(Career $career): View|RedirectResponse
    {
        $guardResponse = $this->ensureEditableCareer($career, request()->user());

        if ($guardResponse) {
            return $guardResponse;
        }

        return view('careers.update', [
            'career' => $career,
            'pageTitle' => 'Edit Career',
            'pageHeading' => 'Edit career',
            'pageDescription' => 'Update the job details, adjust the slug and deadline, then save or publish when ready.',
            'formAction' => route('careers.update', $career),
            'formMethod' => 'PUT',
            'categories' => config('careers.categories', []),
            'locationSuggestions' => Career::query()
                ->whereNotNull('location')
                ->select('location')
                ->distinct()
                ->orderBy('location')
                ->pluck('location'),
        ]);
    }

    public function update(UpdateCareerRequest $request, Career $career): RedirectResponse
    {
        $guardResponse = $this->ensureEditableCareer($career, $request->user());

        if ($guardResponse) {
            return $guardResponse;
        }

        try {
            $updatedCareer = $this->careerService->updateCareer(
                $request->validated(),
                $career,
                $request->user()
            );

            $successMessage = $request->input('action') === 'publish'
                ? 'Career updated and published successfully.'
                : 'Career updated successfully.';

            return redirect()
                ->route('careers.index')
                ->with('success', $successMessage);
        } catch (\Throwable $exception) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the career. Please try again.');
        }
    }

    protected function ensureEditableCareer(Career $career, $actor): ?RedirectResponse
    {
        if ($career->status !== 'draft') {
            return redirect()
                ->route('careers.index')
                ->with('error', 'Only draft careers can be edited.');
        }

        if ($career->created_by !== $actor->id) {
            return redirect()
                ->route('careers.index')
                ->with('error', 'You can only edit your own draft careers.');
        }

        return null;
    }
}
