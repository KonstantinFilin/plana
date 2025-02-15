<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaskGroupRequest extends FormRequest
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
            'name' => 'required|string|between:3,250',
            'abbr' => 'nullable|string|between:1,5',
            'pid' => 'nullable|integer',
        ];
    }
}
