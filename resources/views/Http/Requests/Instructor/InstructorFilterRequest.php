<?php

namespace App\Http\Requests\Instructor;

use Illuminate\Foundation\Http\FormRequest;

class InstructorFilterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'department' => 'nullable|string|max:100',
            'specialization' => 'nullable|string|max:100',
            'search' => 'nullable|string|max:100',
            'has_subjects' => 'nullable|in:yes,no',
            'sort_by' => 'nullable|in:full_name,email,employee_id,department,specialization,created_at',
            'sort_order' => 'nullable|in:asc,desc',
            'per_page' => 'nullable|integer|min:5|max:100',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'per_page.min' => 'Results per page must be at least 5.',
            'per_page.max' => 'Results per page cannot exceed 100.',
            'sort_by.in' => 'Invalid sort field selected.',
            'sort_order.in' => 'Sort order must be either ascending or descending.',
            'has_subjects.in' => 'Invalid filter option for subjects.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Set defaults
        $this->merge([
            'sort_by' => $this->sort_by ?? 'created_at',
            'sort_order' => $this->sort_order ?? 'desc',
            'per_page' => $this->per_page ?? 15,
        ]);

        // Clean search term
        if ($this->has('search')) {
            $this->merge([
                'search' => trim($this->search)
            ]);
        }
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
                    'message' => 'Filter validation failed',
                    'errors' => $validator->errors()
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }
}
