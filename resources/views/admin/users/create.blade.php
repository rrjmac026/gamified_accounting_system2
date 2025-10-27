<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] dark:from-[#4B4B4B] dark:to-[#3B3B3B] 
                        backdrop-blur-sm overflow-hidden shadow-lg sm:rounded-2xl p-8 border border-[#FFC8FB]/50">
                
                <h2 class="text-2xl font-bold text-[#FF92C2] dark:text-[#FF92C2] mb-6">Create New User</h2>

                <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
                    @csrf

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
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">First Name</label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" 
                                class="w-full rounded-lg shadow-sm bg-white dark:from-[#FF92C2] dark:to-[#FF92C2] 
                                      border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                      text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200" required>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Last Name</label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" 
                                class="w-full rounded-lg shadow-sm bg-white dark:from-[#FF92C2] dark:to-[#FF92C2] 
                                      border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                      text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200" required>
                        </div>
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}"
                               class="w-full rounded-lg shadow-sm bg-white dark:from-[#FF92C2] dark:to-[#FF92C2] 
                                      border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                      text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200"
                               required>
                    </div>

                    {{-- ID Number --}}
                    <div id="studentFields" 
                        class="col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6 hidden">
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">
                                Student ID Number
                            </label>
                            <input type="text" 
                                name="student_number" 
                                value="{{ old('student_number') }}"
                                class="w-full rounded-lg shadow-sm 
                                        bg-white border border-[#FFC8FB] 
                                        focus:border-[#FF92C2] focus:ring focus:ring-pink-200
                                        text-gray-800 px-4 py-2 transition duration-200"
                                required>
                        </div>
                    </div>


                    {{-- Role --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Role</label>
                        <select name="role" id="role" onchange="toggleRoleFields()"
                                class="w-full rounded-lg shadow-sm bg-white dark:from-[#FF92C2] dark:to-[#FF92C2] 
                                       border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                       text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200"
                                required>
                            <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                            <option value="instructor" {{ old('role') == 'instructor' ? 'selected' : '' }}>Instructor</option>
                            <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>

                    <!-- Student Fields -->
                    <div id="studentFields" class="col-span-2 gap-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Course</label>
                                <select name="course_id" 
                                        class="w-full rounded-lg shadow-sm bg-white dark:from-[#595758] dark:to-[#4B4B4B] 
                                               border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                               text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200">
                                    <option value="">Select Course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                            {{ $course->course_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Year Level</label>
                                <select name="year_level" 
                                       class="w-full rounded-lg shadow-sm bg-white dark:from-[#595758] dark:to-[#4B4B4B] 
                                              border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                              text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200">
                                    <option value="">Select Year Level</option>
                                    @for ($i = 1; $i <= 4; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
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
                                            {{ (is_array(old('subjects')) && in_array($subject->id, old('subjects'))) ? 'checked' : '' }}
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
                            <input type="hidden" name="section" id="selected-section" value="{{ old('section') }}">
                            <div id="section-list"
                                class="w-full rounded-lg shadow-sm bg-white border border-[#FFC8FB] 
                                        text-gray-800 px-4 py-2 transition-all duration-200 max-h-48 overflow-y-auto">
                                @foreach($sections as $section)
                                    <label class="flex items-center space-x-3 py-2 hover:bg-[#FFC8FB]/20 rounded cursor-pointer section-item">
                                        <input type="radio" name="section_radio" value="{{ $section->name }}" 
                                            {{ old('section') == $section->name ? 'checked' : '' }}
                                            class="text-[#FF92C2] border-[#FFC8FB] focus:ring-[#FF92C2] focus:ring-2"
                                            onchange="updateSelectedSection(this.value)">
                                        <span class="text-gray-800">{{ $section->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Instructor Fields -->
                    <div id="instructorFields" class="col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6" style="display: none;">
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Employee ID</label>
                            <input type="text" name="employee_id" value="{{ old('employee_id') }}"
                                   class="w-full rounded-lg shadow-sm bg-white dark:from-[#FF92C2] dark:to-[#FF92C2] 
                                          border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                          text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Department</label>
                            <input type="text" name="department" value="{{ old('department') }}"
                                   class="w-full rounded-lg shadow-sm bg-white dark:from-[#FF92C2] dark:to-[#FF92C2] 
                                          border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                          text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Specialization</label>
                            <input type="text" name="specialization" value="{{ old('specialization') }}"
                                   class="w-full rounded-lg shadow-sm bg-white dark:from-[#FF92C2] dark:to-[#FF92C2] 
                                          border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                          text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200">
                        </div>
                    </div>

                    {{-- Password --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Password</label>
                        <input type="password" name="password"
                               class="w-full rounded-lg shadow-sm bg-white dark:from-[#FF92C2] dark:to-[#FF92C2] 
                                      border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                      text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200"
                               required>
                    </div>

                    {{-- Confirm Password --}}
                    <div>
                        <label class="block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation"
                               class="w-full rounded-lg shadow-sm bg-white dark:from-[#FF92C2] dark:to-[#FF92C2] 
                                      border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200 dark:focus:ring-pink-500
                                      text-gray-800 dark:text-black-200 px-4 py-2 transition-all duration-200"
                               required>
                    </div>

                    {{-- Submit --}}
                    <div class="flex justify-end">
                        <button type="submit" 
                                class="px-6 py-2 bg-gradient-to-r from-[#FF92C2] to-[#FF5DA2] hover:from-[#FF5DA2] hover:to-[#FF92C2] 
                                       text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                            Create User
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function toggleRoleFields() {
            const role = document.getElementById('role').value;
            const studentFields = document.getElementById('studentFields');
            const instructorFields = document.getElementById('instructorFields');

            // Hide all role-specific fields first
            studentFields.style.display = 'none';
            instructorFields.style.display = 'none';

            // Show fields based on selected role
            if (role === 'student') {
                studentFields.style.display = 'block';
            } else if (role === 'instructor') {
                instructorFields.style.display = 'block';
            }

            // Update required attributes based on selected role
            const studentInputs = studentFields.querySelectorAll('input, select');
            const instructorInputs = instructorFields.querySelectorAll('input');

            studentInputs.forEach(input => input.required = (role === 'student'));
            instructorInputs.forEach(input => input.required = (role === 'instructor'));
        }

        // Section search functionality
        document.getElementById('section-search').addEventListener('keyup', function () {
            let query = this.value.toLowerCase();
            document.querySelectorAll('#section-list .section-item').forEach(function (item) {
                let text = item.innerText.toLowerCase();
                item.style.display = text.includes(query) ? '' : 'none';
            });
        });

        // Update hidden input when section is selected
        function updateSelectedSection(value) {
            document.getElementById('selected-section').value = value;
        }

        // Subject search functionality
        document.getElementById('subject-search').addEventListener('keyup', function () {
            let query = this.value.toLowerCase();
            document.querySelectorAll('#subject-list .subject-item').forEach(function (item) {
                let text = item.innerText.toLowerCase();
                item.style.display = text.includes(query) ? '' : 'none';
            });
        });

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleRoleFields();
        });
    </script>
</x-app-layout>
