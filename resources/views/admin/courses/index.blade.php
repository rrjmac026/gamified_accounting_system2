@section('title', 'Course Page')
<x-app-layout>
    {{-- Add Course Header Section --}}
    <div class="flex flex-col sm:flex-row justify-between items-center px-4 sm:px-8 mt-4 gap-4">
        <h2 class="text-lg sm:text-xl font-semibold text-[#FF92C2]">Course Management</h2>
        <div class="w-full sm:w-auto">
            <a href="{{ route('admin.courses.create') }}" 
               class="w-full sm:w-auto px-4 sm:px-6 py-2 bg-gradient-to-r from-pink-500 to-pink-600 text-white rounded-lg hover:from-pink-600 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center shadow-md hover:shadow-lg">
                <i class="fas fa-plus mr-2"></i>
                Add Course
            </a>
        </div>
    </div>

    {{-- Enhanced Search Form --}}
    <div class="px-4 sm:px-8 mt-8">
        <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-[#FFC8FB]/30 p-6">
            <div class="flex items-center mb-4">
                <div class="w-8 h-8 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-full flex items-center justify-center mr-3">
                    <i class="fas fa-search text-white text-sm"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-800">Search Courses</h3>
            </div>
            
            <div class="space-y-4">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                           id="course-search"
                           placeholder="Search by course code, name, or department..." 
                           class="w-full pl-11 pr-4 py-3 border border-[#FFC8FB]/50 rounded-xl bg-white/70 focus:bg-white focus:border-[#FF92C2] focus:ring-2 focus:ring-[#FF92C2]/20 focus:outline-none transition-all duration-200 text-gray-700 placeholder-gray-400">
                </div>
                <div class="flex justify-end">
                    <span class="text-xs text-gray-500" id="course-counter">
                        Showing {{ $courses->count() }} courses
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Section --}}
    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 sm:rounded-lg">
                <div class="p-4 sm:p-6 text-gray-700">
                    @if (session('success'))
                        <div class="mb-4 px-4 py-2 bg-green-100 border border-green-200 text-green-700 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Responsive Table --}}
                    <div class="relative">
                        <div class="overflow-x-auto">
                            <div class="inline-block min-w-full align-middle">
                                <div class="overflow-hidden shadow-md rounded-lg">
                                    <table class="min-w-full divide-y divide-[#FFC8FB]">
                                        <thead class="bg-[#FFC8FB] text-xs uppercase">
                                            <tr>
                                                <th scope="col" class="py-3 px-4">
                                                    Course Code
                                                </th>
                                                <th scope="col" class="py-3 px-4">
                                                    Course Name
                                                </th>
                                                <th scope="col" class="hidden md:table-cell py-3 px-4">
                                                    Department
                                                </th>
                                                <th scope="col" class="hidden lg:table-cell py-3 px-4">
                                                    Duration
                                                </th>
                                                <th scope="col" class="py-3 px-4">
                                                    Status
                                                </th>
                                                <th scope="col" class="py-3 px-4">
                                                    Actions
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody id="course-table-body">
                                            @forelse ($courses as $course)
                                                <tr class="bg-white border-b border-[#FFC8FB] hover:bg-[#FFD9FF] transition-colors duration-150">
                                                    <td class="py-3 px-4 font-medium">
                                                        {{ $course->course_code }}
                                                    </td>
                                                    <td class="py-3 px-4">
                                                        {{ $course->course_name }}
                                                    </td>
                                                    <td class="hidden md:table-cell py-3 px-4">
                                                        <span class="text-sm">{{ $course->department }}</span>
                                                    </td>
                                                    <td class="hidden lg:table-cell py-3 px-4">
                                                        <div class="flex items-center gap-2">
                                                            <i class="fas fa-clock text-xs text-gray-400"></i>
                                                            <span class="text-sm">{{ $course->duration_years }} years</span>
                                                        </div>
                                                    </td>
                                                    <td class="py-3 px-4">
                                                        @if($course->is_active)
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                <i class="fas fa-check-circle mr-1"></i>
                                                                Active
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                                <i class="fas fa-times-circle mr-1"></i>
                                                                Inactive
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="py-3 px-4">
                                                        <div class="flex flex-col sm:flex-row gap-2">
                                                            <a href="{{ route('admin.courses.show', $course) }}" 
                                                               class="text-[#FF92C2] hover:text-[#ff6fb5]">
                                                                <i class="fas fa-eye"></i>
                                                                <span class="ml-2 sm:hidden">View</span>
                                                            </a>
                                                            <a href="{{ route('admin.courses.edit', $course) }}" 
                                                               class="text-[#FF92C2] hover:text-[#ff6fb5]">
                                                                <i class="fas fa-edit"></i>
                                                                <span class="ml-2 sm:hidden">Edit</span>
                                                            </a>
                                                            <form action="{{ route('admin.courses.destroy', $course) }}" 
                                                                  method="POST" 
                                                                  class="inline-flex" 
                                                                  onsubmit="return confirm('Are you sure you want to delete this course?');">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="inline-flex items-center text-red-600 hover:text-red-900">
                                                                    <i class="fas fa-trash"></i>
                                                                    <span class="ml-2 sm:hidden">Delete</span>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" class="py-8 px-4 text-center text-gray-500">
                                                        <div class="flex flex-col items-center">
                                                            <i class="fas fa-graduation-cap text-4xl mb-4"></i>
                                                            <p class="text-lg font-medium text-gray-900 mb-1">No courses found</p>
                                                            <p class="text-gray-600">Add a new course to get started</p>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Pagination --}}
                    @if($courses->hasPages())
                        <div class="mt-4 sm:mt-6">
                            {{ $courses->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Client-side Search Script --}}
    <script>
        const courseSearch = document.getElementById("course-search");
        const courseTableBody = document.getElementById("course-table-body");
        const courseRows = courseTableBody.getElementsByTagName("tr");
        const courseCounter = document.getElementById("course-counter");

        courseSearch.addEventListener("keyup", function() {
            let searchValue = this.value.toLowerCase();
            let visibleCount = 0;

            for (let i = 0; i < courseRows.length; i++) {
                let rowText = courseRows[i].textContent.toLowerCase();
                if (rowText.includes(searchValue)) {
                    courseRows[i].style.display = "";
                    visibleCount++;
                } else {
                    courseRows[i].style.display = "none";
                }
            }
            courseCounter.textContent = `Showing ${visibleCount} course${visibleCount !== 1 ? 's' : ''}`;
        });
    </script>
</x-app-layout>