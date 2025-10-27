<?php

namespace App\Services;

use App\Models\XpTransaction;
use App\Models\Student;

class XpEngine
{
    public function award(
    int $studentId,
    int $amount,
    string $type,
    string $source,
    ?int $sourceId = null,
    ?string $description = null
    ) {
        // Ensure amount can be negative for deductions
        XpTransaction::create([
            'student_id'  => $studentId,
            'amount'      => $amount,
            'type'        => $type,
            'source'      => $source,
            'source_id'   => $sourceId,
            'description' => $description,
            'processed_at'=> now()
        ]);

        $student = Student::find($studentId);
        if ($student) {
            if ($amount >= 0) {
                $student->increment('total_xp', $amount);
            } else {
                
                $student->decrement('total_xp', abs($amount));
                if ($student->total_xp < 0) {
                    $student->total_xp = 0;
                    $student->save();
                }
            }
        }
    }

}
