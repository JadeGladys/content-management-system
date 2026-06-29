<?php

namespace App\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use App\Services\Article\PublicArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicArticleController extends Controller
{
    protected const FILTER_KEYS = [
        'category',
        'type',
    ];

    public function __construct(
        private readonly PublicArticleService $publicarticleService
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

        $articles = $this->publicarticleService->getPublishedArticles($search, $filters);

        return response()->json([
            'success' => true,
            'data' => $articles,
        ]);
    }

    public function filters(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->publicarticleService->getFilterOptions(),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $article = $this->publicarticleService->getPublishedArticleBySlug($slug);

        if (! $article) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $article,
        ]);
    }
}