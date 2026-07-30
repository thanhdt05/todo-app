<?php

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexTaskRequest extends FormRequest
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
            'keyword' => 'nullable|string|max:255',
            'status' => ['nullable', Rule::enum(TaskStatus::class)],
            'priority' => ['nullable', Rule::enum(TaskPriority::class)],
            'sort' => ['nullable', Rule::in(['due_date', 'created_at', 'priority', 'title'])],
            'direction' => ['nullable', Rule::in(['asc', 'desc'])],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
            'page' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'keyword.max' => 'Từ khóa tìm kiếm không được vượt quá 255 ký tự',
            'status.enum' => 'Trạng thái không hợp lệ',
            'per_page.integer' => 'Số lượng công việc mỗi trang phải là số nguyên',
            'per_page.min' => 'Số lượng công việc mỗi trang phải ít nhất là 1',
            'per_page.max' => 'Số lượng công việc mỗi trang không được vượt quá 100',
            'page.integer' => 'Số trang phải là số nguyên',
            'page.min' => 'Số trang phải ít nhất là 1',
        ];
    }

    public function attributes(): array
    {
        return [
            'keyword' => 'Từ khóa tìm kiếm',
            'status' => 'Trạng thái',
            'per_page' => 'Số lượng công việc mỗi trang',
            'page' => 'Số trang',
        ];
    }
}
