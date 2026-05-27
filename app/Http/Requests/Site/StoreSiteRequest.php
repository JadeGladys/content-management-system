<?php

namespace App\Http\Requests\Site;

use Illuminate\Foundation\Http\FormRequest;

class StoreSiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'domain' => [
                'nullable',
                'string',
                'max:255',
                'unique:sites,domain',
                'regex:/^(?!-)([A-Za-z0-9-]{1,63}\.)+[A-Za-z]{2,}$/',
            ],
            'status' => ['required', 'in:active,inactive'],
        ];
    }
    public function messages(): array
    {
        return [
            'domain.regex' => 'Enter a valid domain name like example.com.',
        ];
    }
}