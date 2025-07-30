<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogArticle extends FormRequest
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
                'title_' . $key             => 'required|string|max:255',
                'excerpt_' . $key           => 'required|string|max:500',
                'content_' . $key           => 'required|string',
                'meta_title_' . $key        => 'required|string|max:255',
                'meta_description_' . $key  => 'required|string|max:500',
            ];
        })->merge([
            'blog_category_id' => 'required|exists:blog_categories,id',
            'status'           => 'required|in:0,1',
            'images'     => 'required|array|min:2',
            'images.*' => 'image',

            'keywords'         => 'nullable|array',
            'keywords.*'       => 'required|array',
        ])->merge(
            collect(locales())->flatMap(function ($value, $key) {
                return [
                    'keywords.*.' . $key => 'required|string|max:255',
                ];
            })
        )->all();
    }
}
