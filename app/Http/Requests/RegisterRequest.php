<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|string|max:255|unique:users,email',
            'password' => 'required|string|min:8|max:255',
            'password_confirmation' => 'required|string|min:8|max:255|same:password',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên không được để trống',
            'name.max' => 'Tên không được vượt quá 255 ký tự',
            'email.required' => 'Email không được để trống',
            'email.max' => 'Email không được vượt quá 255 ký tự',
            'email.email' => 'Email không hợp lệ',
            'email.unique' => 'Email đã tồn tại',
            'password.required' => 'Password không được để trống',
            'password.min' => 'Password phải có ít nhất 8 ký tự',
            'password.max' => 'Password không được vượt quá 255 ký tự',
            'password_confirmation.required' => 'Xác nhận password không được để trống',
            'password_confirmation.min' => 'Xác nhận password phải có ít nhất 8 ký tự',
            'password_confirmation.max' => 'Xác nhận password không được vượt quá 255 ký tự',
            'password_confirmation.same' => 'Xác nhận password không khớp',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'password_confirmation' => 'Password Confirmation',
        ];
    }
}
