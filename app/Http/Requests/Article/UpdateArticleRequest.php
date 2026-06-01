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
        ]);
    }

    public function rules(): array
    {
        $article = $this->route('article');
        $articleId = is_object($article) ? $article->getKey() : $article;

        return [
            'action' => ['required', Rule::in(['save', 'publish'])],
            'title' => ['required', 'string', 'max:255', Rule::unique('articles', 'title')->ignore($articleId)],
            'category' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('articles', 'slug')->ignore($articleId)],
            'tag_ids' => ['nullable', 'array'],
            'tag_ids.*' => ['nullable', 'exists:tags,id'],
            'new_tags' => ['nullable', 'array'],
            'new_tags.*' => ['nullable', 'string', 'max:50'],
            'overview' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'json'],
            'featured_image_id' => ['nullable', 'exists:media,id'],
            'featured_image_upload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:10240'],
        ];
    }
}
