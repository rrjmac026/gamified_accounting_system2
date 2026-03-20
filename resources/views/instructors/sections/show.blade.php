<x-app-layout>
    <div class="py-12">
        @if(session('import_success'))
            @php $result = session('import_success'); @endphp
            <div class="mb-6 bg-green-50 border-2 border-green-200 rounded-xl p-5 shadow-sm" id="importSuccessBanner">
                <div class="flex items-start justify-between">
                    <div class="flex items-start">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-check-circle text-green-600 text-lg"></i>
                        </div>
                        <div>
                            <p class="font-bold text-green-800 text-base">Import Completed!</p>
                            <div class="flex flex-wrap gap-3 mt-2">
                                <span class="inline-flex items-center gap-1.5 bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-semibold border border-green-200">
                                    <i class="fas fa-user-plus text-xs"></i>
                                    {{ $result['imported'] }} student(s) added
                                </span>
                                @if($result['skipped'] > 0)
                                <span class="inline-flex items-center gap-1.5 bg-yellow-100 text-yellow-700 px-3 py-1 rounded-full text-sm font-semibold border border-yellow-200">
                                    <i class="fas fa-forward text-xs"></i>
                                    {{ $result['skipped'] }} skipped
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <button onclick="document.getElementById('importSuccessBanner').remove()"
                            class="text-green-400 hover:text-green-600 transition-colors ml-2">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>

            @if(session('import_errors') && count(session('import_errors')))
            <div class="mb-6 bg-yellow-50 border-2 border-yellow-200 rounded-xl p-5 shadow-sm" id="importErrorBanner">
                <div class="flex items-start justify-between">
                    <div class="flex items-start">
                        <div class="w-9 h-9 bg-yellow-100 rounded-full flex items-center justify-center mr-3 flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-yellow-600"></i>
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold text-yellow-800 mb-2">Some rows were skipped:</p>
                            <ul class="space-y-1 max-h-40 overflow-y-auto pr-1">
                                @foreach(session('import_errors') as $err)
                                    <li class="flex items-start text-sm text-yellow-700">
                                        <i class="fas fa-circle mt-1.5 mr-2 text-yellow-400 text-xs flex-shrink-0"></i>
                                        {{ $err }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button onclick="document.getElementById('importErrorBanner').remove()"
                            class="text-yellow-400 hover:text-yellow-600 transition-colors ml-2">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            @endif

            <script>
                const b = document.getElementById('importSuccessBanner');
                if (b) setTimeout(() => { b.style.transition = 'opacity 0.5s'; b.style.opacity = '0'; setTimeout(() => b.remove(), 500); }, 6000);
            </script>
        @endif
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