<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] dark:from-[#4B4B4B] dark:to-[#3B3B3B] 
                        backdrop-blur-sm overflow-hidden shadow-lg sm:rounded-2xl p-8 border border-[#FFC8FB]/50">
                
                <h2 class="text-2xl font-bold text-[#FF92C2] dark:text-[#FF92C2] mb-6">Edit User</h2>

                <form action="{{ route('admin.users.update', $user) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Name --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">First Name</label>
                            <input type="text" name="first_name" value="{{ $user->first_name }}" 
                                class="w-full rounded-lg shadow-sm bg-white dark:from-[#FF92C2] dark:to-[#FF92C2] 
                                      border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                      text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200" required>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Last Name</label>
                            <input type="text" name="last_name" value="{{ $user->last_name }}" 
                                class="w-full rounded-lg shadow-sm bg-white dark:from-[#FF92C2] dark:to-[#FF92C2] 
                                      border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                      text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200" required>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Email</label>
                        <input type="email" name="email" value="{{ $user->email }}" class="w-full rounded-lg shadow-sm bg-white dark:from-[#FF92C2] dark:to-[#FF92C2] 
                                  border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                  text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200" required>
                    </div>

                    {{-- ID Number --}}
                    <div id="studentFields" class="col-span-2 gap-6 student-fields" style="display: none;">
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">
                                Student ID Number
                            </label>
                            <input type="text" 
                                name="student_number" 
                                value="{{ old('student_number', $user->student->student_number ?? '') }}"
                                class="w-full rounded-lg shadow-sm 
                                        bg-white border border-[#FFC8FB] 
                                        focus:border-[#FF92C2] focus:ring focus:ring-pink-200
                                        text-gray-800 px-4 py-2 transition duration-200"
                                required>
                        </div>
                    </div>

                    {{-- Role Selection --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FFC8FB] mb-1">Role</label>
                        <select name="role" id="role" onchange="toggleRoleFields()" 
                                class="w-full rounded-lg shadow-sm bg-white dark:from-[#FF92C2] dark:to-[#FF92C2] 
                                      border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                      text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200" required>
                            <option value="student" {{ $user->role === 'student' ? 'selected' : '' }}>Student</option>
                            <option value="instructor" {{ $user->role === 'instructor' ? 'selected' : '' }}>Instructor</option>
                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>

                    <!-- Student Fields -->
                    <div id="studentFields" class="col-span-2 gap-6 student-fields" style="display: none;">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Course</label>
                                <select name="course_id" 
                                        class="w-full rounded-lg shadow-sm bg-white dark:from-[#595758] dark:to-[#4B4B4B] 
                                               border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                               text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200">
                                    <option value="">Select Course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ old('course_id', $user->student->course_id ?? '') == $course->id ? 'selected' : '' }}>
                                            {{ $course->course_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Year Level</label>
                                <select name="year_level" 
                                       class="w-full rounded-lg shadow-sm bg-white dark:from-[#595758] dark:to-[#4B4B4B] 
                                              border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                              text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200">
                                    <option value="">Select Year Level</option>
                                    @for ($i = 1; $i <= 4; $i++)
                                        <option value="{{ $i }}" {{ old('year_level', $user->student->year_level ?? '') == $i ? 'selected' : '' }}>
                                            {{ $i }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Assign Subjects</label>
                            <div class="mb-2">
                                <input type="text" id="subject-search" placeholder="Search subjects..."
                                       class="w-full rounded-lg border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                               text-gray-800 px-3 py-2 text-sm transition-all duration-200" />
                            </div>
                            <div id="subject-list"
                                 class="w-full rounded-lg shadow-sm bg-white border border-[#FFC8FB] 
                                         text-gray-800 px-4 py-2 transition-all duration-200 max-h-48 overflow-y-auto">
                                @foreach($subjects as $subject)
                                    <label class="flex items-center space-x-3 py-2 hover:bg-[#FFC8FB]/20 rounded cursor-pointer subject-item">
                                        <input type="checkbox" name="subjects[]" value="{{ $subject->id }}"
                                            {{ (is_array(old('subjects', optional($user->student)->subjects?->pluck('id')->toArray() ?: [])) 
                                                && in_array($subject->id, old('subjects', optional($user->student)->subjects?->pluck('id')->toArray() ?: []))) ? 'checked' : '' }}
                                            class="text-[#FF92C2] border-[#FFC8FB] rounded focus:ring-[#FF92C2] focus:ring-2">
                                        <span class="text-gray-800">{{ $subject->subject_name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Section</label>
                            <div class="mb-2">
                                <input type="text" id="section-search" placeholder="Search sections..."
                                       class="w-full rounded-lg border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                               text-gray-800 px-3 py-2 text-sm transition-all duration-200" />
                            </div>
                            <input type="hidden" name="section" id="selected-section" value="{{ old('section', $user->student->section ?? '') }}">
                            <div id="section-list"
                                 class="w-full rounded-lg shadow-sm bg-white border border-[#FFC8FB] 
                                         text-gray-800 px-4 py-2 transition-all duration-200 max-h-48 overflow-y-auto">
                                @foreach($sections as $section)
                                    <label class="flex items-center space-x-3 py-2 hover:bg-[#FFC8FB]/20 rounded cursor-pointer section-item">
                                        <input type="radio" name="section_radio" value="{{ $section->name }}" 
                                               {{ old('section', $user->student->section ?? '') == $section->name ? 'checked' : '' }}
                                               class="text-[#FF92C2] border-[#FFC8FB] focus:ring-[#FF92C2] focus:ring-2"
                                               onchange="updateSelectedSection(this.value)">
                                        <span class="text-gray-800">{{ $section->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Instructor Fields -->
                        <div id="instructorFields" class="col-span-2 instructor-fields" style="display: none;">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                {{-- Employee ID --}}
                                <div>
                                    <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Employee ID</label>
                                    <input type="text" name="employee_id" 
                                        value="{{ old('employee_id', $user->instructor->employee_id ?? '') }}"
                                        class="w-full rounded-lg shadow-sm bg-white dark:from-[#FF92C2] dark:to-[#FF92C2] 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                                text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200">
                                </div>

                                {{-- Department (Dropdown) --}}
                                <div>
                                    <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Department</label>
                                    <select name="department"
                                            class="w-full rounded-lg shadow-sm bg-white dark:from-[#FF92C2] dark:to-[#FF92C2] 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                                text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200">
                                        <option value="">-- Select Department --</option>
                                        <option value="Bachelor of Science in Accountancy & Accounting Information System"
                                            {{ old('department', $user->instructor->department ?? '') == 'Bachelor of Science in Accountancy & Accounting Information System' ? 'selected' : '' }}>
                                            Bachelor of Science in Accountancy & Accounting Information System
                                        </option>
                                        <option value="Bachelor of Science in Information Technology"
                                            {{ old('department', $user->instructor->department ?? '') == 'Bachelor of Science in Information Technology' ? 'selected' : '' }}>
                                            Bachelor of Science in Information Technology
                                        </option>
                                        <option value="Bachelor of Science in Information System"
                                            {{ old('department', $user->instructor->department ?? '') == 'Bachelor of Science in Information System' ? 'selected' : '' }}>
                                            Bachelor of Science in Information System
                                        </option>
                                    </select>
                                </div>

                                {{-- Specialization --}}
                                <div>
                                    <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Specialization</label>
                                    <input type="text" name="specialization" 
                                        value="{{ old('specialization', $user->instructor->specialization ?? '') }}"
                                        placeholder="e.g. Auditing, Programming"
                                        class="w-full rounded-lg shadow-sm bg-white dark:from-[#FF92C2] dark:to-[#FF92C2] 
                                                border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                                text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200">
                                </div>
                            </div>
                        </div>


                    {{-- Password Fields --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">New Password</label>
                            <input type="password" name="password"
                                   class="w-full rounded-lg shadow-sm bg-white dark:from-[#FF92C2] dark:to-[#FF92C2] 
                                          border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                          text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200">
                            <p class="text-sm text-gray-500 mt-1">Leave blank to keep current password</p>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Confirm New Password</label>
                            <input type="password" name="password_confirmation"
                                   class="w-full rounded-lg shadow-sm bg-white dark:from-[#FF92C2] dark:to-[#FF92C2] 
                                          border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                          text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200">
                        </div>
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="flex justify-end space-x-4">
                        <a href="{{ route('admin.users.index') }}" 
                           class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-gradient-to-r from-[#FF92C2] to-[#FF5DA2] hover:from-[#FF5DA2] hover:to-[#FF92C2] 
                                       text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                            Update User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Add this before closing </x-app-layout> tag --}}
    <script>
        function toggleRoleFields() {
            const role = document.getElementById('role').value;
            const studentFields = document.querySelectorAll('.student-fields');
            const instructorFields = document.querySelectorAll('.instructor-fields');
            
            // Reset all fields
            studentFields.forEach(field => {
                field.style.display = 'none';
                field.querySelectorAll('input, select').forEach(input => {
                    input.required = false;
                });
            });
            
            instructorFields.forEach(field => {
                field.style.display = 'none';
                field.querySelectorAll('input, select').forEach(input => {
                    input.required = false;
                });
            });

            // Show and set required fields based on role
            if (role === 'student') {
                studentFields.forEach(field => {
                    field.style.display = 'block';
                    field.querySelectorAll('input, select').forEach(input => {
                        // Skip subject checkboxes and search fields from required
                        if (!input.matches('[type="checkbox"], [type="search"], [id*="search"]')) {
                            input.required = true;
                        }
                    });
                });
            } else if (role === 'instructor') {
                instructorFields.forEach(field => {
                    field.style.display = 'block';
                    field.querySelectorAll('input:not([type="search"])').forEach(input => {
                        input.required = true;
                    });
                });
            }
        }

        // Initialize role fields on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleRoleFields();
            
            // Add event listener for role changes
            const roleSelect = document.getElementById('role');
            if (roleSelect) {
                roleSelect.addEventListener('change', toggleRoleFields);
            }
        });

        // Section search functionality
        const sectionSearch = document.getElementById('section-search');
        if (sectionSearch) {
            sectionSearch.addEventListener('keyup', function () {
                let query = this.value.toLowerCase();
                document.querySelectorAll('#section-list .section-item').forEach(function (item) {
                    let text = item.innerText.toLowerCase();
                    item.style.display = text.includes(query) ? '' : 'none';
                });
            });
        }

        // Update hidden input when section is selected
        function updateSelectedSection(value) {
            document.getElementById('selected-section').value = value;
        }

        // Subject search functionality
        const subjectSearch = document.getElementById('subject-search');
        if (subjectSearch) {
            subjectSearch.addEventListener('keyup', function () {
                let query = this.value.toLowerCase();
                document.querySelectorAll('#subject-list .subject-item').forEach(function (item) {
                    let text = item.innerText.toLowerCase();
                    item.style.display = text.includes(query) ? '' : 'none';
                });
            });
        }
    </script>
</x-app-layout>
