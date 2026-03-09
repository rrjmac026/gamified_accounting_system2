<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/jspreadsheet-ce@4.13.4/dist/index.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jspreadsheet-ce@4.13.4/dist/jspreadsheet.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/jsuites/dist/jsuites.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsuites/dist/jsuites.css" />

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Breadcrumb --}}
        <div class="flex items-center gap-2 text-sm text-gray-500 mb-4 flex-wrap">
            <a href="{{ route('instructors.performance-tasks.submissions.index') }}" class="hover:text-blue-600">All Submissions</a>
            <span>/</span>
            <a href="{{ route('instructors.performance-tasks.submissions.show', $task->id) }}" class="hover:text-blue-600">{{ $task->title }}</a>
            <span>/</span>
            <a href="{{ route('instructors.performance-tasks.submissions.show-student', ['task' => $task->id, 'student' => $student->id]) }}" class="hover:text-blue-600">{{ $student->name }}</a>
            <span>/</span>
            <span class="text-gray-900">Step {{ $step }} Comparison</span>
        </div>

        {{-- Header --}}
        <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900">{{ $stepTitle }} — Answer Comparison</h2>
                <p class="text-sm text-gray-500 mt-1">Step {{ $step }} · {{ $student->name }}</p>

                {{-- Exercise badge (if submission used an exercise) --}}
                @if($exercise)
                    <span class="inline-flex items-center gap-1.5 mt-2 px-3 py-1 text-xs font-semibold rounded-full bg-pink-100 text-[#D5006D] border border-pink-200">
                        <i class="fas fa-file-alt text-xs"></i>
                        Exercise: {{ $exercise->title }}
                    </span>
                @elseif($answerSheet)
                    <span class="inline-flex items-center gap-1.5 mt-2 px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 border border-blue-200">
                        <i class="fas fa-key text-xs"></i>
                        Legacy Answer Sheet
                    </span>
                @endif
            </div>

            <a href="{{ route('instructors.performance-tasks.submissions.show-student', ['task' => $task->id, 'student' => $student->id]) }}"
               class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Submissions
            </a>
        </div>

        {{-- No answer key warning --}}
        @unless($hasAnswerKey)
            <div class="mb-6 p-4 bg-yellow-50 border border-yellow-200 rounded-lg flex items-start gap-3">
                <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5"></i>
                <div>
                    <p class="text-sm font-semibold text-yellow-800">No answer key found for this step.</p>
                    <p class="text-xs text-yellow-700 mt-0.5">
                        The student submitted this step but no exercise or answer sheet is linked.
                        @if($submission && $submission->exercise_id)
                            Exercise ID {{ $submission->exercise_id }} may have been deleted.
                        @endif
                    </p>
                </div>
            </div>
        @endunless

        {{-- ── STUDENT ANSWER ─────────────────────────────────────────────── --}}
        @if($submission)
            <div class="mb-6 bg-red-50 rounded-xl border border-red-200 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-red-200">
                    <h3 class="text-base font-semibold text-red-900 flex items-center gap-2">
                        <i class="fas fa-user-graduate text-red-500"></i>
                        {{ $student->name }}'s Answer
                    </h3>
                    <div class="flex items-center gap-3">
                        {{-- Status badge --}}
                        <span class="px-3 py-1 rounded-full text-xs font-semibold
                            @if($submission->status === 'correct') bg-green-100 text-green-800
                            @elseif($submission->status === 'passed') bg-blue-100 text-blue-800
                            @elseif($submission->status === 'wrong') bg-red-100 text-red-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ ucfirst($submission->status) }}
                        </span>
                        <span class="text-sm font-bold text-red-900">
                            Score: {{ $submission->score }} / {{ $task->max_score / 10 }}
                        </span>
                    </div>
                </div>

                <div class="p-6">
                    @if($submission->submission_data)
                        <div class="border border-red-200 rounded-lg bg-white overflow-hidden">
                            <div id="student-sheet" class="overflow-x-auto"></div>
                        </div>
                    @else
                        <p class="text-sm text-red-700 italic">No spreadsheet data recorded for this submission.</p>
                    @endif

                    <div class="mt-4 grid grid-cols-2 gap-4 text-sm text-red-800">
                        <div class="space-y-1">
                            <p><span class="font-semibold">Attempts:</span> {{ $submission->attempts }} / {{ $task->max_attempts }}</p>
                            <p><span class="font-semibold">Submitted:</span> {{ $submission->created_at->format('M d, Y g:i A') }}</p>
                            @if($submission->updated_at != $submission->created_at)
                                <p><span class="font-semibold">Last updated:</span> {{ $submission->updated_at->format('M d, Y g:i A') }}</p>
                            @endif
                        </div>
                        <div>
                            @if($submission->remarks)
                                <p class="font-semibold mb-1">System Remarks:</p>
                                <p class="text-xs leading-relaxed">{{ $submission->remarks }}</p>
                            @endif
                        </div>
                    </div>

                    @if($submission->instructor_feedback)
                        <div class="mt-4 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                            <div class="flex items-center justify-between mb-2">
                                <h4 class="text-sm font-semibold text-purple-900 flex items-center gap-1">
                                    <i class="fas fa-comment-dots text-purple-500 text-xs"></i> Your Feedback
                                </h4>
                                <span class="text-xs text-purple-600">
                                    {{ $submission->feedback_given_at?->format('M d, Y g:i A') }}
                                </span>
                            </div>
                            <p class="text-sm text-purple-800 whitespace-pre-wrap">{{ $submission->instructor_feedback }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @else
            <div class="mb-6 bg-gray-50 rounded-xl border border-gray-200 p-10 text-center">
                <i class="fas fa-inbox text-4xl text-gray-300 mb-3"></i>
                <h3 class="text-base font-semibold text-gray-700">No Submission Yet</h3>
                <p class="text-sm text-gray-500 mt-1">{{ $student->name }} hasn't submitted Step {{ $step }}.</p>
            </div>
        @endif

        {{-- ── CORRECT ANSWER KEY ──────────────────────────────────────────── --}}
        @if($hasAnswerKey)
            @php
                // Normalise: both exercise->correct_data and answerSheet->correct_data are cast to array
                $correctData = $exercise
                    ? ($exercise->correct_data ?? [])
                    : ($answerSheet->correct_data ?? []);

                $keyTitle    = $exercise ? $exercise->title : 'Answer Sheet';
                $keyDesc     = $exercise ? ($exercise->description ?? null) : null;
            @endphp

            <div class="bg-green-50 rounded-xl border border-green-200 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-green-200">
                    <h3 class="text-base font-semibold text-green-900 flex items-center gap-2">
                        <i class="fas fa-check-circle text-green-500"></i>
                        Correct Answer Key
                        <span class="ml-1 text-sm font-normal text-green-700">— {{ $keyTitle }}</span>
                    </h3>
                    @if($exercise)
                        <span class="text-xs text-green-700 italic">Exercise #{{ $exercise->order }}</span>
                    @endif
                </div>

                <div class="p-6">
                    @if($keyDesc)
                        <p class="text-sm text-green-800 mb-4 italic">{{ $keyDesc }}</p>
                    @endif

                    <div class="border border-green-200 rounded-lg bg-white overflow-hidden">
                        <div id="correct-sheet" class="overflow-x-auto"></div>
                    </div>
                </div>
            </div>
        @endif

        {{-- ── QUICK ACTIONS ───────────────────────────────────────────────── --}}
        @if($submission)
            <div class="mt-6 flex items-center justify-between bg-white p-4 rounded-xl border border-gray-200">
                <p class="text-sm text-gray-600">
                    Compare the answers above to provide better feedback for {{ $student->name }}.
                </p>
                <a href="{{ route('instructors.performance-tasks.submissions.feedback-form', ['task' => $task->id, 'student' => $student->id, 'step' => $step]) }}"
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors
                          {{ $submission->instructor_feedback ? 'bg-green-600 hover:bg-green-700' : 'bg-blue-600 hover:bg-blue-700' }}">
                    <i class="fas fa-{{ $submission->instructor_feedback ? 'pen' : 'plus' }} mr-2 text-xs"></i>
                    {{ $submission->instructor_feedback ? 'Edit Feedback' : 'Add Feedback' }}
                </a>
            </div>
        @endif
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const isMobile = window.innerWidth < 640;
        const colW     = isMobile ? 80 : 110;

        function parseData(raw) {
            if (!raw) return null;
            try {
                // Handle double-encoding: if it's a string, parse it
                let parsed = (typeof raw === 'string') ? JSON.parse(raw) : raw;
                // Handle {data, metadata} wrapper
                if (parsed && typeof parsed === 'object' && parsed.data) {
                    parsed = parsed.data;
                }
                return Array.isArray(parsed) ? parsed : null;
            } catch (e) {
                console.error('parseData error:', e, raw);
                return null;
            }
        }

        function buildColumns(data) {
            const maxCols = Math.max(...data.map(r => Array.isArray(r) ? r.length : 0), 10);
            return Array(maxCols).fill(null).map((_, i) => ({
                type    : 'text',
                width   : colW,
                align   : 'center',
                title   : String.fromCharCode(65 + i),
                readOnly: true,
            }));
        }

        function makeSheet(containerId, data) {
            const container = document.getElementById(containerId);
            if (!container || !data || !data.length) {
                if (container) container.innerHTML = '<p class="p-4 text-sm text-gray-400 italic">No data to display.</p>';
                return;
            }

            jspreadsheet(container, {
                data             : data,
                columns          : buildColumns(data),
                tableOverflow    : true,
                tableHeight      : '400px',
                tableWidth       : '100%',
                columnSorting    : false,
                columnDrag       : false,
                rowDrag          : false,
                allowInsertRow   : false,
                allowDeleteRow   : false,
                allowInsertColumn: false,
                allowDeleteColumn: false,
                columnResize     : true,
                contextMenu      : false,
                editable         : false,
            });
        }

        // ── Student sheet ──────────────────────────────────────────────────────
        @if($submission && $submission->submission_data)
            const studentRaw  = @json($submission->submission_data);
            const studentData = parseData(studentRaw);
            console.log('Student data:', studentData);
            makeSheet('student-sheet', studentData);
        @endif

        // ── Correct answer sheet ───────────────────────────────────────────────
        @if($hasAnswerKey)
            const correctRaw  = @json($correctData ?? []);
            const correctData = parseData(correctRaw);
            console.log('Correct data:', correctData);
            makeSheet('correct-sheet', correctData);
        @endif
    });
</script>

<style>
    .jexcel td { border-color: #e5e7eb !important; font-size: 12px; }
    #student-sheet .jexcel td { background-color: #fff5f5 !important; }
    #correct-sheet .jexcel td { background-color: #f0fdf4 !important; }
    .jexcel_content { overflow: auto !important; }
</style>

</x-app-layout>