{{--
    resources/views/partials/task-chat-button.blade.php

    USAGE (student step view):
        @include('partials.task-chat-button', [
            'task'   => $performanceTask,
            'role'   => 'student',
            'step'   => $step,          // int|null
        ])

    USAGE (instructor submission view):
        @include('partials.task-chat-button', [
            'task'   => $task,
            'role'   => 'instructor',
            'step'   => null,
        ])
--}}

@php
    use App\Models\PerformanceTaskComment;

    $viewerRole   = $role ?? 'student';
    $oppositeRole = $viewerRole === 'student' ? 'instructor' : 'student';

    $unread = PerformanceTaskComment::where('performance_task_id', $task->id)
        ->where('sender_role', $oppositeRole)
        ->where('is_read', false)
        ->when(isset($step) && $step, fn($q) => $q->where('step', $step))
        ->count();

    $routeName = $viewerRole === 'student'
        ? 'students.performance-tasks.comments.show'
        : 'instructors.performance-tasks.comments.show';

    $params = ['task' => $task->id];
    if (isset($step) && $step) $params['step'] = $step;
@endphp

<a href="{{ route($routeName, $params) }}"
   class="inline-flex items-center gap-2 relative px-4 py-2.5 bg-white border border-gray-200 text-gray-700 rounded-xl text-sm font-medium shadow-sm hover:border-[#FF92C2] hover:text-[#FF92C2] hover:shadow-md transition-all duration-200">
    <i class="fas fa-comment-dots text-base"></i>
    <span>{{ isset($step) && $step ? 'Step Discussion' : 'Task Discussion' }}</span>

    @if($unread > 0)
        <span class="absolute -top-1.5 -right-1.5 inline-flex items-center justify-center min-w-[18px] h-[18px] px-1
                     bg-[#FF92C2] text-white text-[10px] font-bold rounded-full shadow animate-bounce">
            {{ $unread > 9 ? '9+' : $unread }}
        </span>
    @endif
</a>