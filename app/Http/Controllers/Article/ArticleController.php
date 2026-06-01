<?php

namespace App\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\Article\StoreArticleRequest;
use App\Http\Requests\Article\UpdateArticleRequest;
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
            'categories' => config('articles.categories', []),
        ]);
    }

    public function store(StoreArticleRequest $request): RedirectResponse
    {
        try {
            $this->articleService->createArticle(
                $request->validated(),
                $request->user()
            );

            return redirect()
                ->route('articles.index')
                ->with('success', 'Article draft created successfully.');
        } catch (\Throwable $exception) {
            return redirect()
                ->route('articles.index')
                ->withInput()
                ->with('error', 'Something went wrong while creating the article draft. Please try again.');
        }
    }

    public function edit(Article $article): View|RedirectResponse
    {
        $guardResponse = $this->ensureEditableArticle($article, request()->user());

        if ($guardResponse) {
            return $guardResponse;
        }

        return view('articles.update', [
            'article' => $article,
            'pageTitle' => 'Edit Article',
            'pageHeading' => 'Edit article',
            'pageDescription' => 'Update the article details, adjust the slug and tags, then save or publish when ready.',
            'formAction' => route('articles.update', $article),
            'formMethod' => 'PUT',
            'categories' => config('articles.categories', []),
            'mediaLibrary' => Media::query()
                ->latest()
                ->get(['id', 'file_name', 'file_path', 'file_type', 'file_size']),
        ]);
    }

    public function update(UpdateArticleRequest $request, Article $article): RedirectResponse
    {
        $guardResponse = $this->ensureEditableArticle($article, $request->user());

        if ($guardResponse) {
            return $guardResponse;
        }

        try {
            $result = $this->articleService->updateArticle(
                $request->validated(),
                $article,
                $request->user(),
                $request->file('featured_image_upload')
            );

            $successMessage = $request->input('action') === 'publish'
                ? 'Article updated and published successfully.'
                : 'Article updated successfully.';

            if ($result['reused_existing_featured_image']) {
                $successMessage .= ' This image already exists in the media library, so the existing asset was reused.';
            }

            return redirect()
                ->route('articles.index')
                ->with('success', $successMessage);
        } catch (\Throwable $exception) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Something went wrong while updating the article. Please try again.');
        }
    }

    protected function ensureEditableArticle(Article $article, $actor): ?RedirectResponse
    {
        if ($article->status !== 'draft') {
            return redirect()
                ->route('articles.index')
                ->with('error', 'Only draft articles can be edited.');
        }

        if ($article->author !== $actor->id) {
            return redirect()
                ->route('articles.index')
                ->with('error', 'You can only edit your own draft articles.');
        }

        return null;
    }
}
