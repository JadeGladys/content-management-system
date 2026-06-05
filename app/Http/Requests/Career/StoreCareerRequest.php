<?php

namespace App\Http\Requests\Career;

use App\Models\Career;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

class StoreCareerRequest extends FormRequest
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
            'department' => trim((string) $this->input('department', '')),
            'slug' => Str::slug($slugSource) ?: 'career',

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

        return [
            'action' => ['required', Rule::in(['save', 'publish', 'generate_seo'])],
            'title' => ['required', 'string', 'max:255', Rule::unique('careers', 'title')],
            'category' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('careers', 'slug')],
            'location' => [Rule::requiredIf($publishing), 'nullable', 'string', 'max:255'],
            'about' => [Rule::requiredIf($publishing), 'nullable', 'json'],
            'description' => [Rule::requiredIf($publishing), 'nullable', 'json'],
            'requirements' => [Rule::requiredIf($publishing), 'nullable', 'json'],
            'deadline' => [Rule::requiredIf($publishing), 'nullable', 'date'],

            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'meta_keywords' => ['nullable', 'string', 'max:320'],
            'canonical_url' => ['nullable', 'url', 'max:2048'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:320'],
            'no_index' => ['nullable', 'boolean'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            if (
                filled($this->input('slug')) &&
                Career::query()->where('slug', $this->input('slug'))->exists()
            ) {
                $validator->errors()->add(
                    'slug',
                    'This slug already exists. Update the title or edit the slug manually.'
                );
            }
        });
    }
}
