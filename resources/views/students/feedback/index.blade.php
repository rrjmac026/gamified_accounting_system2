<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-700">
                    <!-- Error Messages -->
                    @if($errors->any())
                        <div class="mb-4 p-4 rounded-md bg-red-50 border border-red-200">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Error:</h3>
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

                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-[#FF92C2]">My Feedback Records</h2>
                        <a href="{{ route('students.feedback.create') }}" 
                           class="px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5] transition-colors duration-200">
                            Submit New Feedback
                        </a>
                    </div>

                    <div class="w-full overflow-x-auto">
                        <table class="min-w-full table-auto text-xs sm:text-sm">
                            <thead class="bg-[#FFC8FB]">
                                <tr>
                                    <th class="py-2 sm:py-3 px-3 sm:px-6 text-left font-medium text-pink-900">Task</th>
                                    <th class="py-2 sm:py-3 px-3 sm:px-6 text-left font-medium text-pink-900">Rating</th>
                                    <th class="py-2 sm:py-3 px-3 sm:px-6 text-left font-medium text-pink-900">Type</th>
                                    <th class="py-2 sm:py-3 px-3 sm:px-6 text-left font-medium text-pink-900">Date</th>
                                    <th class="py-2 sm:py-3 px-3 sm:px-6 text-left font-medium text-pink-900">Status</th>
                                    <th class="py-2 sm:py-3 px-3 sm:px-6 text-left font-medium text-pink-900">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-[#FFF6FD] divide-y divide-[#FFC8FB]">
                                @forelse($feedbacks as $feedback)
                                    <tr class="hover:bg-[#FFD9FF] transition-colors duration-150">
                                        <td class="px-6 py-4 text-gray-900 dark:text-[black]">
                                            {{ $feedback->task->title }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="fas fa-star {{ $i <= $feedback->rating ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                                                @endfor
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-gray-900 dark:text-[black] capitalize">
                                            {{ $feedback->feedback_type }}
                                        </td>
                                        <td class="px-6 py-4 text-gray-900 dark:text-[black]">
                                            {{ $feedback->created_at->format('M d, Y h:i A') }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs rounded-full {{ $feedback->is_read ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $feedback->is_read ? 'Read' : 'Unread' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <a href="{{ route('students.feedback.show', $feedback) }}" 
                                               class="text-[#FF92C2] hover:text-[#ff6fb5]">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="py-8 px-6 text-sm text-center text-gray-500">
                                            No feedback records found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $feedbacks->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>