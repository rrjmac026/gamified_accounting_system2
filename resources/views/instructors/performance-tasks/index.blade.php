<x-app-layout>
    <div class="flex justify-end px-4 sm:px-8 mt-4">
        <div class="flex gap-4">
            <a href="{{ route('instructors.performance-tasks.create') }}" 
               class="w-full sm:w-auto inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-[#D5006D] hover:bg-[#FF6F91] rounded-lg shadow-sm hover:shadow">
                <i class="fas fa-plus mr-2"></i>Create Performance Task
            </a>
        </div>
    </div>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Error Messages -->
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg">
                    <h4 class="font-semibold mb-2">Errors:</h4>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)
                            <li class="text-sm">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-[#FAF3F3] overflow-hidden shadow-xl rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-[#D5006D]">Performance Tasks Management</h2>
                        <div class="text-sm text-[#FF9AAB]">
                            Manage all performance tasks
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-[#FF9AAB]/20">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-[#D5006D]">Title</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-[#D5006D]">Description</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-[#D5006D]">Section</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-[#D5006D]">Subject</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-[#D5006D]">Due Date</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-[#D5006D]">XP Reward</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-[#D5006D]">Max Attempts</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-[#D5006D]">Students</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold text-[#D5006D]">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-[#FF9AAB]/30">
                                @forelse($tasks as $task)
                                    <tr class="hover:bg-[#FAF3F3]">
                                        <td class="px-6 py-4 font-medium text-[#D5006D]">{{ $task->title }}</td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-600 max-w-xs truncate">
                                                {!! Str::limit(strip_tags($task->description), 50) !!}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $task->section->name }}
                                        </td>
                                        <td class="px-6 py-4">{{ $task->subject->subject_name }}</td>
                                        <td class="px-6 py-4">
                                            @if($task->due_date)
                                                <div class="text-sm">
                                                    <div class="font-medium text-[#D5006D]">{{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}</div>
                                                    <div class="text-[#FF6F91] text-xs">{{ \Carbon\Carbon::parse($task->due_date)->format('h:i A') }}</div>
                                                </div>
                                            @else
                                                <span class="text-gray-500">No due date</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ $task->xp_reward }} XP
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="text-sm font-medium text-[#D5006D]">{{ $task->max_attempts }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm">
                                                <div class="text-[#D5006D] font-medium">
                                                    {{ $task->section->students->count() }} students
                                                </div>
                                                <div class="text-[#FF6F91] text-xs">
                                                    Max Score: {{ $task->max_score }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 space-x-3">
                                            <a href="{{ route('instructors.performance-tasks.submissions.show', $task->id) }}" 
                                               class="text-[#D5006D] hover:text-[#FF6F91]" title="View Submissions">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('instructors.performance-tasks.edit', $task->id) }}" 
                                               class="text-[#D5006D] hover:text-[#FF6F91]" title="Edit Task">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('instructors.performance-tasks.destroy', $task->id) }}" 
                                                  method="POST" 
                                                  class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this performance task? This action cannot be undone.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700" title="Delete Task">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-8 text-center">
                                            <div class="flex flex-col items-center">
                                                <i class="fas fa-chart-line text-4xl text-[#FF9AAB] mb-3"></i>
                                                <p class="text-[#D5006D] font-medium mb-2">No performance tasks found.</p>
                                                <a href="{{ route('instructors.performance-tasks.create') }}" 
                                                   class="text-[#FF6F91] hover:underline">
                                                    Create your first performance task
                                                </a>
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
                    <h3 class="text-sm font-medium text-[#FF6F91]">Total Performance Tasks</h3>
                    <p class="text-2xl font-bold text-[#D5006D]">{{ $tasks->count() }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow border border-[#FF9AAB]/30">
                    <h3 class="text-sm font-medium text-[#FF6F91]">Total Students Assigned</h3>
                    <p class="text-2xl font-bold text-[#D5006D]">
                        {{ $tasks->sum(function($task) { 
                            return $task->section->students->count(); 
                        }) }}
                    </p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow border border-[#FF9AAB]/30">
                    <h3 class="text-sm font-medium text-[#FF6F91]">Total XP Available</h3>
                    <p class="text-2xl font-bold text-green-600">
                        {{ $tasks->sum('xp_reward') }} XP
                    </p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow border border-[#FF9AAB]/30">
                    <h3 class="text-sm font-medium text-[#FF6F91]">Avg Max Score</h3>
                    <p class="text-2xl font-bold text-blue-600">
                        {{ $tasks->count() > 0 ? number_format($tasks->avg('max_score'), 1) : 0 }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>