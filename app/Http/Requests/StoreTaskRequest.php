<?php

namespace App\Http\Requests;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'due_date' => ['nullable', 'date', 'after_or_equal:today'],
            'status' => ['nullable', Rule::enum(TaskStatus::class)],
            'priority' => ['nullable', Rule::enum(TaskPriority::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề không được để trống',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự',
            'description.max' => 'Mô tả không được vượt quá 255 ký tự',
            'due_date.date' => 'Ngày hết hạn không hợp lệ',
            'due_date.after_or_equal' => 'Ngày hết hạn phải lớn hơn hoặc bằng thời gian hiện tại',
            'status.enum' => 'Trạng thái không hợp lệ',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'Tiêu đề',
            'description' => 'Mô tả',
            'due_date' => 'Ngày hết hạn',
        ];
    }
}
