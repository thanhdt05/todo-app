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
            "name" => "required|string|max:255",
            "email" => "required|email|string|max:255|unique:users,email",
            "password" => "required|string|min:8|max:255",
        ];
    }

    public function messages(): array
    {
        return [
            "name.required" => "Name is required",
            "name.max" => "Name must be at most 255 characters long",
            "email.required" => "Email is required",
            "email.max" => "Email must be at most 255 characters long",
            "email.email" => "Email is invalid",
            "email.unique" => "Email already exists",
            "password.required" => "Password is required",
            "password.min" => "Password must be at least 8 characters long",
            "password.max" => "Password must be at most 255 characters long",
        ];
    }

    public function attributes(): array
    {
        return [
            "name" => "Name",
            "email" => "Email",
            "password" => "Password",
        ];
    }
}
