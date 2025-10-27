<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-lg rounded-lg sm:rounded-2xl">
                <div class="p-4 sm:p-6 text-gray-700">
                    @if (session('error'))
                        <div class="mb-4 px-4 py-2 bg-red-100 border border-red-200 text-red-700 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-4 sm:mb-6 gap-4">
                        <h2 class="text-xl sm:text-2xl font-bold text-[#FF92C2]">Course Details</h2>
                        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                            <a href="{{ route('admin.courses.edit', $course) }}" 
                               class="px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5]">
                                Edit Course
                            </a>
                            <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this course?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                                    Delete Course
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                        <div class="bg-white p-4 sm:p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-[#FF92C2] mb-4">Basic Information</h3>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Course Code:</dt>
                                    <dd class="text-gray-900">{{ $course->course_code }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Course Name:</dt>
                                    <dd class="text-gray-900">{{ $course->course_name }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Department:</dt>
                                    <dd class="text-gray-900">{{ $course->department }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Duration:</dt>
                                    <dd class="text-gray-900">{{ $course->duration_years }} years</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Status:</dt>
                                    <dd>
                                        @if($course->is_active)
                                            <span class="text-green-600 font-medium">Active</span>
                                        @else
                                            <span class="text-red-600 font-medium">Inactive</span>
                                        @endif
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <div class="bg-white p-4 sm:p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-[#FF92C2] mb-4">Description</h3>
                            <p class="text-gray-700">{{ $course->description ?? 'No description available.' }}</p>
                        </div>

                        <div class="bg-white p-4 sm:p-6 rounded-lg shadow md:col-span-2">
                            <h3 class="text-lg font-semibold text-[#FF92C2] mb-4">Students Enrolled</h3>
                            @if($course->students->count() > 0)
                                <div class="overflow-x-auto">
                                    <table class="min-w-full table-auto">
                                        <thead class="bg-[#FFC8FB]">
                                            <tr>
                                                <th class="py-2 px-4 text-left">Name</th>
                                                <th class="py-2 px-4 text-left">Year Level</th>
                                                <th class="py-2 px-4 text-left">Section</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-[#FFC8FB]">
                                            @foreach($course->students as $student)
                                                <tr class="hover:bg-[#FFD9FF]">
                                                    <td class="py-2 px-4">{{ $student->name }}</td>
                                                    <td class="py-2 px-4">{{ $student->year_level }}</td>
                                                    <td class="py-2 px-4">{{ $student->section }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <p class="text-gray-500">No students enrolled in this course yet.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
