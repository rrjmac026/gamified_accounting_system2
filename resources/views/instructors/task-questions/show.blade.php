<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] backdrop-blur-sm overflow-hidden shadow-lg rounded-lg p-8 border border-[#FFC8FB]/50">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-[#FF92C2]">Question Details</h2>
                    <div class="flex gap-4">
                        <a href="{{ route('instructors.task-questions.edit', $taskQuestion) }}" 
                           class="px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5]">
                            Edit Question
                        </a>
                        <a href="{{ route('instructors.task-questions.index') }}" 
                           class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            Back to List
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    <div>
                        <h3 class="text-sm font-semibold text-[#FF92C2] mb-1">Task</h3>
                        <p class="text-gray-700">{{ $taskQuestion->task->title }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-[#FF92C2] mb-1">Question Text</h3>
                        <p class="text-gray-700 whitespace-pre-line">{{ $taskQuestion->question_text }}</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-semibold text-[#FF92C2] mb-1">Question Type</h3>
                            <p class="text-gray-700 capitalize">{{ str_replace('_', ' ', $taskQuestion->question_type) }}</p>
                        </div>

                        <div>
                            <h3 class="text-sm font-semibold text-[#FF92C2] mb-1">Points</h3>
                            <p class="text-gray-700">{{ $taskQuestion->points }}</p>
                        </div>
                    </div>

                    @if($taskQuestion->question_type === 'multiple_choice' && $taskQuestion->options)
                        <div>
                            <h3 class="text-sm font-semibold text-[#FF92C2] mb-2">Options</h3>
                            <div class="space-y-2">
                                @foreach($taskQuestion->options as $option)
                                    <div class="flex items-center gap-2">
                                        <span class="w-6 h-6 flex items-center justify-center rounded-full border-2 border-[#FFC8FB]">
                                            {{ chr(65 + $loop->index) }}
                                        </span>
                                        <p class="text-gray-700">{{ $option }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div>
                        <h3 class="text-sm font-semibold text-[#FF92C2] mb-1">Correct Answer</h3>
                        <p class="text-gray-700">{{ $taskQuestion->correct_answer }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
