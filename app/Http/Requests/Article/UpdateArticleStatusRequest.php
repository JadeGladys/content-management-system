<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateArticleStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'target_status' => trim((string) $this->input('target_status', '')),
        ]);
    }

    public function rules(): array
    {
        return [
            'target_status' => ['required', Rule::in(['draft', 'archived'])],
        ];
    }
}
