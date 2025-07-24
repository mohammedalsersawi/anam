<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactInfoRequest extends FormRequest
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
                'sub_title_' . $key       => 'required|string',
                'description_' . $key     => 'required|string',
                'sub_description_' . $key => 'required|string',
                'address_' . $key         => 'required|string|max:1000',
            ];
        })->merge([
            'email'     => 'required|email',
            'phone'     => 'required|regex:/^05[69]\d{7}$/',
            'phone_alt' => 'nullable|regex:/^05[69]\d{7}$/',
            'whatsapp'  => 'required|url',
            'facebook'  => 'required|url',
            'instagram' => 'required|url',
            'youtube'   => 'required|url',

            'images'     => 'required|array|min:2',
            'images.*'   => 'image',
        ])->all();
    }
}
