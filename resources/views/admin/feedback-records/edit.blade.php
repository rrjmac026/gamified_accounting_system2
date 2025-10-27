<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] dark:bg-[#595758] overflow-hidden shadow-lg sm:rounded-2xl p-8 border border-[#FFC8FB]/50">
                <h2 class="text-2xl font-bold text-[#FF92C2] dark:text-[#FFC8FB] mb-6">Edit Feedback</h2>

                <form action="{{ route('feedback-records.update', $feedbackRecord) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Student</label>
                            <select name="student_id" class="w-full rounded-lg bg-white dark:bg-[#595758] border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200" required>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ $feedbackRecord->student_id == $student->id ? 'selected' : '' }}>
                                        {{ $student->user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Task</label>
                            <select name="task_id" class="w-full rounded-lg bg-white dark:bg-[#595758] border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200" required>
                                @foreach($tasks as $task)
                                    <option value="{{ $task->id }}" {{ $feedbackRecord->task_id == $task->id ? 'selected' : '' }}>
                                        {{ $task->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Feedback Type</label>
                            <select name="feedback_type" class="w-full rounded-lg bg-white dark:bg-[#595758] border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200" required>
                                <option value="positive" {{ $feedbackRecord->feedback_type == 'positive' ? 'selected' : '' }}>Positive</option>
                                <option value="constructive" {{ $feedbackRecord->feedback_type == 'constructive' ? 'selected' : '' }}>Constructive</option>
                                <option value="suggestion" {{ $feedbackRecord->feedback_type == 'suggestion' ? 'selected' : '' }}>Suggestion</option>
                            </select>
                        </div>

                        <div class="col-span-2">
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Feedback Content</label>
                            <textarea name="content" rows="4" class="w-full rounded-lg bg-white dark:bg-[#595758] border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200" required>{{ $feedbackRecord->content }}</textarea>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('feedback-records.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Cancel</a>
                        <button type="submit" class="px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5]">Update Feedback</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
