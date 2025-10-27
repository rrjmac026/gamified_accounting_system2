<x-app-layout>
    <script src="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@14.1.0/dist/handsontable.full.min.css" />

    {{-- ✅ Add padding-top to prevent navbar overlap --}}
    <div class="py-6 max-w-7xl mx-auto px-4 pt-20 sm:pt-24">
        <h1 class="text-2xl font-bold mb-6">Step {{ $step }} - Answer Key</h1>

        <!-- Student's Answer at TOP -->
        <div class="mb-6 bg-red-50 p-6 rounded-lg border border-red-200">
            <h2 class="text-lg font-semibold text-red-900 mb-4">
                📝 Your Answer ({{ $submission->score }}/{{ $performanceTask->max_score }})
            </h2>
            <div class="overflow-x-auto">
                <x-answer-sheets.display :data="$submission->submission_data" :step="$step . '-student'" />
            </div>
            
            <div class="mt-4 text-sm text-red-800">
                <p><strong>Attempt:</strong> {{ $submission->attempts }}/{{ $performanceTask->max_attempts }}</p>
                <p><strong>Status:</strong> {{ ucfirst($submission->status) }}</p>
                <p class="mt-2">{{ $submission->remarks }}</p>
            </div>
        </div>

        <!-- Correct Answer at BOTTOM -->
        <div class="bg-green-50 p-6 rounded-lg border border-green-200">
            <h2 class="text-lg font-semibold text-green-900 mb-4">
                ✅ Correct Answer
            </h2>
            <div class="overflow-x-auto">
                <x-answer-sheets.display :data="$answerSheet->correct_data" :step="$step . '-correct'" />
            </div>
            
            <div class="mt-4 p-4 bg-green-100 rounded-lg">
                <p class="text-sm text-green-800">
                    <strong>💡 Study Tip:</strong> Compare your answer above with the correct answer. 
                    Review the differences carefully to understand your mistakes.
                </p>
            </div>
        </div>

        <!-- Navigation -->
        <div class="mt-6 flex justify-between">
            <a href="{{ route('students.performance-tasks.step', [$performanceTask->id, $step]) }}" 
               class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                Back to Step {{ $step }}
            </a>
            
            @if($step < 10)
                <a href="{{ route('students.performance-tasks.step', [$performanceTask->id, $step + 1]) }}" 
                   class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700">
                    Continue to Step {{ $step + 1 }} →
                </a>
            @endif
        </div>
    </div>

    {{-- ✅ Add CSS to fix Handsontable z-index issues --}}
    <style>
        /* Fix Handsontable header overlapping navbar */
        .handsontable {
            position: relative;
            z-index: 1 !important;
        }
        
        .handsontable thead th {
            z-index: 2 !important;
        }
        
        /* Ensure navbar stays on top */
        nav {
            z-index: 9999 !important;
        }
        
        /* Fix scrolling issues */
        .overflow-x-auto {
            -webkit-overflow-scrolling: touch;
        }
    </style>
</x-app-layout>