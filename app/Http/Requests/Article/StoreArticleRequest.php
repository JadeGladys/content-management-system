<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', Rule::in(config('articles.categories', []))],
            'tags' => ['nullable', 'string', 'max:255'],
            'overview' => ['nullable', 'string', 'max:255'],
            'content' => ['nullable', 'json'],
            'featured_image_id' => ['nullable', 'exists:media,id'],
            'featured_image_upload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:10240'],
        ];
    }
}
