<?php

namespace App\Services\Career;

use App\Models\Career;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CareerSeoService
{
    public function buildPayload(
        array $data,
        Career $career,
        bool $forceGenerate = false
    ): array {
        $generatedFields = [];
        $metaTitle = $data['meta_title'] ?? null;

        if ($forceGenerate || ! filled($metaTitle)) {
            $metaTitle = $career->title;
            $generatedFields[] = 'meta_title';
        }

        $metaDescription = $data['meta_description'] ?? null;

        if ($forceGenerate || ! filled($metaDescription)) {
            $metaDescription = $this->generateMetaDescription(
                $data['overview'] ?? $career->overview,
                $data['description'] ?? $career->description,
                $data['requirements'] ?? $career->requirements
            );
            $generatedFields[] = 'meta_description';
        }

        $metaKeywords = $data['meta_keywords'] ?? null;

        if ($forceGenerate || ! filled($metaKeywords)) {
            $metaKeywords = $this->generateMetaKeywords($career);
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
                'no_index' => (bool) ($data['no_index'] ?? false),
            ],
            'generated_fields' => $generatedFields,
        ];
    }

    public function logGeneratedFields(Career $career, User $actor, array $generatedFields, string $context): void
    {
        if ($generatedFields === []) {
            return;
        }

        Log::info('Career SEO defaults generated.', [
            'actor_id' => $actor->id,
            'career_id' => $career->id,
            'context' => $context,
            'generated_fields' => $generatedFields,
        ]);
    }

    protected function generateMetaDescription(
        array|string|null $overview,
        array|string|null $description,
        array|string|null $requirements
    ): ?string
    {
        foreach ([$overview, $description, $requirements] as $section) {
            $plainText = $this->extractContentPlainText($section);

            if (filled($plainText)) {
                return Str::limit($plainText, 160, '');
            }
        }

        return null;
    }

    protected function generateMetaKeywords(Career $career): ?string
    {
        $stopWords = [
            'a', 'an', 'and', 'are', 'as', 'at', 'be', 'but', 'by', 'for', 'from', 'how',
            'in', 'into', 'is', 'it', 'its', 'just', 'not', 'of', 'on', 'or', 'that',
            'the', 'this', 'to', 'was', 'what', 'when', 'where', 'why', 'with',
        ];

        $extractKeywords = function (array|string|null $content) use ($stopWords) {
            $plainText = $this->extractContentPlainText($content);

            return collect(preg_split('/[^a-z0-9]+/i', $plainText))
                ->map(fn ($word) => trim((string) $word))
                ->filter(function ($word) use ($stopWords) {
                    return filled($word)
                        && Str::length($word) > 2
                        && ! in_array(Str::lower($word), $stopWords, true);
                })
                ->map(fn ($word) => Str::title(Str::lower($word)));
        };

        $titleKeywords = collect(preg_split('/[^a-z0-9]+/i', $career->title))
            ->map(fn ($word) => trim((string) $word))
            ->filter(function ($word) use ($stopWords) {
                return filled($word)
                    && Str::length($word) > 2
                    && ! in_array(Str::lower($word), $stopWords, true);
            })
            ->map(fn ($word) => Str::title(Str::lower($word)));

        $contentKeywords = $extractKeywords($career->overview)
            ->merge($extractKeywords($career->description))
            ->merge($extractKeywords($career->requirements));

        $existingKeywords = collect([$career->category?->name])
            ->filter()
            ->merge($titleKeywords->take(2))
            ->map(fn ($value) => Str::lower(trim((string) $value)));

        $keywords = collect([$career->category?->name])
            ->filter()
            ->merge($titleKeywords->take(2))
            ->merge(
                $contentKeywords->reject(function ($keyword) use ($existingKeywords) {
                    return $existingKeywords->contains(Str::lower($keyword));
                })->take(9)
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
