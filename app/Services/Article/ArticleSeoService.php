<?php

namespace App\Services\Article;

use App\Models\Article;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ArticleSeoService
{
    public function buildPayload(
        array $data,
        Article $article,
        array $tagNames = [],
        ?string $featuredImageId = null,
        bool $forceGenerate = false
    ): array {
        $generatedFields = [];
        $metaTitle = $data['meta_title'] ?? null;

        if ($forceGenerate || ! filled($metaTitle)) {
            $metaTitle = $article->title;
            $generatedFields[] = 'meta_title';
        }

        $metaDescription = $data['meta_description'] ?? null;

        if ($forceGenerate || ! filled($metaDescription)) {
            $metaDescription = $this->generateMetaDescription(
                $data['overview'] ?? $article->overview,
                $data['content'] ?? $article->content
            );
            $generatedFields[] = 'meta_description';
        }

        $metaKeywords = $data['meta_keywords'] ?? null;

        if ($forceGenerate || ! filled($metaKeywords)) {
            $metaKeywords = $this->generateMetaKeywords($article, $tagNames);
            $generatedFields[] = 'meta_keywords';
        }

        $ogTitle = $data['og_title'] ?? null;

        if ($forceGenerate || ! filled($ogTitle)) {
            $ogTitle = $metaTitle;
            $generatedFields[] = 'og_title';
        }

        $ogDescription = $data['og_description'] ?? null;

        if ($forceGenerate || ! filled($ogDescription)) {
            $ogDescription = $metaDescription;
            $generatedFields[] = 'og_description';
        }

        $ogImageId = $data['og_image_id'] ?? null;

        if ($forceGenerate || ! filled($ogImageId)) {
            $ogImageId = $featuredImageId ?? $article->featured_image_id;
            $generatedFields[] = 'og_image_id';
        }

        return [
            'payload' => [
                'meta_title' => $metaTitle,
                'meta_description' => $metaDescription,
                'meta_keywords' => $metaKeywords,
                'canonical_url' => filled($data['canonical_url'] ?? null)
                    ? $data['canonical_url']
                    : null,
                'og_title' => $ogTitle,
                'og_description' => $ogDescription,
                'og_image_id' => $ogImageId,
                'no_index' => (bool) ($data['no_index'] ?? false),
            ],
            'generated_fields' => $generatedFields,
        ];
    }

    public function logGeneratedFields(Article $article, User $actor, array $generatedFields, string $context): void
    {
        if ($generatedFields === []) {
            return;
        }

        Log::info('Article SEO defaults generated.', [
            'actor_id' => $actor->id,
            'article_id' => $article->id,
            'context' => $context,
            'generated_fields' => $generatedFields,
        ]);
    }

    protected function generateMetaDescription(?string $overview, array|string|null $content): ?string
    {
        if (filled($overview)) {
            return Str::limit(trim($overview), 160, '');
        }

        $plainText = $this->extractContentPlainText($content);

        return filled($plainText)
            ? Str::limit($plainText, 160, '')
            : null;
    }

    protected function generateMetaKeywords(Article $article, array $tagNames = []): ?string
    {
        $stopWords = [
            'a', 'an', 'and', 'are', 'as', 'at', 'be', 'but', 'by', 'for', 'from', 'how',
            'in', 'into', 'is', 'it', 'its', 'just', 'not', 'of', 'on', 'or', 'that',
            'the', 'this', 'to', 'was', 'what', 'when', 'where', 'why', 'with',
        ];

        $titleKeywords = collect(preg_split('/[^a-z0-9]+/i', $article->title))
            ->map(fn ($word) => trim((string) $word))
            ->filter(function ($word) use ($stopWords) {
                return filled($word)
                    && Str::length($word) > 2
                    && ! in_array(Str::lower($word), $stopWords, true);
            })
            ->map(fn ($word) => Str::title(Str::lower($word)));

        $keywords = collect($tagNames)
            ->merge([$article->category?->name])
            ->merge(
                $titleKeywords->reject(function ($keyword) use ($tagNames, $article) {
                    $existingKeywords = collect($tagNames)
                        ->merge([$article->category?->name])
                        ->filter()
                        ->map(fn ($value) => Str::lower(trim((string) $value)));

                    return $existingKeywords->contains(Str::lower($keyword));
                })->take(2)
            )
            ->map(fn ($keyword) => trim(preg_replace('/\s+/', ' ', (string) $keyword) ?? ''))
            ->filter()
            ->unique(fn ($keyword) => Str::lower($keyword))
            ->values()
            ->take(12)
            ->implode(', ');

        return filled($keywords) ? $keywords : null;
    }

    protected function extractContentPlainText(array|string|null $content): string
    {
        $contentArray = is_string($content)
            ? json_decode($content, true)
            : $content;

        if (! is_array($contentArray)) {
            return '';
        }

        return trim($this->flattenTextNodes($contentArray));
    }

    protected function flattenTextNodes(array $node): string
    {
        $text = '';

        if (isset($node['text']) && is_string($node['text'])) {
            $text .= ' ' . $node['text'];
        }

        if (! empty($node['content']) && is_array($node['content'])) {
            foreach ($node['content'] as $childNode) {
                if (is_array($childNode)) {
                    $text .= ' ' . $this->flattenTextNodes($childNode);
                }
            }
        }

        return preg_replace('/\s+/', ' ', $text) ?? '';
    }
}
