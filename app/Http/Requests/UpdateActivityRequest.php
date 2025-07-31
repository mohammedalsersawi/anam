<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateActivityRequest extends FormRequest
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
                'title_' . $key       => 'required|string|max:255',
                'description_' . $key => 'required|string',
                'facebook_url_' . $key => 'required|url',
                'instagram_url_' . $key => 'required|url',
                'button_text_' . $key => 'required|string',
            ];
        })->merge([
            'status'           => 'required|in:0,1',
            'image'         => 'nullable|image',
        ])->all();
    }
}
