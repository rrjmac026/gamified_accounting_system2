<!-- Feedback Show Page -->
<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 sm:rounded-lg">
                <div class="p-6 sm:p-8">
                    <!-- Error Messages -->
                    @if($errors->any())
                        <div class="mb-4 p-4 rounded-md bg-red-50 border border-red-200">
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
                        <div class="mb-4 p-4 rounded-md bg-green-50 border border-green-200">
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
                        <h2 class="text-2xl font-bold text-[#FF92C2]">Feedback Details</h2>
                        <a href="{{ route('students.feedback.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5] transition-colors duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to List
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-[#FFF6FD] border border-[#FFC8FB] p-4 rounded-lg">
                            <h3 class="text-sm font-semibold text-pink-900 uppercase tracking-wide mb-2">Task</h3>
                            <p class="text-gray-900 font-medium">
                                {{ $feedback->performanceTask ? $feedback->performanceTask->title : 'Task not found' }}
                            </p>
                        </div>

                        <div class="bg-[#FFF6FD] border border-[#FFC8FB] p-4 rounded-lg">
                            <h3 class="text-sm font-semibold text-pink-900 uppercase tracking-wide mb-2">Type</h3>
                            <span class="capitalize px-3 py-1 rounded-full text-sm inline-block
                                {{ $feedback->feedback_type === 'general' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $feedback->feedback_type === 'improvement' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $feedback->feedback_type === 'question' ? 'bg-amber-100 text-amber-800' : '' }}">
                                {{ $feedback->feedback_type }}
                            </span>
                        </div>

                        <!-- Rating Display -->
                        <div class="bg-[#FFF6FD] border border-[#FFC8FB] p-4 rounded-lg">
                            <h3 class="text-sm font-semibold text-pink-900 uppercase tracking-wide mb-2">Rating</h3>
                            @if($feedback->rating)
                                <div class="flex items-center space-x-2">
                                    <div class="flex">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $feedback->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                    <span class="text-gray-900 font-medium">{{ $feedback->rating }}/5</span>
                                </div>
                            @else
                                <p class="text-gray-500">No rating provided</p>
                            @endif
                        </div>

                        <div class="bg-[#FFF6FD] border border-[#FFC8FB] p-4 rounded-lg">
                            <h3 class="text-sm font-semibold text-pink-900 uppercase tracking-wide mb-2">Generated At</h3>
                            <p class="text-gray-900 font-medium">{{ $feedback->generated_at->format('F j, Y g:i A') }}</p>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-[#FF92C2] mb-4 flex items-center">
                                <i class="fas fa-comment-dots mr-2"></i>
                                Feedback
                            </h3>
                            <div class="bg-[#FFE6F4] border border-[#FFC8FB] p-6 rounded-lg">
                                <p class="text-gray-800 whitespace-pre-line leading-relaxed">{{ $feedback->feedback_text }}</p>
                            </div>
                        </div>

                        @if($feedback->recommendations && count($feedback->recommendations) > 0)
                            <div>
                                <h3 class="text-lg font-semibold text-[#FF92C2] mb-4 flex items-center">
                                    <i class="fas fa-lightbulb mr-2"></i>
                                    Recommendations
                                </h3>
                                <div class="bg-[#FFE6F4] border border-[#FFC8FB] p-6 rounded-lg">
                                    <ul class="space-y-3">
                                        @foreach($feedback->recommendations as $recommendation)
                                            <li class="flex items-start">
                                                <i class="fas fa-check-circle text-[#FF92C2] mt-1 mr-3 flex-shrink-0"></i>
                                                <span class="text-gray-800">{{ $recommendation }}</span>
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
    </div>
</x-app-layout>