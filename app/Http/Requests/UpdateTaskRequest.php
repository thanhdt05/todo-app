<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:255',
            'due_date' => 'sometimes|date',
            'status' => 'sometimes|in:todo,doing,done',
        ];
    }

    public function messages(): array {
        return [
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự',
            'description.max' => 'Mô tả không được vượt quá 255 ký tự',
            'due_date.date' => 'Ngày hết hạn không hợp lệ',
            'status.in' => 'Trạng thái không hợp lệ',
        ];
    }

    public function attributes(): array {
        return [
            'title' => 'Tiêu đề',
            'description' => 'Mô tả',
            'due_date' => 'Ngày hết hạn',
            'status' => 'Trạng thái',
        ];
    }
}
