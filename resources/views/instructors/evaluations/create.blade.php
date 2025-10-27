<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] dark:bg-[#595758] overflow-hidden shadow-lg sm:rounded-2xl p-8">
                <h2 class="text-2xl font-bold text-[#FF92C2] dark:text-[#FFC8FB] mb-6">Submit Course Evaluation</h2>

                <form action="{{ route('evaluations.store') }}" method="POST" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Select Instructor</label>
                            <select name="instructor_id" class="w-full rounded-lg bg-white dark:bg-[#595758] border-[#FFC8FB]" required>
                                <option value="">Choose an instructor</option>
                                @foreach($instructors as $instructor)
                                    <option value="{{ $instructor->id }}">{{ $instructor->user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Select Course</label>
                            <select name="course_id" class="w-full rounded-lg bg-white dark:bg-[#595758] border-[#FFC8FB]" required>
                                <option value="">Choose a course</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Comments</label>
                            <textarea name="comments" rows="4" class="w-full rounded-lg bg-white dark:bg-[#595758] border-[#FFC8FB]" required></textarea>
                        </div>

                        <!-- Add your evaluation criteria/questions here -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-semibold text-[#FF92C2] dark:text-[#FFC8FB]">Evaluation Criteria</h3>
                            @foreach($criteria as $key => $criterion)
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-[#FFC8FB] mb-1">{{ $criterion }}</label>
                                    <div class="flex space-x-4">
                                        @for($i = 1; $i <= 5; $i++)
                                            <label class="flex items-center">
                                                <input type="radio" name="responses[{{ $key }}]" value="{{ $i }}" class="text-[#FF92C2]" required>
                                                <span class="ml-2 text-sm text-gray-600 dark:text-[#FFC8FB]">{{ $i }}</span>
                                            </label>
                                        @endfor
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <button type="submit" class="px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5]">
                            Submit Evaluation
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
