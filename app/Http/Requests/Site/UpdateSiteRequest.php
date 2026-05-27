<?php

namespace App\Http\Requests\Site;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSiteRequest extends FormRequest
{
    protected $errorBag = 'updateSite';

    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        $site = $this->route('site');

        return [
            'name' => ['required', 'string', 'max:255'],
            'domain' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('sites', 'domain')->ignore($site),
                'regex:/^(?!-)([A-Za-z0-9-]{1,63}\\.)+[A-Za-z]{2,}$/',
            ],
            'status' => ['required', 'in:active,inactive'],
            'assigned_user_ids' => ['nullable', 'array'],
            'assigned_user_ids.*' => [
                'string',
                Rule::exists('users', 'id')->where(fn ($query) => $query->where('role', 'editor')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'domain.regex' => 'Enter a valid domain name like example.com.',
        ];
    }
}
