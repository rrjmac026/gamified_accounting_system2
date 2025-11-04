<!-- Feedback Create Page -->
<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] backdrop-blur-sm overflow-hidden shadow-lg rounded-xl sm:rounded-2xl p-8 border border-[#FFC8FB]/50">
                <h2 class="text-2xl font-bold text-[#FF5CA2] mb-6">Submit Feedback</h2>

                <!-- Error Messages -->
                @if($errors->any())
                    <div class="mb-4 p-4 rounded-xl bg-[#FFE4E6] border border-[#FF99A0]">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-[#FF5A6E]"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-semibold text-[#B91C1C]">Please fix the following errors:</h3>
                                <ul class="mt-2 text-sm text-[#991B1B] list-disc list-inside">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Success Message -->
                @if(session('success'))
                    <div class="mb-4 p-4 rounded-xl bg-[#ECFDF5] border border-[#A7F3D0]">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-check-circle text-[#059669]"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-[#065F46]">{{ session('success') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('students.feedback.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <!-- Hidden Student ID -->
                    <input type="hidden" name="student_id" value="{{ Auth::user()->student->id }}">

                    <!-- Task -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Task</label>
                        <select name="performance_task_id" required class="w-full rounded-xl bg-white border border-[#FFC8FB]/70 text-gray-900 focus:border-[#FF92C2] focus:ring-2 focus:ring-[#FFD6EB] transition-colors duration-200">
                            <option value="">Select Task</option>
                            @foreach($performanceTasks as $task)
                                <option value="{{ $task->id }}" {{ old('performance_task_id') == $task->id ? 'selected' : '' }}>
                                    {{ $task->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('performance_task_id')
                            <p class="mt-1 text-sm text-[#E11D48]">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Feedback Type -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Feedback Type</label>
                        <select name="feedback_type" required class="w-full rounded-xl bg-white border border-[#FFC8FB]/70 text-gray-900 focus:border-[#FF92C2] focus:ring-2 focus:ring-[#FFD6EB] transition-colors duration-200">
                            <option value="">Select Type</option>
                            <option value="general" {{ old('feedback_type') == 'general' ? 'selected' : '' }}>General Feedback</option>
                            <option value="improvement" {{ old('feedback_type') == 'improvement' ? 'selected' : '' }}>Improvement Suggestion</option>
                            <option value="question" {{ old('feedback_type') == 'question' ? 'selected' : '' }}>Question</option>
                        </select>
                        @error('feedback_type')
                            <p class="mt-1 text-sm text-[#E11D48]">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Feedback Text -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Feedback Text</label>
                        <textarea name="feedback_text" rows="4" required 
                                  class="w-full rounded-xl bg-white border border-[#FFC8FB]/70 text-gray-900 focus:border-[#FF92C2] focus:ring-2 focus:ring-[#FFD6EB] transition-colors duration-200"
                                  placeholder="Share your thoughts about this task...">{{ old('feedback_text') }}</textarea>
                        @error('feedback_text')
                            <p class="mt-1 text-sm text-[#E11D48]">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Recommendations -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Recommendations</label>
                        <p class="text-sm text-gray-500 mb-2">Enter each recommendation on a new line</p>
                        <textarea name="recommendations" rows="4" required 
                                  class="w-full rounded-xl bg-white border border-[#FFC8FB]/70 text-gray-900 focus:border-[#FF92C2] focus:ring-2 focus:ring-[#FFD6EB] transition-colors duration-200"
                                  placeholder="Enter your recommendations here">{{ old('recommendations') }}</textarea>
                        @error('recommendations')
                            <p class="mt-1 text-sm text-[#E11D48]">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Metadata -->
                    <input type="hidden" name="generated_at" value="{{ now() }}">
                    <input type="hidden" name="is_read" value="0">

                    <!-- Rating -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            Rating <span class="text-red-500">*</span>
                        </label>
                        <p class="text-sm text-gray-500 mb-3">Please rate your experience with this task</p>
                        <div class="flex flex-wrap gap-4">
                            @for($i = 1; $i <= 5; $i++)
                                <label class="flex items-center space-x-2 cursor-pointer hover:bg-[#FFE6F4] p-2 rounded-xl transition-colors">
                                    <input type="radio" name="rating" value="{{ $i }}" 
                                           {{ old('rating') == $i ? 'checked' : '' }}
                                           {{ $i == 3 && !old('rating') ? 'checked' : '' }}
                                           class="text-[#FF5CA2] focus:ring-[#FF92C2] focus:ring-2" required>
                                    <span class="text-gray-700 font-medium">{{ $i }}</span>
                                    <div class="flex">
                                        @for($j = 1; $j <= $i; $j++)
                                            <i class="fas fa-star text-amber-400 text-sm"></i>
                                        @endfor
                                        @for($k = $i + 1; $k <= 5; $k++)
                                            <i class="far fa-star text-gray-300 text-sm"></i>
                                        @endfor
                                    </div>
                                </label>
                            @endfor
                        </div>
                        @error('rating')
                            <p class="mt-1 text-sm text-[#E11D48]">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end space-x-4 pt-4">
                        <a href="{{ route('students.feedback.index') }}" 
                           class="px-6 py-2 bg-gray-400 text-white rounded-xl hover:bg-gray-500 transition-colors font-medium">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-[#FF5CA2] text-white rounded-xl hover:bg-[#FF3B8D] transition-colors font-medium">
                            Submit Feedback
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Star Interaction Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ratingInputs = document.querySelectorAll('input[name="rating"]');
            
            ratingInputs.forEach((input) => {
                input.addEventListener('change', function() {
                    updateStarDisplay(parseInt(this.value));
                });
            });

            function updateStarDisplay(selectedRating) {
                ratingInputs.forEach((input) => {
                    const stars = input.closest('label').querySelectorAll('i');
                    const inputRating = parseInt(input.value);
                    
                    stars.forEach((star, starIndex) => {
                        if (starIndex < inputRating && inputRating <= selectedRating) {
                            star.className = 'fas fa-star text-amber-400 text-sm';
                        } else {
                            star.className = 'far fa-star text-gray-300 text-sm';
                        }
                    });
                });
            }
        });
    </script>
</x-app-layout>
