<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-xl sm:rounded-2xl">
                <div class="p-6 text-gray-700">

                    {{-- Breadcrumb --}}
                    <div class="mb-6">
                        <nav class="flex items-center space-x-2 text-sm">
                            <a href="{{ route('instructors.subjects.index') }}" class="text-[#FF92C2] hover:text-[#ff6fb5]">
                                My Subjects
                            </a>
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            <span class="text-gray-600">{{ $subject->subject_code }}</span>
                        </nav>
                    </div>

                    {{-- Subject Header --}}
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-[#FF92C2] mb-2">
                            {{ $subject->subject_code }} - {{ $subject->subject_name }}
                        </h2>
                        <p class="text-gray-600">{{ $subject->description }}</p>
                    </div>

                    {{-- Flash Messages --}}
                    @if(session('success'))
                        <div class="mb-6 flex items-start gap-3 bg-green-50 border border-green-200 text-green-800 rounded-xl px-5 py-4">
                            <i class="fas fa-check-circle mt-0.5 text-green-500"></i>
                            <span class="text-sm font-medium">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="mb-6 flex items-start gap-3 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-xl px-5 py-4">
                            <i class="fas fa-exclamation-triangle mt-0.5 text-yellow-500"></i>
                            <span class="text-sm font-medium">{{ session('warning') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-6 flex items-start gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-5 py-4">
                            <i class="fas fa-times-circle mt-0.5 text-red-500"></i>
                            <div class="text-sm font-medium">
                                @foreach($errors->all() as $error)
                                    <p>{{ $error }}</p>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- ═══════════════════════════════════════════ --}}
                    {{-- IMPORT STUDENTS SECTION                     --}}
                    {{-- ═══════════════════════════════════════════ --}}
                    <div class="bg-white rounded-xl shadow-sm border border-[#FFC8FB]/30 p-6 mb-6">
                        <div class="flex items-center justify-between mb-5">
                            <h3 class="text-lg font-semibold text-[#FF92C2] flex items-center">
                                <i class="fas fa-file-import mr-2"></i>
                                Import Students via CSV
                            </h3>

                            {{-- Download Template Button --}}
                            <a href="{{ route('instructors.subjects.import-template', $subject->id) }}"
                               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-[#FF92C2] border-2 border-[#FF92C2] rounded-xl hover:bg-[#FF92C2] hover:text-white transition-all duration-200">
                                <i class="fas fa-download"></i>
                                Download Template
                            </a>
                        </div>

                        <p class="text-sm text-gray-500 mb-5 leading-relaxed">
                            Download the CSV template, fill in the <strong>student_number</strong> column for each student
                            you want to enroll, then upload the file below. Students already enrolled will be skipped automatically.
                        </p>

                        <form action="{{ route('instructors.subjects.import-students', $subject->id) }}"
                              method="POST"
                              enctype="multipart/form-data">
                            @csrf

                            <div class="flex flex-col sm:flex-row gap-4 items-start sm:items-end">

                                {{-- File Input --}}
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        Select CSV File
                                        <span class="text-red-500">*</span>
                                    </label>

                                    <label class="relative flex items-center gap-3 w-full cursor-pointer group">
                                        {{-- Hidden native input --}}
                                        <input type="file"
                                               name="import_file"
                                               id="import_file"
                                               accept=".csv,.txt"
                                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                               onchange="updateFileName(this)">

                                        {{-- Custom styled area --}}
                                        <div class="flex items-center gap-3 w-full px-4 py-3 border-2 border-dashed border-[#FFC8FB] rounded-xl bg-[#FFF6FD] group-hover:border-[#FF92C2] group-hover:bg-[#FFF0FA] transition-all duration-200">
                                            <div class="flex-shrink-0 w-9 h-9 bg-[#FF92C2]/10 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-file-csv text-[#FF92C2]"></i>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <p id="file-name-display" class="text-sm text-gray-500 truncate">
                                                    No file chosen — click to browse
                                                </p>
                                                <p class="text-xs text-gray-400 mt-0.5">CSV or TXT · max 2 MB</p>
                                            </div>
                                            <span class="flex-shrink-0 text-xs font-semibold text-[#FF92C2] border border-[#FF92C2] rounded-lg px-3 py-1">
                                                Browse
                                            </span>
                                        </div>
                                    </label>

                                    @error('import_file')
                                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Submit Button --}}
                                <button type="submit"
                                        class="flex-shrink-0 inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-[#FF92C2] to-[#ff6fb5] hover:from-[#ff6fb5] hover:to-[#FF92C2] text-white rounded-xl font-semibold shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                                    <i class="fas fa-upload"></i>
                                    Import Students
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- ═══════════════════════════════════════════ --}}
                    {{-- STUDENTS TABLE                              --}}
                    {{-- ═══════════════════════════════════════════ --}}
                    <div class="bg-white rounded-xl shadow-sm border border-[#FFC8FB]/30 p-6">
                        <h3 class="text-lg font-semibold text-[#FF92C2] mb-4 flex items-center">
                            <i class="fas fa-users w-5 h-5 mr-2"></i>
                            Students ({{ $subject->students->count() }})
                        </h3>

                        @if($subject->students->isNotEmpty())
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-[#FFC8FB]/50">
                                    <thead class="bg-gradient-to-r from-[#FFC8FB]/80 to-[#FFC8FB]/60">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-[#595758]">Student No.</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-[#595758]">Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-[#595758]">Email</th>
                                            <th class="px-6 py-3 text-left text-xs font-semibold text-[#595758]">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-[#FFC8FB]/30">
                                        @foreach($subject->students as $student)
                                            <tr class="hover:bg-[#FFF6FD] transition-colors">
                                                <td class="px-6 py-4 text-sm text-gray-500 font-mono">
                                                    {{ $student->student_number }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                                    {{ $student->user->name }}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-600">
                                                    {{ $student->user->email }}
                                                </td>
                                                <td class="px-6 py-4">
                                                    <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800 font-medium">
                                                        Active
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fas fa-user-plus text-2xl text-gray-400"></i>
                                </div>
                                <p class="text-gray-500 font-medium">No students enrolled yet.</p>
                                <p class="text-gray-400 text-sm mt-1">Use the import tool above to add students.</p>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- File name display script --}}
    <script>
        function updateFileName(input) {
            const display = document.getElementById('file-name-display');
            if (input.files && input.files.length > 0) {
                display.textContent = input.files[0].name;
                display.classList.remove('text-gray-500');
                display.classList.add('text-gray-800', 'font-medium');
            } else {
                display.textContent = 'No file chosen — click to browse';
                display.classList.add('text-gray-500');
                display.classList.remove('text-gray-800', 'font-medium');
            }
        }
    </script>
</x-app-layout>