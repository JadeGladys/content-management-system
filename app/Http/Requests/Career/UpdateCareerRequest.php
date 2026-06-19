<?php

namespace App\Http\Requests\Career;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class UpdateCareerRequest extends FormRequest
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
            'slug' => Str::slug($slugSource) ?: 'career',
            'application_url' => trim((string) $this->input('application_url', '')) ?: null,
            'location' => trim((string) $this->input('location', '')) ?: null,

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
        $career = $this->route('career');
        $careerId = is_object($career) ? $career->getKey() : $career;

        return [
            'action' => ['required', Rule::in(['save', 'publish', 'generate_seo'])],
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('careers', 'slug')->ignore($careerId)],
            'type' => [Rule::requiredIf($publishing), 'nullable', 'string', Rule::in([ 'security', 'corporate', 'technology', ])],
            'employment_type' => [Rule::requiredIf($publishing), 'nullable', 'string', Rule::in([ 'full_time', 'part_time', 'contract', 'internship', 'temporary', ])],
            'work_mode' => [Rule::requiredIf($publishing), 'nullable', 'string', Rule::in([ 'onsite', 'remote', 'hybrid', ])],
            'application_url' => [Rule::requiredIf($publishing), 'nullable', 'url', 'max:2048'],
            'location' => [Rule::requiredIf($publishing), 'nullable', 'string', 'max:255'],
            'overview' => ['nullable', 'json'],
            'description' => [Rule::requiredIf($publishing), 'nullable', 'json'],
            'requirements' => [Rule::requiredIf($publishing), 'nullable', 'json'],
            'deadline' => ['nullable', 'date'],

            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'meta_keywords' => ['nullable', 'string', 'max:320'],
            'canonical_url' => ['nullable', 'url', 'max:2048'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:320'],
            'no_index' => ['nullable', 'boolean'],
        ];
    }

    protected function withValidator($validator): void
    {
        $validator->after(function ($validator) {

            if (
                $this->input('action') === 'publish'
                && filled($this->deadline)
                && now()->greaterThan($this->deadline)
            ) {
                $validator->errors()->add(
                    'deadline',
                    'Cannot publish a career with an expired deadline.'
                );
            }

        });
    }
}
