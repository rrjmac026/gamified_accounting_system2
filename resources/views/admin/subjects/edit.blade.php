<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] backdrop-blur-sm overflow-hidden shadow-lg rounded-xl sm:rounded-2xl p-4 sm:p-8 border border-[#FFC8FB]/50">
                <h2 class="text-xl sm:text-2xl font-bold text-[#FF92C2] mb-4 sm:mb-6">Edit Subject</h2>

                <form action="{{ route('admin.subjects.update', $subject) }}" method="POST" class="space-y-4 sm:space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Subject Code & Name --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Subject Code</label>
                            <input type="text" name="subject_code" value="{{ old('subject_code', $subject->subject_code) }}" class="w-full rounded-lg shadow-sm bg-white border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 text-gray-800 px-4 py-2 transition-all duration-200" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Subject Name</label>
                            <input type="text" name="subject_name" value="{{ old('subject_name', $subject->subject_name) }}" class="w-full rounded-lg shadow-sm bg-white border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 text-gray-800 px-4 py-2 transition-all duration-200" required>
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full rounded-lg shadow-sm bg-white border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 text-gray-800 px-4 py-2 transition-all duration-200" required>{{ old('description', $subject->description) }}</textarea>
                    </div>

                    {{-- Multiple Instructors Selection --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Assign Instructors</label>
                        <div class="mb-2">
                            <input type="text" id="instructor-search" placeholder="Search instructors..."
                                   class="w-full rounded-lg border border-[#FFC8FB] px-3 py-2 text-sm focus:border-pink-400 focus:ring focus:ring-pink-200">
                        </div>
                        <div class="max-h-48 overflow-y-auto p-4 border border-[#FFC8FB] rounded-lg">
                            <div class="flex justify-between items-center mb-2">
                                <div class="flex items-center space-x-4">
                                    <button type="button" id="select-all-instructors" class="text-sm text-[#FF92C2] hover:text-[#ff6fb5]">Select All</button>
                                    <button type="button" id="deselect-all-instructors" class="text-sm text-[#FF92C2] hover:text-[#ff6fb5]">Deselect All</button>
                                </div>
                                <span id="instructor-counter" class="text-sm text-gray-500">0 selected</span>
                            </div>
                            @foreach($instructors as $instructor)
                                <label class="flex items-center space-x-2 py-1 instructor-item">
                                    <input type="checkbox" name="instructor_ids[]" value="{{ $instructor->id }}" 
                                           {{ in_array($instructor->id, old('instructor_ids', $subject->instructors->pluck('id')->toArray())) ? 'checked' : '' }}
                                           class="instructor-checkbox rounded border-[#FFC8FB] text-[#FF92C2] focus:ring-[#FF92C2]">
                                    <span class="font-medium">{{ $instructor->user->name }}</span>
                                    <span class="text-gray-500 text-sm">({{ $instructor->department ?? 'No Dept' }})</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Semester & Academic Year --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Semester</label>
                            <select name="semester" class="w-full rounded-lg shadow-sm bg-white border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 text-gray-800 px-4 py-2 transition-all duration-200" required>
                                <option value="">-- Select Semester --</option>
                                <option value="1st" {{ old('semester', $subject->semester) == '1st' ? 'selected' : '' }}>1st Semester</option>
                                <option value="2nd" {{ old('semester', $subject->semester) == '2nd' ? 'selected' : '' }}>2nd Semester</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Academic Year</label>
                            <select name="academic_year" 
                                    class="w-full rounded-lg shadow-sm bg-white border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 text-gray-800 px-4 py-2 transition-all duration-200" 
                                    required>
                                @php
                                    $currentYear = now()->year;
                                    for ($i = 0; $i < 6; $i++) {
                                        $start = $currentYear + $i;
                                        $end = $start + 1;
                                        $value = $start . '-' . $end;
                                @endphp
                                    <option value="{{ $value }}" {{ old('academic_year', $subject->academic_year) == $value ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                @php } @endphp
                            </select>
                        </div>
                    </div>

                    {{-- Units --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Units</label>
                        <input type="number" name="units" min="1" max="6" value="{{ old('units', $subject->units) }}" class="w-full rounded-lg shadow-sm bg-white border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 text-gray-800 px-4 py-2 transition-all duration-200" required>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Status</label>
                        <select name="is_active" class="w-full rounded-lg shadow-sm bg-white border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 text-gray-800 px-4 py-2 transition-all duration-200" required>
                            <option value="1" {{ $subject->is_active ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !$subject->is_active ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    {{-- Submit --}}
                    <div class="flex flex-col sm:flex-row justify-end gap-3">
                        <a href="{{ route('admin.subjects.index') }}" 
                           class="w-full sm:w-auto px-4 sm:px-6 py-2 bg-gray-500 text-white text-center rounded-lg hover:bg-gray-600">
                            Cancel
                        </a>
                        <button type="submit" class="w-full sm:w-auto px-4 sm:px-6 py-2 bg-gradient-to-r from-[#FF92C2] to-[#FF5DA2] hover:from-[#FF5DA2] hover:to-[#FF92C2] text-white text-sm sm:text-base font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                            Update Subject
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const checkboxes = document.querySelectorAll('.instructor-checkbox');
            const counter = document.getElementById('instructor-counter');
            const searchBox = document.getElementById('instructor-search');

            function updateCounter() {
                const checked = document.querySelectorAll('.instructor-checkbox:checked').length;
                counter.textContent = checked + ' selected';
            }

            // Checkbox counter
            checkboxes.forEach(cb => cb.addEventListener('change', updateCounter));

            // Select/Deselect all
            document.getElementById('select-all-instructors').addEventListener('click', () => {
                checkboxes.forEach(cb => cb.checked = true);
                updateCounter();
            });
            document.getElementById('deselect-all-instructors').addEventListener('click', () => {
                checkboxes.forEach(cb => cb.checked = false);
                updateCounter();
            });

            // Search filter
            searchBox.addEventListener('input', function(e) {
                const term = e.target.value.toLowerCase();
                document.querySelectorAll('.instructor-item').forEach(item => {
                    const name = item.querySelector('.font-medium').textContent.toLowerCase();
                    const dept = item.querySelector('.text-gray-500').textContent.toLowerCase();
                    item.style.display = name.includes(term) || dept.includes(term) ? '' : 'none';
                });
            });

            // Init counter
            updateCounter();
        });
    </script>
</x-app-layout>