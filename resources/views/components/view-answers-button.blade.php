@props(['submission', 'performanceTask', 'step'])

@if($submission && $submission->attempts >= $performanceTask->max_attempts)
    <div class="mb-6 relative overflow-hidden rounded-xl border-2 border-amber-300 bg-gradient-to-br from-amber-50 via-yellow-50 to-orange-50 shadow-lg">
        <!-- Decorative corner accent -->
        <div class="absolute top-0 right-0 w-32 h-32 bg-amber-200 rounded-bl-full opacity-20"></div>
        
        <div class="relative p-6">
            <div class="flex items-start gap-4">
                <!-- Icon -->
                <div class="flex-shrink-0">
                    <div class="w-12 h-12 bg-amber-100 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                </div>
                
                <!-- Content -->
                <div class="flex-1 min-w-0">
                    <h3 class="text-lg font-bold text-amber-900 mb-1">
                        Maximum Attempts Reached
                    </h3>
                    <p class="text-sm text-amber-800 mb-4">
                        You've completed all <span class="font-semibold">{{ $performanceTask->max_attempts }} attempts</span> for this step. 
                        Review the correct answers to improve your understanding.
                    </p>
                    
                    <!-- Score badge -->
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-white rounded-lg border border-amber-200 mb-4">
                        <span class="text-xs font-medium text-gray-600">Your Score:</span>
                        <span class="text-lg font-bold {{ $submission->score >= ($performanceTask->max_score * 0.7) ? 'text-green-600' : 'text-red-600' }}">
                            {{ $submission->score }}/{{ $performanceTask->max_score }}
                        </span>
                        @if($submission->score >= ($performanceTask->max_score * 0.7))
                            <span class="text-xs px-2 py-0.5 bg-green-100 text-green-700 rounded-full font-medium">Passed</span>
                        @else
                            <span class="text-xs px-2 py-0.5 bg-red-100 text-red-700 rounded-full font-medium">Need Review</span>
                        @endif
                    </div>
                    
                    <!-- Action button -->
                    <a href="{{ route('students.performance-tasks.show-answers', ['id' => $performanceTask->id, 'step' => $step]) }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-200 font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span>View Correct Answers</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif