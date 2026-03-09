<x-app-layout>
    <!-- Quill Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    
    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] backdrop-blur-sm overflow-hidden shadow-lg rounded-xl sm:rounded-2xl p-4 sm:p-8 border border-[#FFC8FB]/50">
                <h2 class="text-xl sm:text-2xl font-bold text-[#FF92C2] mb-4 sm:mb-6">Edit Performance Task</h2>

                {{-- Error Alert --}}
                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        <h4 class="font-semibold mb-2">Please fix the following errors:</h4>
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('instructors.performance-tasks.update', $task) }}" method="POST" id="taskForm" class="space-y-4 sm:space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Task Information Section --}}
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-[#FF92C2] border-b border-[#FFC8FB] pb-2">Task Information</h3>
                        
                        <div>
                            <label for="title" class="block text-sm font-semibold text-[#FF92C2] mb-1">
                                Task Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title', $task->title) }}"
                                class="w-full rounded-lg shadow-sm bg-white 
                                        border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                        text-gray-800 px-4 py-2 transition-all duration-200
                                        @error('title') border-red-500 @enderror" required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-semibold text-[#FF92C2] mb-1">Description</label>
                            <div id="editor" style="height: 200px; background: white;" 
                                class="rounded-lg border border-[#FFC8FB] focus-within:border-pink-400"></div>
                            <textarea id="description" name="description" style="display:none;">{{ old('description', $task->description) }}</textarea>
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
                                    @foreach($subjects as $subject)
                                        <option value="{{ $subject->id }}" 
                                            {{ old('subject_id', $task->subject_id) == $subject->id ? 'selected' : '' }}>
                                            {{ $subject->subject_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('subject_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

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
                                    @foreach($sections as $section)
                                        <option value="{{ $section->id }}" 
                                            {{ old('section_id', $task->section_id) == $section->id ? 'selected' : '' }}>
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

                    {{-- ══════════════════════════════════════════════════════════ --}}
                    {{-- ENABLED STEPS SECTION                                     --}}
                    {{-- ══════════════════════════════════════════════════════════ --}}
                    <div class="space-y-4">
                        <div class="flex items-center justify-between border-b border-[#FFC8FB] pb-2">
                            <div>
                                <h3 class="text-lg font-semibold text-[#FF92C2]">Accounting Steps</h3>
                                <p class="text-xs text-gray-500 mt-0.5">Select which steps students are required to complete. Only enabled steps will be visible to students.</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" id="selectAllSteps"
                                    class="px-3 py-1.5 text-xs font-semibold text-[#FF92C2] border border-[#FFC8FB] rounded-lg hover:bg-[#FFC8FB]/20 transition-colors">
                                    Select All
                                </button>
                                <button type="button" id="clearAllSteps"
                                    class="px-3 py-1.5 text-xs font-semibold text-gray-500 border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">
                                    Clear All
                                </button>
                            </div>
                        </div>

                        @php
                            $stepList = [
                                1  => ['title' => 'Analyze Transactions'],
                                2  => ['title' => 'Journalize Transactions'],
                                3  => ['title' => 'Post to Ledger Accounts'],
                                4  => ['title' => 'Prepare Trial Balance'],
                                5  => ['title' => 'Journalize & Post Adjusting Entries'],
                                6  => ['title' => 'Prepare Adjusted Trial Balance'],
                                7  => ['title' => 'Prepare Financial Statements'],
                                8  => ['title' => 'Journalize & Post Closing Entries'],
                                9  => ['title' => 'Prepare Post-Closing Trial Balance'],
                                10 => ['title' => 'Reverse (Optional Step)'],
                            ];

                            // Priority: validation flash → saved task value (null = all 10 for legacy)
                            $savedSteps = old('enabled_steps', $task->enabled_steps ?? array_keys($stepList));
                            $savedSteps = array_map('intval', (array) $savedSteps);

                            // Warn if removing a step that already has exercises
                            $stepsWithExercises = $task->exercises()
                                ->select('step')
                                ->distinct()
                                ->pluck('step')
                                ->toArray();
                        @endphp

                        @if(count($stepsWithExercises) > 0)
                            <div class="flex items-start gap-2 p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-800">
                                <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5 flex-shrink-0"></i>
                                <span>
                                    Steps <strong>{{ implode(', ', $stepsWithExercises) }}</strong> already have exercises.
                                    Disabling a step hides it from students but does <strong>not</strong> delete its exercises.
                                </span>
                            </div>
                        @endif

                        @error('enabled_steps')
                            <p class="text-sm text-red-600">{{ $message }}</p>
                        @enderror

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3" id="stepsGrid">
                            @foreach ($stepList as $num => $step)
                                @php
                                    $isChecked      = in_array($num, $savedSteps);
                                    $hasExercises   = in_array($num, $stepsWithExercises);
                                @endphp
                                <label for="step_{{ $num }}"
                                    class="step-card flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer select-none transition-all duration-200
                                           {{ $isChecked
                                               ? 'bg-[#FFF0FA] border-[#FF92C2] shadow-sm'
                                               : 'bg-white border-gray-200 hover:border-[#FFC8FB] hover:bg-[#FFF8FD]' }}">

                                    <input type="checkbox"
                                           id="step_{{ $num }}"
                                           name="enabled_steps[]"
                                           value="{{ $num }}"
                                           class="step-checkbox sr-only"
                                           {{ $isChecked ? 'checked' : '' }}>

                                    {{-- Custom checkbox visual --}}
                                    <div class="step-check-visual flex-shrink-0 mt-0.5 w-5 h-5 rounded-md border-2 flex items-center justify-center transition-all duration-200
                                                {{ $isChecked ? 'bg-[#FF92C2] border-[#FF92C2]' : 'border-gray-300 bg-white' }}">
                                        <svg class="w-3 h-3 text-white {{ $isChecked ? '' : 'hidden' }}" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>

                                    {{-- Step number badge --}}
                                    <div class="flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-200
                                                {{ $isChecked ? 'bg-[#FF92C2] text-white' : 'bg-gray-100 text-gray-500' }}">
                                        {{ $num }}
                                    </div>

                                    {{-- Step info --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold {{ $isChecked ? 'text-[#D5006D]' : 'text-gray-600' }} transition-colors duration-200 leading-tight">
                                            {{ $step['title'] }}
                                        </p>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            @if ($hasExercises)
                                                <span class="inline-flex items-center gap-1 text-xs text-[#D5006D] font-medium">
                                                    <i class="fas fa-tasks text-[10px]"></i> Has exercises
                                                </span>
                                            @endif
                                            @if ($num === 10)
                                                <span class="text-xs text-gray-400">Students may skip this step</span>
                                            @endif
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>

                        {{-- Live count --}}
                        <div class="flex items-center gap-2 text-sm text-gray-600 bg-white border border-[#FFC8FB] rounded-lg px-4 py-2.5">
                            <i class="fas fa-info-circle text-[#FF92C2]"></i>
                            <span>
                                <span id="stepCountLabel" class="font-bold text-[#D5006D]">0</span>
                                steps selected — students will only see and submit these steps.
                            </span>
                        </div>
                    </div>
                    {{-- ══════════════════════════════════════════════════════════ --}}

                    {{-- Scoring Configuration Section --}}
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-[#FF92C2] border-b border-[#FFC8FB] pb-2">Scoring Configuration</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="max_score" class="block text-sm font-semibold text-[#FF92C2] mb-1">
                                    Maximum Score <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="max_score" id="max_score" 
                                    value="{{ old('max_score', $task->max_score) }}" min="1"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('max_score') border-red-500 @enderror" required>
                                @error('max_score')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="deduction_per_error" class="block text-sm font-semibold text-[#FF92C2] mb-1">
                                    Deduction Per Error <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="deduction_per_error" id="deduction_per_error" 
                                    value="{{ old('deduction_per_error', $task->deduction_per_error) }}" min="0"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('deduction_per_error') border-red-500 @enderror" required>
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
                            <div>
                                <label for="due_date" class="block text-sm font-semibold text-[#FF92C2] mb-1">
                                    Due Date <span class="text-red-500">*</span>
                                </label>
                                <input type="datetime-local" name="due_date" id="due_date" 
                                    value="{{ old('due_date', $task->due_date?->format('Y-m-d\TH:i')) }}"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('due_date') border-red-500 @enderror" required>
                                @error('due_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="late_until" class="block text-sm font-semibold text-[#FF92C2] mb-1">
                                    Accept Late Until (Optional)
                                </label>
                                <input type="datetime-local" name="late_until" id="late_until" 
                                    value="{{ old('late_until', $task->late_until?->format('Y-m-d\TH:i')) }}"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('late_until') border-red-500 @enderror">
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
                            <div>
                                <label for="xp_reward" class="block text-sm font-semibold text-[#FF92C2] mb-1">
                                    XP Reward <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="xp_reward" id="xp_reward" 
                                    value="{{ old('xp_reward', $task->xp_reward) }}" min="0"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('xp_reward') border-red-500 @enderror" required>
                                @error('xp_reward')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="max_attempts" class="block text-sm font-semibold text-[#FF92C2] mb-1">
                                    Maximum Attempts <span class="text-red-500">*</span>
                                </label>
                                <input type="number" name="max_attempts" id="max_attempts" 
                                    value="{{ old('max_attempts', $task->max_attempts) }}" min="1"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('max_attempts') border-red-500 @enderror" required>
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
                                <p>Performance tasks are designed to assess students' practical application of knowledge. Students will receive notifications when the task is updated. Only the steps you enable above will be visible and submittable by students.</p>
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
                            <i class="fas fa-save mr-2"></i>Update Performance Task
                        </button>
                    </div>
                </form>
            </div>

            {{-- Current Task Info --}}
            <div class="mt-6 bg-white rounded-lg shadow-lg p-6 border border-[#FFC8FB]/50">
                <h3 class="text-lg font-semibold text-[#FF92C2] mb-4">Current Task Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-600">Created:</span>
                        <span class="font-medium ml-2">{{ $task->created_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Last Updated:</span>
                        <span class="font-medium ml-2">{{ $task->updated_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Current Section:</span>
                        <span class="font-medium ml-2">{{ $task->section->name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-600">Students Assigned:</span>
                        <span class="font-medium ml-2">{{ $task->section->students->count() }}</span>
                    </div>
                    @if($task->due_date)
                    <div>
                        <span class="text-gray-600">Due Date:</span>
                        <span class="font-medium ml-2">{{ $task->due_date->format('M d, Y h:i A') }}</span>
                    </div>
                    @endif
                    @if($task->late_until)
                    <div>
                        <span class="text-gray-600">Late Deadline:</span>
                        <span class="font-medium ml-2">{{ $task->late_until->format('M d, Y h:i A') }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        // ── Quill editor ─────────────────────────────────────────────────────
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
        var oldContent = `{!! old('description', $task->description) !!}`;
        if (oldContent) quill.root.innerHTML = oldContent;
        document.getElementById('taskForm').onsubmit = function() {
            document.getElementById('description').value = quill.root.innerHTML;
        };

        // ── Date validation ───────────────────────────────────────────────────
        document.getElementById('late_until').addEventListener('change', function() {
            const dueDate  = document.getElementById('due_date').value;
            const lateUntil = this.value;
            if (dueDate && lateUntil && new Date(lateUntil) <= new Date(dueDate)) {
                alert('Late submission deadline must be after the due date.');
                this.value = '';
            }
        });

        // ── Step checkbox interactions ────────────────────────────────────────
        (function () {
            const countLabel   = document.getElementById('stepCountLabel');
            const selectAllBtn = document.getElementById('selectAllSteps');
            const clearAllBtn  = document.getElementById('clearAllSteps');

            function updateCount() {
                const checked = document.querySelectorAll('.step-checkbox:checked').length;
                countLabel.textContent = checked;
            }

            function applyCardState(checkbox) {
                const card     = checkbox.closest('label.step-card');
                const visual   = card.querySelector('.step-check-visual');
                const checkSvg = visual.querySelector('svg');
                const badge    = card.querySelectorAll('div')[1];
                const title    = card.querySelector('p');

                if (checkbox.checked) {
                    card.classList.replace('bg-white', 'bg-[#FFF0FA]');
                    card.classList.replace('border-gray-200', 'border-[#FF92C2]');
                    card.classList.add('shadow-sm');
                    visual.classList.replace('border-gray-300', 'border-[#FF92C2]');
                    visual.classList.replace('bg-white', 'bg-[#FF92C2]');
                    checkSvg.classList.remove('hidden');
                    badge.classList.replace('bg-gray-100', 'bg-[#FF92C2]');
                    badge.classList.replace('text-gray-500', 'text-white');
                    title.classList.replace('text-gray-600', 'text-[#D5006D]');
                } else {
                    card.classList.replace('bg-[#FFF0FA]', 'bg-white');
                    card.classList.replace('border-[#FF92C2]', 'border-gray-200');
                    card.classList.remove('shadow-sm');
                    visual.classList.replace('border-[#FF92C2]', 'border-gray-300');
                    visual.classList.replace('bg-[#FF92C2]', 'bg-white');
                    checkSvg.classList.add('hidden');
                    badge.classList.replace('bg-[#FF92C2]', 'bg-gray-100');
                    badge.classList.replace('text-white', 'text-gray-500');
                    title.classList.replace('text-[#D5006D]', 'text-gray-600');
                }
            }

            document.querySelectorAll('.step-checkbox').forEach(function(cb) {
                cb.addEventListener('change', function() {
                    applyCardState(this);
                    updateCount();
                });
            });

            selectAllBtn.addEventListener('click', function() {
                document.querySelectorAll('.step-checkbox').forEach(function(cb) {
                    cb.checked = true;
                    applyCardState(cb);
                });
                updateCount();
            });

            clearAllBtn.addEventListener('click', function() {
                document.querySelectorAll('.step-checkbox').forEach(function(cb) {
                    cb.checked = false;
                    applyCardState(cb);
                });
                updateCount();
            });

            // Form submit guard
            document.getElementById('taskForm').addEventListener('submit', function(e) {
                const checked = document.querySelectorAll('.step-checkbox:checked').length;
                if (checked === 0) {
                    e.preventDefault();
                    alert('Please select at least one step for students to complete.');
                }
            });

            updateCount();
        })();
    </script>
</x-app-layout>