<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Header Card --}}
            <div class="bg-gradient-to-r from-white/90 to-[#FFF0FA]/90 backdrop-blur-sm rounded-2xl shadow-xl border border-[#FFC8FB]/30 p-8 mb-10 relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-[#FF92C2]/5 via-transparent to-[#FFC8FB]/10 pointer-events-none"></div>
                <div class="relative flex justify-between items-start flex-wrap gap-4">
                    <div>
                        <div class="flex items-center mb-4">
                            <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-full mr-4 shadow-md">
                                <i class="fas fa-chalkboard text-white"></i>
                            </div>
                            <div>
                                <h2 class="text-3xl font-bold text-gray-800">Section: {{ $section->name }}</h2>
                                <p class="text-sm text-gray-600">Code: {{ $section->section_code }}</p>
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <span class="px-4 py-2 text-sm font-medium bg-[#FF92C2]/10 text-[#FF92C2] rounded-xl border border-[#FF92C2]/30">
                                <i class="fas fa-graduation-cap mr-2"></i>{{ $section->course->course_name ?? 'No Course' }}
                            </span>
                            <span class="px-4 py-2 text-sm font-medium bg-[#FFC8FB]/20 text-[#595758] rounded-xl border border-[#FFC8FB]/30">
                                <i class="fas fa-users mr-2"></i>{{ $section->students->count() }} Students
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('instructors.sections.manage-students', $section->id) }}" 
                           class="inline-flex items-center justify-center px-6 py-3 bg-white hover:bg-gradient-to-r hover:from-[#FFC8FB]/10 hover:to-[#FF92C2]/10 text-[#FF92C2] border-2 border-[#FF92C2]/30 hover:border-[#FF92C2] rounded-xl font-semibold shadow-md hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-[#FF92C2]/30 transition-all duration-300 transform hover:scale-105">
                            <i class="fas fa-user-plus mr-2"></i>
                            Add Students
                        </a>
                        <a href="{{ route('instructors.sections.index') }}" 
                           class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-[#FF92C2] to-[#ff6fb5] text-white rounded-xl font-semibold shadow-lg hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-[#FF92C2]/30 transition-all duration-300 transform hover:-translate-y-1 hover:scale-105">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Sections
                        </a>
                    </div>
                </div>
            </div>

            {{-- Stats Section --}}
            <div class="bg-[#FFF0FA] overflow-hidden shadow-2xl sm:rounded-2xl border border-[#FFC8FB]/30 mb-10">
                <div class="p-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="group bg-white rounded-xl border border-[#FFC8FB]/40 shadow-md hover:shadow-xl hover:border-[#FF92C2]/50 transition-all duration-300 p-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 flex items-center justify-center bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-full text-white shadow-md mr-4">
                                <i class="fas fa-users text-lg"></i>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Total Students</p>
                                <p class="text-2xl font-bold text-[#FF92C2]">{{ $section->students->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="group bg-white rounded-xl border border-[#FFC8FB]/40 shadow-md hover:shadow-xl hover:border-[#FF92C2]/50 transition-all duration-300 p-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 flex items-center justify-center bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-full text-white shadow-md mr-4">
                                <i class="fas fa-chalkboard-teacher text-lg"></i>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Assigned Instructors</p>
                                <p class="text-2xl font-bold text-[#FF92C2]">{{ $section->instructors->count() }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="group bg-white rounded-xl border border-[#FFC8FB]/40 shadow-md hover:shadow-xl hover:border-[#FF92C2]/50 transition-all duration-300 p-6">
                        <div class="flex items-center">
                            <div class="w-12 h-12 flex items-center justify-center bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-full text-white shadow-md mr-4">
                                <i class="fas fa-book text-lg"></i>
                            </div>
                            <div>
                                <p class="text-gray-600 text-sm font-medium">Subjects</p>
                                <p class="text-2xl font-bold text-[#FF92C2]">
                                    {{ $section->subjects->count() ?? 0 }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Students Table --}}
            <div class="bg-[#FFF0FA] overflow-hidden shadow-2xl sm:rounded-2xl border border-[#FFC8FB]/30">
                <div class="p-8 text-gray-700">
                    <div class="flex items-center justify-between mb-6 flex-wrap gap-4">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-full flex items-center justify-center text-white mr-4 shadow-md">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-[#595758]">Enrolled Students</h3>
                        </div>
                        
                        <a href="{{ route('instructors.sections.manage-students', $section->id) }}" 
                           class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-[#FF92C2] to-[#ff6fb5] hover:from-[#ff6fb5] hover:to-[#FF92C2] text-white rounded-xl transition-all duration-300 font-semibold shadow-md hover:shadow-lg transform hover:scale-105">
                            <i class="fas fa-user-plus mr-2"></i>
                            Manage Students
                        </a>
                    </div>

                    <div class="bg-white rounded-xl shadow-md border border-[#FFC8FB]/30 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-[#FFC8FB]/50">
                                <thead class="bg-gradient-to-r from-[#FFC8FB]/80 to-[#FFC8FB]/60">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#595758] uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#595758] uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-semibold text-[#595758] uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#FFC8FB]/30">
                                    @forelse($section->students as $student)
                                        <tr class="hover:bg-[#FFF6FD] transition-colors duration-200">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                                {{ $student->user->name }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                {{ $student->user->email }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="px-3 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                                    Active
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-8 text-center text-gray-500">
                                                <div class="flex flex-col items-center">
                                                    <div class="w-16 h-16 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mb-4">
                                                        <i class="fas fa-user-slash text-2xl text-gray-400"></i>
                                                    </div>
                                                    <p class="text-gray-600 mb-4">No students enrolled in this section yet.</p>
                                                    <a href="{{ route('instructors.sections.manage-students', $section->id) }}" 
                                                       class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-[#FF92C2] to-[#ff6fb5] text-white rounded-xl transition-all duration-300 font-semibold shadow-md hover:shadow-lg transform hover:scale-105">
                                                        <i class="fas fa-user-plus mr-2"></i>
                                                        Add Students Now
                                                    </a>
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

        </div>
    </div>
</x-app-layout>