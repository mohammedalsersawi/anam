<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCourseRequest extends FormRequest
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
            ];
        })->merge([
            'price'         => 'required|numeric|min:0',
            'rating'        => 'nullable|numeric|between:0,5',
            'rating_count'  => 'nullable|integer|min:0',
            'hours'         => 'required|integer|min:1',
            'status'        => 'required|in:0,1',
            'age_from'      => 'required|integer|min:0|lte:age_to',
            'age_to'        => 'required|integer|min:0|gte:age_from',
            'category_id'   => 'required|exists:categories,id',
            'image'         => 'required|image',
        ])->all();
    }
}
