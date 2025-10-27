<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 sm:rounded-lg border border-pink-100">
                <div class="p-4 sm:p-6 text-gray-700">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-pink-600">Task Details</h2>
                        <a href="{{ route('students.tasks.index') }}" 
                           class="text-pink-600 hover:text-pink-700 font-medium">
                            Back to Tasks
                        </a>
                    </div>

                    @if(session('success'))
                        <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-4 p-3 rounded bg-red-100 text-red-800">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    <!-- Task Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                        <div>
                            <h3 class="text-sm font-semibold text-pink-600 mb-1">Subject</h3>
                            <p class="text-gray-700">{{ $task->subject->subject_name }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-pink-600 mb-1">Type</h3>
                            <p class="text-gray-700 capitalize">{{ $task->type }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-pink-600 mb-1">Due Date</h3>
                            <p class="text-gray-700">{{ $task->due_date->format('F j, Y g:i A') }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-semibold text-pink-600 mb-1">Status</h3>
                            @php
                                $status = ucfirst($studentTask->pivot->status);
                                $isLate = $studentTask->pivot->was_late;
                            @endphp

                            <span @class([
                                'px-2 py-1 text-xs rounded-full font-medium',
                                'bg-yellow-100 text-yellow-800' => $studentTask->pivot->status === 'assigned',
                                'bg-blue-100 text-blue-800' => $studentTask->pivot->status === 'in_progress',
                                'bg-green-100 text-green-800' => $studentTask->pivot->status === 'submitted' && !$isLate,
                                'bg-purple-100 text-purple-800' => $studentTask->pivot->status === 'graded' && !$isLate,
                                'bg-red-100 text-red-800' => $isLate && !$task->allow_late_submission,
                                'bg-yellow-200 text-yellow-900' => $isLate && $task->allow_late_submission,
                                'bg-red-100 text-red-800' => $studentTask->pivot->status === 'overdue',
                                'bg-gray-200 text-gray-600' => $studentTask->pivot->status === 'missing',
                            ])>
                                {{ $status }}
                                @if($isLate)
                                    (Late Submission)
                                @endif
                            </span>

                        </div>
                    </div>

                    <!-- Task Content -->
                    @if ($task->attachment)
                        <div class="mt-6">
                            <h3 class="text-lg font-semibold text-pink-600 mb-2">
                                Task File Attachment (Download this file)
                            </h3>
                            
                            <div class="flex items-center justify-between bg-pink-50 border border-pink-200 rounded-lg p-4 shadow-sm">
                                <div class="flex items-center gap-3">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-lg bg-pink-100">
                                        <i class="fas fa-paperclip text-pink-600"></i>
                                    </div>
                                    <span class="text-gray-700 text-sm truncate max-w-[200px]">
                                        {{ basename($task->attachment) }}
                                    </span>
                                </div>
                                <a href="{{ asset('storage/' . $task->attachment) }}" 
                                target="_blank"
                                class="inline-flex items-center px-3 py-1.5 text-sm font-medium rounded-lg
                                        bg-pink-600 text-white hover:bg-pink-700 transition-all">
                                    <i class="fas fa-download mr-1"></i> Download
                                </a>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-6 mb-8">
                        <div>
                            <h3 class="text-lg font-semibold text-pink-600 mb-2">Description</h3>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <p class="text-gray-700">{!! $task->description !!}</p>
                            </div>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-pink-600 mb-2">Instructions</h3>
                            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                <p class="text-gray-700 whitespace-pre-line">{{ $task->instructions }}</p>
                            </div>
                        </div>

                        @if($task->questions->isNotEmpty())
                            <div>
                                <h3 class="text-lg font-semibold text-pink-600 mb-2">Questions</h3>
                                <div class="space-y-4">
                                    @foreach($task->questions as $question)
                                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                            <p class="font-medium text-gray-900 mb-2">
                                                {{ $question->description }}
                                            </p>
                                            @if($question->options)
                                                <div class="space-y-2">
                                                    @foreach($question->options as $option)
                                                        <label class="flex items-center">
                                                            <input type="radio" name="answers[{{ $question->id }}]" 
                                                                   value="{{ $option }}"
                                                                   class="text-pink-600 focus:ring-pink-500">
                                                            <span class="ml-2 text-gray-700">{{ $option }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Submission Form -->
                    @if(!in_array($studentTask->pivot->status, ['submitted', 'graded']))
                        <form action="{{ route('students.tasks.submit', $task) }}" method="POST" 
                              enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            
                            @if($task->type !== 'quiz')
                                <div>
                                    <label class="block text-sm font-semibold text-pink-600 mb-2">
                                        Upload File (if required)
                                    </label>
                                    
                                    <!-- Enhanced Drag & Drop Area -->
                                    <div 
                                        x-data="{ 
                                            isDragging: false,
                                            fileName: '',
                                            fileSize: '',
                                            handleDrop(e) {
                                                this.isDragging = false;
                                                const files = e.dataTransfer.files;
                                                if (files.length > 0) {
                                                    this.$refs.fileInput.files = files;
                                                    this.fileName = files[0].name;
                                                    this.fileSize = this.formatFileSize(files[0].size);
                                                    // Trigger change event
                                                    this.$refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                                                }
                                            },
                                            handleFileSelect(e) {
                                                const files = e.target.files;
                                                if (files.length > 0) {
                                                    this.fileName = files[0].name;
                                                    this.fileSize = this.formatFileSize(files[0].size);
                                                } else {
                                                    this.fileName = '';
                                                    this.fileSize = '';
                                                }
                                            },
                                            removeFile() {
                                                this.$refs.fileInput.value = '';
                                                this.fileName = '';
                                                this.fileSize = '';
                                            },
                                            formatFileSize(bytes) {
                                                if (bytes === 0) return '0 Bytes';
                                                const k = 1024;
                                                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                                                const i = Math.floor(Math.log(bytes) / Math.log(k));
                                                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                                            }
                                        }"
                                        class="relative"
                                    >
                                        <!-- File Drop Zone -->
                                        <div 
                                            x-on:dragenter.prevent="isDragging = true"
                                            x-on:dragover.prevent="isDragging = true"
                                            x-on:dragleave.prevent="isDragging = false"
                                            x-on:drop.prevent="handleDrop($event)"
                                            x-on:click="$refs.fileInput.click()"
                                            class="w-full p-8 border-2 border-dashed rounded-lg cursor-pointer transition-all duration-200"
                                            :class="{ 
                                                'bg-pink-100 border-pink-400 border-solid': isDragging,
                                                'border-pink-300 bg-white hover:border-pink-400 hover:bg-pink-50': !isDragging && !fileName,
                                                'bg-green-50 border-green-300': fileName && !isDragging
                                            }"
                                        >
                                            <div class="text-center">
                                                <!-- Upload Icon -->
                                                <div x-show="!fileName" class="mx-auto w-16 h-16 mb-4">
                                                    <svg class="w-full h-full text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                                    </svg>
                                                </div>

                                                <!-- Success Icon when file selected -->
                                                <div x-show="fileName" class="mx-auto w-16 h-16 mb-4">
                                                    <svg class="w-full h-full text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                </div>
                                                
                                                <!-- Upload Text -->
                                                <div x-show="!fileName">
                                                    <p class="text-lg font-semibold text-pink-600 mb-2">
                                                        Click to upload or drag & drop
                                                    </p>
                                                    <p class="text-sm text-gray-500">
                                                        PDF, DOC, DOCX, PPT, PPTX, TXT, JPG, PNG up to 10MB
                                                    </p>
                                                </div>

                                                <!-- File Selected Text -->
                                                <div x-show="fileName" class="space-y-2">
                                                    <p class="text-lg font-semibold text-green-600">File Selected!</p>
                                                    <p class="text-sm text-gray-700" x-text="fileName"></p>
                                                    <p class="text-xs text-gray-500" x-text="fileSize"></p>
                                                    <button 
                                                        type="button"
                                                        x-on:click.stop="removeFile()"
                                                        class="mt-3 inline-flex items-center px-3 py-1 text-sm font-medium text-red-600 hover:text-red-700 hover:bg-red-50 rounded-md transition-colors"
                                                    >
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                        Remove File
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Hidden File Input -->
                                        <input 
                                            type="file" 
                                            name="file" 
                                            x-ref="fileInput"
                                            x-on:change="handleFileSelect($event)"
                                            class="hidden" 
                                            accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.jpg,.jpeg,.png"
                                        >
                                    </div>
                                </div>
                            @endif

                            <div class="flex justify-end">
                                <button type="submit" 
                                        class="px-6 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 font-medium transition-colors">
                                    Submit Task
                                </button>
                            </div>
                        </form>
                    @else
                        <!-- Show submission details if already submitted -->
                        <div class="bg-pink-50 rounded-lg p-4 border border-pink-200">
                            <h3 class="text-lg font-semibold text-pink-600 mb-2">Submission Details</h3>
                            <div class="space-y-2">
                                <p class="text-gray-700">
                                    <span class="font-medium">Submitted:</span> 
                                    {{ isset($studentTask->pivot->submitted_at) ? \Carbon\Carbon::parse($studentTask->pivot->submitted_at)->format('F j, Y g:i A') : 'N/A' }}
                                </p>

                                @if($studentTask->pivot->submitted_at)
                                <p class="text-gray-700">
                                    <span class="font-medium">Late Submission:</span>
                                    @if($studentTask->pivot->was_late)
                                        @if($task->allow_late_submission)
                                            <span class="text-yellow-700 font-semibold">Yes (Allowed)</span>
                                        @else
                                            <span class="text-red-700 font-semibold">Yes (Not Allowed)</span>
                                        @endif
                                    @else
                                        <span class="text-green-700 font-semibold">No</span>
                                    @endif
                                </p>
                            @endif

                                @if($studentTask->pivot->status === 'graded')
                                    <p class="text-gray-700">
                                        <span class="font-medium">Score:</span> {{ $studentTask->pivot->score }} / {{ $task->max_score }}
                                    </p>
                                    <p class="text-gray-700">
                                        <span class="font-medium">XP Earned:</span> {{ $studentTask->pivot->xp_earned }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="mt-6 bg-gray-50 p-6 rounded-lg shadow border border-gray-200">
                        <h3 class="text-lg font-semibold text-pink-600 mb-3">Instructor Feedback</h3>

                        @if($submission && $submission->status === 'graded')
                            <div class="space-y-2">
                                <p class="text-gray-700">
                                    <span class="font-medium">Score:</span> {{ $submission->score }}
                                </p>
                                <p class="text-gray-700">
                                    <span class="font-medium">XP Earned:</span> {{ $submission->xp_earned }}
                                </p>
                                <p class="text-gray-700">
                                    <span class="font-medium">Feedback:</span> {{ $submission->feedback }}
                                </p>
                            </div>
                        @else
                            <p class="text-gray-500">
                                No feedback yet. Complete and submit your task first.
                            </p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>git