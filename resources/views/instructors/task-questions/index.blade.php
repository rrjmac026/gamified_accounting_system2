<x-app-layout>
    <div class="flex justify-end px-4 sm:px-8 mt-4">
        <a href="{{ route('instructors.task-questions.create', request('task_id')) }}" 
           class="w-full sm:w-auto inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-[#FF92C2] hover:bg-[#ff6fb5] rounded-lg shadow-sm hover:shadow transition-all duration-200">
            <i class="fas fa-plus mr-2"></i>Add Question
        </a>
    </div>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-700">
                    @if (session('success'))
                        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-200 text-green-700 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full table-auto">
                            <thead class="bg-[#FFC8FB]">
                                <tr>
                                    <th class="py-3 px-6 text-left text-sm font-medium text-pink-900">Task</th>
                                    <th class="py-3 px-6 text-left text-sm font-medium text-pink-900">Question</th>
                                    <th class="py-3 px-6 text-left text-sm font-medium text-pink-900">Type</th>
                                    <th class="py-3 px-6 text-left text-sm font-medium text-pink-900">Points</th>
                                    <th class="py-3 px-6 text-left text-sm font-medium text-pink-900">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-[#FFF6FD] divide-y divide-[#FFC8FB]">
                                @forelse($taskQuestions as $question)
                                    <tr class="hover:bg-[#FFD9FF] transition-colors duration-150">
                                        <td class="py-4 px-6 text-sm text-gray-700">{{ $question->task->title }}</td>
                                        <td class="py-4 px-6 text-sm text-gray-700">{{ Str::limit($question->question_text, 50) }}</td>
                                        <td class="py-4 px-6 text-sm text-gray-700 capitalize">{{ str_replace('_', ' ', $question->question_type) }}</td>
                                        <td class="py-4 px-6 text-sm text-gray-700">{{ $question->points }}</td>
                                        <td class="py-4 px-6 text-sm space-x-2">
                                            <a href="{{ route('instructors.task-questions.show', $question->id) }}" class="text-blue-500 hover:text-blue-700">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('instructors.task-questions.edit', $question->id) }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('instructors.task-questions.destroy', $question->id) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-4 px-6 text-sm text-center text-gray-500">No questions found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
