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
            'department' => trim((string) $this->input('department', '')),
            'slug' => Str::slug($slugSource) ?: 'career',
        ]);
    }

    public function rules(): array
    {
        $publishing = $this->input('action') === 'publish';
        $career = $this->route('career');
        $careerId = is_object($career) ? $career->getKey() : $career;

        return [
            'action' => ['required', Rule::in(['save', 'publish'])],
            'title' => ['required', 'string', 'max:255', Rule::unique('careers', 'title')->ignore($careerId)],
            'category' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('careers', 'slug')->ignore($careerId)],
            'location' => [Rule::requiredIf($publishing), 'nullable', 'string', 'max:255'],
            'about' => [Rule::requiredIf($publishing), 'nullable', 'json'],
            'description' => [Rule::requiredIf($publishing), 'nullable', 'json'],
            'requirements' => [Rule::requiredIf($publishing), 'nullable', 'json'],
            'deadline' => [Rule::requiredIf($publishing), 'nullable', 'date'],
        ];
    }
}
