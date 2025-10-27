<x-app-layout>
    <div class="py-8 sm:py-12">
        <div class="max-w-3xl mx-auto px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-8 sm:mb-12 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-[#FF92C2]">Edit Quiz Question</h2>
                    <p class="text-gray-600 mt-2">Question #{{ $quiz->id }}</p>
                </div>
                <a href="{{ route('instructors.quizzes.index') }}" 
                   class="text-gray-600 hover:text-[#FF92C2] transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Questions
                </a>
            </div>

            <!-- Main Form Container -->
            <div class="form-container">
                <div class="bg-gradient-to-b from-[#FFF0FA] to-white shadow-lg rounded-2xl p-8 border border-[#FFC8FB]/50">
                    <form action="{{ route('instructors.quizzes.update', $quiz) }}" method="POST" class="space-y-8">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-8">
                            <!-- Left Column -->
                            <div class="space-y-6 lg:border-r lg:border-[#FFC8FB]/30 lg:pr-12">
                                <h3 class="text-xl font-semibold text-[#FF92C2] mb-6">Question Details</h3>
                                
                                <!-- Task Selection -->
                                <div>
                                    <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Task</label>
                                    <select name="task_id" required class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">
                                        <option value="">Select Task</option>
                                        @foreach($tasks as $task)
                                            <option value="{{ $task->id }}" {{ old('task_id', $quiz->task_id) == $task->id ? 'selected' : '' }}>
                                                {{ $task->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('task_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Question Type -->
                                <div>
                                    <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Question Type</label>
                                    <select name="type" id="questionType" required class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">
                                        @foreach(['multiple_choice', 'true_false', 'identification'] as $type)
                                            <option value="{{ $type }}" {{ old('type', $quiz->type) == $type ? 'selected' : '' }}>
                                                {{ ucwords(str_replace('_', ' ', $type)) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Question Text -->
                                <div>
                                    <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Question Text</label>
                                    <textarea name="question_text" required rows="3" 
                                        class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">{{ old('question_text', $quiz->question_text) }}</textarea>
                                    @error('question_text')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Multiple Choice Options -->
                                <div id="optionsContainer" class="space-y-4" style="display: none;">
                                    <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Options</label>
                                    <div id="optionsList" class="space-y-2">
                                        @if($quiz->type === 'multiple_choice' && $quiz->options)
                                            @foreach($quiz->options as $option)
                                                <div class="flex gap-2">
                                                    <input type="text" name="options[]" value="{{ $option }}" 
                                                        class="flex-1 rounded-lg bg-white border-[#FFC8FB]">
                                                    <button type="button" class="px-2 py-1 text-red-500" onclick="this.parentElement.remove()">×</button>
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    <button type="button" onclick="addOption()" class="text-[#FF92C2] hover:text-[#ff6fb5]">+ Add Option</button>
                                    @error('options')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                    @error('options.*')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Correct Answer -->
                                <div>
                                    <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Correct Answer</label>
                                    <input type="text" name="correct_answer" required value="{{ old('correct_answer', $quiz->correct_answer) }}" 
                                        class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">
                                    @error('correct_answer')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Points -->
                                <div>
                                    <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Points</label>
                                    <input type="number" name="points" required min="1" value="{{ old('points', $quiz->points) }}"
                                        class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">
                                    @error('points')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Right Column -->
                            <div class="space-y-6">
                                <h3 class="text-xl font-semibold text-[#FF92C2] mb-6">Template Configuration</h3>
                                
                                <div class="bg-white/70 backdrop-blur-sm rounded-lg p-6 border border-[#FFC8FB]/30">
                                    <div class="space-y-4">
                                        <!-- Template Name -->
                                        <div>
                                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Template Name</label>
                                            <input type="text" name="template_name" 
                                                class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200"
                                                value="{{ old('template_name', $quiz->template_name) }}"
                                                placeholder="e.g., Student Quiz Template">
                                            @error('template_name')
                                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Template Description -->
                                        <div>
                                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Description (Optional)</label>
                                            <textarea name="template_description" rows="2" 
                                                class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200"
                                                placeholder="Describe what this template is used for...">{{ old('template_description', $quiz->template_description) }}</textarea>
                                            @error('template_description')
                                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- CSV Headers -->
                                        <div>
                                            <label class="block text-sm font-semibold text-[#FF92C2] mb-2">CSV Column Headers</label>
                                            <div id="headers-container" class="space-y-2">
                                                @if($quiz->csv_template_headers)
                                                    @foreach($quiz->csv_template_headers as $header)
                                                        <div class="flex gap-2 mb-2 header-row">
                                                            <input type="text" name="csv_template_headers[]" 
                                                                   class="w-full rounded-lg shadow-sm bg-white border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 text-gray-800 px-4 py-2 transition-all duration-200"
                                                                   value="{{ $header }}"
                                                                   placeholder="Column Header">
                                                            <button type="button" class="px-2 py-1 text-red-500 hover:text-red-700 remove-header">×</button>
                                                        </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                            <button type="button" onclick="addHeader()" class="mt-2 text-[#FF92C2] hover:text-[#ff6fb5]">+ Add Header</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex justify-end gap-4 pt-8 mt-8 border-t border-[#FFC8FB]/30">
                            <a href="{{ route('instructors.quizzes.index') }}" 
                               class="px-6 py-2.5 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors inline-flex items-center">
                                <i class="fas fa-times mr-2"></i>Cancel
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2.5 bg-gradient-to-r from-[#FF92C2] to-[#FF5DA2] hover:from-[#FF5DA2] hover:to-[#FF92C2] 
                                           text-white rounded-lg transition-all duration-200 inline-flex items-center">
                                <i class="fas fa-save mr-2"></i>Update Question
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        const questionType = document.getElementById('questionType');
        const optionsContainer = document.getElementById('optionsContainer');

        questionType.addEventListener('change', function() {
            optionsContainer.style.display = this.value === 'multiple_choice' ? 'block' : 'none';
        });

        function addOption() {
            const optionsList = document.getElementById('optionsList');
            const newOption = document.createElement('div');
            newOption.className = 'flex gap-2';
            newOption.innerHTML = `
                <input type="text" name="options[]" class="flex-1 rounded-lg border-[#FFC8FB]" 
                       placeholder="Option ${optionsList.children.length + 1}">
                <button type="button" class="px-2 py-1 text-red-500" onclick="this.parentElement.remove()">×</button>
            `;
            optionsList.appendChild(newOption);
        }

        function addHeader() {
            const headersContainer = document.getElementById('headers-container');
            const newHeader = document.createElement('div');
            newHeader.className = 'flex gap-2 mb-2 header-row';
            newHeader.innerHTML = `
                <input type="text" name="csv_template_headers[]" 
                       class="w-full rounded-lg shadow-sm bg-white border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 text-gray-800 px-4 py-2 transition-all duration-200"
                       placeholder="Column Header">
                <button type="button" class="px-2 py-1 text-red-500 hover:text-red-700 remove-header" onclick="this.parentElement.remove()">×</button>
            `;
            headersContainer.appendChild(newHeader);
        }

        // Initialize display
        optionsContainer.style.display = questionType.value === 'multiple_choice' ? 'block' : 'none';
    </script>
</x-app-layout>

