<?php

namespace App\Http\Requests\Career;

use App\Models\Career;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;
use Illuminate\Support\Str;

class StoreCareerRequest extends FormRequest
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
            'department' => trim((string) $this->input('department', '')),
            'slug' => Str::slug($title) ?: 'career',
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255', Rule::unique('careers', 'title')],
            'category' => ['required', 'string', Rule::in(config('careers.categories', []))],
            'department' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
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
                    'title',
                    'This title creates a slug that already exists. Choose a different title.'
                );
            }
        });
    }
}
