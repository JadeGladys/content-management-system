<?php

namespace App\Http\Controllers\Career;

use App\Http\Controllers\Controller;
use App\Http\Requests\Career\StoreCareerRequest;
use App\Http\Requests\Career\UpdateCareerRequest;
use App\Http\Requests\Career\UpdateCareerStatusRequest;
use App\Models\Career;
use App\Models\CareerCategory;
use App\Services\Career\CareerContentRenderer;
use App\Services\Career\CareerService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CareerController extends Controller
{
    public function __construct(
        protected CareerService $careerService,
        protected CareerContentRenderer $careerContentRenderer
    ) {
    }

    protected const FILTER_FIELD_LABELS = [
        'category' => 'Category',
        'type' => 'Type',
        'location' => 'Location',
        'employment_type' => 'Employment type',
        'work_mode' => 'Work mode',
    ];

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $filterKeys = array_keys(self::FILTER_FIELD_LABELS);
        $filters = collect($filterKeys)
            ->mapWithKeys(fn ($key) => [
                $key => collect((array) $request->input($key, []))
                    ->filter()
                    ->values()
                    ->all(),
            ])
            ->all();

        $hasActiveFilters = collect($filters)->contains(fn ($values) => ! empty($values));

        $categories = CareerCategory::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $filterOptions = [
            'category' => $categories
                ->map(fn ($category) => ['value' => $category->slug, 'label' => $category->name])
                ->all(),
            'type' => $this->mapOptions(Career::TYPES),
            'location' => $this->distinctColumnOptions('location'),
            'employment_type' => $this->mapOptions(Career::EMPLOYMENT_TYPES),
            'work_mode' => $this->mapOptions(Career::WORK_MODES),
        ];

        $filterFields = collect(self::FILTER_FIELD_LABELS)
            ->map(fn ($label, $key) => [
                'key' => $key,
                'label' => $label,
                'placeholder' => "Select {$label}",
                'options' => $filterOptions[$key],
                'selected' => $filters[$key] ?? [],
            ])
            ->values()
            ->all();

        return view('careers.index', [
            'careers' => $this->careerService->getPaginatedCareers($search, $filters, $request->user()),
            'search' => $search,
            'filters' => $filters,
            'hasActiveFilters' => $hasActiveFilters,
            'filterFields' => $filterFields,
            'categories' => $categories,
            'searchSuggestions' => collect($this->distinctColumnOptions('title'))->pluck('value')
                ->merge(collect($this->distinctColumnOptions('slug'))->pluck('value'))
                ->merge($categories->pluck('name'))
                ->merge(array_values(Career::TYPES))
                ->merge(array_values(Career::EMPLOYMENT_TYPES))
                ->merge(array_values(Career::WORK_MODES))
                ->filter()
                ->unique()
                ->values()
                ->all(),
        ]);
    }

    protected function distinctColumnOptions(string $column): array
    {
        return Career::query()
            ->whereNotNull($column)
            ->select($column)
            ->distinct()
            ->orderBy($column)
            ->pluck($column)
            ->map(fn ($value) => ['value' => $value, 'label' => $value])
            ->all();
    }

    protected function mapOptions(array $options): array
    {
        return collect($options)
            ->map(fn ($label, $value) => ['value' => $value, 'label' => $label])
            ->values()
            ->all();
    }

    public function create(Request $request): View
    {
        return $this->careerFormView(
            new Career([
                'status' => 'draft',
                'no_index' => false,
            ]),
            [
                'pageTitle' => 'Create Career',
                'pageHeading' => 'Create Career',
                'pageDescription' => 'Start a new career entry, save it as a draft, or publish it once all required details are complete.',
                'formAction' => route('careers.store'),
                'formMethod' => 'POST',
            ]
        );
    }

    public function store(StoreCareerRequest $request): RedirectResponse
    {
        try {
            $career = $this->careerService->createCareer($request->validated(), $request->user());

            $action = $request->input('action');
            $successMessage = $action === 'publish'
                ? 'Career created and published successfully.'
                : ($action === 'generate_seo'
                    ? 'Career created and SEO fields generated successfully.'
                    : 'Career created successfully.');

            if ($action === 'generate_seo') {
                return redirect()
                    ->route('careers.edit', [
                        'career' => $career,
                        'tab' => 'seo',
                    ])
                    ->with('success', $successMessage);
            }

            return redirect()
                ->route('careers.index')
                ->with('success', $successMessage);
        } catch (\Throwable $exception) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong while creating the article. Please try again.');
        }
    }

    public function edit(Career $career): View|RedirectResponse
    {
        $guardResponse = $this->ensureEditableCareer($career, request()->user());

        if ($guardResponse) {
            return $guardResponse;
        }

        return $this->careerFormView(
            $career->load('category'),
            [
                'pageTitle' => $career->title,
                'pageHeading' => $career->title,
                'pageDescription' => 'Update the job details, adjust the slug and deadline, then save or publish when ready.',
                'formAction' => route('careers.update', $career),
                'formMethod' => 'PUT',
            ]
        );
    }

    public function show(Career $career, Request $request): View|RedirectResponse
    {
        $guardResponse = $this->ensureViewableCareer($career, $request->user());

        if ($guardResponse) {
            return $guardResponse;
        }

        $career->load(['createdBy', 'category']);

        return view('careers.show', [
            'career' => $career,
            'pageTitle' => $career->title,
            'pageHeading' => $career->title,
            'renderedOverview' => $this->careerContentRenderer->render($career->overview),
            'renderedDescription' => $this->careerContentRenderer->render($career->description),
            'renderedRequirements' => $this->careerContentRenderer->render($career->requirements),
        ]);
    }

    public function update(UpdateCareerRequest $request, Career $career): RedirectResponse
    {
        $guardResponse = $this->ensureEditableCareer($career, $request->user());

        if ($guardResponse) {
            return $guardResponse;
        }

        try {
            $this->careerService->updateCareer(
                $request->validated(),
                $career,
                $request->user()
            );

            $action = $request->input('action');

            $successMessage = $request->input('action') === 'publish'
                ? 'Career updated and published successfully.'
                : ($action === 'generate_seo'
                    ? 'SEO fields generated successfully.'
                    : 'Career updated successfully.');

            if ($action === 'generate_seo') {
                return redirect()
                    ->route('careers.edit', [
                        'career' => $career,
                        'tab' => 'seo',
                    ])
                    ->with('success', $successMessage);
            }

            return redirect()
                ->route('careers.index')
                ->with('success', $successMessage);
        } catch (\Throwable $exception) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong while creating the article. Please try again.');
        }
    }

    public function updateStatus(UpdateCareerStatusRequest $request, Career $career): RedirectResponse
    {
        $guardResponse = $this->ensureManageableCareer($career, $request->user());

        if ($guardResponse) {
            return $guardResponse;
        }

        $targetStatus = $request->validated('target_status');

        if (! $this->careerService->canTransitionStatus($career, $targetStatus)) {
            return redirect()
                ->route('careers.show', $career)
                ->with('error', 'That status change is not allowed for this career.');
        }

        try {
            $career = $this->careerService->transitionStatus(
                $career,
                $targetStatus,
                $request->user()
            );

            $successMessage = match ($targetStatus) {
                'closed' => 'career closed successfully.',
                default => 'career moved to draft successfully.',
            };

            if (
                $targetStatus === 'draft'
                && $request->user()->role === 'admin'
                && $career->created_by !== $request->user()->id
            ) {
                session([
                    'careers.allow_draft_preview_once' => $career->id,
                ]);
            }

            return redirect()
                ->route('careers.show', $career)
                ->with('success', $successMessage);

        } catch (\Throwable $exception) {
            return redirect()
                ->route('careers.show', $career)
                ->with('error', 'Something went wrong while creating the article. Please try again.');
        }
    }

    public function destroy(Career $career, Request $request): RedirectResponse
    {
        $guardResponse = $this->ensureEditableCareer($career, $request->user());

        if ($guardResponse) {
            return $guardResponse;
        }

        try {
            $this->careerService->deleteCareer($career, $request->user());

            return redirect()
                ->route('careers.index')
                ->with('success', 'Career deleted successfully.');
        } catch (\Throwable $exception) {
            return redirect()
                ->route('careers.edit', $career)
                ->with('error', 'Something went wrong while creating the article. Please try again.');
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

    protected function ensureViewableCareer(Career $career, $actor): ?RedirectResponse
    {
        if ($actor->role === 'admin') {
            if ($career->status !== 'draft' || $career->created_by === $actor->id) {
                return null;
            }

            if (session('careers.allow_draft_preview_once') === $career->id) {
                session()->forget('careers.allow_draft_preview_once');

                return null;
            }

            return redirect()
                ->route('careers.index')
                ->with('error', 'You can only view your own draft careers.');
        }

        if ($career->created_by !== $actor->id) {
            return redirect()
                ->route('careers.index')
                ->with('error', 'You can only view your own careers.');
        }

        return null;
    }

    protected function ensureManageableCareer(Career $career, $actor): ?RedirectResponse
    {
        if (! in_array($career->status, ['published', 'closed'], true)) {
            return redirect()
                ->route('careers.show', $career)
                ->with('error', 'Only published or closed careers can be managed from this view.');
        }

        if ($actor->role === 'admin') {
            return null;
        }

        if ($career->created_by !== $actor->id) {
            return redirect()
                ->route('careers.index')
                ->with('error', 'You can only manage your own careers.');
        }

        return null;
    }

    protected function careerFormView(Career $career, array $pageConfig): View
    {
        return view('careers.update', array_merge($pageConfig, [
            'career' => $career,
            'categories' => CareerCategory::query()
                ->orderBy('name')
                ->get(['id', 'name', 'slug']),
            'careerTypes' => Career::TYPES,
            'employmentTypes' => Career::EMPLOYMENT_TYPES,
            'workModes' => Career::WORK_MODES,
            'locationSuggestions' => Career::query()
                ->whereNotNull('location')
                ->select('location')
                ->distinct()
                ->orderBy('location')
                ->pluck('location'),
        ]));
    }
}
