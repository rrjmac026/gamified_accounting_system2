<x-app-layout>
    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('students.performance-tasks.my-progress', $task->id) }}" 
               class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-3 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to my progress
            </a>
            
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Instructor Feedback</h1>
                    <p class="text-sm text-gray-600 mt-1">{{ $task->title }}</p>
                </div>
                
                <!-- Step Badge -->
                <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-lg px-6 py-3 border border-purple-200">
                    <div class="text-xs text-gray-600 mb-1">Step {{ $step }} of 10</div>
                    <div class="text-lg font-bold text-purple-600">
                        {{ $stepTitles[$step] }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Submission Summary -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                Your Performance
            </h3>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-4 border border-gray-200">
                    <p class="text-xs text-gray-600 mb-2">Status</p>
                    <p class="text-lg font-bold 
                        @if($submission->status === 'correct') text-green-600
                        @elseif($submission->status === 'wrong') text-red-600
                        @else text-yellow-600 @endif">
                        {{ ucfirst($submission->status) }}
                    </p>
                </div>

                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                    <p class="text-xs text-gray-600 mb-2">Score</p>
                    <p class="text-lg font-bold text-blue-600">{{ $submission->score }}</p>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                    <p class="text-xs text-gray-600 mb-2">Attempts</p>
                    <p class="text-lg font-bold text-purple-600">{{ $submission->attempts }}</p>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                    <p class="text-xs text-gray-600 mb-2">Submitted</p>
                    <p class="text-lg font-bold text-green-600">{{ $submission->updated_at->format('M d') }}</p>
                </div>
            </div>

            @if($submission->remarks)
            <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-xs font-semibold text-blue-900 mb-1">System Feedback:</p>
                        <p class="text-sm text-blue-800">{{ $submission->remarks }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Instructor Feedback -->
        <div class="bg-gradient-to-br from-purple-50 to-indigo-50 rounded-lg shadow-lg border-2 border-purple-200 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-purple-100 to-indigo-100 border-b-2 border-purple-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-6 h-6 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Instructor's Feedback
                    </h3>
                    <span class="text-xs text-purple-700 bg-purple-200 px-3 py-1 rounded-full">
                        {{ $submission->feedback_given_at->format('M d, Y') }}
                    </span>
                </div>
            </div>

            <div class="p-6">
                <div class="prose max-w-none">
                    <p class="text-gray-800 leading-relaxed whitespace-pre-wrap">{{ $submission->instructor_feedback }}</p>
                </div>

                <div class="mt-6 pt-4 border-t border-purple-200">
                    <p class="text-xs text-gray-600">
                        <strong>Feedback provided by:</strong> {{ $task->instructor->user->name ?? 'Your Instructor' }} 
                        on {{ $submission->feedback_given_at->format('F d, Y \a\t g:i A') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="mt-6 flex gap-3">
            <a href="{{ route('students.performance-tasks.my-progress', $task->id) }}" 
               class="inline-flex items-center px-5 py-2.5 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to Progress
            </a>
            
            <a href="{{ route('students.performance-tasks.step', ['id' => $task->id, 'step' => $step]) }}" 
               class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Review This Step
            </a>
        </div>
    </div>
</x-app-layout>