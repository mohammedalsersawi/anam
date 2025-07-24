<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SuccessUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return collect(locales())->mapWithKeys(function ($value, $key) {
            return [
                'title_' . $key           => 'required|string',
            ];
        })->merge([
            'url_video'     => 'required|url',
            'images'     => 'nullable|array|min:2',
            'images.*'   => 'image',
        ])->all();
    }
}
