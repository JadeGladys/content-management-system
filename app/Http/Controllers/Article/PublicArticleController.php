<?php

namespace App\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use App\Services\Article\PublicArticleService;
use Illuminate\Http\JsonResponse;

class PublicArticleController extends Controller
{
    public function __construct(
        private readonly PublicArticleService $articleService
    ) {}

    public function index(): JsonResponse
    {
        $articles = $this->articleService->getPublishedArticles();

        return response()->json([
            'success' => true,
            'data' => $articles,
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $article = $this->articleService->getPublishedArticleBySlug($slug);

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