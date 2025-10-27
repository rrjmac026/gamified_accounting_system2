<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] backdrop-blur-sm overflow-hidden shadow-lg rounded-lg p-8 border border-[#FFC8FB]/50">
                <h2 class="text-2xl font-bold text-[#FF92C2] mb-6">Edit Question</h2>

                <form action="{{ route('instructors.task-questions.update', $taskQuestion) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Task</label>
                            <select name="task_id" required class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">
                                @foreach($tasks as $task)
                                    <option value="{{ $task->id }}" {{ $taskQuestion->task_id == $task->id ? 'selected' : '' }}>
                                        {{ $task->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Question Text</label>
                            <textarea name="question_text" rows="3" required class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">{{ $taskQuestion->question_text }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Question Type</label>
                            <select name="question_type" required id="questionType" class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">
                                @foreach(['multiple_choice', 'true_false', 'essay', 'calculation'] as $type)
                                    <option value="{{ $type }}" {{ $taskQuestion->question_type == $type ? 'selected' : '' }}>
                                        {{ str_replace('_', ' ', ucfirst($type)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div id="optionsContainer" class="{{ $taskQuestion->question_type == 'multiple_choice' ? '' : 'hidden' }}">
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Options</label>
                            <div class="space-y-2" id="optionsList">
                                @if($taskQuestion->options)
                                    @foreach($taskQuestion->options as $option)
                                        <div class="flex gap-2">
                                            <input type="text" name="options[]" value="{{ $option }}" 
                                                   class="flex-1 rounded-lg border-[#FFC8FB]">
                                            <button type="button" class="px-2 py-1 bg-red-500 text-white rounded" 
                                                    onclick="this.parentElement.remove()">-</button>
                                        </div>
                                    @endforeach
                                @endif
                                <button type="button" class="px-2 py-1 bg-[#FF92C2] text-white rounded" 
                                        onclick="addOption()">Add Option</button>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Correct Answer</label>
                            <input type="text" name="correct_answer" value="{{ $taskQuestion->correct_answer }}" required
                                   class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Points</label>
                                <input type="number" name="points" value="{{ $taskQuestion->points }}" required min="1"
                                       class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Order</label>
                                <input type="number" name="order_index" value="{{ $taskQuestion->order_index }}" required min="0"
                                       class="w-full rounded-lg bg-white border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200">
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-4">
                        <a href="{{ route('instructors.task-questions.index') }}" 
                           class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Cancel</a>
                        <button type="submit" 
                                class="px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5]">
                            Update Question
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
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
            optionsList.insertBefore(newOption, optionsList.lastElementChild);
        }
    </script>
</x-app-layout>
