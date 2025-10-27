<x-app-layout>
    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-[#FFF0FA] shadow-md sm:rounded-lg p-6">
            <h2 class="text-2xl font-bold text-[#FF92C2] mb-6 capitalize">
                {{ str_replace('_', ' ', $status) }} Tasks
            </h2>

            @if($tasks->count())
                <div class="w-full overflow-x-auto">
                    <table class="min-w-full table-auto text-xs sm:text-sm">
                        <thead class="bg-[#FFC8FB]">
                            <tr>
                                <th class="py-2 px-6 text-left">Task</th>
                                <th class="py-2 px-6 text-left">Subject</th>
                                <th class="py-2 px-6 text-left">Due</th>
                                <th class="py-2 px-6 text-left">Score</th>
                            </tr>
                        </thead>
                        <tbody class="bg-[#FFF6FD] divide-y divide-[#FFC8FB]">
                        @foreach($tasks as $task)
                            <tr 
                                onclick="window.location='{{ route('students.tasks.show', $task) }}'" 
                                class="hover:bg-[#FFD9FF] transition-colors duration-150 cursor-pointer"
                            >
                                <td class="px-6 py-4 text-[#FF92C2] font-medium">
                                    {{ $task->title }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $task->subject->subject_name ?? '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $task->due_date ? $task->due_date->format('M d, Y g:i A') : 'No deadline' }}
                                </td>
                                <td class="px-6 py-4">
                                    @php $submission = $task->submissions->first(); @endphp
                                    {{ $submission && $submission->score !== null 
                                        ? $submission->score . ' / ' . $task->max_score 
                                        : 'Not graded' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    </table>
                </div>
            @else
                <p class="text-gray-500">No {{ str_replace('_', ' ', $status) }} tasks.</p>
            @endif

        </div>
    </div>
</x-app-layout>
