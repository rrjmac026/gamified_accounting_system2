<x-app-layout>
    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button and Header -->
        <div class="mb-6">
            <a href="{{ route('instructors.performance-tasks.submissions.show-student', ['task' => $task->id, 'student' => $student->id]) }}" 
               class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 mb-3 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Back to student submissions
            </a>
            
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Provide Feedback</h1>
                    <p class="text-sm text-gray-600 mt-1">Help {{ $student->name }} improve their work</p>
                </div>
                
                <!-- Step Badge -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg px-6 py-3 border border-blue-200">
                    <div class="text-xs text-gray-600 mb-1">Step {{ $step }} of 10</div>
                    <div class="text-lg font-bold text-blue-600">
                        @php
                            $stepTitles = [
                                1 => 'Analyze Transactions',
                                2 => 'Journalize Transactions',
                                3 => 'Post to Ledger Accounts',
                                4 => 'Prepare Trial Balance',
                                5 => 'Journalize & Post Adjusting Entries',
                                6 => 'Prepare Adjusted Trial Balance',
                                7 => 'Prepare Financial Statements',
                                8 => 'Journalize & Post Closing Entries',
                                9 => 'Prepare Post-Closing Trial Balance',
                                10 => 'Reverse (Optional Step)',
                            ];
                        @endphp
                        {{ $stepTitles[$step] ?? "Step $step" }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Student and Task Info Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <div class="flex items-center mb-2">
                        <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Student</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $student->name }}</p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 ml-13">{{ $student->email }}</p>
                </div>
                
                <div>
                    <div class="flex items-center mb-2">
                        <div class="h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Performance Task</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $task->title }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center mb-2">
                        <div class="h-10 w-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Last Updated</p>
                            <p class="text-sm font-semibold text-gray-900">{{ $submission->updated_at->format('M d, Y') }}</p>
                            <p class="text-xs text-gray-500">{{ $submission->updated_at->format('g:i A') }}</p>
                        </div>
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
                Submission Summary
            </h3>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-lg p-4 border border-gray-200">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-xs text-gray-600">Status</p>
                        @if($submission->status === 'correct')
                            <div class="h-2 w-2 rounded-full bg-green-500"></div>
                        @elseif($submission->status === 'wrong')
                            <div class="h-2 w-2 rounded-full bg-red-500"></div>
                        @else
                            <div class="h-2 w-2 rounded-full bg-yellow-500"></div>
                        @endif
                    </div>
                    <p class="text-lg font-bold 
                        @if($submission->status === 'correct') text-green-600
                        @elseif($submission->status === 'wrong') text-red-600
                        @else text-yellow-600
                        @endif">
                        {{ ucfirst($submission->status) }}
                    </p>
                </div>

                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                    <p class="text-xs text-gray-600 mb-2">Score</p>
                    <p class="text-lg font-bold text-blue-600">
                        {{ $submission->score }}<span class="text-sm text-gray-500">/{{ $task->max_score / 10 }}</span>
                    </p>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                    <p class="text-xs text-gray-600 mb-2">Attempts</p>
                    <p class="text-lg font-bold text-purple-600">
                        {{ $submission->attempts }}<span class="text-sm text-gray-500">/{{ $task->max_attempts }}</span>
                    </p>
                </div>

                <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg p-4 border border-orange-200">
                    <p class="text-xs text-gray-600 mb-2">Max Reached</p>
                    <p class="text-lg font-bold text-orange-600">
                        {{ $submission->attempts >= $task->max_attempts ? 'Yes' : 'No' }}
                    </p>
                </div>
            </div>

            @if($submission->remarks)
            <div class="mt-4 bg-blue-50 border border-blue-200 rounded-lg p-4">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <div>
                        <p class="text-xs font-semibold text-blue-900 mb-1">System Remarks:</p>
                        <p class="text-sm text-blue-800">{{ $submission->remarks }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Feedback Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-blue-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    {{ $submission->instructor_feedback ? 'Edit Your Feedback' : 'Write Your Feedback' }}
                </h3>
                <p class="text-sm text-gray-600 mt-1">Provide constructive guidance to help the student understand and improve</p>
            </div>

            <form method="POST" action="{{ route('instructors.performance-tasks.submissions.store-feedback', [
                'task' => $task->id,
                'student' => $student->id,
                'step' => $step
            ]) }}" class="p-6">
                @csrf

                <div class="mb-6">
                    <label for="instructor_feedback" class="block text-sm font-semibold text-gray-700 mb-2">
                        Your Feedback <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <textarea 
                            id="instructor_feedback" 
                            name="instructor_feedback" 
                            rows="8"
                            required
                            minlength="10"
                            maxlength="1000"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 transition-all"
                            placeholder="Example: Great work on identifying the accounts! However, I noticed that the debit and credit entries for transaction #3 need to be reviewed. Remember that assets increase with debits and liabilities increase with credits. Take another look at the accounting equation and try again."
                        >{{ old('instructor_feedback', $submission->instructor_feedback) }}</textarea>
                        
                        <div class="absolute bottom-3 right-3 text-xs text-gray-400">
                            <span id="char-count">{{ strlen(old('instructor_feedback', $submission->instructor_feedback ?? '')) }}</span>/1000
                        </div>
                    </div>
                    
                    @error('instructor_feedback')
                        <div class="mt-2 flex items-center text-sm text-red-600">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            {{ $message }}
                        </div>
                    @enderror
                    
                    <div class="mt-2 flex items-start space-x-2">
                        <svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="text-xs text-gray-600">
                            <strong>Tips for effective feedback:</strong> Be specific about what needs improvement, reference exact entries or calculations, encourage the student, and suggest concrete steps they can take to correct their work.
                        </p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <a href="{{ route('instructors.performance-tasks.submissions.show-student', [
                        'task' => $task->id,
                        'student' => $student->id
                    ]) }}" class="inline-flex items-center px-5 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Cancel
                    </a>
                    
                    <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg hover:from-blue-700 hover:to-indigo-700 transition-all shadow-md hover:shadow-lg font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        {{ $submission->instructor_feedback ? 'Update Feedback' : 'Submit Feedback' }}
                    </button>
                </div>
            </form>
        </div>

        @if($submission->instructor_feedback)
        <!-- Previous Feedback Preview -->
        <div class="mt-6 bg-green-50 border border-green-200 rounded-lg p-6">
            <div class="flex items-start">
                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-green-100 flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-green-900 mb-1">Previous Feedback Saved</p>
                    <p class="text-xs text-green-700">
                        You provided feedback on {{ $submission->feedback_given_at->format('F d, Y \a\t g:i A') }}
                    </p>
                </div>
            </div>
        </div>
        @endif
    </div>

    @push('scripts')
    <script>
        // Character counter
        const textarea = document.getElementById('instructor_feedback');
        const charCount = document.getElementById('char-count');
        
        textarea.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
    </script>
    @endpush
</x-app-layout>