<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] backdrop-blur-sm overflow-hidden shadow-lg rounded-lg p-8 border border-[#FFC8FB]/50">
                <h2 class="text-2xl font-bold text-[#FF92C2] mb-6">Edit Student Task</h2>

                <form action="{{ route('instructors.student-tasks.update', $studentTask) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Student</label>
                            <select name="student_id" required class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">
                                @foreach($students as $student)
                                    <option value="{{ $student->id }}" {{ $studentTask->student_id == $student->id ? 'selected' : '' }}>
                                        {{ $student->user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Task</label>
                            <select name="task_id" required class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">
                                @foreach($tasks as $task)
                                    <option value="{{ $task->id }}" {{ $studentTask->task_id == $task->id ? 'selected' : '' }}>
                                        {{ $task->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Status</label>
                            <select name="status" required class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">
                                @foreach(['assigned', 'in_progress', 'submitted', 'graded', 'overdue'] as $status)
                                    <option value="{{ $status }}" {{ $studentTask->status == $status ? 'selected' : '' }}>
                                        {{ Str::title(str_replace('_', ' ', $status)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Score</label>
                            <input type="number" name="score" value="{{ old('score', $studentTask->score) }}" min="0" step="0.01"
                                   class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">XP Earned</label>
                            <input type="number" name="xp_earned" value="{{ old('xp_earned', $studentTask->xp_earned) }}" min="0"
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
                            Update Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
