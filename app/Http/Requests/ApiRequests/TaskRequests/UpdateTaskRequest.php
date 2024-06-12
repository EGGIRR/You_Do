<?php

namespace App\Http\Requests\ApiRequests\TaskRequests;

use App\Http\Requests\ApiRequest;

class UpdateTaskRequest extends ApiRequest
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
            'description' => 'string|max:255',
            'expired_date' => 'date',
            'important' => 'boolean',
            'card_id' => 'numeric|exists:cards,id',
        ];
    }
}
