<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAboutBlockRequest extends FormRequest
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
        $rules = [];

        $rules['type'] = 'required|in:hero,vision,mission,goals';
        $rules['image'] = 'required|image';
        foreach (locales() as $locale => $lang) {
            $rules["title_$locale"] = 'required_unless:type,goals|string|max:255';
            $rules["description_$locale"] = 'required_unless:type,goals|string';
        }
        $rules['items'] = 'required_unless:type,goals,hero|array|min:1';
        $rules['items.*'] = 'required_unless:type,goals,hero|array';

        foreach (locales() as $locale => $lang) {
            $rules["items.*.$locale"] = 'required_unless:type,goals,hero|string';
        }

        return $rules;
    }
}
