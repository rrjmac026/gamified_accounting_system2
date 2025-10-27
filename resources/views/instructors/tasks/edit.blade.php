<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] backdrop-blur-sm overflow-hidden shadow-lg rounded-lg p-8 border border-[#FFC8FB]/50">
                <h2 class="text-2xl font-bold text-[#FF92C2] mb-6">Edit Task</h2>

                <form action="{{ route('instructors.tasks.update', $task) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')
                    
                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Title</label>
                            <input type="text" name="title" value="{{ old('title', $task->title) }}" required
                                   class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Type</label>
                            <select name="type" required class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">
                                <option value="">Select Type</option>
                                @foreach(['assignment', 'exercise', 'quiz', 'project'] as $type)
                                    <option value="{{ $type }}" {{ old('type', $task->type) == $type ? 'selected' : '' }}>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Attachment</label>
                            @if($task->attachment)
                                <div class="mb-3 p-4 bg-gradient-to-r from-pink-50 to-purple-50 border border-pink-200 rounded-lg shadow-sm">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center flex-1 min-w-0">
                                            <div class="flex-shrink-0 w-10 h-10 bg-pink-100 rounded-full flex items-center justify-center">
                                                @php
                                                    $extension = pathinfo($task->attachment, PATHINFO_EXTENSION);
                                                    $iconClass = match(strtolower($extension)) {
                                                        'pdf' => 'fas fa-file-pdf text-red-500',
                                                        'doc', 'docx' => 'fas fa-file-word text-blue-500',
                                                        'xls', 'xlsx' => 'fas fa-file-excel text-green-500',
                                                        'ppt', 'pptx' => 'fas fa-file-powerpoint text-orange-500',
                                                        'jpg', 'jpeg', 'png' => 'fas fa-file-image text-purple-500',
                                                        default => 'fas fa-file text-gray-500'
                                                    };
                                                @endphp
                                                <i class="{{ $iconClass }}"></i>
                                            </div>
                                            <div class="ml-3 min-w-0 flex-1">
                                                <p class="text-sm font-medium text-gray-900 truncate">
                                                    {{ basename($task->attachment) }}
                                                </p>
                                                <p class="text-xs text-gray-500">
                                                    Current attachment
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center ml-4 space-x-2">
                                            <a href="{{ Storage::url($task->attachment) }}" target="_blank" 
                                               class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-pink-600 bg-pink-100 hover:bg-pink-200 rounded-full transition-colors duration-200">
                                                <i class="fas fa-external-link-alt mr-1"></i>
                                                View
                                            </a>
                                            <label class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-600 bg-red-100 hover:bg-red-200 rounded-full cursor-pointer transition-colors duration-200">
                                                <input type="checkbox" name="remove_attachment" value="1" 
                                                       class="sr-only peer">
                                                <i class="fas fa-trash-alt mr-1"></i>
                                                <span class="peer-checked:line-through">Remove</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            
                            <div class="relative">
                                <input type="file" name="attachment" id="attachment"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-medium file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100 file:cursor-pointer">
                                <p class="text-xs text-gray-500 mt-2 flex items-center">
                                    <i class="fas fa-info-circle mr-1 text-gray-400"></i>
                                    @if($task->attachment)
                                        Upload a new file to replace the current attachment
                                    @else
                                        Supported formats: PDF, DOC, DOCX, XLS, XLSX, PPT, PPTX, JPG, PNG (max 10MB)
                                    @endif
                                </p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Subject</label>
                            <select name="subject_id" required class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id', $task->subject_id) == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->subject_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Due Date</label>
                            <input type="datetime-local" name="due_date" 
                                   value="{{ old('due_date', $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d\TH:i') : '') }}" 
                                   required
                                   class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Section</label>
                            <select name="section_id" required
                                    class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">
                                <option value="">Select Section</option>
                                @if(isset($sections))
                                    @foreach($sections as $section)
                                        <option value="{{ $section->id }}" {{ old('section_id', $task->section_id) == $section->id ? 'selected' : '' }}>
                                            {{ $section->name }} ({{ $section->section_code }})
                                        </option>
                                    @endforeach
                                @else
                                    {{-- Fallback: show current section if sections not loaded --}}
                                    @if($task->section)
                                        <option value="{{ $task->section->id }}" selected>
                                            {{ $task->section->name }} ({{ $task->section->section_code }})
                                        </option>
                                    @endif
                                @endif
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Status</label>
                            <select name="status" class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">
                                <option value="assigned" {{ old('status', $task->status) == 'assigned' ? 'selected' : '' }}>Assigned</option>
                                <option value="in_progress" {{ old('status', $task->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="completed" {{ old('status', $task->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="overdue" {{ old('status', $task->status) == 'overdue' ? 'selected' : '' }}>Overdue</option>
                            </select>
                        </div>

                        <!-- Task Settings -->
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Retry Limit</label>
                            <input type="number" name="retry_limit" value="{{ old('retry_limit', $task->retry_limit) }}" min="1" required
                                   class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Late Penalty (%)</label>
                            <input type="number" name="late_penalty" value="{{ old('late_penalty', $task->late_penalty) }}" min="0" max="100"
                                   class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Maximum Score</label>
                            <input type="number" name="max_score" value="{{ old('max_score', $task->max_score) }}" min="0" required
                                   class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">XP Reward</label>
                            <input type="number" name="xp_reward" value="{{ old('xp_reward', $task->xp_reward) }}" min="0" required
                                   class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">
                        </div>
                    </div>

                    <!-- Description and Instructions -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Description</label>
                            <textarea name="description" rows="3" required
                                    class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">{{ old('description', $task->description) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Instructions</label>
                            <textarea name="instructions" rows="4" required
                                    class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">{{ old('instructions', $task->instructions) }}</textarea>
                        </div>
                    </div>

                    <!-- Task Options -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" 
                                    {{ old('is_active', $task->is_active) ? 'checked' : '' }}
                                    class="rounded border-[#FFC8FB] text-[#FF92C2] focus:ring-pink-200">
                                <span class="ml-2 text-sm text-gray-700">Active</span>
                            </label>

                            <label class="flex items-center">
                                <input type="hidden" name="auto_grade" value="0">
                                <input type="checkbox" name="auto_grade" value="1" 
                                    {{ old('auto_grade', $task->auto_grade) ? 'checked' : '' }}
                                    class="rounded border-[#FFC8FB] text-[#FF92C2] focus:ring-pink-200">
                                <span class="ml-2 text-sm text-gray-700">Auto Grade</span>
                            </label>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Allow Late Submissions Until</label>
                            <input type="datetime-local" name="late_until" 
                                   value="{{ old('late_until', optional($task->late_until)->format('Y-m-d\TH:i')) }}"
                                   class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">
                            <p class="mt-1 text-xs text-gray-500 italic">Leave empty if late submissions are not allowed</p>
                        </div>
                    </div>

                    <!-- Task Statistics (Read-only info) -->
                    @if($task->students->count() > 0)
                        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Task Statistics</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600">Total Students:</span>
                                    <span class="font-medium ml-1">{{ $task->students->count() }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Submitted:</span>
                                    <span class="font-medium ml-1">{{ $task->students->where('pivot.status', 'submitted')->count() }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Graded:</span>
                                    <span class="font-medium ml-1">{{ $task->students->where('pivot.status', 'graded')->count() }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Overdue:</span>
                                    <span class="font-medium ml-1">{{ $task->students->where('pivot.status', 'overdue')->count() }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="flex justify-end gap-4">
                        <a href="{{ route('instructors.tasks.show', $task) }}" 
                           class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5] transition-colors">
                            Update Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>