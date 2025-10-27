<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] dark:bg-[#595758] overflow-hidden shadow-lg sm:rounded-2xl p-8 border border-[#FFC8FB]/50">
                <h2 class="text-2xl font-bold text-[#FF92C2] dark:text-[#FFC8FB] mb-6">Feedback Details</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Student</label>
                        <p class="text-gray-700 dark:text-[#FFC8FB]">{{ $feedbackRecord->student->user->name }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Task</label>
                        <p class="text-gray-700 dark:text-[#FFC8FB]">{{ $feedbackRecord->task->title }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Feedback Type</label>
                        <span class="px-2 py-1 text-xs rounded-full capitalize
                            {{ $feedbackRecord->feedback_type === 'general' ? 'bg-blue-100 text-blue-800' : '' }}
                            {{ $feedbackRecord->feedback_type === 'improvement' ? 'bg-orange-100 text-orange-800' : '' }}
                            {{ $feedbackRecord->feedback_type === 'question' ? 'bg-purple-100 text-purple-800' : '' }}">
                            {{ $feedbackRecord->feedback_type }}
                        </span>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Rating</label>
                        @if($feedbackRecord->rating)
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= $feedbackRecord->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                @endfor
                                <span class="ml-2 text-sm text-gray-600 dark:text-gray-300">({{ $feedbackRecord->rating }}/5)</span>
                            </div>
                        @else
                            <p class="text-gray-500 dark:text-gray-400">No rating provided</p>
                        @endif
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Generated Date</label>
                        <p class="text-gray-700 dark:text-[#FFC8FB]">{{ $feedbackRecord->generated_at ? $feedbackRecord->generated_at->format('F j, Y g:i A') : $feedbackRecord->created_at->format('F j, Y g:i A') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Status</label>
                        <div class="flex items-center space-x-4">
                            <span class="px-2 py-1 text-xs rounded-full
                                {{ $feedbackRecord->is_read ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $feedbackRecord->is_read ? 'Read' : 'Unread' }}
                            </span>
                            @if($feedbackRecord->is_anonymous)
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">
                                    Anonymous
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-span-2">
                        <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Feedback Content</label>
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $feedbackRecord->feedback_text }}</p>
                        </div>
                    </div>

                    @if($feedbackRecord->recommendations && count($feedbackRecord->recommendations) > 0)
                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-3">Recommendations</label>
                            <div class="bg-gradient-to-r from-pink-50 to-purple-50 dark:from-gray-800 dark:to-gray-700 rounded-lg p-4 border border-pink-200 dark:border-gray-600">
                                <ul class="space-y-2">
                                    @foreach($feedbackRecord->recommendations as $recommendation)
                                        <li class="flex items-start">
                                            <i class="fas fa-lightbulb text-yellow-500 mt-1 mr-3 flex-shrink-0"></i>
                                            <span class="text-gray-700 dark:text-gray-300">{{ $recommendation }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200 dark:border-gray-600">
                    <div class="flex space-x-4">
                        @if(!$feedbackRecord->is_read)
                            <form action="{{ route('admin.feedback-records.mark-read', $feedbackRecord) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 text-sm">
                                    <i class="fas fa-check mr-1"></i>Mark as Read
                                </button>
                            </form>
                        @endif
                    </div>
                    
                    <div class="flex space-x-4">
                        <a href="{{ route('admin.feedback-records.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            <i class="fas fa-arrow-left mr-1"></i>Back
                        </a>
                        <a href="{{ route('admin.feedback-records.edit', $feedbackRecord) }}" class="px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5]">
                            <i class="fas fa-edit mr-1"></i>Edit
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>