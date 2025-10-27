<x-app-layout>
    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-[#D5006D]">Answer Sheets</h1>
            <p class="text-sm text-[#FF6F91] mt-1">Manage answer sheets for performance tasks</p>
        </div>

        <div class="bg-[#FAF3F3] rounded-lg shadow-sm border border-[#FF9AAB] overflow-hidden">
            @if($tasks->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[#FF9AAB]/50">
                        <thead class="bg-[#FF9AAB]/20">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-[#D5006D] uppercase tracking-wider">
                                    Task Title
                                </th>
                                <th class="px-6 py-3 text-center text-xs font-medium text-[#D5006D] uppercase tracking-wider">
                                    Progress
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-[#D5006D] uppercase tracking-wider">
                                    Action
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-[#FF9AAB]/30">
                            @foreach ($tasks as $task)
                                <tr class="hover:bg-[#FAF3F3] transition-colors">
                                    <td class="px-6 py-4 text-sm font-medium text-[#D5006D]">
                                        {{ $task->title }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <span class="text-sm font-medium text-[#D5006D]">
                                                {{ $task->answer_sheets_count }}/10
                                            </span>
                                            <div class="w-24 bg-[#FF9AAB]/30 rounded-full h-2">
                                                <div class="bg-gradient-to-r from-[#D5006D] to-[#FF6F91] h-2 rounded-full transition-all" 
                                                     style="width: {{ ($task->answer_sheets_count / 10) * 100 }}%">
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('instructors.performance-tasks.answer-sheets.show', $task->id) }}"
                                           class="text-[#D5006D] hover:text-[#FF6F91] text-sm font-medium transition-colors">
                                            Manage
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center bg-[#FAF3F3]">
                    <svg class="mx-auto h-12 w-12 text-[#FF9AAB] mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="text-sm font-medium text-[#D5006D] mb-1">No tasks found</h3>
                    <p class="text-sm text-[#FF6F91]">Create a performance task to get started.</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
