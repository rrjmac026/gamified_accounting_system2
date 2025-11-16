<x-app-layout>
    <div class="flex justify-between items-center px-4 sm:px-8 mt-4">
        <a href="{{ route('instructors.performance-tasks.index') }}" 
           class="inline-flex items-center text-[#D5006D] hover:text-[#FF6F91] font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Performance Tasks
        </a>
        <div class="flex gap-4">
            <a href="{{ route('instructors.performance-tasks.edit', $task->id) }}" 
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-[#D5006D] hover:bg-[#FF6F91] rounded-lg shadow-sm hover:shadow">
                <i class="fas fa-edit mr-2"></i>Edit Task
            </a>
        </div>
    </div>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Task Details Card -->
            <div class="bg-[#FAF3F3] overflow-hidden shadow-xl rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h2 class="text-3xl font-bold text-[#D5006D] mb-2">{{ $task->title }}</h2>
                            <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                <span class="flex items-center">
                                    <i class="fas fa-book text-[#FF6F91] mr-2"></i>
                                    {{ $task->subject->subject_name }}
                                </span>
                                <span class="flex items-center">
                                    <i class="fas fa-users text-[#FF6F91] mr-2"></i>
                                    {{ $task->section->name }}
                                </span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="px-4 py-2 text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                {{ $task->xp_reward }} XP Reward
                            </span>
                        </div>
                    </div>

                    <!-- Description -->
                    @if($task->description)
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold text-[#D5006D] mb-3">Description</h3>
                            <div class="bg-white p-4 rounded-lg border border-[#FF9AAB]/30 text-gray-700">
                                {!! nl2br(e($task->description)) !!}
                            </div>
                        </div>
                    @endif

                    <!-- Task Information Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <div class="bg-white p-4 rounded-lg border border-[#FF9AAB]/30">
                            <h4 class="text-xs font-medium text-[#FF6F91] mb-1">Due Date</h4>
                            @if($task->due_date)
                                <p class="text-lg font-bold text-[#D5006D]">
                                    {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}
                                </p>
                                <p class="text-xs text-[#FF6F91]">
                                    {{ \Carbon\Carbon::parse($task->due_date)->format('h:i A') }}
                                </p>
                            @else
                                <p class="text-sm text-gray-500">No due date</p>
                            @endif
                        </div>

                        @if($task->late_until)
                            <div class="bg-white p-4 rounded-lg border border-[#FF9AAB]/30">
                                <h4 class="text-xs font-medium text-[#FF6F91] mb-1">Late Submission Until</h4>
                                <p class="text-lg font-bold text-[#D5006D]">
                                    {{ \Carbon\Carbon::parse($task->late_until)->format('M d, Y') }}
                                </p>
                                <p class="text-xs text-[#FF6F91]">
                                    {{ \Carbon\Carbon::parse($task->late_until)->format('h:i A') }}
                                </p>
                            </div>
                        @endif

                        <div class="bg-white p-4 rounded-lg border border-[#FF9AAB]/30">
                            <h4 class="text-xs font-medium text-[#FF6F91] mb-1">Max Score</h4>
                            <p class="text-2xl font-bold text-blue-600">{{ $task->max_score }}</p>
                        </div>

                        <div class="bg-white p-4 rounded-lg border border-[#FF9AAB]/30">
                            <h4 class="text-xs font-medium text-[#FF6F91] mb-1">Max Attempts</h4>
                            <p class="text-2xl font-bold text-[#D5006D]">{{ $task->max_attempts }}</p>
                        </div>

                        <div class="bg-white p-4 rounded-lg border border-[#FF9AAB]/30">
                            <h4 class="text-xs font-medium text-[#FF6F91] mb-1">Deduction Per Error</h4>
                            <p class="text-2xl font-bold text-red-600">{{ $task->deduction_per_error }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students List -->
            <div class="bg-[#FAF3F3] overflow-hidden shadow-xl rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-[#D5006D]">Assigned Students</h3>
                        <div class="text-sm text-[#FF9AAB]">
                            {{ $task->section->students->count() }} students in {{ $task->section->name }}
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-[#FF9AAB]/20">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-[#D5006D]">Student ID</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-[#D5006D]">Name</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-[#D5006D]">Email</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-[#D5006D]">Status</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-[#D5006D]">Score</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-[#FF9AAB]/30">
                                @forelse($task->section->students as $student)
                                    <tr class="hover:bg-[#FAF3F3]">
                                        <td class="px-6 py-4 font-medium text-[#D5006D]">
                                            {{ $student->student_number }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 flex-shrink-0 rounded-full bg-[#FF9AAB]/30 flex items-center justify-center mr-3">
                                                    <span class="text-[#D5006D] font-semibold">
                                                        {{ substr($student->user->first_name, 0, 1) }}{{ substr($student->user->last_name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900">
                                                        {{ $student->user->first_name }} {{ $student->user->last_name }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ $student->user->email }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Not Started
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-sm text-gray-500">-</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center">
                                            <div class="flex flex-col items-center">
                                                <i class="fas fa-users text-4xl text-[#FF9AAB] mb-3"></i>
                                                <p class="text-[#D5006D] font-medium">No students in this section.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-lg shadow border border-[#FF9AAB]/30">
                    <h3 class="text-sm font-medium text-[#FF6F91]">Total Students</h3>
                    <p class="text-2xl font-bold text-[#D5006D]">{{ $task->section->students->count() }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow border border-[#FF9AAB]/30">
                    <h3 class="text-sm font-medium text-[#FF6F91]">Submissions</h3>
                    <p class="text-2xl font-bold text-blue-600">0</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow border border-[#FF9AAB]/30">
                    <h3 class="text-sm font-medium text-[#FF6F91]">Average Score</h3>
                    <p class="text-2xl font-bold text-green-600">-</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow border border-[#FF9AAB]/30">
                    <h3 class="text-sm font-medium text-[#FF6F91]">Completion Rate</h3>
                    <p class="text-2xl font-bold text-purple-600">0%</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>