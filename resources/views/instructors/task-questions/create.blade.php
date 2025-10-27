<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] backdrop-blur-sm overflow-hidden shadow-lg rounded-lg sm:rounded-2xl p-4 sm:p-8 border border-[#FFC8FB]/50">
                <h2 class="text-xl sm:text-2xl font-bold text-[#FF92C2] mb-4 sm:mb-6">Add Question</h2>

                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-300 rounded-lg">
                        <ul class="list-disc list-inside text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('instructors.task-questions.store') }}" method="POST" class="space-y-6" id="questionForm">
                    @csrf

                    <div class="grid grid-cols-1 gap-6">

                        {{-- Select Task --}}
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Select Task</label>
                            <select id="taskSelect" name="task_id" required
                                    class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">
                                <option value="">-- Choose a Task --</option>
                                @foreach ($tasks as $task)
                                    <option value="{{ $task->id }}" 
                                        data-type="{{ $task->type }}" 
                                        data-deadline="{{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y h:i A') }}">
                                        {{ $task->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Display Task Info --}}
                        <div id="taskInfo" class="mt-2 hidden p-3 bg-pink-50 border border-pink-200 rounded-lg">
                            <p><strong>Type:</strong> <span id="taskType"></span></p>
                            <p><strong>Deadline:</strong> <span id="taskDeadline"></span></p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Question Text</label>
                            <textarea name="question_text" rows="3" required
                                class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">{{ old('question_text') }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Question Type</label>
                            <select name="question_type" required id="questionType" 
                                    class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">
                                <option value="">Select Type</option>
                                <option value="multiple_choice" {{ old('question_type') == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                                <option value="true_false" {{ old('question_type') == 'true_false' ? 'selected' : '' }}>True/False</option>
                                <option value="essay" {{ old('question_type') == 'essay' ? 'selected' : '' }}>Essay</option>
                                <option value="calculation" {{ old('question_type') == 'calculation' ? 'selected' : '' }}>Calculation</option>
                            </select>
                        </div>

                        <div id="optionsContainer" class="{{ old('question_type') == 'multiple_choice' ? '' : 'hidden' }}">
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Options</label>
                            <div class="space-y-2" id="optionsList">
                                <div class="flex gap-2">
                                    <input type="text" name="options[]" class="flex-1 rounded-lg border-[#FFC8FB]">
                                    <button type="button" class="px-2 py-1 bg-[#FF92C2] text-white rounded" onclick="addOption()">+</button>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Correct Answer</label>
                            <input type="text" name="correct_answer" value="{{ old('correct_answer') }}" required
                                   class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Points</label>
                                <input type="number" name="points" value="{{ old('points', 1) }}" required min="1"
                                       class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Order</label>
                                <input type="number" name="order_index" value="{{ old('order_index', 0) }}" required min="0"
                                       class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4">
                        <a href="{{ route('instructors.tasks.index') }}" 
                           class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Cancel</a>
                        <button type="submit" 
                                class="px-6 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5]">
                            Add Question
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const select = document.getElementById('taskSelect');
        const infoBox = document.getElementById('taskInfo');
        const typeEl = document.getElementById('taskType');
        const deadlineEl = document.getElementById('taskDeadline');

        select.addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const type = selected.getAttribute('data-type');
            const deadline = selected.getAttribute('data-deadline');

            if (this.value) {
                typeEl.textContent = type || "N/A";
                deadlineEl.textContent = deadline || "N/A";
                infoBox.classList.remove('hidden');
            } else {
                infoBox.classList.add('hidden');
            }
        });
        document.getElementById('questionType').addEventListener('change', function() {
            const optionsContainer = document.getElementById('optionsContainer');
            if (this.value === 'multiple_choice') {
                optionsContainer.classList.remove('hidden');
            } else {
                optionsContainer.classList.add('hidden');
            }
        });

        function addOption() {
            const optionsList = document.getElementById('optionsList');
            const newOption = document.createElement('div');
            newOption.className = 'flex gap-2';
            newOption.innerHTML = `
                <input type="text" name="options[]" class="flex-1 rounded-lg border-[#FFC8FB]">
                <button type="button" class="px-2 py-1 bg-red-500 text-white rounded" onclick="this.parentElement.remove()">-</button>
            `;
            optionsList.appendChild(newOption);
        }
    </script>
</x-app-layout>
