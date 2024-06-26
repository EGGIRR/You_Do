<?php

namespace App\Http\Requests\ApiRequests\TaskRequests;

use App\Http\Requests\ApiRequest;

class StoreTaskRequest extends ApiRequest
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
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'expired_date' => 'required|date',
            'important' => 'boolean',
            'card_id' => 'numeric|exists:cards,id|required',
        ];
    }
}
