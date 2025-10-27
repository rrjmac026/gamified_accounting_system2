<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-[#FF92C2]">Student Tasks</h2>
                <div class="flex space-x-3">
                    <button onclick="toggleUploadForm()" 
                           class="px-4 py-2 bg-[#8B5CF6] text-white rounded-lg hover:bg-[#7C3AED]">
                        <i class="fas fa-upload mr-2"></i>Upload CSV
                    </button>
                    <a href="{{ route('instructors.student-tasks.create') }}" 
                       class="px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5]">
                        Assign New Task
                    </a>
                </div>
            </div>

            <!-- CSV Upload Section (Initially Hidden) -->
            <div id="uploadSection" class="bg-white shadow-lg rounded-lg mb-6 hidden">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-file-csv text-[#8B5CF6] mr-2"></i>
                        Bulk Upload Student Tasks via CSV
                    </h3>
                    
                    <!-- Instructions -->
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    <strong>CSV Format:</strong> student_email, task_id, status, score (optional), xp_earned (optional)
                                </p>
                                <p class="text-sm text-blue-700 mt-1">
                                    <strong>Status options:</strong> assigned, in_progress, submitted, graded, overdue
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Download Template Button -->
                    <div class="mb-4">
                        <a href="{{ route('instructors.student-tasks.csv-template') }}" 
                           class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded-md hover:bg-green-700">
                            <i class="fas fa-download mr-2"></i>
                            Download Template
                        </a>
                    </div>

                    <!-- Upload Form -->
                    <form action="{{ route('instructors.student-tasks.csv-upload') }}" 
                          method="POST" 
                          enctype="multipart/form-data" 
                          class="space-y-4">
                        @csrf
                        
                        <div>
                            <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-2">
                                Select CSV File
                            </label>
                            <div class="flex items-center space-x-4">
                                <input type="file" 
                                       name="csv_file" 
                                       id="csv_file" 
                                       accept=".csv" 
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-[#FF92C2] file:text-white hover:file:bg-[#ff6fb5]" 
                                       required>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Maximum file size: 2MB</p>
                        </div>

                        <!-- Missing Students Handling Option -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4">
                            <h4 class="text-sm font-medium text-yellow-800 mb-2">
                                <i class="fas fa-user-question mr-2"></i>
                                What should happen if students in CSV are not registered?
                            </h4>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="radio" name="handle_missing_students" value="create" checked 
                                           class="mr-2 text-[#FF92C2] focus:ring-[#FF92C2]">
                                    <span class="text-sm text-yellow-700">
                                        <strong>Auto-create accounts</strong> - Create new student accounts automatically (recommended)
                                    </span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="handle_missing_students" value="skip" 
                                           class="mr-2 text-[#FF92C2] focus:ring-[#FF92C2]">
                                    <span class="text-sm text-yellow-700">
                                        <strong>Skip missing students</strong> - Only assign tasks to existing students
                                    </span>
                                </label>
                            </div>
                            <p class="text-xs text-yellow-600 mt-2">
                                New accounts will be created with temporary passwords. Students will need to verify their email.
                            </p>
                        </div>

                        <div class="flex space-x-3">
                            <button type="submit" 
                                    class="px-4 py-2 bg-[#8B5CF6] text-white rounded-lg hover:bg-[#7C3AED]">
                                <i class="fas fa-upload mr-2"></i>Upload & Process
                            </button>
                            <button type="button" onclick="toggleUploadForm()" 
                                    class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                                Cancel
                            </button>
                        </div>

                        @if ($errors->any())
                            <div class="bg-red-50 border border-red-200 rounded-md p-3">
                                <div class="flex">
                                    <i class="fas fa-exclamation-circle text-red-400 mr-2 mt-0.5"></i>
                                    <div>
                                        <h4 class="text-sm font-medium text-red-800">Upload Errors:</h4>
                                        <div class="text-sm text-red-700 mt-1 whitespace-pre-line">
                                            @foreach ($errors->all() as $error)
                                                {{ $error }}
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 rounded-md p-4 mb-6">
                    <div class="flex">
                        <i class="fas fa-check-circle text-green-400 mr-2"></i>
                        <div>
                            <p class="text-green-800 whitespace-pre-line">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('warnings'))
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-6">
                    <div class="flex">
                        <i class="fas fa-exclamation-triangle text-yellow-400 mr-2 mt-0.5"></i>
                        <div>
                            <h4 class="text-sm font-medium text-yellow-800">Upload Warnings:</h4>
                            <ul class="text-sm text-yellow-700 list-disc list-inside mt-1">
                                @foreach(session('warnings') as $warning)
                                    <li>{{ $warning }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 border border-red-200 rounded-md p-4 mb-6">
                    <div class="flex">
                        <i class="fas fa-exclamation-circle text-red-400 mr-2"></i>
                        <p class="text-red-800">{{ session('error') }}</p>
                    </div>
                </div>
            @endif

            <!-- Existing Table -->
            <div class="bg-[#FFF0FA] overflow-hidden shadow-xl rounded-lg">
                <div class="p-6">
                    <table class="min-w-full">
                        <thead class="bg-[#FFC8FB]">
                            <tr>
                                <th class="px-6 py-3 text-left text-sm font-semibold">Student</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold">Task</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold">Status</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold">Score</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold">XP</th>
                                <th class="px-6 py-3 text-left text-sm font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-[#FFC8FB]">
                            @forelse($studentTasks as $task)
                                <tr class="hover:bg-[#FFF6FD]">
                                    <td class="px-6 py-4">{{ $task->student->user->name }}</td>
                                    <td class="px-6 py-4">{{ $task->task->title }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 text-xs rounded-full {{ 
                                            $task->status === 'graded' ? 'bg-green-100 text-green-800' : 
                                            ($task->status === 'submitted' ? 'bg-blue-100 text-blue-800' :
                                             ($task->status === 'overdue' ? 'bg-red-100 text-red-800' : 
                                              'bg-gray-100 text-gray-800')) 
                                        }}">
                                            {{ Str::title($task->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">{{ $task->score ?? 'N/A' }}</td>
                                    <td class="px-6 py-4">{{ $task->xp_earned }}</td>
                                    <td class="px-6 py-4 space-x-2">
                                        <a href="{{ route('instructors.student-tasks.show', $task) }}" 
                                           class="text-[#FF92C2] hover:text-[#ff6fb5]" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('instructors.student-tasks.edit', $task) }}" 
                                           class="text-[#FF92C2] hover:text-[#ff6fb5]" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($task->status === 'submitted' && !$task->graded_at)
                                            <a href="{{ route('instructors.student-tasks.grade', $task) }}" 
                                               class="text-yellow-600 hover:text-yellow-700" title="Grade">
                                                <i class="fas fa-star"></i>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        <i class="fas fa-tasks text-4xl mb-4"></i>
                                        <p>No student tasks found. Start by assigning tasks or uploading a CSV.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    <!-- Pagination -->
                    @if($studentTasks->hasPages())
                        <div class="mt-6">
                            {{ $studentTasks->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleUploadForm() {
            const uploadSection = document.getElementById('uploadSection');
            if (uploadSection.classList.contains('hidden')) {
                uploadSection.classList.remove('hidden');
                uploadSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            } else {
                uploadSection.classList.add('hidden');
            }
        }

        // Auto-hide upload section on successful upload
        @if(session('success') && str_contains(session('success'), 'uploaded'))
            document.addEventListener('DOMContentLoaded', function() {
                const uploadSection = document.getElementById('uploadSection');
                if (uploadSection && !uploadSection.classList.contains('hidden')) {
                    setTimeout(() => {
                        uploadSection.classList.add('hidden');
                    }, 3000); // Hide after 3 seconds
                }
            });
        @endif
    </script>
</x-app-layout>