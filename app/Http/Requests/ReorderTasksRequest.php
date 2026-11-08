<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReorderTasksRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'task_ids' => ['present', 'array', 'list'],
            'task_ids.*' => ['required', 'integer', 'distinct'],
            'version' => ['required', 'integer', 'min:0'],
        ];
    }
}
