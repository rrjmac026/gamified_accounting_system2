<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 text-gray-700">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-[#FF92C2]">User Details</h2>
                        <div class="flex space-x-4">
                            <a href="{{ route('admin.users.edit', $user->id) }}" 
                               class="px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5]">
                                Edit User
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-[#FF92C2] mb-4">Basic Information</h3>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Name:</dt>
                                    <dd class="text-gray-900">{{ $user->name }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Email:</dt>
                                    <dd class="text-gray-900">{{ $user->email }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Role:</dt>
                                    <dd class="text-gray-900 capitalize">{{ $user->role }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Status:</dt>
                                    <dd>
                                        @if($user->is_active)
                                            <span class="text-green-600 font-medium">Active</span>
                                        @else
                                            <span class="text-red-600 font-medium">Inactive</span>
                                        @endif
                                    </dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Joined Date:</dt>
                                    <dd class="text-gray-900">{{ $user->created_at->format('F j, Y') }}</dd>
                                </div>
                            </dl>
                        </div>

                        @if($user->role === 'student' && $user->student)
                            <div class="bg-white p-6 rounded-lg shadow">
                                <h3 class="text-lg font-semibold text-[#FF92C2] mb-4">Student Information</h3>
                                <dl class="space-y-2">
                                    <div class="flex justify-between">
                                        <dt class="text-gray-500">Student Number:</dt>
                                        <dd class="text-gray-900">{{ $user->student->student_number ?? 'N/A' }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-500">Course:</dt>
                                        <dd class="text-gray-900">{{ $user->student->course->course_name ?? 'N/A' }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-500">Year Level:</dt>
                                        <dd class="text-gray-900">{{ $user->student->year_level ?? 'N/A' }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-500">Section:</dt>
                                        <dd class="text-gray-900">{{ $user->student->section ?? 'N/A' }}</dd>
                                    </div>
                                </dl>
                            </div>
                        @elseif($user->role === 'instructor' && $user->instructor)
                            <div class="bg-white p-6 rounded-lg shadow">
                                <h3 class="text-lg font-semibold text-[#FF92C2] mb-4">Instructor Information</h3>
                                <dl class="space-y-2">
                                    <div class="flex justify-between">
                                        <dt class="text-gray-500">Employee ID:</dt>
                                        <dd class="text-gray-900">{{ $user->instructor->employee_id ?? 'N/A' }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-500">Department:</dt>
                                        <dd class="text-gray-900">{{ $user->instructor->department ?? 'N/A' }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-500">Specialization:</dt>
                                        <dd class="text-gray-900">{{ $user->instructor->specialization ?? 'N/A' }}</dd>
                                    </div>
                                </dl>
                            </div>
                        @endif
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex justify-end space-x-4 mt-6">
                        <a href="{{ route('admin.users.index') }}" 
                           class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors duration-200">
                            Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
