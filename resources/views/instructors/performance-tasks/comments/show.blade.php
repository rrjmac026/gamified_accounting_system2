<x-app-layout>
<div class="py-8">
<div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
        <a href="{{ route('instructors.performance-tasks.index') }}" class="hover:text-[#FF92C2] transition">Tasks</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <a href="{{ route('instructors.performance-tasks.comments.index') }}" class="hover:text-[#FF92C2] transition">Conversations</a>
        <i class="fas fa-chevron-right text-xs"></i>
        <span class="text-gray-600 font-medium truncate">{{ $task->title }}</span>
    </div>

    {{-- Task header card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm mb-6 overflow-hidden">
        <div class="h-1 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB]"></div>
        <div class="p-5 flex items-center justify-between flex-wrap gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-[#FF92C2] to-[#FFC8FB] flex items-center justify-center shadow-sm">
                    <i class="fas fa-file-alt text-white"></i>
                </div>
                <div>
                    <h1 class="font-bold text-gray-800 text-lg leading-tight">{{ $task->title }}</h1>
                    <div class="flex items-center gap-3 mt-0.5 text-xs text-gray-400">
                        <span><i class="fas fa-book mr-1"></i>{{ $task->subject->subject_name ?? 'N/A' }}</span>
                        <span><i class="fas fa-users mr-1"></i>{{ $task->section->name ?? 'N/A' }}</span>
                        <span><i class="far fa-calendar mr-1"></i>Due {{ $task->due_date ? $task->due_date->format('M d, Y') : '—' }}</span>
                    </div>
                </div>
            </div>

            <a href="{{ route('instructors.performance-tasks.show', $task->id) }}"
               class="text-xs text-gray-500 hover:text-[#FF92C2] transition flex items-center gap-1.5">
                <i class="fas fa-eye"></i> View Task
            </a>
        </div>
    </div>

    {{-- Comment thread card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
        <div class="flex items-center gap-2 mb-5">
            <i class="fas fa-comments text-[#FF92C2]"></i>
            <h2 class="font-semibold text-gray-800">Conversation</h2>
            <span class="ml-auto text-xs text-gray-400">Replies are visible to all students in this section</span>
        </div>

        @include('components.task-comment-thread', [
            'comments'   => $comments,
            'task'       => $task,
            'storeRoute' => 'instructors.performance-tasks.comments.store',
            'role'       => 'instructor',
            'step'       => $step,
            'stepTitles' => $stepTitles,
        ])
    </div>

</div>
</div>
</x-app-layout>