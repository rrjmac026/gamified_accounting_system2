<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            {{-- ✅ Import Success Banner --}}
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

            {{-- General flash messages --}}
            @if(session('success'))
                <div class="mb-4 px-4 py-3 bg-green-100 border border-green-200 text-green-700 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 px-4 py-3 bg-red-100 border border-red-200 text-red-700 rounded-xl">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-[#FFF0FA] overflow-hidden shadow-lg rounded-lg sm:rounded-2xl">
                <div class="p-4 sm:p-6 text-gray-700">

                    {{-- Header with action buttons --}}
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
                        <h2 class="text-xl sm:text-2xl font-bold text-[#FF92C2]">Section Details</h2>
                        <div class="flex flex-wrap gap-3">
                            {{-- Import Students Button --}}
                            <a href="{{ route('admin.sections.import.form', $section) }}"
                               class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-[#FF92C2] to-[#ff6fb5] hover:from-[#ff6fb5] hover:to-[#FF92C2] text-white rounded-lg font-medium shadow-sm hover:shadow-md transition-all duration-200">
                                <i class="fas fa-file-import mr-2"></i>
                                Import Students
                            </a>
                            <a href="{{ route('admin.sections.edit', $section) }}"
                               class="px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5] transition-colors">
                                Edit Section
                            </a>
                            <form action="{{ route('admin.sections.destroy', $section) }}" method="POST"
                                  onsubmit="return confirm('Are you sure you want to delete this section?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors">
                                    Delete Section
                                </button>
                            </form>
                            <a href="{{ route('admin.sections.index') }}"
                               class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                                Back to Sections
                            </a>
                        </div>
                    </div>

                    {{-- Section Info --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-6">
                        <div class="bg-white p-4 sm:p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-[#FF92C2] mb-4">Basic Information</h3>
                            <dl class="space-y-3">
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Section Code:</dt>
                                    <dd class="font-medium text-gray-900">{{ $section->section_code }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Name:</dt>
                                    <dd class="font-medium text-gray-900">{{ $section->name }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Course:</dt>
                                    <dd class="font-medium text-gray-900">{{ $section->course->course_name ?? 'N/A' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Capacity:</dt>
                                    <dd class="font-medium text-gray-900">{{ $section->capacity ?? 'Unlimited' }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Enrolled:</dt>
                                    <dd class="font-medium text-gray-900">{{ $section->students->count() }} student(s)</dd>
                                </div>
                                @if($section->capacity)
                                <div class="flex justify-between">
                                    <dt class="text-gray-500">Available Slots:</dt>
                                    <dd class="font-medium {{ ($section->capacity - $section->students->count()) > 0 ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $section->capacity - $section->students->count() }}
                                    </dd>
                                </div>
                                @endif
                            </dl>
                        </div>

                        @if($section->notes)
                        <div class="bg-white p-4 sm:p-6 rounded-lg shadow">
                            <h3 class="text-lg font-semibold text-[#FF92C2] mb-4">Notes</h3>
                            <p class="text-gray-700">{{ $section->notes }}</p>
                        </div>
                        @endif
                    </div>

                    {{-- Instructors --}}
                    @if($section->instructors->count() > 0)
                    <div class="bg-white p-4 sm:p-6 rounded-lg shadow mb-6">
                        <h3 class="text-lg font-semibold text-[#FF92C2] mb-4">Assigned Instructors</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($section->instructors as $instructor)
                                <div class="flex items-center p-3 bg-[#FFF0FA] rounded-lg border border-[#FFC8FB]/30">
                                    <div class="w-9 h-9 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-full flex items-center justify-center mr-3 text-white font-bold text-sm">
                                        {{ substr($instructor->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-800 text-sm">{{ $instructor->user->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $instructor->department ?? '' }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Students Table --}}
                    <div class="bg-white p-4 sm:p-6 rounded-lg shadow">
                        <div class="flex items-center justify-between mb-4 flex-wrap gap-3">
                            <h3 class="text-lg font-semibold text-[#FF92C2]">
                                Enrolled Students
                                <span class="ml-2 text-sm font-normal text-gray-500">({{ $section->students->count() }})</span>
                            </h3>
                            <a href="{{ route('admin.sections.import.form', $section) }}"
                               class="inline-flex items-center px-3 py-1.5 text-sm bg-gradient-to-r from-[#FF92C2] to-[#ff6fb5] text-white rounded-lg hover:from-[#ff6fb5] hover:to-[#FF92C2] transition-all duration-200 font-medium">
                                <i class="fas fa-file-import mr-1.5"></i>
                                Import Students
                            </a>
                        </div>

                        @if($section->students->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-[#FFC8FB]">
                                    <thead class="bg-[#FFC8FB]/50">
                                        <tr>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">#</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Name</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Student Number</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Year Level</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-[#FFC8FB]/30">
                                        @foreach($section->students as $i => $student)
                                            <tr class="hover:bg-[#FFF6FD] transition-colors duration-150">
                                                <td class="px-4 py-3 text-sm text-gray-500">{{ $i + 1 }}</td>
                                                <td class="px-4 py-3">
                                                    <div class="flex items-center">
                                                        <div class="w-8 h-8 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-full flex items-center justify-center mr-3 text-white font-bold text-xs flex-shrink-0">
                                                            {{ substr($student->user->name, 0, 1) }}
                                                        </div>
                                                        <span class="text-sm font-medium text-gray-900">{{ $student->user->name }}</span>
                                                    </div>
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-600 font-mono">
                                                    {{ $student->student_number ?? '—' }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-600">
                                                    {{ $student->user->email }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-600">
                                                    {{ $student->year_level ? 'Year ' . $student->year_level : '—' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-user-slash text-2xl text-gray-400"></i>
                                </div>
                                <p class="text-gray-500 font-medium mb-1">No students enrolled yet</p>
                                <p class="text-sm text-gray-400 mb-4">Import students to get started</p>
                                <a href="{{ route('admin.sections.import.form', $section) }}"
                                   class="inline-flex items-center px-5 py-2.5 bg-gradient-to-r from-[#FF92C2] to-[#ff6fb5] text-white rounded-xl font-semibold shadow-md hover:shadow-lg transition-all duration-200">
                                    <i class="fas fa-file-import mr-2"></i>
                                    Import Students Now
                                </a>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>