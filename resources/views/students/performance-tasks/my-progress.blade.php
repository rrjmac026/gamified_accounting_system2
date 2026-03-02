<x-app-layout>

    @if($statistics['feedback_count'] > 0)
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z"/>
            </svg>
            {{ $statistics['feedback_count'] }} New Feedback
        </span>
    @endif
    
    <div class="py-8 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('students.performance-tasks.index') }}" 
               class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-3 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to tasks
            </a>
            
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">My Progress</h1>
                    <p class="text-lg text-gray-700 mt-2 font-medium">{{ $task->title }}</p>
                </div>
                
                <!-- Overall Statistics -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-4 border border-blue-200">
                    <div class="text-sm text-gray-600 mb-2">Overall Performance</div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <div class="text-2xl font-bold text-blue-600">{{ $statistics['total_score'] }}</div>
                            <div class="text-xs text-gray-600">Total Score</div>
                        </div>
                        <div>
                            {{-- ✅ FIX: Use answered_steps (all submitted) not just correct --}}
                            <div class="text-2xl font-bold text-green-600">{{ $statistics['answered_steps'] }}/10</div>
                            <div class="text-xs text-gray-600">Answered</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">

            {{-- ✅ FIX: Show answered_steps (all submitted steps) as the main "Completed" metric --}}
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Steps Answered</p>
                        <p class="text-2xl font-bold text-green-600">{{ $statistics['answered_steps'] }}</p>
                        @if($statistics['correct_steps'] > 0 || $statistics['passed_steps'] > 0)
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $statistics['correct_steps'] }} correct · {{ $statistics['passed_steps'] }} passed
                            </p>
                        @endif
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
                        <p class="text-sm text-gray-600">Wrong / Exhausted</p>
                        <p class="text-2xl font-bold text-red-600">{{ $statistics['wrong_steps'] }}</p>
                    </div>
                    <div class="h-12 w-12 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
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

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Feedback Received</p>
                        <p class="text-2xl font-bold text-purple-600">{{ $statistics['feedback_count'] }}</p>
                    </div>
                    <div class="h-12 w-12 bg-purple-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Progress Bar — based on answered_steps not just correct -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-sm font-semibold text-gray-700">Overall Progress</h3>
                <span class="text-sm font-bold text-gray-900">{{ round(($statistics['answered_steps'] / 10) * 100) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-gradient-to-r from-green-400 to-green-600 h-3 rounded-full transition-all duration-500" 
                     style="width: {{ ($statistics['answered_steps'] / 10) * 100 }}%"></div>
            </div>
            <p class="text-xs text-gray-500 mt-2">
                {{ $statistics['answered_steps'] }} of 10 steps answered
                @if($statistics['correct_steps'] + $statistics['passed_steps'] > 0)
                    · {{ $statistics['correct_steps'] + $statistics['passed_steps'] }} passed
                @endif
            </p>
        </div>

        <!-- Steps Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-900">Step-by-Step Progress</h3>
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
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Date Submitted</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Feedback</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($submissionDetails as $stepNumber => $details)
                            @php
                                $isCorrect   = $details['status'] === 'correct';
                                $isPassed    = $details['status'] === 'passed';
                                $isWrong     = $details['status'] === 'wrong';
                                $isInProgress = !$isCorrect && !$isPassed && !$isWrong;

                                // Circle color
                                if ($isCorrect)       { $circleClass = 'bg-green-100'; $textClass = 'text-green-600'; }
                                elseif ($isPassed)    { $circleClass = 'bg-blue-100';  $textClass = 'text-blue-600'; }
                                elseif ($isWrong)     { $circleClass = 'bg-red-100';   $textClass = 'text-red-600'; }
                                else                  { $circleClass = 'bg-yellow-100'; $textClass = 'text-yellow-600'; }
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full {{ $circleClass }} flex items-center justify-center">
                                        <span class="text-sm font-bold {{ $textClass }}">{{ $stepNumber }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $details['step_title'] }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($isCorrect)
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            ✓ Correct
                                        </span>
                                    @elseif($isPassed)
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            ✓ Passed
                                        </span>
                                    @elseif($isWrong)
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            ✗ Wrong
                                        </span>
                                    @else
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            ⏳ In Progress
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="text-sm font-bold text-gray-900">{{ $details['score'] }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $details['attempts'] }}x
                                    </span>
                                </td>
                                {{-- ✅ NEW: Date Submitted column --}}
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($details['updated_at'])
                                        <div class="text-xs text-gray-600">
                                            {{ \Carbon\Carbon::parse($details['updated_at'])->format('M d, Y') }}
                                        </div>
                                        <div class="text-xs text-gray-400">
                                            {{ \Carbon\Carbon::parse($details['updated_at'])->format('h:i A') }}
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($details['instructor_feedback'])
                                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            📝 Available
                                        </span>
                                    @else
                                        <span class="text-xs text-gray-400">No feedback</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @if($details['instructor_feedback'])
                                        <a href="{{ route('students.performance-tasks.view-feedback', ['id' => $task->id, 'step' => $stepNumber]) }}" 
                                           class="inline-flex items-center px-3 py-1.5 bg-purple-100 text-purple-700 rounded-md hover:bg-purple-200 text-xs font-medium">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            View Feedback
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>

                            <!-- System Feedback Row -->
                            @if($details['remarks'])
                                <tr class="bg-blue-50">
                                    <td colspan="8" class="px-6 py-3">
                                        <div class="flex items-start text-sm">
                                            <svg class="w-5 h-5 text-blue-500 mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                            </svg>
                                            <div>
                                                <p class="text-xs font-semibold text-blue-900 mb-1">System Feedback:</p>
                                                <p class="text-blue-800">{{ $details['remarks'] }}</p>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <p class="text-gray-500">No submissions yet. Start working on this task!</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>