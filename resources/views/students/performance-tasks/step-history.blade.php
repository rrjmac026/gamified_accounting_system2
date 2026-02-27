@props(['performanceTask', 'step', 'submission' => null])

@php
    // Query the history table directly — more reliable than $submission->attempts
    $attemptCount = 0;
    if (auth()->check() && auth()->user()->student) {
        $attemptCount = \App\Models\PerformanceTaskSubmissionHistory::where([
            'task_id'    => $performanceTask->id,
            'student_id' => auth()->user()->student->id,
            'step'       => $step,
        ])->count();
    }
@endphp

@if($attemptCount > 0)
<div>
    <a href="{{ route('students.performance-tasks.step-history', ['id' => $performanceTask->id, 'step' => $step]) }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-white border-2 border-gray-300 text-gray-700 rounded-lg
              hover:bg-gray-50 hover:border-gray-400 hover:text-gray-900
              focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500
              transition-all duration-200 text-sm font-medium shadow-sm group">

        {{-- Clock / history icon --}}
        <svg class="w-4 h-4 text-indigo-500 group-hover:text-indigo-600 transition-colors"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>

        <span>View Attempt History</span>

        {{-- Attempt count badge --}}
        <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-bold
                     bg-indigo-100 text-indigo-700 group-hover:bg-indigo-200 transition-colors">
            {{ $attemptCount }} {{ Str::plural('attempt', $attemptCount) }}
        </span>

        {{-- Arrow --}}
        <svg class="w-4 h-4 text-gray-400 group-hover:translate-x-0.5 transition-transform"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>
</div>
@endif