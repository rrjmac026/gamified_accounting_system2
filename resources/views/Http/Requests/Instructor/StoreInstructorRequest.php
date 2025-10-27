<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreInstructorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Only admins can create instructors
        return Auth::check() && Auth::user()->role === 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            // User data
            'id_number' => 'required|string|max:20|unique:users,id_number',
            'full_name' => 'required|string|max:255|regex:/^[a-zA-Z\s\.\-\']+$/',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'is_active' => 'boolean',
            
            // Instructor specific data
            'employee_id' => 'required|string|max:20|unique:instructors,employee_id',
            'department' => 'required|string|max:100',
            'specialization' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     */
    public function attributes(): array
    {
        return [
            'id_number' => 'ID number',
            'full_name' => 'full name',
            'employee_id' => 'employee ID',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'full_name.regex' => 'The full name may only contain letters, spaces, dots, hyphens, and apostrophes.',
            'password.regex' => 'The password must contain at least one uppercase letter, one lowercase letter, and one number.',
            'employee_id.unique' => 'This employee ID is already registered.',
            'department.required' => 'Please specify the department.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->expectsJson()) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }
}
