<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = [
        'task_id',
        'question_text',
        'type',
        'correct_answer',
        'points',
        'options',
        'template_name',
        'template_description',
        'csv_template_headers'
    ];

    protected $casts = [
        'options' => 'array',
        'csv_template_headers' => 'array'
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function answers()
    {
        return $this->hasMany(StudentAnswer::class);
    }

    /**
     * Get the default CSV template headers
     */
    public static function getDefaultTemplateHeaders()
    {
        return [
            'Student ID Number',
            'Student Name', 
            'Subject Code',
            'Section',
            'Question',
            'Answer',
            'Points'
        ];
    }

    /**
     * Get template headers or default if none set
     */
    public function getTemplateHeaders()
    {
        return $this->csv_template_headers ?? self::getDefaultTemplateHeaders();
    }

    /**
     * Check if CSV headers match the template
     */
    public function validateCsvHeaders(array $csvHeaders)
    {
        $templateHeaders = $this->getTemplateHeaders();
        
        // Normalize headers (trim whitespace, convert to lowercase for comparison)
        $normalizedCsvHeaders = array_map(fn($h) => strtolower(trim($h)), $csvHeaders);
        $normalizedTemplateHeaders = array_map(fn($h) => strtolower(trim($h)), $templateHeaders);
        
        return $normalizedCsvHeaders === $normalizedTemplateHeaders;
    }

    /**
     * Get validation error message for mismatched headers
     */
    public function getCsvHeaderValidationError(array $csvHeaders)
    {
        $templateHeaders = $this->getTemplateHeaders();
        
        return "CSV headers don't match the expected template. Expected: " . 
               implode(', ', $templateHeaders) . 
               ". Found: " . implode(', ', $csvHeaders);
    }
}