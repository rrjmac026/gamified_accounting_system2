<x-app-layout>
    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-[#D5006D]">Answer Sheets</h1>
            <p class="text-sm text-[#FF6F91] mt-1">Manage answer sheets for performance tasks</p>
        </div>

        @if($tasksBySection->count() > 0)
            <div class="space-y-8">
                @foreach ($tasksBySection as $sectionName => $sectionTasks)
                    <div>
                        {{-- Section Header --}}
                        <div class="flex items-center gap-3 mb-3">
                            <div class="flex items-center gap-2">
                                <div class="w-3 h-3 rounded-full bg-gradient-to-r from-[#D5006D] to-[#FF6F91]"></div>
                                <h2 class="text-base font-semibold text-[#D5006D] uppercase tracking-wide">
                                    {{ $sectionName }}
                                </h2>
                            </div>
                            <div class="flex-1 h-px bg-[#FF9AAB]/40"></div>
                            <span class="text-xs text-[#FF6F91] font-medium">
                                {{ $sectionTasks->count() }} task{{ $sectionTasks->count() > 1 ? 's' : '' }}
                            </span>
                        </div>

                        {{-- Tasks Table --}}
                        <div class="bg-[#FAF3F3] rounded-lg shadow-sm border border-[#FF9AAB] overflow-hidden">
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-[#FF9AAB]/50">
                                    <thead class="bg-[#FF9AAB]/20">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-[#D5006D] uppercase tracking-wider">
                                                Task Title
                                            </th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-[#D5006D] uppercase tracking-wider">
                                                Subject
                                            </th>
                                            <th class="px-6 py-3 text-center text-xs font-medium text-[#D5006D] uppercase tracking-wider">
                                                Steps Configured
                                            </th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-[#D5006D] uppercase tracking-wider">
                                                Action
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-[#FF9AAB]/30">
                                        @foreach ($sectionTasks as $task)
                                            @php
                                                $enabledCount   = count($task->enabled_steps_list);
                                                $configuredCount = \App\Models\PerformanceTaskExercise::where('performance_task_id', $task->id)
                                                    ->where('order', 1)
                                                    ->count();
                                                $pct = $enabledCount > 0 ? ($configuredCount / $enabledCount) * 100 : 0;
                                                $allDone = $configuredCount >= $enabledCount;
                                            @endphp
                                            <tr class="hover:bg-[#FAF3F3] transition-colors">
                                                <td class="px-6 py-4 text-sm font-medium text-[#D5006D]">
                                                    {{ $task->title }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500">
                                                    {{ $task->subject->subject_name ?? '—' }}
                                                </td>
                                                <td class="px-6 py-4 text-center">
                                                    <div class="flex items-center justify-center gap-2">
                                                        <span class="text-sm font-medium {{ $allDone ? 'text-green-600' : 'text-[#D5006D]' }}">
                                                            {{ $configuredCount }}/{{ $enabledCount }}
                                                        </span>
                                                        <div class="w-24 bg-[#FF9AAB]/30 rounded-full h-2">
                                                            <div class="h-2 rounded-full transition-all {{ $allDone ? 'bg-green-500' : 'bg-gradient-to-r from-[#D5006D] to-[#FF6F91]' }}"
                                                                 style="width: {{ $pct }}%">
                                                            </div>
                                                        </div>
                                                        @if($allDone)
                                                            <svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                            </svg>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    <a href="{{ route('instructors.performance-tasks.answer-sheets.show', $task->id) }}"
                                                       class="text-[#D5006D] hover:text-[#FF6F91] text-sm font-medium transition-colors">
                                                        Manage →
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="bg-[#FAF3F3] rounded-lg shadow-sm border border-[#FF9AAB] p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-[#FF9AAB] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                <h3 class="text-sm font-medium text-[#D5006D] mb-1">No tasks found</h3>
                <p class="text-sm text-[#FF6F91]">Create a performance task to get started.</p>
            </div>
        @endif
    </div>
</x-app-layout>