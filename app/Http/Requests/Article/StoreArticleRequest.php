<?php

namespace App\Http\Requests\Article;

use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        $title = trim((string) $this->input('title', ''));

        $this->merge([
            'title' => $title,
            'slug' => Str::slug($title) ?: 'article',
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', Rule::unique('articles', 'title')],
            'category' => ['required', 'string', Rule::in(config('articles.categories', []))],
            'slug' => ['required', 'string', 'max:255'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (
                filled($this->input('slug')) &&
                Article::query()->where('slug', $this->input('slug'))->exists()
            ) {
                $validator->errors()->add(
                    'title',
                    'This title creates a slug that already exists. Choose a different title.'
                );
            }
        });
    }
}
