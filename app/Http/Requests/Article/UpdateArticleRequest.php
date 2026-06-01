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
        $slugInput = $this->input('slug');
        $slugSource = blank($slugInput) ? $title : (string) $slugInput;

        $this->merge([
            'title' => $title,
            'slug' => Str::slug($slugSource) ?: 'article',
        ]);
    }

    public function rules(): array
    {
        $article = $this->route('article');
        $articleId = is_object($article) ? $article->getKey() : $article;

        return [
            'action' => ['required', Rule::in(['save', 'publish'])],
            'title' => ['required', 'string', 'max:255', Rule::unique('articles', 'title')->ignore($articleId)],
            'category' => ['required', 'string', Rule::in(config('articles.categories', []))],
            'slug' => ['required', 'string', 'max:255', Rule::unique('articles', 'slug')->ignore($articleId)],
            'tags' => ['nullable', 'string', 'max:255'],
            'overview' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'json'],
            'featured_image_id' => ['nullable', 'exists:media,id'],
            'featured_image_upload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:10240'],
        ];
    }
}
