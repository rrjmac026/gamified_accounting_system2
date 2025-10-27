<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] backdrop-blur-sm overflow-hidden shadow-lg rounded-xl sm:rounded-2xl p-4 sm:p-8 border border-[#FFC8FB]/50">
                <h2 class="text-xl sm:text-2xl font-bold text-[#FF92C2] mb-4 sm:mb-6">Create New Instructor</h2>

                <form action="{{ route('admin.instructors.store') }}" method="POST" class="space-y-4 sm:space-y-6">
                    @csrf

                    {{-- Basic Information Section --}}
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-[#FF92C2] border-b border-[#FFC8FB] pb-2">Basic Information</h3>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            {{-- First Name --}}
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-1">First Name</label>
                                <input type="text" name="first_name" value="{{ old('first_name') }}"
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
                                <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200">
                            </div>

                            {{-- Last Name --}}
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Last Name</label>
                                <input type="text" name="last_name" value="{{ old('last_name') }}"
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
                                <input type="text" name="employee_id" value="{{ old('employee_id') }}"
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
                                <input type="email" name="email" value="{{ old('email') }}"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('email') border-red-500 @enderror" required>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Password --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Password</label>
                                <input type="password" name="password"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200
                                            @error('password') border-red-500 @enderror" required>
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-[#FF92C2] mb-1">Confirm Password</label>
                                <input type="password" name="password_confirmation"
                                    class="w-full rounded-lg shadow-sm bg-white 
                                            border border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200
                                            text-gray-800 px-4 py-2 transition-all duration-200" required>
                            </div>
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
                                        {{ old('department') == 'Bachelor of Science in Accountancy & Accounting Information System' ? 'selected' : '' }}>
                                        Bachelor of Science in Accountancy & Accounting Information System
                                    </option>
                                    <option value="Bachelor of Science in Information Technology"
                                        {{ old('department') == 'Bachelor of Science in Information Technology' ? 'selected' : '' }}>
                                        Bachelor of Science in Information Technology
                                    </option>
                                    <option value="Bachelor of Science in Information System"
                                        {{ old('department') == 'Bachelor of Science in Information System' ? 'selected' : '' }}>
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
                                <input type="text" name="specialization" value="{{ old('specialization') }}"
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

                    {{-- Submit Button --}}
                    <div class="flex justify-end pt-4">
                        <button type="submit"
                            class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-[#FF92C2] to-[#FF5DA2] hover:from-[#FF5DA2] hover:to-[#FF92C2] 
                                    text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition-all duration-200">
                            Create Instructor
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>