<x-app-layout>
    <div class="flex justify-end px-4 sm:px-8 mt-4">
        <div class="flex gap-3">
            <a href="{{ route('instructors.performance-tasks.comments.index') }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-[#D5006D] bg-white border border-[#FF9AAB] hover:bg-[#FFF0F5] rounded-lg shadow-sm hover:shadow transition-all relative">
                <i class="fas fa-comments"></i>
                Conversations
                @php
                    $totalUnread = \App\Models\PerformanceTaskComment::whereHas('task', function($q) {
                            $q->where('instructor_id', auth()->user()->instructor->id);
                        })
                        ->where('sender_role', 'student')
                        ->where('is_read', false)
                        ->count();
                @endphp
                @if($totalUnread > 0)
                    <span class="absolute -top-1.5 -right-1.5 inline-flex items-center justify-center min-w-[20px] h-5 px-1.5
                                 bg-[#D5006D] text-white text-[10px] font-bold rounded-full shadow">
                        {{ $totalUnread > 99 ? '99+' : $totalUnread }}
                    </span>
                    <span class="absolute -top-1.5 -right-1.5 inline-flex min-w-[20px] h-5 rounded-full bg-[#D5006D] opacity-40 animate-ping"></span>
                @endif
            </a>

            <a href="{{ route('instructors.performance-tasks.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-[#D5006D] hover:bg-[#FF6F91] rounded-lg shadow-sm hover:shadow transition-all">
                <i class="fas fa-plus"></i>
                <span class="hidden sm:inline">Create Performance Task</span>
                <span class="sm:hidden">Create</span>
            </a>
        </div>
    </div>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

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

            {{-- ── Desktop table (md+) ──────────────────────────────────────── --}}
            <div class="bg-[#FAF3F3] overflow-hidden shadow-xl rounded-lg hidden md:block">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-[#D5006D]">Performance Tasks Management</h2>
                        <div class="text-sm text-[#FF9AAB]">Manage all performance tasks</div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-[#FF9AAB]/20">
                                <tr>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#D5006D]">Title</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#D5006D]">Section</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#D5006D]">Subject</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#D5006D]">Due Date</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#D5006D]">XP</th>
                                    <th class="px-4 py-3 text-center text-sm font-semibold text-[#D5006D]">Attempts</th>
                                    <th class="px-4 py-3 text-left text-sm font-semibold text-[#D5006D]">Students</th>
                                    <th class="px-4 py-3 text-center text-sm font-semibold text-[#D5006D]">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-[#FF9AAB]/30">
                                @forelse($tasks as $task)
                                    @php
                                        $taskUnread = \App\Models\PerformanceTaskComment::where('performance_task_id', $task->id)
                                            ->where('sender_role', 'student')
                                            ->where('is_read', false)
                                            ->count();
                                    @endphp
                                    <tr class="hover:bg-[#FAF3F3] transition-colors">
                                        <td class="px-4 py-4">
                                            <div class="font-medium text-[#D5006D] max-w-[180px] truncate">{{ $task->title }}</div>
                                            <div class="text-xs text-gray-400 mt-0.5 max-w-[180px] truncate">
                                                {!! Str::limit(strip_tags($task->description), 40) !!}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-sm text-gray-700">{{ $task->section->name }}</td>
                                        <td class="px-4 py-4 text-sm text-gray-700">{{ $task->subject->subject_name }}</td>
                                        <td class="px-4 py-4">
                                            @if($task->due_date)
                                                <div class="text-sm font-medium text-[#D5006D]">{{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}</div>
                                                <div class="text-xs text-[#FF6F91]">{{ \Carbon\Carbon::parse($task->due_date)->format('h:i A') }}</div>
                                            @else
                                                <span class="text-gray-400 text-sm">No due date</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ $task->xp_reward }} XP
                                            </span>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <span class="text-sm font-medium text-[#D5006D]">{{ $task->max_attempts }}</span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="text-sm font-medium text-[#D5006D]">{{ $task->section->students->count() }} students</div>
                                            <div class="text-xs text-[#FF6F91]">Max: {{ $task->max_score }}</div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex gap-2 pt-3 border-t border-gray-100">
                                                {{-- Show --}}
                                                <a href="{{ route('instructors.performance-tasks.show', $task->id) }}"
                                                class="inline-flex items-center justify-center w-9 h-9 text-blue-500 bg-blue-50 rounded-lg hover:bg-blue-100 transition-colors" title="View">
                                                    <i class="fas fa-eye text-sm"></i>
                                                </a>

                                                {{-- Exercises --}}
                                                <a href="{{ route('instructors.performance-tasks.exercises.show', $task->id) }}"
                                                class="inline-flex items-center justify-center w-9 h-9 text-purple-500 bg-purple-50 rounded-lg hover:bg-purple-100 transition-colors" title="Exercises">
                                                    <i class="fas fa-tasks text-sm"></i>
                                                </a>

                                                <a href="{{ route('instructors.performance-tasks.submissions.show', $task->id) }}"
                                                class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-[#D5006D] bg-[#FFF0F5] rounded-lg hover:bg-[#FFD6E8] transition-colors">
                                                    <i class="fas fa-clipboard-list"></i> Submissions
                                                </a>

                                                <a href="{{ route('instructors.performance-tasks.comments.show', $task->id) }}"
                                                class="relative inline-flex items-center justify-center w-9 h-9 rounded-lg transition-colors
                                                        {{ $taskUnread > 0 ? 'bg-[#D5006D] text-white hover:bg-[#FF6F91]' : 'text-[#D5006D] bg-[#FFF0F5] hover:bg-[#FFD6E8]' }}">
                                                    <i class="fas fa-comments text-sm"></i>
                                                    @if($taskUnread > 0)
                                                        <span class="absolute -top-1.5 -right-1.5 inline-flex items-center justify-center min-w-[16px] h-4 px-1
                                                                    bg-white text-[#D5006D] text-[9px] font-bold rounded-full border border-[#D5006D]">
                                                            {{ $taskUnread > 9 ? '9+' : $taskUnread }}
                                                        </span>
                                                    @endif
                                                </a>

                                                <a href="{{ route('instructors.performance-tasks.edit', $task->id) }}"
                                                class="inline-flex items-center justify-center w-9 h-9 text-[#D5006D] bg-[#FFF0F5] rounded-lg hover:bg-[#FFD6E8] transition-colors" title="Edit">
                                                    <i class="fas fa-edit text-sm"></i>
                                                </a>

                                                <form action="{{ route('instructors.performance-tasks.destroy', $task->id) }}"
                                                    method="POST" class="inline"
                                                    onsubmit="return confirm('Delete this task? This cannot be undone.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                            class="inline-flex items-center justify-center w-9 h-9 text-red-400 bg-red-50 rounded-lg hover:bg-red-100 transition-colors" title="Delete">
                                                        <i class="fas fa-trash text-sm"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-12 text-center">
                                            <i class="fas fa-chart-line text-4xl text-[#FF9AAB] mb-3 block"></i>
                                            <p class="text-[#D5006D] font-medium mb-2">No performance tasks found.</p>
                                            <a href="{{ route('instructors.performance-tasks.create') }}" class="text-[#FF6F91] hover:underline text-sm">
                                                Create your first performance task
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ── Mobile cards (< md) ──────────────────────────────────────── --}}
            <div class="md:hidden space-y-4">
                <h2 class="text-xl font-bold text-[#D5006D] px-1">Performance Tasks</h2>

                @forelse($tasks as $task)
                    @php
                        $taskUnread = \App\Models\PerformanceTaskComment::where('performance_task_id', $task->id)
                            ->where('sender_role', 'student')
                            ->where('is_read', false)
                            ->count();
                    @endphp
                    <div class="bg-white rounded-xl border border-[#FF9AAB]/30 shadow-sm overflow-hidden">
                        {{-- Pink top bar --}}
                        <div class="h-1 bg-gradient-to-r from-[#D5006D] to-[#FF9AAB]"></div>

                        <div class="p-4">
                            {{-- Title + badges --}}
                            <div class="flex items-start justify-between gap-2 mb-3">
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-semibold text-[#D5006D] truncate">{{ $task->title }}</h3>
                                    <p class="text-xs text-gray-400 mt-0.5 line-clamp-1">
                                        {!! Str::limit(strip_tags($task->description), 60) !!}
                                    </p>
                                </div>
                                <span class="shrink-0 px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $task->xp_reward }} XP
                                </span>
                            </div>

                            {{-- Info grid --}}
                            <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 mb-4">
                                <div class="flex items-center gap-1.5">
                                    <i class="fas fa-users text-[#FF9AAB] w-3.5"></i>
                                    <span class="truncate">{{ $task->section->name }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <i class="fas fa-book text-[#FF9AAB] w-3.5"></i>
                                    <span class="truncate">{{ $task->subject->subject_name }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <i class="far fa-calendar text-[#FF9AAB] w-3.5"></i>
                                    <span>{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('M d, Y') : 'No deadline' }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <i class="fas fa-user-graduate text-[#FF9AAB] w-3.5"></i>
                                    <span>{{ $task->section->students->count() }} students</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <i class="fas fa-redo text-[#FF9AAB] w-3.5"></i>
                                    <span>{{ $task->max_attempts }} attempts</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <i class="fas fa-star text-[#FF9AAB] w-3.5"></i>
                                    <span>Max: {{ $task->max_score }}</span>
                                </div>
                            </div>

                            {{-- Action buttons --}}
                            <div class="flex gap-2 pt-3 border-t border-gray-100">
                                <a href="{{ route('instructors.performance-tasks.submissions.show', $task->id) }}"
                                   class="flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium text-[#D5006D] bg-[#FFF0F5] rounded-lg hover:bg-[#FFD6E8] transition-colors">
                                    <i class="fas fa-eye"></i> Submissions
                                </a>

                                <a href="{{ route('instructors.performance-tasks.comments.show', $task->id) }}"
                                   class="relative flex-1 inline-flex items-center justify-center gap-1.5 px-3 py-2 text-xs font-medium rounded-lg transition-colors
                                          {{ $taskUnread > 0 ? 'bg-[#D5006D] text-white hover:bg-[#FF6F91]' : 'text-[#D5006D] bg-[#FFF0F5] hover:bg-[#FFD6E8]' }}">
                                    <i class="fas fa-comments"></i>
                                    Discussion
                                    @if($taskUnread > 0)
                                        <span class="ml-1 inline-flex items-center justify-center min-w-[18px] h-4.5 px-1.5
                                                     bg-white text-[#D5006D] text-[10px] font-bold rounded-full">
                                            {{ $taskUnread > 9 ? '9+' : $taskUnread }}
                                        </span>
                                    @endif
                                </a>

                                <a href="{{ route('instructors.performance-tasks.edit', $task->id) }}"
                                   class="inline-flex items-center justify-center w-9 h-9 text-[#D5006D] bg-[#FFF0F5] rounded-lg hover:bg-[#FFD6E8] transition-colors" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>

                                <form action="{{ route('instructors.performance-tasks.destroy', $task->id) }}"
                                      method="POST" class="inline"
                                      onsubmit="return confirm('Delete this task? This cannot be undone.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center justify-center w-9 h-9 text-red-400 bg-red-50 rounded-lg hover:bg-red-100 transition-colors" title="Delete">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 bg-white rounded-xl border border-[#FF9AAB]/30">
                        <i class="fas fa-chart-line text-4xl text-[#FF9AAB] mb-3 block"></i>
                        <p class="text-[#D5006D] font-medium mb-2">No performance tasks found.</p>
                        <a href="{{ route('instructors.performance-tasks.create') }}" class="text-[#FF6F91] hover:underline text-sm">
                            Create your first performance task
                        </a>
                    </div>
                @endforelse
            </div>

            {{-- ── Quick Stats ──────────────────────────────────────────────── --}}
            <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-lg shadow border border-[#FF9AAB]/30">
                    <h3 class="text-xs font-medium text-[#FF6F91]">Total Tasks</h3>
                    <p class="text-2xl font-bold text-[#D5006D]">{{ $tasks->count() }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow border border-[#FF9AAB]/30">
                    <h3 class="text-xs font-medium text-[#FF6F91]">Students Assigned</h3>
                    <p class="text-2xl font-bold text-[#D5006D]">
                        {{ $tasks->sum(fn($t) => $t->section->students->count()) }}
                    </p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow border border-[#FF9AAB]/30">
                    <h3 class="text-xs font-medium text-[#FF6F91]">Total XP Available</h3>
                    <p class="text-2xl font-bold text-green-600">{{ $tasks->sum('xp_reward') }} XP</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow border border-[#FF9AAB]/30">
                    <h3 class="text-xs font-medium text-[#FF6F91]">Avg Max Score</h3>
                    <p class="text-2xl font-bold text-blue-600">
                        {{ $tasks->count() > 0 ? number_format($tasks->avg('max_score'), 1) : 0 }}
                    </p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>