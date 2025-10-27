<x-app-layout>
    <!-- Quill Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] backdrop-blur-sm overflow-hidden shadow-lg rounded-xl sm:rounded-2xl p-4 sm:p-8 border border-[#FFC8FB]/50">
                <h2 class="text-xl sm:text-2xl font-bold text-[#FF92C2] mb-4 sm:mb-6">Create Performance Task</h2>

                {{-- Error Alert --}}
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        <h4 class="font-semibold mb-2">Please fix the following errors:</h4>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('instructors.performance-tasks.store') }}" method="POST" id="taskForm" class="space-y-4 sm:space-y-6">
                    @csrf

                    {{-- Task Information Section --}}
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-[#FF92C2] border-b border-[#FFC8FB] pb-2">Task Information</h3>
                        
                        {{-- Title --}}
                        <div>
                            <label for="title" class="block text-sm font-semibold text-[#FF92C2] mb-1">
                                Task Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}"
                                class="w-full rounded-lg shadow-sm bg-white 
                                        border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                        text-gray-800 px-4 py-2 transition-all duration-200
                                        @error('title') border-red-500 @enderror" required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-semibold text-[#FF92C2] mb-1">Description</label>
                            <div id="editor" style="height: 200px; background: white;" 
                                class="rounded-lg border border-[#FFC8FB] focus-within:border-pink-400"></div>
                            <textarea id="description" name="description" style="display:none;">{{ old('description') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Provide detailed instructions for the performance task</p>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Assignment Details Section --}}
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-[#FF92C2] border-b border-[#FFC8FB] pb-2">Assignment Details</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Subject --}}
                            <div>
                                <label for="subject_id" class="block text-sm font-semibold text-[#FF92C2] mb-1">
                                    Subject <span class="text-red-500">*</span>
                                </label>
                                <select name="subject_id" id="subject_id"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('subject_id') border-red-500 @enderror" required>
                                    <option value="">Select Subject</option>
                                    @foreach ($subjects as $subject)
                                        <option value="{{ $subject->id }}" {{ old('subject_id') == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->subject_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Section --}}
                            <div>
                                <label for="section_id" class="block text-sm font-semibold text-[#FF92C2] mb-1">
                                    Section <span class="text-red-500">*</span>
                                </label>
                                <select name="section_id" id="section_id"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('section_id') border-red-500 @enderror" required>
                                    <option value="">Select Section</option>
                                    @foreach ($sections as $section)
                                        <option value="{{ $section->id }}" {{ old('section_id') == $section->id ? 'selected' : '' }}>
                                            {{ $section->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('section_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Scoring Configuration Section --}}
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-[#FF92C2] border-b border-[#FFC8FB] pb-2">Scoring Configuration</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Max Score --}}
                            <div>
                                <label for="max_score" class="block text-sm font-semibold text-[#FF92C2] mb-1">
                                    Maximum Score <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="max_score" id="max_score" 
                                    value="{{ old('max_score', 100) }}" min="1"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('max_score') border-red-500 @enderror" required>
                                <p class="text-xs text-gray-500 mt-1">Perfect score for this task</p>
                                @error('max_score')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Deduction Per Error --}}
                            <div>
                                <label for="deduction_per_error" class="block text-sm font-semibold text-[#FF92C2] mb-1">
                                    Deduction Per Error <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="deduction_per_error" id="deduction_per_error" 
                                    value="{{ old('deduction_per_error', 5) }}" min="0"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('deduction_per_error') border-red-500 @enderror" required>
                                <p class="text-xs text-gray-500 mt-1">Points deducted for each error</p>
                                @error('deduction_per_error')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Due Dates Section --}}
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-[#FF92C2] border-b border-[#FFC8FB] pb-2">Due Dates</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Due Date --}}
                            <div>
                                <label for="due_date" class="block text-sm font-semibold text-[#FF92C2] mb-1">
                                    Due Date <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="due_date" id="due_date" 
                                    value="{{ old('due_date') }}"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('due_date') border-red-500 @enderror" required>
                                <p class="text-xs text-gray-500 mt-1">Primary deadline for submission</p>
                                @error('due_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Late Until --}}
                            <div>
                                <label for="late_until" class="block text-sm font-semibold text-[#FF92C2] mb-1">
                                    Accept Late Until (Optional)
                                </label>
                                <input type="datetime-local" name="late_until" id="late_until" 
                                    value="{{ old('late_until') }}"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('late_until') border-red-500 @enderror">
                                <p class="text-xs text-gray-500 mt-1">Final deadline for late submissions (with penalty)</p>
                                @error('late_until')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Task Settings Section --}}
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-[#FF92C2] border-b border-[#FFC8FB] pb-2">Task Settings</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- XP Reward --}}
                            <div>
                                <label for="xp_reward" class="block text-sm font-semibold text-[#FF92C2] mb-1">
                                    XP Reward <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="xp_reward" id="xp_reward" 
                                    value="{{ old('xp_reward', 50) }}" min="0"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('xp_reward') border-red-500 @enderror" required>
                                <p class="text-xs text-gray-500 mt-1">Experience points awarded upon completion</p>
                                @error('xp_reward')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Max Attempts --}}
                            <div>
                                <label for="max_attempts" class="block text-sm font-semibold text-[#FF92C2] mb-1">
                                    Maximum Attempts <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="max_attempts" id="max_attempts" 
                                    value="{{ old('max_attempts', 2) }}" min="1"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('max_attempts') border-red-500 @enderror" required>
                                <p class="text-xs text-gray-500 mt-1">Number of times students can attempt this task</p>
                                @error('max_attempts')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Info Box --}}
                    <div class="bg-[#FFC8FB]/20 border border-[#FFC8FB] rounded-lg p-4">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-[#FF92C2] mt-0.5 mr-3"></i>
                            <div class="text-sm text-gray-700">
                                <p class="font-semibold mb-1 text-[#FF92C2]">About Performance Tasks</p>
                                <p>Performance tasks are designed to assess students' practical application of knowledge. Students will receive notifications when a new task is assigned.</p>
                            </div>
                        </div>
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4">
                        <a href="{{ route('instructors.performance-tasks.index') }}" 
                           class="w-full sm:w-auto px-6 py-3 bg-gray-500 text-white font-semibold rounded-lg hover:bg-gray-600 text-center shadow-md hover:shadow-lg transition-all duration-200">
                            Cancel
                        </a>
                        <button type="submit" 
                            class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-[#FF92C2] to-[#FF5DA2] hover:from-[#FF5DA2] hover:to-[#FF92C2] 
                                    text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                            Create Performance Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote', 'code-block'],
                    [{ 'header': 1 }, { 'header': 2 }],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'indent': '-1'}, { 'indent': '+1' }],
                    ['link'],
                    ['clean']
                ]
            }
        });
        
        // Load old content if validation fails
        var oldContent = `{!! old('description') !!}`;
        if(oldContent) {
            quill.root.innerHTML = oldContent;
        }
        
        // Sync Quill content to textarea on form submit
        document.getElementById('taskForm').onsubmit = function() {
            document.getElementById('description').value = quill.root.innerHTML;
        };

        // Validate that late_until is after due_date
        document.getElementById('late_until').addEventListener('change', function() {
            const dueDate = document.getElementById('due_date').value;
            const lateUntil = this.value;
            
            if (dueDate && lateUntil && new Date(lateUntil) <= new Date(dueDate)) {
                alert('Late submission deadline must be after the due date.');
                this.value = '';
            }
        });

        // Set minimum date for due_date to current date/time
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            const minDateTime = now.toISOString().slice(0, 16);
            document.getElementById('due_date').min = minDateTime;
        });

        // Update late_until minimum when due_date changes
        document.getElementById('due_date').addEventListener('change', function() {
            const lateUntilInput = document.getElementById('late_until');
            if (this.value) {
                lateUntilInput.min = this.value;
            }
        });
    </script>
</x-app-layout>