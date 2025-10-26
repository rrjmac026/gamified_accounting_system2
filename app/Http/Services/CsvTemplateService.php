<?php

namespace App\Services;

use App\Models\Quiz;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvTemplateService
{
    /**
     * Generate and download a CSV template file
     */
    public function downloadTemplate(Quiz $quiz): StreamedResponse
    {
        $headers = $quiz->getTemplateHeaders();
        $filename = $this->generateTemplateFilename($quiz);

        $response = new StreamedResponse(function () use ($headers) {
            $handle = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel compatibility
            fwrite($handle, "\xEF\xBB\xBF");
            
            // Write headers
            fputcsv($handle, $headers);
            
            // Add sample data rows
            $this->writeSampleData($handle, $headers);
            
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

        return $response;
    }

    /**
     * Write sample data to the CSV template
     */
    private function writeSampleData($handle, array $headers): void
    {
        $sampleData = $this->generateSampleData($headers);
        
        foreach ($sampleData as $row) {
            fputcsv($handle, $row);
        }
    }

    /**
     * Generate sample data based on headers
     */
    private function generateSampleData(array $headers): array
    {
        // Generate only one row of sample data
        return [array_map(function($header) {
            return $this->getSampleValueForHeader($header, 1);
        }, $headers)];
    }

    /**
     * Get sample value for a specific header type
     */
    private function getSampleValueForHeader(string $header, int $index): string
    {
        $headerLower = strtolower(trim($header));
        
        switch (true) {
            case str_contains($headerLower, 'student id') || str_contains($headerLower, 'id number'):
                return '2024001';
                
            case str_contains($headerLower, 'student name') || str_contains($headerLower, 'name'):
                return 'John Doe';
                
            case str_contains($headerLower, 'subject') && str_contains($headerLower, 'code'):
                return 'CS101';
                
            case str_contains($headerLower, 'section'):
                return 'Section A';
                
            case str_contains($headerLower, 'question'):
                return 'What is the answer to this question?';
                
            case str_contains($headerLower, 'answer'):
                return 'Sample Answer';
                
            case str_contains($headerLower, 'point'):
                return '10';
                
            case str_contains($headerLower, 'email'):
                return 'student@example.com';
                
            case str_contains($headerLower, 'grade') || str_contains($headerLower, 'score'):
                return '85';
                
            default:
                return 'Sample Data';
        }
    }

    /**
     * Generate template filename
     */
    private function generateTemplateFilename(Quiz $quiz): string
    {
        $taskName = $quiz->task->title ?? 'Quiz';
        $templateName = $quiz->template_name ?? 'Template';
        
        $filename = $taskName . '_' . $templateName . '_Template';
        $filename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $filename);
        
        return $filename . '.csv';
    }

    /**
     * Validate uploaded CSV against template
     */
    public function validateCsvStructure($filePath, Quiz $quiz): array
    {
        $errors = [];
        
        try {
            $handle = fopen($filePath, 'r');
            
            if (!$handle) {
                return ['Unable to read the uploaded file.'];
            }

            // Read the first line (headers)
            $headers = fgetcsv($handle);
            fclose($handle);

            if (!$headers) {
                return ['The uploaded file appears to be empty or invalid.'];
            }

            // Validate headers
            if (!$quiz->validateCsvHeaders($headers)) {
                $errors[] = $quiz->getCsvHeaderValidationError($headers);
            }

            // Additional validations
            $templateHeaders = $quiz->getTemplateHeaders();
            
            if (count($headers) !== count($templateHeaders)) {
                $errors[] = "Expected " . count($templateHeaders) . " columns, but found " . count($headers) . " columns.";
            }

        } catch (\Exception $e) {
            $errors[] = 'Error reading file: ' . $e->getMessage();
        }

        return $errors;
    }

    /**
     * Get preview data for template
     */
    public function getTemplatePreview(array $headers): array
    {
        return [
            'headers' => $headers,
            'sample_data' => $this->generateSampleData($headers)
        ];
    }
}