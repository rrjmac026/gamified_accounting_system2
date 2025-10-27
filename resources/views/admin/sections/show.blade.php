<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-[#FF92C2]">Section Details</h2>
                        <a href="{{ route('admin.sections.edit', $section) }}" 
                           class="inline-flex items-center px-4 py-2 bg-[#FF92C2] text-white rounded-md hover:bg-[#ff6fb5]">
                            <i class="fas fa-edit mr-2"></i>Edit Section
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-[#595758] mb-4">Section Information</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Section Code</dt>
                                    <dd class="mt-1 text-sm text-[#595758]">{{ $section->section_code }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="mt-1 text-sm text-[#595758]">{{ $section->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Course</dt>
                                    <dd class="mt-1 text-sm text-[#595758]">{{ $section->course->course_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Capacity</dt>
                                    <dd class="mt-1 text-sm text-[#595758]">{{ $section->capacity ?? 'Unlimited' }}</dd>
                                </div>
                            </dl>
                        </div>

                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-[#595758] mb-4">Statistics</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Students</dt>
                                    <dd class="mt-1 text-sm text-[#595758]">{{ $section->students->count() }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Subjects</dt>
                                    <dd class="mt-1 text-sm text-[#595758]">{{ $section->subjects->count() }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Total Instructors</dt>
                                    <dd class="mt-1 text-sm text-[#595758]">{{ $section->instructors->count() }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- Instructors List -->
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold text-[#595758] mb-4">Assigned Instructors</h3>
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-[#FFC8FB]">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-[#595758] uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-[#595758] uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-[#595758] uppercase tracking-wider">Department</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-[#595758] uppercase tracking-wider">Assigned Subjects</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-[#595758] uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($section->instructors as $instructor)
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $instructor->user->name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $instructor->user->email }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $instructor->department ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 text-sm">
                                                @if($instructor->subjects->count() > 0)
                                                    <ul class="list-disc list-inside">
                                                        @foreach($instructor->subjects as $subject)
                                                            <li class="text-gray-500">{{ $subject->subject_name }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-gray-500">No subjects assigned</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-4 text-sm text-center text-gray-500">No instructors assigned</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Students List -->
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold text-[#595758] mb-4">Enrolled Students</h3>
                        <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-[#FFC8FB]">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-[#595758] uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-[#595758] uppercase tracking-wider">Student ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-[#595758] uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($section->students as $student)
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $student->user->name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $student->student_number }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">Active</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-4 text-sm text-center text-gray-500">No students enrolled</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <!-- Subjects List -->
                        <div class="mt-8">
                            <h3 class="text-lg font-semibold text-[#595758] mb-4">Subjects in this Section</h3>
                            <div class="bg-white p-4 rounded-lg shadow-sm">
                                @if($section->subjects->count() > 0)
                                    <ul class="list-disc list-inside text-gray-500">
                                        @foreach($section->subjects as $subject)
                                            <li>{{ $subject->subject_name }}</li>
                                        @endforeach
                                    </ul>
                                @else
                                    <span class="text-gray-500">No subjects assigned to this section</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
