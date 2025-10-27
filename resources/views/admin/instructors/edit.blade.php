<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] backdrop-blur-sm overflow-hidden shadow-lg rounded-xl sm:rounded-2xl p-4 sm:p-8 border border-[#FFC8FB]/50">
                <h2 class="text-xl sm:text-2xl font-bold text-[#FF92C2] mb-4 sm:mb-6">Edit Instructor</h2>

                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('admin.instructors.update', $instructor) }}" method="POST" class="space-y-4 sm:space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Basic Information Section --}}
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-[#FF92C2] border-b border-[#FFC8FB] pb-2">Basic Information</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            {{-- First Name --}}
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-1">First Name</label>
                                <input type="text" name="first_name" value="{{ old('first_name', $instructor->user->first_name) }}"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('first_name') border-red-500 @enderror" required>
                                @error('first_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Middle Name --}}
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Middle Name</label>
                                <input type="text" name="middle_name" value="{{ old('middle_name', $instructor->user->middle_name) }}"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200">
                            </div>

                            {{-- Last Name --}}
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Last Name</label>
                                <input type="text" name="last_name" value="{{ old('last_name', $instructor->user->last_name) }}"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('last_name') border-red-500 @enderror" required>
                                @error('last_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Employee ID --}}
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Employee ID</label>
                                <input type="text" name="employee_id" value="{{ old('employee_id', $instructor->employee_id) }}"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('employee_id') border-red-500 @enderror" required>
                                @error('employee_id')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Email</label>
                                <input type="email" name="email" value="{{ old('email', $instructor->email) }}"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('email') border-red-500 @enderror" required>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
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
                            @error('password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Professional Information Section --}}
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-[#FF92C2] border-b border-[#FFC8FB] pb-2">Professional Information</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            {{-- Department --}}
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Department</label>
                                <select name="department"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('department') border-red-500 @enderror" required>
                                    <option value="">Select Department</option>
                                    <option value="Bachelor of Science in Accountancy & Accounting Information System"
                                        {{ old('department', $instructor->department) == 'Bachelor of Science in Accountancy & Accounting Information System' ? 'selected' : '' }}>
                                        Bachelor of Science in Accountancy & Accounting Information System
                                    </option>
                                    <option value="Bachelor of Science in Information Technology"
                                        {{ old('department', $instructor->department) == 'Bachelor of Science in Information Technology' ? 'selected' : '' }}>
                                        Bachelor of Science in Information Technology
                                    </option>
                                    <option value="Bachelor of Science in Information System"
                                        {{ old('department', $instructor->department) == 'Bachelor of Science in Information System' ? 'selected' : '' }}>
                                        Bachelor of Science in Information System
                                    </option>
                                </select>
                                @error('department')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Specialization --}}
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Specialization</label>
                                <input type="text" name="specialization" value="{{ old('specialization', $instructor->specialization) }}"
                                    placeholder="e.g. Auditing, Programming, Data Analytics"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('specialization') border-red-500 @enderror" required>
                                @error('specialization')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Submit Buttons --}}
                    <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4">
                        <a href="{{ route('admin.instructors.index') }}" 
                           class="w-full sm:w-auto px-6 py-3 bg-gray-500 text-white font-semibold rounded-lg hover:bg-gray-600 text-center shadow-md hover:shadow-lg transition-all duration-200">
                            Cancel
                        </a>
                        <button type="submit" 
                            class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-[#FF92C2] to-[#FF5DA2] hover:from-[#FF5DA2] hover:to-[#FF92C2] 
                                    text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                            Update Instructor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>