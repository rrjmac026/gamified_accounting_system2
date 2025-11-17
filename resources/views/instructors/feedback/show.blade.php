@section('title', 'Feedback Details')

<x-app-layout>
    {{-- Header Section --}}
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 sm:rounded-lg">
                <div class="p-6 sm:p-8">
                    {{-- Error Messages --}}
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

                    {{-- Success Message --}}
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
                        <a href="{{ route('instructors.feedback-records.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5] transition-colors duration-200">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back to List
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div class="bg-[#FFF6FD] border border-[#FFC8FB] p-4 rounded-lg">
                            <h3 class="text-sm font-semibold text-pink-900 uppercase tracking-wide mb-2">Student</h3>
                            <p class="text-gray-900 font-medium">{{ $feedbackRecord->student->user->name }}</p>
                        </div>

                        <div class="bg-[#FFF6FD] border border-[#FFC8FB] p-4 rounded-lg">
                            <h3 class="text-sm font-semibold text-pink-900 uppercase tracking-wide mb-2">Task</h3>
                            <p class="text-gray-900 font-medium">
                                {{ $feedbackRecord->performanceTask->title ?? 'Task not found' }}
                            </p>
                        </div>

                        <div class="bg-[#FFF6FD] border border-[#FFC8FB] p-4 rounded-lg">
                            <h3 class="text-sm font-semibold text-pink-900 uppercase tracking-wide mb-2">Step</h3>
                            <p class="text-gray-900 font-medium">{{ $stepTitles[$feedbackRecord->step] ?? 'N/A' }}</p>
                        </div>

                        <div class="bg-[#FFF6FD] border border-[#FFC8FB] p-4 rounded-lg">
                            <h3 class="text-sm font-semibold text-pink-900 uppercase tracking-wide mb-2">Type</h3>
                            <span class="capitalize px-3 py-1 rounded-full text-sm inline-block
                                {{ $feedbackRecord->feedback_type === 'general' ? 'bg-blue-100 text-blue-800' : '' }}
                                {{ $feedbackRecord->feedback_type === 'improvement' ? 'bg-purple-100 text-purple-800' : '' }}
                                {{ $feedbackRecord->feedback_type === 'question' ? 'bg-amber-100 text-amber-800' : '' }}">
                                {{ $feedbackRecord->feedback_type }}
                            </span>
                        </div>

                        {{-- Rating Display --}}
                        <div class="bg-[#FFF6FD] border border-[#FFC8FB] p-4 rounded-lg">
                            <h3 class="text-sm font-semibold text-pink-900 uppercase tracking-wide mb-2">Rating</h3>
                            @if($feedbackRecord->rating)
                                <div class="flex items-center space-x-2">
                                    <div class="flex">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star {{ $i <= $feedbackRecord->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                        @endfor
                                    </div>
                                    <span class="text-gray-900 font-medium">{{ $feedbackRecord->rating }}/5</span>
                                </div>
                            @else
                                <p class="text-gray-500">No rating provided</p>
                            @endif
                        </div>

                        <div class="bg-[#FFF6FD] border border-[#FFC8FB] p-4 rounded-lg">
                            <h3 class="text-sm font-semibold text-pink-900 uppercase tracking-wide mb-2">Generated At</h3>
                            <p class="text-gray-900 font-medium">{{ $feedbackRecord->generated_at ? $feedbackRecord->generated_at->format('F j, Y g:i A') : $feedbackRecord->created_at->format('F j, Y g:i A') }}</p>
                        </div>

                        <div class="bg-[#FFF6FD] border border-[#FFC8FB] p-4 rounded-lg">
                            <h3 class="text-sm font-semibold text-pink-900 uppercase tracking-wide mb-2">Status</h3>
                            <div class="flex items-center space-x-2">
                                <span class="px-3 py-1 text-xs font-medium rounded-full
                                    {{ $feedbackRecord->is_read ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $feedbackRecord->is_read ? 'Read' : 'Unread' }}
                                </span>
                                @if($feedbackRecord->is_anonymous)
                                    <span class="px-3 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-800">
                                        <i class="fas fa-user-secret mr-1"></i>Anonymous
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <h3 class="text-lg font-semibold text-[#FF92C2] mb-4 flex items-center">
                                <i class="fas fa-comment-dots mr-2"></i>
                                Feedback
                            </h3>
                            <div class="bg-[#FFE6F4] border border-[#FFC8FB] p-6 rounded-lg">
                                <p class="text-gray-800 whitespace-pre-line leading-relaxed">{{ $feedbackRecord->feedback_text ?? 'No content provided' }}</p>
                            </div>
                        </div>

                        @if($feedbackRecord->recommendations && count($feedbackRecord->recommendations) > 0)
                            <div>
                                <h3 class="text-lg font-semibold text-[#FF92C2] mb-4 flex items-center">
                                    <i class="fas fa-lightbulb mr-2"></i>
                                    Recommendations
                                </h3>
                                <div class="bg-[#FFE6F4] border border-[#FFC8FB] p-6 rounded-lg">
                                    <ul class="space-y-3">
                                        @foreach($feedbackRecord->recommendations as $recommendation)
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

                    {{-- Action Buttons --}}
                    <div class="flex flex-col sm:flex-row justify-between items-center mt-8 pt-6 border-t border-[#FFC8FB]/50 gap-4">
                        <div class="flex flex-wrap gap-3">
                            @if(!$feedbackRecord->is_read)
                                <form action="{{ route('instructors.feedback.toggle-read', $feedbackRecord) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors duration-200 text-sm font-medium flex items-center gap-2">
                                        <i class="fas fa-check"></i>Mark as Read
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
