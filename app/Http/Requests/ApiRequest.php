<?php

namespace App\Http\Requests;

use App\Exceptions\ApiException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ApiRequest extends FormRequest
{

    protected function failedValidation(Validator $validator)
    {
        throw new ApiException(422, 'Validation error', $validator->errors());
    }

    public function messages()
    {
        $messages = parent::messages();
        $messages += [
            'exists' => ':attribute does not exist'
        ];
        return $messages;
    }
}
