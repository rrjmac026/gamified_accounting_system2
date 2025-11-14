<x-app-layout>
    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            {{-- Card Container --}}
            <div class="bg-white/80 backdrop-blur-sm shadow-xl rounded-2xl p-8 border border-pink-200/40">

                {{-- Header with Action Buttons --}}
                <div class="mb-6 flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-2xl font-bold text-gray-800 mb-2">
                            {{ $performanceTask->title ?? 'Your Accounting Cycle Task' }}
                        </h3>
                        <p class="text-sm text-gray-600">
                            Complete all 10 steps of the accounting cycle to finish your performance task.  
                            You can retry each step up to <strong>{{ $performanceTask->max_attempts ?? 2 }} attempts</strong>.
                        </p>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="ml-6 flex flex-col gap-2">
                        <a href="{{ route('students.performance-tasks.my-progress', $performanceTask->id) }}" 
                           class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all shadow-md hover:shadow-lg text-sm font-medium whitespace-nowrap">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            View My Progress
                        </a>

                        @php
                            $feedbackCount = \App\Models\PerformanceTaskSubmission::where('task_id', $performanceTask->id)
                                ->where('student_id', auth()->user()->student->id)
                                ->whereNotNull('instructor_feedback')
                                ->count();
                        @endphp

                        @if($feedbackCount > 0)
                            <a href="{{ route('students.performance-tasks.my-progress', $performanceTask->id) }}" 
                               class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-orange-500 to-pink-500 text-white rounded-lg hover:from-orange-600 hover:to-pink-600 transition-all shadow-md hover:shadow-lg text-sm font-medium whitespace-nowrap animate-pulse">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
                                </svg>
                                {{ $feedbackCount }} New Feedback
                            </a>
                        @endif
                    </div>
                </div>

                {{-- Progress Bar --}}
                @php
                    $completedSteps = $completedSteps ?? [];
                    $progress = count($completedSteps) / 10 * 100;
                @endphp

                <div class="w-full bg-gray-200 rounded-full h-3 mb-8 overflow-hidden">
                    <div class="bg-pink-500 h-3 transition-all duration-500" style="width: {{ $progress }}%;"></div>
                </div>
                <p class="text-sm text-gray-600 mb-6">
                    Progress: {{ count($completedSteps) }} / 10 steps completed ({{ round($progress) }}%)
                </p>

                {{-- Feedback Alert Banner --}}
                @if($feedbackCount > 0)
                    <div class="mb-6 bg-gradient-to-r from-purple-50 to-pink-50 border-l-4 border-purple-500 p-4 rounded-r-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                </svg>
                            </div>
                            <div class="ml-3 flex-1">
                                <p class="text-sm font-semibold text-purple-900">
                                    💬 You have {{ $feedbackCount }} new feedback message{{ $feedbackCount > 1 ? 's' : '' }} from your instructor!
                                </p>
                                <p class="mt-1 text-sm text-purple-800">
                                    Check your progress page to see detailed feedback on your submissions.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Step List --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @for ($i = 1; $i <= 10; $i++)
                        @php
                            $isCompleted = in_array($i, $completedSteps);
                            $isNext = $i === (count($completedSteps) + 1);
                            
                            // Check if this step has feedback
                            $stepHasFeedback = \App\Models\PerformanceTaskSubmission::where('task_id', $performanceTask->id)
                                ->where('student_id', auth()->user()->student->id)
                                ->where('step', $i)
                                ->whereNotNull('instructor_feedback')
                                ->exists();
                        @endphp

                        <div class="relative group border border-gray-200 hover:border-pink-300 rounded-xl p-5 bg-gradient-to-b from-white to-pink-50 shadow-sm hover:shadow-md transition-all">
                            {{-- Feedback Badge --}}
                            @if($stepHasFeedback)
                                <div class="absolute -top-2 -right-2">
                                    <span class="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-purple-600 rounded-full animate-bounce">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clip-rule="evenodd"/>
                                        </svg>
                                        New
                                    </span>
                                </div>
                            @endif

                            <div class="flex justify-between items-center mb-3">
                                <h4 class="text-lg font-semibold text-gray-800">
                                    Step {{ $i }}
                                </h4>
                                @if ($isCompleted)
                                    <span class="text-green-600 text-sm font-medium flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                        Completed
                                    </span>
                                @elseif ($isNext)
                                    <span class="text-pink-500 text-sm font-medium">Next →</span>
                                @else
                                    <span class="text-gray-400 text-sm font-medium">Pending</span>
                                @endif
                            </div>

                            <p class="text-sm text-gray-600 mb-4">
                                {{ [
                                    1 => 'Analyze Transactions',
                                    2 => 'Journalize Entries',
                                    3 => 'Post to Ledger',
                                    4 => 'Prepare Trial Balance',
                                    5 => 'Adjusting Entries',
                                    6 => 'Prepare Adjusted Trial Balance',
                                    7 => 'Prepare Worksheet',
                                    8 => 'Prepare Financial Statements',
                                    9 => 'Prepare Closing Entries',
                                    10 => 'Prepare Post-Closing Trial Balance'
                                ][$i] }}
                            </p>

                            {{-- Feedback Indicator --}}
                            @if($stepHasFeedback)
                                <div class="mb-3 flex items-center text-xs text-purple-700 bg-purple-100 rounded px-2 py-1">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 13V5a2 2 0 00-2-2H4a2 2 0 00-2 2v8a2 2 0 002 2h3l3 3 3-3h3a2 2 0 002-2zM5 7a1 1 0 011-1h8a1 1 0 110 2H6a1 1 0 01-1-1zm1 3a1 1 0 100 2h3a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                    </svg>
                                    Instructor feedback available
                                </div>
                            @endif

                            <div class="flex justify-end gap-2">
                                @if($stepHasFeedback)
                                    <a href="{{ route('students.performance-tasks.view-feedback', ['id' => $performanceTask->id, 'step' => $i]) }}"
                                       class="text-xs font-medium px-3 py-1.5 bg-purple-100 text-purple-700 rounded-lg hover:bg-purple-200 transition flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                                        </svg>
                                        Feedback
                                    </a>
                                @endif

                                @if ($isCompleted)
                                    <a href="{{ route('students.performance-tasks.step', ['id' => $performanceTask->id, 'step' => $i]) }}"
                                       class="text-sm font-medium px-4 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition">
                                        Review
                                    </a>
                                @elseif ($isNext || $i === 1)
                                    <a href="{{ route('students.performance-tasks.step', ['id' => $performanceTask->id, 'step' => $i]) }}"
                                       class="text-sm font-medium px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition">
                                        Continue
                                    </a>
                                @else
                                    <button disabled
                                            class="text-sm font-medium px-4 py-2 bg-gray-200 text-gray-400 rounded-lg cursor-not-allowed">
                                        Locked
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endfor
                </div>

                {{-- Bottom Action Bar --}}
                <div class="mt-8 pt-6 border-t border-gray-200 flex justify-between items-center">
                    <a href="{{ route('students.performance-tasks.index') }}" 
                       class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 transition-colors">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back to Tasks
                    </a>

                    <div class="flex items-center gap-3">
                        @if($feedbackCount > 0)
                            <span class="text-sm text-purple-700 font-medium flex items-center">
                                <span class="relative flex h-3 w-3 mr-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-purple-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-purple-500"></span>
                                </span>
                                {{ $feedbackCount }} feedback waiting
                            </span>
                        @endif

                        <a href="{{ route('students.performance-tasks.my-progress', $performanceTask->id) }}" 
                           class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-700 hover:to-indigo-700 transition-all shadow-md hover:shadow-lg text-sm font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                            </svg>
                            View Detailed Progress
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>