<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] backdrop-blur-sm overflow-hidden shadow-lg rounded-lg p-8 border border-[#FFC8FB]/50">
                <h2 class="text-2xl font-bold text-[#FF92C2] mb-6">Assign Task to Student</h2>

                <form action="{{ route('instructors.student-tasks.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Student</label>
                            <select name="student_id" required class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">
                                <option value="">Select Student</option>
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}">{{ $student->user->name }}</option>
                                @endforeach
                            </select>
                            @error('student_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Task</label>
                            <select name="task_id" required class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">
                                <option value="">Select Task</option>
                                @foreach($tasks as $task)
                                    <option value="{{ $task->id }}">{{ $task->title }}</option>
                                @endforeach
                            </select>
                            @error('task_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Status</label>
                            <select name="status" required class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">
                                <option value="assigned">Assigned</option>
                                <option value="in_progress">In Progress</option>
                                <option value="submitted">Submitted</option>
                                <option value="graded">Graded</option>
                                <option value="overdue">Overdue</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Due Date</label>
                            <input type="datetime-local" name="due_date" required 
                                   class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">
                        </div>
                    </div>

                    <div class="flex justify-end gap-4">
                        <a href="{{ route('instructors.student-tasks.index') }}" 
                           class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5]">
                            Assign Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
