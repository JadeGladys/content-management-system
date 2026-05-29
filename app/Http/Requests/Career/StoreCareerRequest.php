<?php

namespace App\Http\Requests\Career;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCareerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', Rule::in(config('careers.categories', []))],
            'location' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'about' => ['required', 'json'],
            'description' => ['nullable', 'json'],
            'requirements' => ['nullable', 'json'],
            'deadline' => ['nullable', 'date'],
        ];
    }
}
