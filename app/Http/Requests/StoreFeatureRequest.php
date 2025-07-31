<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFeatureRequest extends FormRequest
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
        $rules = collect(locales())->mapWithKeys(function ($value, $key) {
            return [
                'title_' . $key => 'required|string|max:255',
            ];
        })->merge([
            'status' => 'required|in:0,1',
            'image'  => 'required|image',

            // التحقق من أن items مصفوفة


            // التحقق من كل عنصر داخل items
            'items.*.sub_title'   => 'required|array',
            'items.*.description' => 'required|array',
            'items.*.icon'        => 'nullable|string',
            'items.*.button_text' => 'nullable|array',
            'items.*.button_link' => 'nullable|string|url',
        ]);

        // نضيف التحقق من اللغات داخل الحقول المترجمة
        foreach (locales() as $localeKey => $language) {
            $rules['items.*.sub_title.' . $localeKey]   = 'required|string|max:255';
            $rules['items.*.description.' . $localeKey] = 'required|string';
            $rules['items.*.button_text.' . $localeKey] = 'nullable|string|max:100';
        }

        return $rules->all();
    }
}
