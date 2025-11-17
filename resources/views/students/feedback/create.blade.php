<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 sm:rounded-lg">
                <div class="p-6 text-gray-700">
                    <!-- Back Button -->
                    <div class="mb-6">
                        <a href="{{ route('students.feedback.index') }}" 
                           class="text-[#FF92C2] hover:text-[#ff6fb5] flex items-center gap-2">
                            <i class="fas fa-arrow-left"></i>
                            Back to Feedback List
                        </a>
                    </div>

                    <!-- Header -->
                    <div class="mb-6">
                        <h2 class="text-2xl font-bold text-[#FF92C2]">Submit Feedback</h2>
                        <div class="mt-2 p-4 bg-white rounded-lg border border-[#FFC8FB]">
                            <p class="text-sm text-gray-600"><strong>Performance Task:</strong> {{ $task->title }}</p>
                            <p class="text-sm text-gray-600 mt-1"><strong>Step {{ $step }}:</strong> {{ $stepTitles[$step] }}</p>
                            <div class="mt-2 flex items-center gap-4 text-sm">
                                <span class="px-3 py-1 rounded-full bg-blue-100 text-blue-800">
                                    Score: {{ $submission->score }}
                                </span>
                                <span class="px-3 py-1 rounded-full capitalize
                                    {{ $submission->status === 'correct' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $submission->status === 'passed' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $submission->status === 'wrong' ? 'bg-red-100 text-red-800' : '' }}">
                                    {{ $submission->status }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Error Messages -->
                    @if($errors->any())
                        <div class="mb-4 p-4 rounded-md bg-red-50 border border-red-200">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                                    <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Feedback Form -->
                    <form action="{{ route('students.feedback.store') }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="performance_task_id" value="{{ $task->id }}">
                        <input type="hidden" name="step" value="{{ $step }}">

                        <!-- Feedback Type -->
                        <div>
                            <label for="feedback_type" class="block text-sm font-medium text-gray-700 mb-2">
                                Feedback Type <span class="text-red-500">*</span>
                            </label>
                            <select id="feedback_type" name="feedback_type" required
                                    class="w-full px-4 py-2 border border-[#FFC8FB] rounded-lg focus:ring-2 focus:ring-[#FF92C2] focus:border-transparent">
                                <option value="">Select feedback type</option>
                                <option value="general" {{ old('feedback_type') === 'general' ? 'selected' : '' }}>
                                    General Feedback
                                </option>
                                <option value="improvement" {{ old('feedback_type') === 'improvement' ? 'selected' : '' }}>
                                    Suggestion for Improvement
                                </option>
                                <option value="question" {{ old('feedback_type') === 'question' ? 'selected' : '' }}>
                                    Question/Clarification
                                </option>
                            </select>
                        </div>

                        <!-- Rating -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Rate this Step <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center gap-2">
                                @for($i = 1; $i <= 5; $i++)
                                    <label class="cursor-pointer">
                                        <input type="radio" name="rating" value="{{ $i }}" 
                                               {{ old('rating') == $i ? 'checked' : '' }}
                                               class="hidden peer" required>
                                        <i class="fas fa-star text-3xl text-gray-300 peer-checked:text-yellow-400 hover:text-yellow-300 transition-colors"></i>
                                    </label>
                                @endfor
                                <span class="ml-2 text-sm text-gray-600">
                                    (1 = Poor, 5 = Excellent)
                                </span>
                            </div>
                        </div>

                        <!-- Feedback Text -->
                        <div>
                            <label for="feedback_text" class="block text-sm font-medium text-gray-700 mb-2">
                                Your Feedback <span class="text-red-500">*</span>
                            </label>
                            <textarea id="feedback_text" name="feedback_text" rows="6" required
                                      class="w-full px-4 py-2 border border-[#FFC8FB] rounded-lg focus:ring-2 focus:ring-[#FF92C2] focus:border-transparent"
                                      placeholder="Share your thoughts about this step... (minimum 10 characters)">{{ old('feedback_text') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">
                                Minimum 10 characters, maximum 5000 characters
                            </p>
                        </div>

                        <!-- Recommendations -->
                        <div>
                            <label for="recommendations" class="block text-sm font-medium text-gray-700 mb-2">
                                Recommendations for Improvement (Optional)
                            </label>
                            <textarea id="recommendations" name="recommendations" rows="4"
                                      class="w-full px-4 py-2 border border-[#FFC8FB] rounded-lg focus:ring-2 focus:ring-[#FF92C2] focus:border-transparent"
                                      placeholder="Any specific suggestions to improve this step?">{{ old('recommendations') }}</textarea>
                            <p class="mt-1 text-xs text-gray-500">
                                Enter each recommendation on a new line (max 2000 characters)
                            </p>
                        </div>

                        <!-- Anonymous Option -->
                        <div class="flex items-center">
                            <input id="is_anonymous" name="is_anonymous" type="checkbox" value="1"
                                   {{ old('is_anonymous') ? 'checked' : '' }}
                                   class="w-4 h-4 text-[#FF92C2] border-gray-300 rounded focus:ring-[#FF92C2]">
                            <label for="is_anonymous" class="ml-2 block text-sm text-gray-700">
                                Submit this feedback anonymously
                            </label>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex items-center justify-end gap-4 pt-4 border-t border-[#FFC8FB]">
                            <a href="{{ route('students.feedback.index') }}" 
                               class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="px-6 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5] transition-colors flex items-center gap-2">
                                <i class="fas fa-paper-plane"></i>
                                Submit Feedback
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Character counter for feedback text
        const feedbackText = document.getElementById('feedback_text');
        if (feedbackText) {
            feedbackText.addEventListener('input', function() {
                const length = this.value.length;
                const parent = this.parentElement;
                let counter = parent.querySelector('.char-counter');
                
                if (!counter) {
                    counter = document.createElement('p');
                    counter.className = 'char-counter mt-1 text-xs text-gray-500';
                    parent.appendChild(counter);
                }
                
                counter.textContent = `${length} / 5000 characters`;
                
                if (length < 10) {
                    counter.classList.add('text-red-500');
                    counter.classList.remove('text-gray-500');
                } else {
                    counter.classList.add('text-gray-500');
                    counter.classList.remove('text-red-500');
                }
            });
        }
    </script>
</x-app-layout>