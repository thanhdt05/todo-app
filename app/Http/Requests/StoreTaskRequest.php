<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'due_date' => 'required|date',
        ];
    }

    public function messages(): array {
        return [
            'title.required' => 'Tiêu đề không được để trống',
            'title.max' => 'Tiêu đề không được vượt quá 255 ký tự',
            'description.required' => 'Mô tả không được để trống',
            'description.max' => 'Mô tả không được vượt quá 255 ký tự',
            'due_date.required' => 'Ngày hết hạn không được để trống',
            'due_date.date' => 'Ngày hết hạn không hợp lệ',
        ];
    }

    public function attributes(): array {
        return [
            'title' => 'Tiêu đề',
            'description' => 'Mô tả',
            'due_date' => 'Ngày hết hạn',
        ];
    }
}
