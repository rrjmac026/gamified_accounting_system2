<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.css" />
    
    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <div class="flex items-center gap-2 text-sm text-gray-500 mb-4">
                <a href="{{ route('instructors.performance-tasks.submissions.index') }}" 
                   class="hover:text-blue-600">All Submissions</a>
                <span>/</span>
                <a href="{{ route('instructors.performance-tasks.submissions.show', $task->id) }}" 
                   class="hover:text-blue-600">{{ $task->title }}</a>
                <span>/</span>
                <a href="{{ route('instructors.performance-tasks.submissions.show-student', ['task' => $task->id, 'student' => $student->id]) }}" 
                   class="hover:text-blue-600">{{ $student->name }}</a>
                <span>/</span>
                <span class="text-gray-900">Answer Comparison</span>
            </div>

            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900">{{ $stepTitle }} - Answer Comparison</h2>
                    <p class="text-sm text-gray-500 mt-1">Compare student's answer with correct answer for Step {{ $step }}</p>
                </div>
                
                <a href="{{ route('instructors.performance-tasks.submissions.show-student', ['task' => $task->id, 'student' => $student->id]) }}" 
                   class="inline-flex items-center px-4 py-2 bg-gray-600 text-white text-sm font-medium rounded-lg hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Student Submission
                </a>
            </div>
        </div>

        @if($submission)
            <!-- Student's Answer (TOP) -->
            <div class="mb-6 bg-red-50 p-6 rounded-lg border border-red-200">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-red-900">
                        📝 {{ $student->name }}'s Answer
                    </h2>
                    <div class="flex items-center gap-4">
                        <span class="px-3 py-1 rounded-full text-xs font-medium
                            @if($submission->status === 'correct') bg-green-100 text-green-800
                            @elseif($submission->status === 'wrong') bg-red-100 text-red-800
                            @elseif($submission->status === 'passed') bg-blue-100 text-blue-800
                            @else bg-yellow-100 text-yellow-800
                            @endif">
                            {{ ucfirst($submission->status) }}
                        </span>
                        <span class="text-sm font-bold text-red-900">
                            Score: {{ $submission->score }}/{{ $task->max_score / 10 }}
                        </span>
                    </div>
                </div>
                
                <div class="border border-red-300 rounded-lg p-4 bg-white overflow-x-auto">
                    <x-answer-sheets.display :data="$submission->submission_data" :step="$step . '-student'" />
                </div>
                
                <div class="mt-4 grid grid-cols-2 gap-4 text-sm text-red-800">
                    <div>
                        <p><strong>Attempt:</strong> {{ $submission->attempts }}/{{ $task->max_attempts }}</p>
                        <p><strong>Submitted:</strong> {{ $submission->created_at->format('M d, Y g:i A') }}</p>
                    </div>
                    <div>
                        @if($submission->remarks)
                            <p><strong>System Feedback:</strong></p>
                            <p class="text-xs mt-1">{{ $submission->remarks }}</p>
                        @endif
                    </div>
                </div>
                
                @if($submission->instructor_feedback)
                    <div class="mt-4 p-4 bg-purple-50 border border-purple-200 rounded-lg">
                        <div class="flex items-center justify-between mb-2">
                            <h4 class="text-sm font-semibold text-purple-900">💬 Your Feedback</h4>
                            <span class="text-xs text-purple-700">
                                {{ $submission->feedback_given_at->format('M d, Y g:i A') }}
                            </span>
                        </div>
                        <p class="text-sm text-purple-800 whitespace-pre-wrap">{{ $submission->instructor_feedback }}</p>
                    </div>
                @endif
            </div>
        @else
            <!-- No Submission Yet -->
            <div class="mb-6 bg-gray-50 p-6 rounded-lg border border-gray-200">
                <div class="flex items-center justify-center py-8">
                    <div class="text-center">
                        <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">No Submission Yet</h3>
                        <p class="text-gray-500">{{ $student->name }} hasn't submitted Step {{ $step }} yet.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Correct Answer (BOTTOM) -->
        <div class="bg-green-50 p-6 rounded-lg border border-green-200">
            <h2 class="text-lg font-semibold text-green-900 mb-4">
                ✅ Correct Answer Key
            </h2>
            
            <div class="border border-green-300 rounded-lg p-4 bg-white overflow-x-auto">
                <x-answer-sheets.display :data="$answerSheet->correct_data" :step="$step . '-correct'" />
            </div>

            @if($answerSheet->notes)
                <div class="mt-4 p-4 bg-green-100 border border-green-300 rounded-lg">
                    <h4 class="text-sm font-semibold text-green-900 mb-2">📝 Instructor Notes:</h4>
                    <p class="text-sm text-green-800">{{ $answerSheet->notes }}</p>
                </div>
            @endif
        </div>

        @if($submission)
            <!-- Quick Actions -->
            <div class="mt-6 flex items-center justify-between bg-white p-4 rounded-lg border border-gray-200">
                <div class="text-sm text-gray-600">
                    <p><strong>Quick Actions:</strong></p>
                    <p class="text-xs mt-1">Compare the answers above to provide better feedback to {{ $student->name }}</p>
                </div>
                <div class="flex gap-3">
                    @if($submission->instructor_feedback)
                        <a href="{{ route('instructors.performance-tasks.submissions.feedback-form', [
                            'task' => $task->id,
                            'student' => $student->id,
                            'step' => $step
                        ]) }}" 
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit Feedback
                        </a>
                    @else
                        <a href="{{ route('instructors.performance-tasks.submissions.feedback-form', [
                            'task' => $task->id,
                            'student' => $student->id,
                            'step' => $step
                        ]) }}" 
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                            </svg>
                            Add Feedback
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
    
    {{-- ✅ Fix z-index issues --}}
    <style>
        .handsontable {
            position: relative;
            z-index: 1 !important;
        }
        
        .handsontable thead th {
            z-index: 2 !important;
        }
        
        nav {
            z-index: 9999 !important;
        }
        
        .overflow-x-auto {
            -webkit-overflow-scrolling: touch;
        }
    </style>
</x-app-layout>