<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreArticleComment extends FormRequest
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
        return [
            'body' => 'required|string',
            'blog_article_id' => 'required|exists:blog_articles,id',
            'type' => 'required|in:1,2',
            'parent_id' => 'required_if:type,2',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $isUser = auth('api')->check();
            $isAdmin = auth('admin')->check();

            $type = (int) $this->input('type');

            if ($type == 1) {
                if (!$isUser) {
                    $validator->errors()->add('auth', 'Only users can post main comments.');
                }
            }

            if ($type == 2) {
                if (!$isAdmin) {
                    $validator->errors()->add('auth', 'Only admins can reply to comments.');
                }
            }
        });
    }
}
