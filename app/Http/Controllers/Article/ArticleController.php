<?php

namespace App\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\Article\StoreArticleRequest;
use App\Models\Article;
use App\Models\Media;
use App\Services\Article\ArticleService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
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

    public function create(): View
    {
        return view('articles.create', [
            'categories' => config('articles.categories', []),
            'mediaLibrary' => Media::query()
                ->latest()
                ->get(['id', 'file_name', 'file_path', 'file_type']),
        ]);
    }

    public function store(StoreArticleRequest $request): RedirectResponse
    {
        try {
            $this->articleService->createArticle(
                $request->validated(), 
                $request->user(),
                $request->file('featured_image_upload')
            );

            return redirect()
                ->route('articles.index')
                ->with('success', 'Article created successfully.');
        } catch (\Throwable $exception) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong while creating the article. Please try again.');
        }
    }
}
