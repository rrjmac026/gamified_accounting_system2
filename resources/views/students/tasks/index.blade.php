<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-700">
                    <h2 class="text-2xl font-bold text-[#FF92C2] mb-6">Tasks</h2>

                    @if (session('success'))
                        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-200 text-green-700 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="w-full overflow-x-auto">
                        <table class="min-w-full table-auto text-xs sm:text-sm">
                            <thead class="bg-[#FFC8FB]">
                                <tr>
                                    <th class="py-2 sm:py-3 px-3 sm:px-6 text-left font-medium text-pink-900">Title</th>
                                    <th class="py-2 sm:py-3 px-3 sm:px-6 text-left font-medium text-pink-900">Subject</th>
                                    <th class="py-2 sm:py-3 px-3 sm:px-6 text-left font-medium text-pink-900">Due Date</th>
                                    <th class="py-2 sm:py-3 px-3 sm:px-6 text-left font-medium text-pink-900">Status</th>
                                    <th class="py-2 sm:py-3 px-3 sm:px-6 text-left font-medium text-pink-900">Score</th>
                                    <th class="py-2 sm:py-3 px-3 sm:px-6 text-left font-medium text-pink-900">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-[#FFF6FD] divide-y divide-[#FFC8FB]">
                            @foreach($tasks as $task)
                                <tr 
                                    class="hover:bg-[#FFD9FF] transition-colors duration-150 cursor-pointer"
                                    onclick="window.location='{{ route('students.tasks.show', $task) }}'">
                                    
                                    <td class="px-6 py-4 text-gray-900">{{ $task->title }}</td>
                                    <td class="px-6 py-4 text-gray-900">{{ $task->subject->subject_name }}</td>
                                    <td class="px-6 py-4 text-gray-900">
                                        {{ $task->due_date->format('M d, Y g:i A') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @php $submission = $task->submissions->first(); @endphp
                                        <span @class([
                                            'px-2 py-1 text-xs rounded-full',
                                            'bg-yellow-100 text-yellow-800' => $task->pivot->status === 'assigned',
                                            'bg-blue-100 text-blue-800' => $task->pivot->status === 'in_progress',
                                            'bg-green-100 text-green-800' => $task->pivot->status === 'submitted',
                                            'bg-purple-100 text-purple-800' => $task->pivot->status === 'graded',
                                            'bg-red-100 text-red-800' => in_array($task->pivot->status, ['late', 'missing', 'overdue'])
                                        ])>
                                            {{ ucfirst($task->pivot->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-900">
                                        {{ $submission && $submission->score !== null ? $submission->score : 'Not graded' }}
                                    </td>
                                    <td class="px-6 py-4 text-[#FF92C2] hover:text-[#ff6fb5]">
                                        View Details
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
