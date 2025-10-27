<!-- Feedback Show Page -->
<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-xl p-8 border border-gray-200 dark:border-gray-700">
                <!-- Error Messages -->
                @if($errors->any())
                    <div class="mb-6 p-4 rounded-md bg-red-50 border border-red-200">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">An error occurred:</h3>
                                <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Success Message -->
                @if(session('success'))
                    <div class="mb-6 p-4 rounded-md bg-green-50 border border-green-200">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-green-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-2xl font-bold text-indigo-600 dark:text-indigo-400">Feedback Details</h2>
                    <a href="{{ route('students.feedback.index') }}" 
                       class="inline-flex items-center px-4 py-2 text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to List
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Task</h3>
                        <p class="text-gray-900 dark:text-gray-100 font-medium">{{ $feedback->task->title }}</p>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Type</h3>
                        <p class="text-gray-900 dark:text-gray-100 font-medium capitalize">{{ $feedback->feedback_type }}</p>
                    </div>

                    <!-- Add Rating Display -->
                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Rating</h3>
                        @if($feedback->rating)
                            <div class="flex items-center space-x-2">
                                <div class="flex">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $feedback->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                    @endfor
                                </div>
                                <span class="text-gray-900 dark:text-gray-100 font-medium">{{ $feedback->rating }}/5</span>
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400">No rating provided</p>
                        @endif
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                        <h3 class="text-sm font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide mb-2">Generated At</h3>
                        <p class="text-gray-900 dark:text-gray-100 font-medium">{{ $feedback->generated_at->format('F j, Y g:i A') }}</p>
                    </div>
                </div>

                <div class="space-y-8">
                    <div>
                        <h3 class="text-lg font-semibold text-indigo-600 dark:text-indigo-400 mb-4 flex items-center">
                            <i class="fas fa-comment-dots mr-2"></i>
                            Feedback
                        </h3>
                        <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 p-6 rounded-lg">
                            <p class="text-gray-800 dark:text-gray-200 whitespace-pre-line leading-relaxed">{{ $feedback->feedback_text }}</p>
                        </div>
                    </div>

                    @if($feedback->recommendations)
                        <div>
                            <h3 class="text-lg font-semibold text-emerald-600 dark:text-emerald-400 mb-4 flex items-center">
                                <i class="fas fa-lightbulb mr-2"></i>
                                Recommendations
                            </h3>
                            <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 p-6 rounded-lg">
                                <ul class="space-y-3">
                                    @foreach($feedback->recommendations as $recommendation)
                                        <li class="flex items-start">
                                            <i class="fas fa-check-circle text-emerald-600 dark:text-emerald-400 mt-1 mr-3 flex-shrink-0"></i>
                                            <span class="text-gray-800 dark:text-gray-200">{{ $recommendation }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>