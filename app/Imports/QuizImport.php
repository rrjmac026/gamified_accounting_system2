<?php

namespace App\Imports;

use App\Models\Quiz;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuizImport implements ToModel, WithHeadingRow
{
    protected $taskId;

    public function __construct($taskId)
    {
        $this->taskId = $taskId;
    }

    public function model(array $row)
    {
        $options = null;

        if (!empty($row['options'])) {
            $options = explode(';', $row['options']); // convert to array
        }

        return new Quiz([
            'task_id'        => $this->taskId,
            'type'           => strtolower(trim($row['type'])),
            'question_text'  => $row['question_text'],
            'options'        => $options,
            'correct_answer' => $row['correct_answer'],
            'points'         => 1, // default
        ]);
    }
}
