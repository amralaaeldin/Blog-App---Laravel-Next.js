<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class StoreCommentRequest extends FormRequest
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
            'body' => 'required|string|max:2500',
        ];
    }
}
