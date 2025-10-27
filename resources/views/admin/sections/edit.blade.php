<x-app-layout>
    <div class="py-16 sm:py-20">
        <div class="max-w-4xl mx-auto px-8 sm:px-12 lg:px-16">
            <div class="bg-[#FFF0FA] backdrop-blur-sm overflow-hidden shadow-lg rounded-lg p-10 sm:p-12 border border-[#FFC8FB]/50">
                <h2 class="text-2xl font-bold text-[#FF92C2] mb-6">Edit Section</h2>
                <form action="{{ route('admin.sections.update', $section) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="bg-red-50 border border-red-200 rounded-md p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-exclamation-circle text-red-400"></i>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Please fix the following errors:</h3>
                                    <div class="mt-2 text-sm text-red-700">
                                        <ul class="list-disc list-inside space-y-1">
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <!-- Section Code -->
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Section Code</label>
                            <input type="text" name="section_code" value="{{ old('section_code', $section->section_code) }}" required
                                   class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">
                        </div>

                        <!-- Section Name -->
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Section Name</label>
                            <input type="text" name="name" value="{{ old('name', $section->name) }}" required
                                   class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">
                        </div>


                        <!-- Course -->
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Course</label>
                                <select name="course_id" required
                                        class="w-full rounded-lg shadow-sm bg-white 
                                                    border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                    text-gray-800 px-4 py-2 transition-all duration-200">
                                    <option value="">Select Course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ old('course_id', $section->course_id) == $course->id ? 'selected' : '' }}>
                                            {{ $course->course_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Capacity -->
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Capacity (Optional)</label>
                                <input type="number" name="capacity" min="1" value="{{ old('capacity', $section->capacity) }}"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                                    border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                    text-gray-800 px-4 py-2 transition-all duration-200">
                            </div>

                        <!-- Instructors -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Assign Instructors</label>
                            <div class="bg-white rounded-lg border border-[#FFC8FB] overflow-hidden">
                                <!-- Search -->
                                <div class="p-4 border-b border-[#FFC8FB]">
                                    <div class="relative">
                                        <input type="text" id="instructor-search"
                                               placeholder="Search instructors..."
                                               class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        </div>
                                    </div>
                                </div>

                                <!-- Controls -->
                                <div class="px-4 py-2 bg-gray-50 border-b border-[#FFC8FB] flex justify-between items-center">
                                    <div class="flex items-center gap-2">
                                        <button type="button" id="select-all-instructors" class="text-sm text-[#FF92C2] hover:text-[#ff6fb5]">Select All</button>
                                        <button type="button" id="deselect-all-instructors" class="text-sm text-[#FF92C2] hover:text-[#ff6fb5]">Deselect All</button>
                                    </div>
                                    <span class="text-xs text-gray-500" id="instructor-counter">0 selected</span>
                                </div>

                                <!-- List -->
                                <div class="max-h-64 overflow-y-auto p-2" id="instructors-container">
                                    @foreach($instructors as $instructor)
                                        <div class="instructor-item p-3 hover:bg-pink-50 rounded-lg transition-colors mb-2">
                                            <label class="flex items-center gap-3">
                                                <input type="checkbox" name="instructors[]" value="{{ $instructor->id }}"
                                                       {{ in_array($instructor->id, old('instructors', $section->instructors->pluck('id')->toArray())) ? 'checked' : '' }}
                                                       class="instructor-checkbox rounded border-[#FFC8FB] text-[#FF92C2] focus:ring-pink-200">
                                                <div>
                                                    <span class="font-medium block">{{ $instructor->user->name }}</span>
                                                    <span class="text-sm text-gray-500">
                                                        {{ $instructor->subjects->count() ? $instructor->subjects->pluck('subject_name')->join(', ') : 'No subjects' }}
                                                    </span>
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Students -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Assign Students</label>
                            <div class="bg-white rounded-lg border border-[#FFC8FB] overflow-hidden">
                                <!-- Search -->
                                <div class="p-4 border-b border-[#FFC8FB]">
                                    <div class="relative">
                                        <input type="text" id="student-search"
                                               placeholder="Search students by name or email..."
                                               class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        </div>
                                    </div>
                                </div>

                                <!-- Controls -->
                                <div class="px-4 py-2 bg-gray-50 border-b border-[#FFC8FB] flex justify-between items-center">
                                    <div class="flex items-center gap-2">
                                        <button type="button" id="select-all-students" class="text-sm text-[#FF92C2] hover:text-[#ff6fb5]">Select All</button>
                                        <button type="button" id="deselect-all-students" class="text-sm text-[#FF92C2] hover:text-[#ff6fb5]">Deselect All</button>
                                    </div>
                                    <span class="text-xs text-gray-500" id="student-counter">0 selected</span>
                                </div>

                                <!-- List -->
                                <div class="max-h-64 overflow-y-auto p-2" id="students-container">
                                    @foreach($students as $student)
                                        <div class="student-item p-3 hover:bg-pink-50 rounded-lg transition-colors mb-2">
                                            <label class="flex items-center gap-3">
                                                <input type="checkbox" name="students[]" value="{{ $student->id }}"
                                                       {{ in_array($student->id, old('students', $section->students->pluck('id')->toArray())) ? 'checked' : '' }}
                                                       class="student-checkbox rounded border-[#FFC8FB] text-[#FF92C2] focus:ring-pink-200">
                                                <div>
                                                    <span class="font-medium block">{{ $student->user->name }}</span>
                                                    <span class="text-sm text-gray-500">{{ $student->user->email }}</span>
                                                </div>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        

                        <!-- Notes -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Notes (Optional)</label>
                            <textarea name="notes" rows="3"
                                      class="w-full rounded-lg shadow-sm bg-white 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 px-4 py-2 transition-all duration-200">{{ old('notes', $section->notes) }}</textarea>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex flex-wrap justify-end gap-4 mt-4">
                        <!-- Cancel -->
                        <a href="{{ route('admin.sections.index') }}" 
                        class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                            Cancel
                        </a>

                        <!-- Manage Subjects -->
                        <a href="{{ route('admin.sections.subjects', $section->id) }}" 
                        class="px-6 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5]">
                            Manage Subjects
                        </a>

                        <!-- Update Section -->
                        <button type="submit" 
                                class="px-6 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5]">
                            Update Section
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>

    <script>
        function updateCounters() {
            document.getElementById('instructor-counter').textContent =
                document.querySelectorAll('.instructor-checkbox:checked').length + ' selected';
            document.getElementById('student-counter').textContent =
                document.querySelectorAll('.student-checkbox:checked').length + ' selected';
        }

        // Checkbox events
        document.querySelectorAll('.instructor-checkbox, .student-checkbox').forEach(cb => {
            cb.addEventListener('change', updateCounters);
        });

        // Select/Deselect all
        document.getElementById('select-all-instructors').addEventListener('click', () => {
            document.querySelectorAll('.instructor-checkbox').forEach(cb => cb.checked = true);
            updateCounters();
        });
        document.getElementById('deselect-all-instructors').addEventListener('click', () => {
            document.querySelectorAll('.instructor-checkbox').forEach(cb => cb.checked = false);
            updateCounters();
        });

        document.getElementById('select-all-students').addEventListener('click', () => {
            document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = true);
            updateCounters();
        });
        document.getElementById('deselect-all-students').addEventListener('click', () => {
            document.querySelectorAll('.student-checkbox').forEach(cb => cb.checked = false);
            updateCounters();
        });

        // Search instructors
        document.getElementById('instructor-search').addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            document.querySelectorAll('.instructor-item').forEach(item => {
                const name = item.querySelector('.font-medium').textContent.toLowerCase();
                const info = item.querySelector('.text-gray-500').textContent.toLowerCase();
                item.style.display = name.includes(term) || info.includes(term) ? '' : 'none';
            });
        });

        // Search students
        document.getElementById('student-search').addEventListener('input', function(e) {
            const term = e.target.value.toLowerCase();
            document.querySelectorAll('.student-item').forEach(item => {
                const name = item.querySelector('.font-medium').textContent.toLowerCase();
                const email = item.querySelector('.text-gray-500').textContent.toLowerCase();
                item.style.display = name.includes(term) || email.includes(term) ? '' : 'none';
            });
        });

        // Init counters
        updateCounters();
    </script>
</x-app-layout>
