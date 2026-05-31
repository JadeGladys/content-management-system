<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'media_upload' => ['required', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:10240'],
        ];
    }
}
