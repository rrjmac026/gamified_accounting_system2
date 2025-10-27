<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-lg rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-[#FF92C2]">Subject Details</h2>
                        <div class="space-x-4">
                            <a href="{{ route('admin.subjects.showAssignInstructorsForm', $subject) }}" 
                               class="inline-flex items-center px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5]">
                                <i class="fas fa-user-plus mr-2"></i>Manage Instructors
                            </a>
                            <a href="{{ route('admin.subjects.edit', $subject) }}" 
                               class="inline-flex items-center px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5]">
                                <i class="fas fa-edit mr-2"></i>Edit Subject
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Basic Information --}}
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-[#FF92C2] mb-4">Subject Information</h3>
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Subject Code</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $subject->subject_code }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Subject Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $subject->subject_name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Description</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $subject->description }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Units</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $subject->units }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Semester</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $subject->semester }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Academic Year</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $subject->academic_year }}</dd>
                                </div>
                            </dl>
                        </div>

                        {{-- All Instructors in System --}}
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-[#FF92C2] mb-4">Assigned Instructors</h3>

                            @if($subject->instructors->isEmpty())
                                <p class="text-gray-500">No instructors assigned to this subject yet.</p>
                            @else
                                <ul class="space-y-3">
                                    @foreach($subject->instructors as $instructor)
                                        <li class="flex justify-between items-center p-3 bg-gray-50 rounded-lg">
                                            <div>
                                                <p class="font-medium text-gray-800">{{ $instructor->user->name }}</p>
                                                <p class="text-sm text-gray-500">{{ $instructor->department }}</p>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>

                        {{-- Students Section --}}
                        <div class="bg-white p-6 rounded-lg shadow md:col-span-2">
                            <h3 class="text-lg font-semibold text-[#FF92C2] mb-4">Enrolled Students</h3>
                            @if($subject->students->isEmpty())
                                <p class="text-gray-500">No students enrolled</p>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-[#FFC8FB]">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Number</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($subject->students as $student)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->user->name }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->student_number }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->course->course_name }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
