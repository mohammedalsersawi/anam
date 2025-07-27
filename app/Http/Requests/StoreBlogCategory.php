<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogCategory extends FormRequest
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
                'name_' . $key       => 'required|string|max:255',
                'description_' . $key => 'nullable|string',
            ];
        })->merge([
            'status'        => 'required|in:0,1',
        ])->all();
    }
}
