<x-app-layout>
<div class="py-8">
<div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

    {{-- Header --}}
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Task Conversations</h1>
            <p class="text-sm text-gray-500 mt-1">One-on-one discussions with students per performance task</p>
        </div>
        <a href="{{ route('instructors.performance-tasks.index') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 transition">
            <i class="fas fa-arrow-left"></i> Back to Tasks
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Task List --}}
    <div class="space-y-4">
        @forelse($tasks as $task)
            <a href="{{ route('instructors.performance-tasks.comments.show', $task->id) }}"
               class="group flex items-center justify-between bg-white rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 p-5">

                <div class="flex items-center gap-4 min-w-0">
                    {{-- Icon --}}
                    <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-[#FF92C2] to-[#FFC8FB] flex items-center justify-center shadow-sm shrink-0">
                        <i class="fas fa-file-alt text-white text-sm"></i>
                    </div>

                    {{-- Info --}}
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="font-semibold text-gray-800 group-hover:text-[#FF92C2] transition-colors truncate">
                                {{ $task->title }}
                            </span>
                            @if($task->unread_count > 0)
                                <span class="inline-flex items-center justify-center min-w-[22px] h-5 px-1.5 bg-[#FF92C2] text-white text-xs font-bold rounded-full animate-pulse">
                                    {{ $task->unread_count }}
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 mt-1 text-xs text-gray-400">
                            <span><i class="fas fa-book mr-1"></i>{{ $task->subject->subject_name ?? 'N/A' }}</span>
                            <span><i class="fas fa-users mr-1"></i>{{ $task->section->name ?? 'N/A' }}</span>
                            <span><i class="fas fa-comments mr-1"></i>{{ $task->comments_count }} message(s)</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-3 shrink-0 ml-4">
                    @if($task->unread_count > 0)
                        <span class="text-xs font-medium text-[#FF92C2]">Unread</span>
                    @endif
                    <i class="fas fa-chevron-right text-gray-300 group-hover:text-[#FF92C2] transition-colors"></i>
                </div>
            </a>
        @empty
            <div class="text-center py-20 bg-white rounded-xl border border-gray-100">
                <div class="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-comments text-2xl text-gray-300"></i>
                </div>
                <p class="text-gray-500 font-medium">No performance tasks yet.</p>
                <p class="text-gray-400 text-sm mt-1">Create a task to start conversations with students.</p>
            </div>
        @endforelse
    </div>

</div>
</div>
</x-app-layout>