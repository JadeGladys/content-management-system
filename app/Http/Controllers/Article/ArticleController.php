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

    protected const FILTER_FIELD_LABELS = [
        'category' => 'Category',
        'type' => 'Type',
        'status' => 'Status',
    ];

    protected const ARTICLE_TYPES = [
        'article' => 'Article',
        'case_study' => 'Case Study',
        'capability_sheet' => 'Capability Sheet',
        'whitepaper' => 'Whitepaper',
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

        $categories = ArticleCategory::query()
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $filterOptions = [
            'category' => $categories
                ->map(fn ($category) => ['value' => $category->slug, 'label' => $category->name])
                ->all(),
            'type' => $this->mapOptions(self::ARTICLE_TYPES),
            'status' => $this->distinctColumnOptions('status'),
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

        return view('articles.index', [
            'articles' => $this->articleService->getPaginatedArticles($search, $filters, $request->user()),
            'search' => $search,
            'filters' => $filters,
            'hasActiveFilters' => $hasActiveFilters,
            'filterFields' => $filterFields,
            'categories' => $categories,
            'searchSuggestions' => collect($this->distinctColumnOptions('title'))->pluck('value')
                ->merge(collect($this->distinctColumnOptions('slug'))->pluck('value'))
                ->merge($categories->pluck('name'))
                ->merge(array_values(self::ARTICLE_TYPES))
                ->filter()
                ->unique()
                ->values()
                ->all(),
        ]);
    }

    protected function distinctColumnOptions(string $column): array
    {
        return Article::query()
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
        $article = new Article([
            'status' => 'draft',
            'no_index' => false,
        ]);

        $article->setRelation('tags', collect());

        return $this->articleFormView($article, [
            'pageTitle' => 'Create Article',
            'pageHeading' => 'Create Article',
            'formAction' => route('articles.store'),
            'formMethod' => 'POST',
        ]);
    }

    public function store(StoreArticleRequest $request): RedirectResponse
    {
        try {
            $result = $this->articleService->createArticle(
                $request->validated(),
                $request->user(),
                $request->file('featured_image_upload')
            );

            $article = $result['article'];
            $action = $request->input('action');

            $successMessage = $action === 'publish'
                ? 'Article created and published successfully.'
                : ($action === 'generate_seo'
                    ? 'Article created and SEO fields generated successfully.'
                    : 'Article created successfully.');

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
                ->with('error', 'Something went wrong while creating the article. Please try again.');
        }
    }

    public function edit(Article $article): View|RedirectResponse
    {
        $guardResponse = $this->ensureEditableArticle($article, request()->user());

        if ($guardResponse) {
            return $guardResponse;
        }

        return $this->articleFormView(
            $article->load(['category', 'tags', 'featuredImage']),
            [
                'pageTitle' => $article->title,
                'pageHeading' => $article->title,
                'formAction' => route('articles.update', $article),
                'formMethod' => 'PUT',
            ]
        );
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

    public function destroy(Article $article, Request $request): RedirectResponse
    {
        $guardResponse = $this->ensureEditableArticle($article, $request->user());

        if ($guardResponse) {
            return $guardResponse;
        }

        try {
            $this->articleService->deleteArticle($article, $request->user());

            return redirect()
                ->route('articles.index')
                ->with('success', 'Article deleted successfully.');
        } catch (\Throwable $exception) {
            return redirect()
                ->route('articles.edit', $article)
                ->with('error', 'Something went wrong while deleting the article. Please try again.');
        }
    }

    protected function articleFormView(Article $article, array $pageConfig): View
    {
        return view('articles.update', array_merge($pageConfig,[
            'article' => $article,
            'articleTypes' => self::ARTICLE_TYPES,
            'categories' => ArticleCategory::query()
                ->orderBy('name')
                ->get(['id', 'name', 'slug']),
            'mediaLibrary' => Media::query()
                ->latest()
                ->get(['id', 'file_name', 'file_path', 'file_type', 'file_size']),
            'availableTags' => Tag::query()
                ->orderBy('name')
                ->get(['id', 'name', 'slug']),
        ]));
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
