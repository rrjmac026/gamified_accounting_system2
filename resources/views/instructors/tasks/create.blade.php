<x-app-layout>


    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] backdrop-blur-sm overflow-hidden shadow-lg rounded-lg p-8 border border-[#FFC8FB]/50">
                <h2 class="text-2xl font-bold text-[#FF92C2] mb-6">Create New Task</h2>

                <form action="{{ route('instructors.tasks.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                            <strong class="font-bold">Validation Errors:</strong>
                            <ul class="mt-2 list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Title</label>
                            <input type="text" name="title" value="{{ old('title') }}" required
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
                                    <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>
                                        {{ ucfirst($type) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Fixed Drag and Drop Area -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Attachment</label>
                            <div 
                                x-data="{ 
                                    isDragging: false,
                                    fileName: '',
                                    handleDrop(e) {
                                        this.isDragging = false;
                                        const files = e.dataTransfer.files;
                                        if (files.length > 0) {
                                            this.$refs.fileInput.files = files;
                                            this.fileName = files[0].name;
                                            // Trigger change event for any listeners
                                            this.$refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                                        }
                                    },
                                    handleFileSelect(e) {
                                        const files = e.target.files;
                                        if (files.length > 0) {
                                            this.fileName = files[0].name;
                                        } else {
                                            this.fileName = '';
                                        }
                                    }
                                }"
                                x-on:dragenter.prevent="isDragging = true"
                                x-on:dragover.prevent="isDragging = true"
                                x-on:dragleave.prevent="isDragging = false"
                                x-on:drop.prevent="handleDrop($event)"
                                x-on:click="$refs.fileInput.click()"
                                class="w-full p-6 border-2 border-dashed rounded-lg cursor-pointer transition-all duration-200"
                                :class="{ 
                                    'bg-pink-50 border-pink-400 border-solid': isDragging,
                                    'border-pink-300 bg-white hover:border-pink-400': !isDragging 
                                }"
                            >
                                <div class="text-center">
                                    <!-- File upload icon -->
                                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    
                                    <p class="text-center text-gray-500 mb-2" x-show="!fileName">
                                        <span class="font-semibold text-[#FF92C2]">Click to upload</span> or drag & drop your file here
                                    </p>
                                    <p class="text-center text-green-600 font-semibold" x-show="fileName" x-text="'Selected: ' + fileName"></p>
                                    <p class="text-sm text-gray-400">PDF, DOC, DOCX, PPT, PPTX up to 10MB</p>
                                </div>
                                
                                <input 
                                    type="file" 
                                    name="attachment" 
                                    x-ref="fileInput"
                                    x-on:change="handleFileSelect($event)"
                                    class="hidden" 
                                    accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.jpg,.jpeg,.png"
                                >
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Subject</label>
                            <select name="subject_id" required class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                        {{ $subject->subject_name }} - {{ $subject->subject_code }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Due Date</label>
                            <input type="datetime-local" name="due_date" value="{{ old('due_date') }}" required
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
                                @foreach($sections as $section)
                                    <option value="{{ $section->id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>
                                        {{ $section->name }} ({{ $section->section_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Status</label>
                            <select name="status" class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">
                                <option value="assigned" selected>Assigned</option>
                                <option value="in_progress">In Progress</option>
                            </select>
                        </div>

                        <!-- Task Settings -->
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Retry Limit</label>
                            <input type="number" name="retry_limit" value="{{ old('retry_limit', 1) }}" min="1" required
                                   class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Late Penalty (%)</label>
                            <input type="number" name="late_penalty" value="{{ old('late_penalty', 0) }}" min="0" max="100"
                                   class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Maximum Score</label>
                            <input type="number" name="max_score" value="{{ old('max_score', 100) }}" min="0" required
                                   class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">XP Reward</label>
                            <input type="number" name="xp_reward" value="{{ old('xp_reward', 0) }}" min="0" required
                                   class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">
                        </div>
                    </div>

                    <!-- Description and Instructions with Rich Text Editor -->
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Description</label>
                            <textarea 
                                id="description" 
                                name="description" 
                                rows="3" 
                                required
                                class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200">{{ old('description') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Instructions</label>
                            <textarea name="instructions" rows="4" required
                                    class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">{{ old('instructions') }}</textarea>
                        </div>
                    </div>

                    <!-- Task Options -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="flex items-center space-x-4">
                            <label class="flex items-center">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                                    class="rounded border-[#FFC8FB] text-[#FF92C2] focus:ring-pink-200">
                                <span class="ml-2 text-sm text-gray-700">Active</span>
                            </label>

                            <label class="flex items-center">
                                <input type="hidden" name="auto_grade" value="0">
                                <input type="checkbox" name="auto_grade" value="1" {{ old('auto_grade') ? 'checked' : '' }}
                                    class="rounded border-[#FFC8FB] text-[#FF92C2] focus:ring-pink-200">
                                <span class="ml-2 text-sm text-gray-700">Auto Grade</span>
                            </label>
                            
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Allow Late Submissions Until</label>
                            <input type="datetime-local" name="late_until" value="{{ old('late_until') }}"
                                   class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">
                            <p class="mt-1 text-xs text-gray-500 italic">Leave empty if late submissions are not allowed</p>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4">
                        <a href="{{ route('instructors.tasks.index') }}" 
                           class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5] transition-colors">
                            Create Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            tinymce.init({
                selector: '#description, #instructions',
                height: 300,
                menubar: false,
                plugins: [
                    'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                    'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                    'insertdatetime', 'media', 'table', 'help', 'wordcount'
                ],
                toolbar: 'undo redo | blocks | ' +
                    'bold italic forecolor | alignleft aligncenter ' +
                    'alignright alignjustify | bullist numlist outdent indent | ' +
                    'removeformat | help',
                content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size:14px }',
                branding: false,
                promotion: false,
                setup: function (editor) {
                    editor.on('change', function () {
                        editor.save();
                    });
                }
            });
        });

        
        document.querySelector('form').addEventListener('submit', function(e) {
            if (typeof tinymce !== 'undefined') {
                tinymce.triggerSave();
            }
        });
    </script>

    @push('scripts')
    @endpush
</x-app-layout>