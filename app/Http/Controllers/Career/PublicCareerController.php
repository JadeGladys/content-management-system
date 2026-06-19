<?php

namespace App\Http\Controllers\Career;

use App\Http\Controllers\Controller;
use App\Services\Career\PublicCareerService;
use Illuminate\Http\JsonResponse;

class PublicCareerController extends Controller
{
    public function __construct(
        private readonly PublicCareerService $publiccareerService
    ) {}

    public function index(): JsonResponse
    {
        $careers = $this->publiccareerService->getPublishedCareers();

        return response()->json([
            'success' => true,
            'data' => $careers,
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