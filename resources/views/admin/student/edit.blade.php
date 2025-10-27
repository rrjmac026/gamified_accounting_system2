<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] backdrop-blur-sm overflow-hidden shadow-lg rounded-xl sm:rounded-2xl p-4 sm:p-8 border border-[#FFC8FB]/50">
                <h2 class="text-xl sm:text-2xl font-bold text-[#FF92C2] mb-4 sm:mb-6">Edit Student</h2>

                {{-- Error Alert --}}
                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('admin.student.update', $student) }}" method="POST" class="space-y-4 sm:space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Basic Information Section --}}
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-[#FF92C2] border-b border-[#FFC8FB] pb-2">Basic Information</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            {{-- First Name --}}
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-1">First Name</label>
                                <input type="text" name="first_name" value="{{ old('first_name', $student->user->first_name) }}"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('first_name') border-red-500 @enderror" required>
                                @error('first_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Middle Name --}}
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Middle Name</label>
                                <input type="text" name="middle_name" value="{{ old('middle_name', $student->user->middle_name) }}"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200">
                            </div>

                            {{-- Last Name --}}
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Last Name</label>
                                <input type="text" name="last_name" value="{{ old('last_name', $student->user->last_name) }}"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('last_name') border-red-500 @enderror" required>
                                @error('last_name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Student Number --}}
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Student ID</label>
                                <input type="text" name="student_number" value="{{ old('student_number', $student->student_number) }}"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('student_number') border-red-500 @enderror" required>
                                @error('student_number') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email', $student->user->email) }}"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('email') border-red-500 @enderror" required>
                                @error('email') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Password Update --}}
                        <div>
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-1">
                                Change Password <span class="text-gray-500 font-normal">(leave blank to keep current)</span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <input type="password" name="password" placeholder="New Password"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200">
                                <input type="password" name="password_confirmation" placeholder="Confirm Password"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200">
                            </div>
                            @error('password') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Academic Information Section --}}
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-[#FF92C2] border-b border-[#FFC8FB] pb-2">Academic Information</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            {{-- Course --}}
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Course</label>
                                <select name="course_id" required
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('course_id') border-red-500 @enderror">
                                    <option value="">Select Course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->id }}" {{ old('course_id', $student->course_id) == $course->id ? 'selected' : '' }}>
                                            {{ $course->course_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('course_id') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Year Level --}}
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Year Level</label>
                                <select name="year_level" required
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('year_level') border-red-500 @enderror">
                                    <option value="">Select Year Level</option>
                                    @for ($i = 1; $i <= 4; $i++)
                                        <option value="{{ $i }}" {{ old('year_level', $student->year_level) == $i ? 'selected' : '' }}>Year {{ $i }}</option>
                                    @endfor
                                </select>
                                @error('year_level') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                            </div>

                            {{-- Section --}}
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Section</label>
                                
                                {{-- Search Input with icon --}}
                                <div class="relative mb-2">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400 text-sm"></i>
                                    </div>
                                    <input type="text" id="section-search" placeholder="Search sections..."
                                        class="w-full pl-10 pr-4 py-2 rounded-lg border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                                text-gray-800 text-sm transition-all duration-200 bg-white" />
                                </div>

                                {{-- Hidden input to store selected section --}}
                                <input type="hidden" name="section_id" id="selected-section-id" value="{{ old('section_id', $student->section_id) }}">

                                {{-- Sections Radio List --}}
                                <div id="section-list"
                                    class="w-full rounded-lg shadow-sm bg-white border border-[#FFC8FB] 
                                            max-h-60 overflow-y-auto">
                                    @foreach($sections as $section)
                                        <label class="flex items-center space-x-3 px-4 py-2.5 hover:bg-[#FFC8FB]/20 cursor-pointer section-item border-b border-[#FFC8FB]/30 last:border-b-0">
                                            <input type="radio" name="section_radio" value="{{ $section->id }}"
                                                {{ old('section_id', $student->section_id) == $section->id ? 'checked' : '' }}
                                                class="text-[#FF92C2] border-[#FFC8FB] focus:ring-[#FF92C2] focus:ring-2"
                                                onchange="document.getElementById('selected-section-id').value = this.value;">
                                            <span class="text-gray-800 text-sm font-medium">
                                                {{ $section->name }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('section_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Subjects Section --}}
                        <div class="col-span-1 sm:col-span-3">
                            <label class="block text-sm font-semibold text-[#FF92C2] mb-2">Assign Subjects</label>
                            
                            {{-- Search Input with icon --}}
                            <div class="relative mb-3">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none z-10">
                                    <i class="fas fa-search text-gray-500 text-sm"></i>
                                </div>
                                <input type="text" 
                                       id="subject-search" 
                                       placeholder="Search subjects by code or name..."
                                       class="relative w-full pl-10 pr-4 py-2.5 rounded-lg border-2 border-[#FFC8FB] 
                                              focus:border-[#FF92C2] focus:ring-2 focus:ring-[#FF92C2]/20 focus:outline-none
                                              text-gray-800 text-sm transition-all duration-200 bg-white shadow-sm" />
                            </div>

                            {{-- Subjects Checkbox List --}}
                            <div id="subject-list"
                                class="w-full rounded-lg shadow-md bg-white border-2 border-[#FFC8FB] 
                                        max-h-60 overflow-y-auto">
                                @foreach($subjects as $subject)
                                    <label class="flex items-center space-x-3 px-4 py-2.5 hover:bg-[#FFC8FB]/20 cursor-pointer subject-item border-b border-[#FFC8FB]/30 last:border-b-0">
                                        <input type="checkbox" name="subjects[]" value="{{ $subject->id }}"
                                            {{ in_array($subject->id, old('subjects', $student->subjects->pluck('id')->toArray())) ? 'checked' : '' }}
                                            class="text-[#FF92C2] border-[#FFC8FB] rounded focus:ring-[#FF92C2] focus:ring-2">
                                        <span class="text-gray-800 text-sm">
                                            <span class="font-semibold">{{ $subject->subject_code }}</span> - {{ $subject->subject_name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                            @error('subjects')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4">
                        <a href="{{ route('admin.student.index') }}" 
                           class="w-full sm:w-auto px-6 py-3 bg-gray-500 text-white font-semibold rounded-lg hover:bg-gray-600 text-center shadow-md hover:shadow-lg transition-all duration-200">
                            Cancel
                        </a>
                        <button type="submit" 
                                class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-[#FF92C2] to-[#FF5DA2] hover:from-[#FF5DA2] hover:to-[#FF92C2] 
                                       text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                            Update Student
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Subject search functionality
        document.getElementById('subject-search').addEventListener('keyup', function () {
            let query = this.value.toLowerCase();
            document.querySelectorAll('#subject-list .subject-item').forEach(function (item) {
                let text = item.innerText.toLowerCase();
                item.style.display = text.includes(query) ? '' : 'none';
            });
        });

        // Section search functionality
        document.getElementById('section-search').addEventListener('keyup', function () {
            let query = this.value.toLowerCase();
            document.querySelectorAll('#section-list .section-item').forEach(function (item) {
                let text = item.innerText.toLowerCase();
                item.style.display = text.includes(query) ? '' : 'none';
            });
        });
    </script>
</x-app-layout>