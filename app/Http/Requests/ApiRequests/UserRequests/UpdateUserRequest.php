<?php

namespace App\Http\Requests\ApiRequests\UserRequests;

use App\Http\Requests\ApiRequest;

class UpdateUserRequest extends ApiRequest
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
            'name' => 'string|max:255',
            'email' => 'email|max:255|unique:users,email,',
            'password' => 'string|min:8',
        ];
    }
}
