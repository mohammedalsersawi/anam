<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTestRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return collect(locales())->mapWithKeys(function ($value, $key) {
            return [
                'title_' . $key       => 'required|string|max:255',
                'description_' . $key => 'nullable|string',
            ];
        })->merge([
            'price'            => 'required|numeric|min:0',
            'rating'           => 'nullable|numeric|between:0,5',
            'questions_count'  => 'required|integer|min:1',
            'age_from'         => 'required|integer|min:0|lte:age_to',
            'age_to'           => 'required|integer|min:0|gte:age_from',
            'status'           => 'required|in:0,1',
            'rating_count'     => 'nullable|integer|min:0',
            'category_id'      => 'required|exists:categories,id',
            'image'         => 'required|image',
        ])->all();
    }
}
