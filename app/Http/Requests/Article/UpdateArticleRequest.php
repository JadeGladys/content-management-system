<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        $title = trim((string) $this->input('title', ''));
        $category = trim((string) $this->input('category', ''));
        $slugInput = $this->input('slug');
        $slugSource = blank($slugInput) ? $title : (string) $slugInput;

        $this->merge([
            'title' => $title,
            'category' => $category,
            'slug' => Str::slug($slugSource) ?: 'article',
            'tag_ids' => $this->input('tag_ids', []),
            'new_tags' => $this->input('new_tags', []),

            'meta_title' => trim((string) $this->input('meta_title', '')) ?: null,
            'meta_description' => trim((string) $this->input('meta_description', '')) ?: null,
            'meta_keywords' => trim((string) $this->input('meta_keywords', '')) ?: null,
            'canonical_url' => trim((string) $this->input('canonical_url', '')) ?: null,
            'og_title' => trim((string) $this->input('og_title', '')) ?: null,
            'og_description' => trim((string) $this->input('og_description', '')) ?: null,
            'no_index' => $this->boolean('no_index'),
        ]);
    }

    public function rules(): array
    {
        $publishing = $this->input('action') === 'publish';
        $article = $this->route('article');
        $articleId = is_object($article) ? $article->getKey() : $article;

        return [
            'action' => ['required', Rule::in(['save', 'publish', 'generate_seo'])],
            'title' => ['required', 'string', 'max:255', Rule::unique('articles', 'title')->ignore($articleId)],
            'category' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('articles', 'slug')->ignore($articleId)],
            
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['nullable', 'exists:tags,id'],
            'new_tags' => ['nullable', 'array'],
            'new_tags.*' => ['nullable', 'string', 'max:50'],
            
            'overview' => [Rule::requiredIf($publishing), 'nullable', 'string', 'max:255'],
            'content' => [Rule::requiredIf($publishing), 'nullable', 'json'],
            'featured_image_id' => ['nullable', 'exists:media,id'],
            'featured_image_upload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:10240'],

            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'canonical_url' => ['nullable', 'url', 'max:2048'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:320'],
            'og_image_id' => ['nullable', 'exists:media,id'],
            'no_index' => ['nullable', 'boolean'],
        ];
    }
}
