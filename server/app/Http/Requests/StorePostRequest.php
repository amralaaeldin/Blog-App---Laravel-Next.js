<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function failedValidation(Validator $validator)
    {
        throw new \App\Exceptions\BadRequestException($validator->errors()->toJson());
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:2500',
            'tagNames' => 'array|max:10'
        ];
    }
}
