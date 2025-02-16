<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use App\Models\Task;

class TaskRequest extends FormRequest
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
            'group_id' => 'nullable|integer|exists:' . TBL_TASK_GROUP . ',id',
            'short' => 'required|string|between:3,250',
            'description' => 'nullable|string:5,15000',
            'duration_plan' => 'required|integer|between:5,250',
            'duration_fact' => 'nullable|integer|between:5,250',
            'priority' => 'required|string|size:1|in:' . implode(',', Task::priorityList()),
            'sum_paid' => 'nullable|integer|between:100,10000',
            'sum_rest' => 'nullable|integer|between:100,10000',
            'plan_dt' => 'nullable|date_format:Y-m-d',
            'plan_time' => 'nullable|date_format:H:i',
            'dt_closed' => 'nullable|date_format:Y-m-d',
        ];
    }
}
