<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 

use App\Models\XpTransaction;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use App\Traits\Loggable;

class XpTransactionController extends Controller
{
    use Loggable;

    public function index()
    {
        $transactions = XpTransaction::with('student')
            ->latest('processed_at')
            ->paginate(10); // 👈 shows 10 records per page

        return view('admin.xp-transactions.index', compact('transactions'));
    }


    public function create()
    {
        $students = Student::all();
        $types = ['earned', 'bonus', 'penalty', 'adjustment'];
        $sources = ['task_completion', 'quiz_score', 'bonus_activity', 'manual'];
        return view('admin.xp-transactions.create', compact('students', 'types', 'sources'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|integer',
            'type' => 'required|in:earned,bonus,penalty,adjustment',
            'source' => 'required|in:task_completion,quiz_score,bonus_activity,manual',
            'source_id' => 'nullable|string',
            'description' => 'required|string',
            'processed_at' => 'required|date'
        ]);

        // Create the transaction
        $transaction = XpTransaction::create($validated);

        // Update student's total XP
        $student = Student::find($validated['student_id']);
        $student->increment('total_xp', $validated['amount']);

        // 🔥 Check and award badges automatically
        $badges = \App\Models\Badge::where('xp_threshold', '<=', $student->total_xp)
            ->where('is_active', true)
            ->get();

        foreach ($badges as $badge) {
            if (!$student->badges->contains($badge->id)) {
                $student->badges()->attach($badge->id, ['earned_at' => now()]);
            }
        }

        // ✅ Log activity
        $this->logActivity(
            "Created XP Transaction",
            "XpTransaction",
            $transaction->id,
            [
                'student_id' => $student->id,
                'amount' => $transaction->amount,
                'type' => $transaction->type,
                'source' => $transaction->source,
            ]
        );

        return redirect()->route('admin.xp-transactions.index')
            ->with('success', 'XP Transaction created successfully and badges updated!');
    }


    public function show(XpTransaction $xpTransaction)
    {
        return view('admin.xp-transactions.show', compact('xpTransaction'));
    }

    public function edit(XpTransaction $xpTransaction)
    {
        $students = Student::all();
        $types = ['earned', 'bonus', 'penalty', 'adjustment'];
        $sources = ['task_completion', 'quiz_score', 'bonus_activity', 'manual'];
        return view('admin.xp-transactions.edit', compact('xpTransaction', 'students', 'types', 'sources'));
    }

    public function update(Request $request, XpTransaction $xpTransaction)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|integer',
            'type' => 'required|in:earned,bonus,penalty,adjustment',
            'source' => 'required|in:task_completion,quiz_score,bonus_activity,manual',
            'source_id' => 'nullable|string',
            'description' => 'required|string',
            'processed_at' => 'required|date'
        ]);

        // Update transaction
        $xpTransaction->update($validated);

        // 🔄 Recalculate total XP for the student
        $student = Student::find($validated['student_id']);
        $student->total_xp = XpTransaction::where('student_id', $student->id)->sum('amount');
        $student->save();

        // 🔥 Recheck and award badges
        $badges = \App\Models\Badge::where('xp_threshold', '<=', $student->total_xp)
            ->where('is_active', true)
            ->get();

        foreach ($badges as $badge) {
            if (!$student->badges->contains($badge->id)) {
                $student->badges()->attach($badge->id, ['earned_at' => now()]);
            }
        }

        $this->logActivity(
            "Updated XP Transaction",
            "XpTransaction",
            $xpTransaction->id,
            [
                'student_id' => $student->id,
                'amount' => $validated['amount'],
                'type' => $validated['type'],
                'source' => $validated['source'],
            ]
        );
        $this->logActivity(
            "Deleted XP Transaction",
            "XpTransaction",
            $xpTransaction->id,
            ['description' => $xpTransaction->description]
        );


        return redirect()->route('admin.xp-transactions.index')
            ->with('success', 'XP Transaction updated successfully and badges updated!');
    }


    public function destroy(XpTransaction $xpTransaction)
    {
        $xpTransaction->delete();
        return redirect()->route('admin.xp-transactions.index')
            ->with('success', 'XP Transaction deleted successfully');
    }
}
