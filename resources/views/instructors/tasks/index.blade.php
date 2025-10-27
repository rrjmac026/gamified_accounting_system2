<x-app-layout>
    <div class="flex justify-end px-4 sm:px-8 mt-4">
        <div class="flex gap-4">
            <!-- <a href="{{ route('instructors.tasks.create') }}" 
               class="w-full sm:w-auto inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-[#FF92C2] hover:bg-[#ff6fb5] rounded-lg shadow-sm hover:shadow">
                <i class="fas fa-plus mr-2"></i>Create New Task
            </a> -->

            <a href="{{ route('instructors.performance-tasks.create') }}" 
               class="w-full sm:w-auto inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-[#9B59B6] hover:bg-[#8E44AD] rounded-lg shadow-sm hover:shadow">
                <i class="fas fa-plus mr-2"></i>Create Performance Task
            </a>
            
            <!-- Sync All Button -->
            <form action="{{ route('instructors.tasks.sync-all') }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-sm hover:shadow">
                    <i class="fas fa-sync mr-2"></i>Sync All Tasks
                </button>
            </form>
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

            <!-- Warnings -->
            @if(session('warnings'))
                <div class="mb-4 p-4 bg-yellow-100 border border-yellow-200 text-yellow-700 rounded-lg">
                    <h4 class="font-semibold mb-2">Warnings:</h4>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach(session('warnings') as $warning)
                            <li class="text-sm">{{ $warning }}</li>
                        @endforeach
                    </ul>
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

            <div class="bg-[#FFF0FA] overflow-hidden shadow-xl rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-[#FF92C2]">All Tasks Management</h2>
                        <div class="text-sm text-gray-600">
                            Regular Tasks & Performance Tasks
                        </div>
                    </div>
                    
                    <!-- Filter Tabs -->
                    <div class="mb-4 flex gap-2">
                        <button onclick="filterTasks('all')" id="filter-all" class="px-4 py-2 text-sm font-medium rounded-lg bg-[#FF92C2] text-white">
                            All Tasks
                        </button>
                        <button onclick="filterTasks('regular')" id="filter-regular" class="px-4 py-2 text-sm font-medium rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300">
                            Regular Tasks
                        </button>
                        <button onclick="filterTasks('performance')" id="filter-performance" class="px-4 py-2 text-sm font-medium rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300">
                            Performance Tasks
                        </button>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead class="bg-[#FFC8FB]">
                                <tr>
                                    <th class="px-6 py-3 text-left text-sm font-semibold">Task Type</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold">Title</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold">Type</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold">Section</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold">Subject</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold">Due Date</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold">Status</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold">Submissions</th>
                                    <th class="px-6 py-3 text-left text-sm font-semibold">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-[#FFC8FB]">
                                @forelse($allTasks as $task)
                                    <tr class="hover:bg-[#FFF6FD] task-row" data-task-type="{{ $task->task_type }}">
                                        <td class="px-6 py-4">
                                            @if($task->task_type === 'regular')
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    <i class="fas fa-tasks mr-1"></i>Regular
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                                    <i class="fas fa-chart-line mr-1"></i>Performance
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 font-medium">{{ $task->title }}</td>
                                        <td class="px-6 py-4 capitalize">
                                            @if($task->task_type === 'regular')
                                                {{ $task->type }}
                                            @else
                                                <span class="text-gray-500">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $task->section->name }}
                                        </td>
                                        <td class="px-6 py-4">{{ $task->subject->subject_name }}</td>
                                        <td class="px-6 py-4">
                                            @if($task->task_type === 'regular' && $task->due_date)
                                                {{ $task->due_date->format('M d, Y H:i') }}
                                            @else
                                                <span class="text-gray-500">No due date</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($task->task_type === 'regular')
                                                <span class="px-2 py-1 text-xs rounded-full 
                                                    {{ $task->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ $task->is_active ? 'Active' : 'Inactive' }}
                                                </span>
                                            @else
                                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($task->task_type === 'regular')
                                                <div class="text-sm">
                                                    <div class="text-gray-900 font-medium">
                                                        {{ $task->submissions->count() }} submitted
                                                    </div>
                                                    <div class="text-gray-500 text-xs">
                                                        of {{ $task->students->count() }} assigned
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-sm">
                                                    <div class="text-gray-900 font-medium">
                                                        {{ $task->students->count() }} students
                                                    </div>
                                                    <div class="text-gray-500 text-xs">
                                                        {{ $task->max_attempts }} max attempts
                                                    </div>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 space-x-3">
                                            @if($task->task_type === 'regular')
                                                <a href="{{ route('instructors.tasks.show', $task->id) }}" 
                                                   class="text-[#FF92C2] hover:text-[#ff6fb5]" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('instructors.tasks.edit', $task->id) }}" 
                                                   class="text-[#FF92C2] hover:text-[#ff6fb5]" title="Edit Task">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('instructors.tasks.destroy', $task->id) }}" 
                                                      method="POST" 
                                                      class="inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this task? This will also delete all associated questions and student assignments.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700" title="Delete Task">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('instructors.performance-tasks.submissions.index', ['task' => $task->id]) }}" 
                                                   class="text-[#9B59B6] hover:text-[#8E44AD]" title="View Details">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('instructors.performance-tasks.edit', $task->id) }}" 
                                                   class="text-[#9B59B6] hover:text-[#8E44AD]" title="Edit Task">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('instructors.performance-tasks.destroy', $task->id) }}" 
                                                      method="POST" 
                                                      class="inline"
                                                      onsubmit="return confirm('Are you sure you want to delete this performance task?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-500 hover:text-red-700" title="Delete Task">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-6 py-4 text-center text-gray-500">
                                            No tasks found. <a href="{{ route('instructors.tasks.create') }}" class="text-[#FF92C2] hover:underline">Create your first task</a>
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
                <div class="bg-white p-4 rounded-lg shadow border border-[#FFC8FB]/30">
                    <h3 class="text-sm font-medium text-gray-500">Total Tasks</h3>
                    <p class="text-2xl font-bold text-[#FF92C2]">{{ $allTasks->count() }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow border border-[#FFC8FB]/30">
                    <h3 class="text-sm font-medium text-gray-500">Performance Tasks</h3>
                    <p class="text-2xl font-bold text-purple-600">{{ $allTasks->where('task_type', 'performance')->count() }}</p>
                </div>
                <div class="bg-white p-4 rounded-lg shadow border border-[#FFC8FB]/30">
                    <h3 class="text-sm font-medium text-gray-500">Total Submissions</h3>
                    <p class="text-2xl font-bold text-green-600">
                        {{ $allTasks->where('task_type', 'regular')->sum(function($task) { 
                            return $task->submissions->count(); 
                        }) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function filterTasks(type) {
            const rows = document.querySelectorAll('.task-row');
            const buttons = {
                'all': document.getElementById('filter-all'),
                'regular': document.getElementById('filter-regular'),
                'performance': document.getElementById('filter-performance')
            };

            // Reset all button styles
            Object.values(buttons).forEach(btn => {
                btn.classList.remove('bg-[#FF92C2]', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700');
            });

            // Highlight active button
            buttons[type].classList.remove('bg-gray-200', 'text-gray-700');
            buttons[type].classList.add('bg-[#FF92C2]', 'text-white');

            // Filter rows
            rows.forEach(row => {
                const taskType = row.getAttribute('data-task-type');
                if (type === 'all' || taskType === type) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
</x-app-layout>