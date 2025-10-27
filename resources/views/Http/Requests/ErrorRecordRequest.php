<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ErrorRecordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'task_submission_id' => 'required|exists:task_submissions,id',
            'student_id' => 'required|exists:students,id',
            'error_type' => 'required|string|max:100',
            'error_description' => 'required|string',
            'question_id' => 'required|exists:task_questions,id',
            'frequency' => 'required|integer|min:1',
            'severity_level' => 'required|integer|between:1,5',
            'identified_at' => 'required|date'
        ];
    }
}
