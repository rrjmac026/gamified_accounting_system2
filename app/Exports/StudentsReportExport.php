<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsReportExport implements FromCollection, WithHeadings, WithStyles
{
    protected $students;
    protected $stats;

    public function __construct($students, $stats)
    {
        $this->students = $students;
        $this->stats = $stats;
    }

    public function collection()
    {
        return $this->students->map(function ($student) {
            return [
                'Name' => $student->user->name,
                'Total XP' => number_format($student->total_xp),
                'Tasks Completed' => $student->assignedTasks->where('status', 'completed')->count(),
                'Performance Rating' => number_format($student->performance_rating, 1) . '%',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Student Name',
            'Total XP',
            'Tasks Completed',
            'Performance Rating'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}