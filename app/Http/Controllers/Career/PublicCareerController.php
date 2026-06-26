<?php

namespace App\Http\Controllers\Career;

use App\Http\Controllers\Controller;
use App\Services\Career\PublicCareerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicCareerController extends Controller
{
    protected const FILTER_KEYS = [
        'category',
        'type',
        'employment_type',
        'work_mode',
        'location',
    ];

    public function __construct(
        private readonly PublicCareerService $publiccareerService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $search = $request->string('search')->toString();

        $filters = collect(self::FILTER_KEYS)
            ->mapWithKeys(fn ($key) => [
                $key => collect((array) $request->input($key, []))
                    ->filter()
                    ->values()
                    ->all(),
            ])
            ->all();

        $careers = $this->publiccareerService->getPublishedCareers($search, $filters);

        return response()->json([
            'success' => true,
            'data' => $careers,
        ]);
    }

    public function filters(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->publiccareerService->getFilterOptions(),
        ]);
    }


    public function show(string $slug): JsonResponse
    {
        $career = $this->publiccareerService->getPublishedCareerBySlug($slug);

        if (! $career) {
            return response()->json([
                'success' => false,
                'message' => 'Career not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $career,
        ]);
    }
}