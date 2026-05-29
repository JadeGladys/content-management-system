<?php

namespace App\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use App\Services\Article\ArticleService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct(
        protected ArticleService $articleService
    ) {
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        return view('articles.index', [
            'articles' => $this->articleService->getPaginatedArticles($search, $request->user()),
            'search' => $search,
        ]);
    }
}
