<x-app-layout>
    <div class="py-8 sm:py-12">
        <div class="max-w-3xl mx-auto px-6 lg:px-8">
            <!-- Page Header -->
            <div class="mb-8 sm:mb-12 flex justify-between items-center">
                <h2 class="text-2xl sm:text-3xl font-bold text-[#FF92C2]">Create Quiz Question</h2>
                <a href="{{ route('instructors.quizzes.index') }}" 
                   class="text-gray-600 hover:text-[#FF92C2] transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Questions
                </a>
            </div>

            <!-- Main Form Container -->
            <div class="form-container">
                <div class="bg-gradient-to-b from-[#FFF0FA] to-white shadow-lg rounded-2xl p-8 border border-[#FFC8FB]/50">
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold bg-gradient-to-r from-[#FF92C2] to-[#ff6fb5] bg-clip-text text-transparent">
                            Create New Quiz
                        </h2>
                        <p class="text-gray-600 mt-2">Fill in the details below to create a new quiz question.</p>
                    </div>

                    <form action="{{ route('instructors.quizzes.store') }}" method="POST" class="space-y-8" id="quiz-form">
                        @csrf
                        
                        <!-- Question Details Section -->
                        <div class="space-y-6">
                            <h3 class="text-xl font-semibold text-[#FF92C2] mb-6">Question Details</h3>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <!-- Task and Type selection -->
                                <div>
                                    <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Task</label>
                                    <select name="task_id" required class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">
                                        <option value="">Select Task</option>
                                        @foreach($tasks as $task)
                                            <option value="{{ $task->id }}" {{ old('task_id') == $task->id ? 'selected' : '' }}>
                                                {{ $task->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('task_id')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Question Type</label>
                                    <select name="type" id="questionType" required class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">
                                        <option value="">Select Type</option>
                                        <option value="multiple_choice" {{ old('type') == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                                        <option value="true_false" {{ old('type') == 'true_false' ? 'selected' : '' }}>True/False</option>
                                        <option value="identification" {{ old('type') == 'identification' ? 'selected' : '' }}>Identification</option>
                                    </select>
                                    @error('type')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="bg-white/50 backdrop-blur-sm rounded-xl p-6 border border-[#FFC8FB]/30">
                                <div>
                                    <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Question Text</label>
                                    <textarea name="question_text" required rows="3" class="w-full rounded-lg shadow-sm bg-white 
                                                    border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                    text-gray-800 px-4 py-2 transition-all duration-200">{{ old('question_text') }}</textarea>
                                    @error('question_text')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div id="optionsContainer" class="space-y-4" style="display: none;">
                                    <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Options</label>
                                    <div id="optionsList" class="space-y-2">
                                        @for($i = 0; $i < 4; $i++)
                                            <div class="flex gap-2 option-row">
                                                <input type="text" name="options[]" class="flex-1 rounded-lg border-[#FFC8FB]" placeholder="Option {{ $i + 1 }}" value="{{ old('options.' . $i) }}">
                                                @if($i > 1)
                                                    <button type="button" class="px-2 py-1 text-red-500 hover:text-red-700 remove-option">×</button>
                                                @endif
                                            </div>
                                        @endfor
                                    </div>
                                    <button type="button" onclick="addOption()" class="text-[#FF92C2] hover:text-[#ff6fb5]">+ Add Option</button>
                                    @error('options')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Correct Answer and Points -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Correct Answer</label>
                                    <input type="text" name="correct_answer" id="correct_answer" required value="{{ old('correct_answer') }}" 
                                           class="w-full rounded-lg shadow-sm bg-white 
                                                    border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                    text-gray-800 px-4 py-2 transition-all duration-200">
                                    @error('correct_answer')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Points</label>
                                    <input type="number" name="points" required min="1" value="{{ old('points', 1) }}"
                                           class="w-full rounded-lg shadow-sm bg-white 
                                                    border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                    text-gray-800 px-4 py-2 transition-all duration-200">
                                    @error('points')
                                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Template Configuration Section -->
                        <div class="space-y-6">
                            <h3 class="text-xl font-semibold text-[#FF92C2] mb-6">Template Configuration</h3>
                            
                            <div class="bg-white/70 backdrop-blur-sm rounded-xl p-6 border border-[#FFC8FB]/30">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Template Name</label>
                                        <input type="text" name="template_name" 
                                               class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200" 
                                               placeholder="e.g., Student Quiz Template" 
                                               value="{{ old('template_name') }}">
                                        @error('template_name')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Description (Optional)</label>
                                        <textarea name="template_description" rows="2" 
                                                  class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200" 
                                                  placeholder="Describe what this template is used for...">{{ old('template_description') }}</textarea>
                                        @error('template_description')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-semibold text-[#FF92C2] mb-2">CSV Column Headers</label>
                                        <div id="headers-container">
                                            @php 
                                                $headers = old('csv_template_headers', []);
                                            @endphp
                                            @foreach($headers as $index => $header)
                                                <div class="flex gap-2 mb-2 header-row">
                                                    <input type="text" name="csv_template_headers[]" 
                                                           class="w-full rounded-lg shadow-sm bg-white border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 text-gray-800 px-4 py-2 transition-all duration-200 header-input" 
                                                           value="{{ $header }}" 
                                                           placeholder="Column Header">
                                                    <button type="button" 
                                                            class="px-2 py-1 text-red-500 hover:text-red-700 remove-header">×</button>
                                                </div>
                                            @endforeach
                                        </div>
                                        <button type="button" id="add-header" class="text-[#FF92C2] hover:text-[#ff6fb5] text-sm">+ Add Column</button>
                                        @error('csv_template_headers')
                                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Template Preview Section -->
                            <div class="bg-white/50 backdrop-blur-sm rounded-xl p-6 border border-[#FFC8FB]/30">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="text-sm font-semibold text-[#FF92C2]">Preview</h4>
                                    <button type="button" id="refresh-preview" 
                                            class="text-sm text-[#FF92C2] hover:text-[#ff6fb5] flex items-center">
                                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                                    </button>
                                </div>
                                <div class="border border-[#FFC8FB]/50 rounded-lg overflow-hidden bg-white">
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full text-sm" id="template-preview">
                                            <thead class="bg-[#FFF0FA]/50">
                                                <tr id="preview-headers">
                                                    <!-- Headers will be populated by JavaScript -->
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-[#FFC8FB]/30" id="preview-body">
                                                <!-- Sample data will be populated by JavaScript -->
                                            </tbody>
                                        </table>
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
                                <i class="fas fa-save mr-2"></i>Create Question
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const questionType = document.getElementById('questionType');
            const optionsContainer = document.getElementById('optionsContainer');
            const correctAnswerInput = document.getElementById('correct_answer');

            // Question type change handler
            questionType.addEventListener('change', function() {
                if (this.value === 'multiple_choice') {
                    optionsContainer.style.display = 'block';
                    correctAnswerInput.placeholder = 'Enter the correct option (A, B, C, etc.)';
                } else {
                    optionsContainer.style.display = 'none';
                    if (this.value === 'true_false') {
                        correctAnswerInput.placeholder = 'Enter True or False';
                    } else if (this.value === 'identification') {
                        correctAnswerInput.placeholder = 'Enter the correct answer';
                    } else {
                        correctAnswerInput.placeholder = '';
                    }
                }
            });

            // Remove option functionality
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-option')) {
                    e.target.closest('.option-row').remove();
                }
            });

            // Add header functionality
            document.getElementById('add-header').addEventListener('click', function() {
                const headersContainer = document.getElementById('headers-container');
                const newHeaderDiv = document.createElement('div');
                newHeaderDiv.className = 'flex gap-2 mb-2 header-row';
                newHeaderDiv.innerHTML = `
                    <input type="text" name="csv_template_headers[]" 
                           class="w-full rounded-lg shadow-sm bg-white border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 text-gray-800 px-4 py-2 transition-all duration-200 header-input" 
                           placeholder="Column Header">
                    <button type="button" class="px-2 py-1 text-red-500 hover:text-red-700 remove-header">×</button>
                `;
                headersContainer.appendChild(newHeaderDiv);
                updatePreview();
            });

            // Remove header functionality
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-header')) {
                    const headersContainer = document.getElementById('headers-container');
                    if (headersContainer.children.length > 3) { // Keep minimum 3 headers
                        e.target.closest('.header-row').remove();
                        updatePreview();
                    }
                }
            });

            // Header input change handler
            document.addEventListener('input', function(e) {
                if (e.target.classList.contains('header-input')) {
                    updatePreview();
                }
            });

            // Refresh preview button
            document.getElementById('refresh-preview').addEventListener('click', function() {
                updatePreview();
            });

            // Initialize display
            if (questionType.value === 'multiple_choice') {
                optionsContainer.style.display = 'block';
            }

            // Initialize preview
            updatePreview();
        });

        function addOption() {
            const optionsList = document.getElementById('optionsList');
            const newOption = document.createElement('div');
            newOption.className = 'flex gap-2 option-row';
            newOption.innerHTML = `
                <input type="text" name="options[]" class="flex-1 rounded-lg border-[#FFC8FB]" 
                       placeholder="Option ${optionsList.children.length + 1}">
                <button type="button" class="px-2 py-1 text-red-500 hover:text-red-700 remove-option">×</button>
            `;
            optionsList.appendChild(newOption);
        }

        // Update preview function
        function updatePreview() {
            const headerInputs = document.querySelectorAll('.header-input');
            const headers = Array.from(headerInputs).map(input => input.value.trim()).filter(val => val);
            
            if (headers.length === 0) {
                return;
            }

            // Update preview table headers
            const previewHeaders = document.getElementById('preview-headers');
            previewHeaders.innerHTML = headers.map(header => 
                `<th class="px-4 py-2 text-left text-xs font-semibold text-[#FF92C2] uppercase tracking-wider border-b border-[#FFC8FB]/30">${header}</th>`
            ).join('');

            // Generate sample data
            const sampleData = generateSampleData(headers);
            const previewBody = document.getElementById('preview-body');
            previewBody.innerHTML = sampleData.map(row => 
                `<tr>${row.map(cell => 
                    `<td class="px-4 py-2 text-sm text-gray-700">${cell}</td>`
                ).join('')}</tr>`
            ).join('');
        }

        // Generate sample data based on headers
        function generateSampleData(headers) {
            // Change to only generate one row
            return [headers.map(header => getSampleValueForHeader(header, 1))];
        }

        // Get sample value for a specific header type
        function getSampleValueForHeader(header, index) {
            const headerLower = header.toLowerCase().trim();
            
            // Simplify the sample data to remove index-based variations
            if (headerLower.includes('student id') || headerLower.includes('id number')) {
                return '2024001';
            } else if (headerLower.includes('student name') || headerLower.includes('name')) {
                return 'John Doe';
            } else if (headerLower.includes('subject') && headerLower.includes('code')) {
                return 'CS101';
            } else if (headerLower.includes('section')) {
                return 'Section A';
            } else if (headerLower.includes('question')) {
                return 'What is the answer to this question?';
            } else if (headerLower.includes('answer')) {
                return 'Sample Answer';
            } else if (headerLower.includes('point')) {
                return '10';
            } else if (headerLower.includes('email')) {
                return 'student@example.com';
            } else if (headerLower.includes('grade') || headerLower.includes('score')) {
                return '85';
            } else {
                return 'Sample Data';
            }
        }
    </script>
</x-app-layout>