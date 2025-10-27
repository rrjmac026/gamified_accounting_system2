<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-xl rounded-lg p-6">
                <div class="mb-6 flex justify-between items-center">
                    <h2 class="text-2xl font-bold text-[#FF92C2]">Task Details</h2>
                    <a href="{{ route('instructors.student-tasks.index') }}" 
                       class="text-[#FF92C2] hover:text-[#ff6fb5]">
                        Back to List
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600">Student</h3>
                        <p class="text-lg">{{ $studentTask->student->user->name }}</p>
                    </div>
                    
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600">Task</h3>
                        <p class="text-lg">{{ $studentTask->task->title }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-gray-600">Status</h3>
                        <p class="text-lg">{{ Str::title($studentTask->status) }}</p>
                    </div>

                    <div>
                        <h3 class="text-sm font-semibold text-gray-600">Score</h3>
                        <p class="text-lg">{{ $studentTask->score ?? 'Not graded' }}</p>
                    </div>
                </div>

                @if($studentTask->status === 'submitted')
                    <div class="mt-6">
                        <a href="{{ route('instructors.student-tasks.grade', $studentTask) }}"
                           class="inline-block px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5]">
                            Grade Submission
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
