<x-app-layout>
    <div class="py-8 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button and Header -->
        <div class="mb-6">
            <a href="{{ route('instructors.performance-tasks.submissions.show', $task->id) }}" 
               class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-3 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to student list
            </a>
            
            <div class="flex items-start justify-between flex-wrap gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $student->name }}</h1>
                    <p class="text-sm text-gray-600 mt-1">{{ $student->email }}</p>
                    <p class="text-lg text-gray-700 mt-2 font-medium">{{ $task->title }}</p>
                    @if(!empty($statistics['section_name']))
                        <span class="inline-flex items-center gap-1.5 mt-1 px-2.5 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-700 border border-indigo-200">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $statistics['section_name'] }}
                        </span>
                    @endif
                </div>
                
                <!-- Overall Statistics Card -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-200 shrink-0">
                    <div class="text-sm text-gray-600 mb-2 font-medium">Overall Performance</div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-2xl font-bold text-blue-600">{{ $statistics['total_score'] }}</div>
                            <div class="text-xs text-gray-600">Total Score</div>
                        </div>
                        <div>
                            {{-- ✅ FIX: answered_steps = all submitted, not just correct --}}
                            <div class="text-2xl font-bold text-green-600">{{ $statistics['answered_steps'] }}/10</div>
                            <div class="text-xs text-gray-600">Steps Answered</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Overview -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

            {{-- ✅ FIX: Answered = all submitted steps --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Answered</p>
                        <p class="text-2xl font-bold text-green-600">{{ $statistics['answered_steps'] }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $statistics['correct_steps'] }} correct · {{ $statistics['passed_steps'] }} passed
                        </p>
                    </div>
                    <div class="h-12 w-12 bg-green-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Wrong</p>
                        <p class="text-2xl font-bold text-red-600">{{ $statistics['wrong_steps'] }}</p>
                    </div>
                    <div class="h-12 w-12 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </div>
                </div>
            </div>

            {{-- ✅ FIX: Not Started = steps with no submission yet (was "In Progress") --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Not Started</p>
                        <p class="text-2xl font-bold text-gray-500">{{ $statistics['in_progress_steps'] }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">steps remaining</p>
                    </div>
                    <div class="h-12 w-12 bg-gray-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Attempts</p>
                        <p class="text-2xl font-bold text-blue-600">{{ $statistics['total_attempts'] }}</p>
                    </div>
                    <div class="h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Bar — based on answered steps -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Overall Progress</h3>
                <span class="text-sm font-bold text-gray-900">{{ round($statistics['progress_percent']) }}%</span>
            </div>
            {{-- Stacked bar: green=correct, blue=passed, red=wrong --}}
            <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden flex">
                <div class="bg-green-500 h-full transition-all" style="width: {{ ($statistics['correct_steps'] / 10) * 100 }}%"></div>
                <div class="bg-blue-500 h-full transition-all"  style="width: {{ ($statistics['passed_steps']  / 10) * 100 }}%"></div>
                <div class="bg-red-400 h-full transition-all"   style="width: {{ ($statistics['wrong_steps']   / 10) * 100 }}%"></div>
            </div>
            <div class="mt-2 flex items-center gap-4 text-xs text-gray-500">
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>{{ $statistics['correct_steps'] }} correct</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-500 inline-block"></span>{{ $statistics['passed_steps'] }} passed</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-400 inline-block"></span>{{ $statistics['wrong_steps'] }} wrong</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-gray-300 inline-block"></span>{{ $statistics['in_progress_steps'] }} not started</span>
            </div>
        </div>

        <!-- Submissions Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Step-by-Step Submissions</h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Step</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Score</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Attempts</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Submitted At</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach(range(1, 10) as $stepNumber)
                            @php
                                $details    = $submissionDetails[$stepNumber] ?? null;
                                $submission = $submissionsByStep[$stepNumber] ?? null;

                                $status = $details['status'] ?? 'not-started';

                                // Circle + badge colors
                                if ($status === 'correct') {
                                    $circleBg   = 'bg-green-100'; $circleText = 'text-green-600';
                                } elseif ($status === 'passed') {
                                    $circleBg   = 'bg-blue-100';  $circleText = 'text-blue-600';
                                } elseif ($status === 'wrong') {
                                    $circleBg   = 'bg-red-100';   $circleText = 'text-red-600';
                                } else {
                                    $circleBg   = 'bg-gray-200';  $circleText = 'text-gray-400';
                                }

                                $isSubmitted = $submission !== null;
                            @endphp

                            @if($isSubmitted)
                                {{-- Submitted row --}}
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full {{ $circleBg }} flex items-center justify-center">
                                            <span class="text-sm font-bold {{ $circleText }}">{{ $stepNumber }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $details['step_title'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        @if($status === 'correct')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                Correct
                                            </span>
                                        @elseif($status === 'passed')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                                Passed
                                            </span>
                                        @elseif($status === 'wrong')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                                                Wrong
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                In Progress
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="text-sm font-bold text-gray-900">{{ $details['score'] }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $details['attempts'] }}x
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                        @if($details['updated_at'])
                                            <div>{{ \Carbon\Carbon::parse($details['updated_at'])->format('M d, Y') }}</div>
                                            <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($details['updated_at'])->format('g:i A') }}</div>
                                        @elseif($details['submitted_at'])
                                            <div>{{ \Carbon\Carbon::parse($details['submitted_at'])->format('M d, Y') }}</div>
                                            <div class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($details['submitted_at'])->format('g:i A') }}</div>
                                        @else
                                            <span class="text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            {{-- Feedback button --}}
                                            @if($submission->instructor_feedback)
                                                <a href="{{ route('instructors.performance-tasks.submissions.feedback-form', ['task' => $task->id, 'student' => $student->id, 'step' => $stepNumber]) }}" 
                                                   class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-700 rounded-md hover:bg-green-200 transition-colors text-xs font-medium">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    Edit Feedback
                                                </a>
                                            @elseif($submission->attempts >= $task->max_attempts && !in_array($status, ['correct', 'passed']))
                                                <a href="{{ route('instructors.performance-tasks.submissions.feedback-form', ['task' => $task->id, 'student' => $student->id, 'step' => $stepNumber]) }}" 
                                                   class="inline-flex items-center px-3 py-1.5 bg-orange-100 text-orange-700 rounded-md hover:bg-orange-200 transition-colors text-xs font-medium">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                    Add Feedback
                                                </a>
                                            @else
                                                <a href="{{ route('instructors.performance-tasks.submissions.feedback-form', ['task' => $task->id, 'student' => $student->id, 'step' => $stepNumber]) }}" 
                                                   class="inline-flex items-center px-3 py-1.5 bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors text-xs font-medium">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                                    Add Feedback
                                                </a>
                                            @endif

                                            {{-- View Answers --}}
                                            <a href="{{ route('instructors.performance-tasks.submissions.answer-sheet', ['task' => $task->id, 'student' => $student->id, 'step' => $stepNumber]) }}" 
                                               class="inline-flex items-center px-3 py-1.5 bg-purple-100 text-purple-700 rounded-md hover:bg-purple-200 transition-colors text-xs font-medium">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                View Answers
                                            </a>
                                        </div>
                                    </td>
                                </tr>

                                {{-- System Remarks --}}
                                @if($details['feedback'])
                                    <tr class="bg-gradient-to-r from-blue-50 to-indigo-50 border-l-4 border-blue-400">
                                        <td colspan="7" class="px-6 py-4">
                                            <div class="flex items-start space-x-3">
                                                <div class="flex-shrink-0 h-8 w-8 bg-blue-500 rounded-lg flex items-center justify-center shadow-sm">
                                                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex items-center mb-2">
                                                        <h4 class="text-sm font-bold text-blue-900">System Evaluation</h4>
                                                        <span class="ml-2 px-2 py-0.5 text-xs font-medium bg-blue-200 text-blue-800 rounded-full">Auto-Generated</span>
                                                    </div>
                                                    <div class="bg-white/70 rounded-lg p-3 border border-blue-200">
                                                        <p class="text-sm text-gray-800 leading-relaxed">{{ $details['feedback'] }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif

                                {{-- Instructor Feedback --}}
                                @if($submission->instructor_feedback)
                                    <tr class="bg-gradient-to-r from-purple-50 to-pink-50 border-l-4 border-purple-400">
                                        <td colspan="7" class="px-6 py-4">
                                            <div class="flex items-start space-x-3">
                                                <div class="flex-shrink-0 h-8 w-8 bg-purple-500 rounded-lg flex items-center justify-center shadow-sm">
                                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                                </div>
                                                <div class="flex-1">
                                                    <div class="flex items-center justify-between mb-2">
                                                        <div class="flex items-center">
                                                            <h4 class="text-sm font-bold text-purple-900">Instructor Feedback</h4>
                                                            <span class="ml-2 px-2 py-0.5 text-xs font-medium bg-purple-200 text-purple-800 rounded-full">Personalized</span>
                                                        </div>
                                                        @if($submission->feedback_given_at)
                                                            <div class="flex items-center text-xs text-purple-700">
                                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                                {{ $submission->feedback_given_at->format('M d, Y g:i A') }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="bg-white/80 rounded-lg p-4 border border-purple-200 shadow-sm">
                                                        <p class="text-sm text-gray-800 leading-relaxed whitespace-pre-wrap">{{ $submission->instructor_feedback }}</p>
                                                    </div>
                                                    <div class="mt-2 flex items-center text-xs text-purple-700">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                                                        <span class="font-medium">Feedback by:</span>
                                                        <span class="ml-1">{{ $task->instructor->user->name ?? 'Instructor' }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endif

                            @else
                                {{-- Not submitted row --}}
                                <tr class="bg-gray-50 opacity-60">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gray-200 flex items-center justify-center">
                                            <span class="text-sm font-bold text-gray-400">{{ $stepNumber }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-400">{{ $stepTitles[$stepNumber] ?? "Step $stepNumber" }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-500">
                                            Not Started
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-400">—</td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-400">—</td>
                                    <td class="px-6 py-4 text-sm text-gray-400">—</td>
                                    <td class="px-6 py-4 text-center text-sm text-gray-400">—</td>
                                </tr>
                            @endif
                        @endforeach

                        @if(count($submissionDetails) === 0)
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No Submissions Yet</h3>
                                    <p class="text-gray-500">This student hasn't submitted any work for this task.</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>