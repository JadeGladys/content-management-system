<?php

namespace App\Services\Career;

use Illuminate\Support\HtmlString;

class CareerContentRenderer
{
    public function render(?array $document): HtmlString
    {
        if (empty($document['content']) || ! is_array($document['content'])) {
            return new HtmlString(
                '<p class="text-base leading-8 text-slate-500">No career content has been added yet.</p>'
            );
        }

        return new HtmlString($this->renderNodes($document['content']));
    }

    protected function renderNodes(array $nodes): string
    {
        return collect($nodes)
            ->map(fn ($node) => is_array($node) ? $this->renderNode($node) : '')
            ->implode('');
    }

    protected function renderNode(array $node): string
    {
        return match ($node['type'] ?? null) {
            'doc' => $this->renderNodes($node['content'] ?? []),
            'paragraph' => $this->renderParagraph($node),
            'heading' => $this->renderHeading($node),
            'bulletList' => $this->renderList($node, false),
            'orderedList' => $this->renderList($node, true),
            'listItem' => $this->renderListItem($node),
            'blockquote' => $this->renderBlockquote($node),
            'text' => $this->renderText($node),
            default => $this->renderNodes($node['content'] ?? []),
        };
    }

    protected function renderParagraph(array $node): string
    {
        $content = $this->renderInlineNodes($node['content'] ?? []);

        if ($content === '') {
            return '';
        }

        return sprintf(
            '<p class="text-base leading-8 text-slate-700">%s</p>',
            $content
        );
    }

    protected function renderHeading(array $node): string
    {
        $level = (int) ($node['attrs']['level'] ?? 2);
        $content = $this->renderInlineNodes($node['content'] ?? []);

        if ($content === '') {
            return '';
        }

        $tag = in_array($level, [2, 3], true) ? "h{$level}" : 'h2';
        $classes = $tag === 'h3'
            ? 'mb-4 mt-10 text-2xl font-semibold tracking-tight text-slate-900'
            : 'mb-5 mt-12 text-3xl font-semibold tracking-tight text-slate-900';

        return sprintf('<%1$s class="%2$s">%3$s</%1$s>', $tag, $classes, $content);
    }

    protected function renderList(array $node, bool $ordered): string
    {
        $items = $this->renderNodes($node['content'] ?? []);

        if ($items === '') {
            return '';
        }

        $tag = $ordered ? 'ol' : 'ul';
        $classes = $ordered
            ? 'mb-8 list-decimal space-y-3 pl-6 text-base leading-8 text-slate-700'
            : 'mb-8 list-disc space-y-3 pl-6 text-base leading-8 text-slate-700';

        return sprintf('<%1$s class="%2$s">%3$s</%1$s>', $tag, $classes, $items);
    }

    protected function renderListItem(array $node): string
    {
        $content = collect($node['content'] ?? [])
            ->map(function ($child) {
                if (! is_array($child)) {
                    return '';
                }

                if (($child['type'] ?? null) === 'paragraph') {
                    return $this->renderInlineNodes($child['content'] ?? []);
                }

                return $this->renderNode($child);
            })
            ->filter()
            ->implode('');

        if ($content === '') {
            return '';
        }

        return sprintf('<li class="pl-1">%s</li>', $content);
    }

    protected function renderBlockquote(array $node): string
    {
        $content = $this->renderNodes($node['content'] ?? []);

        if ($content === '') {
            return '';
        }

        return sprintf(
            '<blockquote class="mb-8 rounded-r-3xl border-l-4 border-blue-200 bg-blue-50/60 px-6 py-1 text-lg italic leading-8 text-slate-700">%s</blockquote>',
            $content
        );
    }

    protected function renderInlineNodes(array $nodes): string
    {
        return collect($nodes)
            ->map(fn ($node) => is_array($node) ? $this->renderNode($node) : '')
            ->implode('');
    }

    protected function renderText(array $node): string
    {
        $text = e((string) ($node['text'] ?? ''));

        foreach ($node['marks'] ?? [] as $mark) {
            $text = match ($mark['type'] ?? null) {
                'bold' => sprintf('<strong class="font-semibold text-slate-900">%s</strong>', $text),
                'italic' => sprintf('<em>%s</em>', $text),
                default => $text,
            };
        }

        return $text;
    }
}
