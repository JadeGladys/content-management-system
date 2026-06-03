<?php

namespace App\Http\Controllers\Article;

use App\Http\Controllers\Controller;
use App\Http\Requests\Article\StoreArticleRequest;
use App\Http\Requests\Article\UpdateArticleStatusRequest;
use App\Http\Requests\Article\UpdateArticleRequest;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\Media;
use App\Models\Tag;
use App\Services\Article\ArticleContentRenderer;
use App\Services\Article\ArticleService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function __construct(
        protected ArticleService $articleService,
        protected ArticleContentRenderer $articleContentRenderer
    ) {
    }

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        return view('articles.index', [
            'articles' => $this->articleService->getPaginatedArticles($search, $request->user()),
            'search' => $search,
            'categories' => ArticleCategory::query()
                ->orderBy('name')
                ->get(['id', 'name', 'slug']),
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
            'article' => $article->load(['category', 'tags', 'featuredImage']),
            'pageTitle' => $article->title,
            'pageHeading' => $article->title,
            'formAction' => route('articles.update', $article),
            'formMethod' => 'PUT',
            'categories' => ArticleCategory::query()
                ->orderBy('name')
                ->get(['id', 'name', 'slug']),
            'mediaLibrary' => Media::query()
                ->latest()
                ->get(['id', 'file_name', 'file_path', 'file_type', 'file_size']),
            'availableTags' => Tag::query()
                ->orderBy('name')
                ->get(['id', 'name', 'slug']),
        ]);
    }

    public function show(Article $article, Request $request): View|RedirectResponse
    {
        $guardResponse = $this->ensureViewableArticle($article, $request->user());

        if ($guardResponse) {
            return $guardResponse;
        }

        $article->load(['authorUser', 'category', 'featuredImage', 'tags']);

        return view('articles.show', [
            'article' => $article,
            'pageTitle' => $article->title,
            'pageHeading' => $article->title,
            'renderedContent' => $this->articleContentRenderer->render($article->content),
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

            $action = $request->input('action');

            $successMessage = $action === 'publish'
                ? 'Article updated and published successfully.'
                : ($action === 'generate_seo'
                    ? 'SEO fields generated successfully.'
                    : 'Article updated successfully.');

            if ($result['reused_existing_featured_image']) {
                $successMessage .= ' This image already exists in the media library, so the existing asset was reused.';
            }

            if ($action === 'generate_seo') {
                return redirect()
                    ->route('articles.edit', [
                        'article' => $article,
                        'tab' => 'seo',
                    ])
                    ->with('success', $successMessage);
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

    public function updateStatus(UpdateArticleStatusRequest $request, Article $article): RedirectResponse
    {
        $guardResponse = $this->ensureManageableArticle($article, $request->user());

        if ($guardResponse) {
            return $guardResponse;
        }

        $targetStatus = $request->validated('target_status');

        if (! $this->articleService->canTransitionStatus($article, $targetStatus)) {
            return redirect()
                ->route('articles.show', $article)
                ->with('error', 'That status change is not allowed for this article.');
        }

        try {
            $article = $this->articleService->transitionStatus(
                $article,
                $targetStatus,
                $request->user()
            );

            $successMessage = match ($targetStatus) {
                'archived' => 'Article archived successfully.',
                default => 'Article moved to draft successfully.',
            };

            if (
                $targetStatus === 'draft'
                && $request->user()->role === 'admin'
                && $article->author !== $request->user()->id
            ) {
                session([
                    'articles.allow_draft_preview_once' => $article->id,
                ]);
            }

            return redirect()
                ->route('articles.show', $article)
                ->with('success', $successMessage);
                
        } catch (\Throwable $exception) {
            return redirect()
                ->route('articles.show', $article)
                ->with('error', 'Something went wrong while changing the article status. Please try again.');
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

    protected function ensureViewableArticle(Article $article, $actor): ?RedirectResponse
    {
        if ($actor->role === 'admin') {
            if ($article->status !== 'draft' || $article->author === $actor->id) {
                return null;
            }

            if (session('articles.allow_draft_preview_once') === $article->id) {
                session()->forget('articles.allow_draft_preview_once');

                return null;
            }

            return redirect()
                ->route('articles.index')
                ->with('error', 'You can only view your own draft articles.');
        }

        if ($article->author !== $actor->id) {
            return redirect()
                ->route('articles.index')
                ->with('error', 'You can only view your own articles.');
        }

        return null;
    }

    protected function ensureManageableArticle(Article $article, $actor): ?RedirectResponse
    {
        if (! in_array($article->status, ['published', 'archived'], true)) {
            return redirect()
                ->route('articles.show', $article)
                ->with('error', 'Only published or archived articles can be managed from this view.');
        }

        if ($actor->role === 'admin') {
            return null;
        }

        if ($article->author !== $actor->id) {
            return redirect()
                ->route('articles.index')
                ->with('error', 'You can only manage your own articles.');
        }

        return null;
    }
}
