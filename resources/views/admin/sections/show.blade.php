<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">

                    {{-- Header --}}
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-3">
                        <h2 class="text-2xl font-bold text-[#FF92C2]">Section Details</h2>
                        <div class="flex flex-wrap gap-2">
                            <button onclick="document.getElementById('importModal').classList.remove('hidden')"
                                    class="inline-flex items-center px-4 py-2 bg-white border border-[#FF92C2] text-[#FF92C2] rounded-md hover:bg-[#FFF0FA] transition text-sm font-medium shadow-sm">
                                <i class="fas fa-file-import mr-2"></i>Import Students
                            </button>
                            <a href="{{ route('admin.sections.edit', $section) }}"
                               class="inline-flex items-center px-4 py-2 bg-[#FF92C2] text-white rounded-md hover:bg-[#ff6fb5] transition text-sm font-medium shadow-sm">
                                <i class="fas fa-edit mr-2"></i>Edit Section
                            </a>
                        </div>
                    </div>

                    {{-- Flash: success --}}
                    @if (session('success'))
                        <div class="flex items-start gap-3 px-4 py-3 bg-green-50 border border-green-200 text-green-700 rounded-xl mb-5 text-sm">
                            <i class="fas fa-check-circle mt-0.5 text-green-500 flex-shrink-0"></i>
                            <div>
                                <span class="font-medium">{{ session('success') }}</span>
                                @if (session('import_errors') && count(session('import_errors')))
                                    <p class="mt-2 font-semibold text-green-800">Row details:</p>
                                    <ul class="list-disc list-inside mt-1 space-y-0.5 text-green-700">
                                        @foreach (session('import_errors') as $err)
                                            <li>{{ $err }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Flash: import_error (hard errors) --}}
                    @if (session('import_error'))
                        <div class="flex items-start gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl mb-5 text-sm">
                            <i class="fas fa-exclamation-triangle mt-0.5 text-red-500 flex-shrink-0"></i>
                            <span>{{ session('import_error') }}</span>
                        </div>
                    @endif

                    {{-- Validation errors (file type etc.) --}}
                    @if ($errors->any())
                        <div class="flex items-start gap-3 px-4 py-3 bg-red-50 border border-red-200 text-red-700 rounded-xl mb-5 text-sm">
                            <i class="fas fa-exclamation-triangle mt-0.5 text-red-500 flex-shrink-0"></i>
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Info Cards --}}
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

                    {{-- Instructors --}}
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

                    {{-- Students --}}
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
                                            <td colspan="3" class="px-6 py-4 text-sm text-center text-gray-500">No students enrolled yet</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Subjects --}}
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

    {{-- ═══════════════ IMPORT MODAL ═══════════════ --}}
    <div id="importModal"
         class="hidden fixed inset-0 z-50 flex items-center justify-center px-4"
         onclick="if(event.target===this) closeImportModal()">

        <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

        <div class="relative bg-[#FFF0FA] rounded-2xl shadow-2xl w-full max-w-lg p-6 z-10">

            {{-- Modal header --}}
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-full flex items-center justify-center shadow-sm">
                        <i class="fas fa-file-import text-white text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-800">Import Students</h3>
                        <p class="text-xs text-gray-500">
                            Section: <span class="font-medium text-[#FF92C2]">{{ $section->section_code }} — {{ $section->name }}</span>
                        </p>
                    </div>
                </div>
                <button onclick="closeImportModal()" class="text-gray-400 hover:text-gray-600 transition text-2xl leading-none">&times;</button>
            </div>

            <form action="{{ route('admin.sections.import.store', $section) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  id="importForm">
                @csrf

                {{-- Drop zone --}}
                <label for="import_file"
                       id="dropZone"
                       class="group flex flex-col items-center justify-center w-full h-36 border-2 border-dashed border-[#FFC8FB] rounded-xl bg-white/70 hover:bg-white hover:border-[#FF92C2] cursor-pointer transition-all duration-200 mb-1">

                    <div class="flex flex-col items-center pointer-events-none" id="dropContent">
                        <div class="w-10 h-10 bg-[#FFE6F0] rounded-full flex items-center justify-center mb-2 group-hover:bg-[#FF92C2]/20 transition-colors">
                            <i class="fas fa-cloud-upload-alt text-[#FF92C2] text-lg"></i>
                        </div>
                        <p class="text-sm font-medium text-gray-700">Click to browse or drag & drop</p>
                        <p class="text-xs text-gray-400 mt-0.5">.xlsx or .xls — max 5 MB</p>
                    </div>

                    <div class="hidden flex-col items-center pointer-events-none" id="fileSelectedContent">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mb-2">
                            <i class="fas fa-file-excel text-green-500 text-lg"></i>
                        </div>
                        <p class="text-sm font-semibold text-gray-800 max-w-xs truncate" id="fileName">—</p>
                        <p class="text-xs text-gray-400 mt-0.5">Click to change</p>
                    </div>

                    <input type="file" name="import_file" id="import_file" class="sr-only" accept=".xlsx,.xls">
                </label>

                @error('import_file')
                    <p class="text-xs text-red-500 mb-2 mt-1">{{ $message }}</p>
                @enderror

                {{-- Instructions --}}
                <div class="bg-white/60 rounded-xl p-3 my-4 text-xs text-gray-600 space-y-1.5 border border-[#FFC8FB]/40">
                    <p><i class="fas fa-info-circle text-[#FF92C2] mr-1"></i>Column A = <span class="font-semibold">student_number</span> (required, starts at row 9).</p>
                    <p><i class="fas fa-info-circle text-[#FF92C2] mr-1"></i>Column B = year_level (optional: 1, 2, 3, or 4).</p>
                    <p><i class="fas fa-info-circle text-[#FF92C2] mr-1"></i>Students already in this section are skipped.</p>
                    <p><i class="fas fa-info-circle text-[#FF92C2] mr-1"></i>
                        Not sure what student_numbers to use? Check
                        <a href="{{ route('admin.student.index') }}" target="_blank" class="text-[#FF92C2] underline font-medium">Students list</a>.
                    </p>
                </div>

                {{-- Buttons --}}
                <div class="flex flex-col sm:flex-row gap-2">
                    <button type="submit"
                            id="submitBtn"
                            class="flex-1 px-5 py-2.5 bg-gradient-to-r from-pink-500 to-pink-600 text-white rounded-lg hover:from-pink-600 hover:to-pink-700 transition flex items-center justify-center font-medium text-sm shadow-md">
                        <i class="fas fa-cloud-upload-alt mr-2"></i>Import Now
                    </button>
                    <a href="{{ route('admin.sections.import.template') }}"
                       class="flex-1 px-5 py-2.5 bg-white border border-[#FF92C2] text-[#FF92C2] rounded-lg hover:bg-[#FFF0FA] transition flex items-center justify-center font-medium text-sm shadow-sm">
                        <i class="fas fa-download mr-2"></i>Get Template
                    </a>
                    <button type="button"
                            onclick="closeImportModal()"
                            class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Re-open modal if Laravel redirected back with validation errors
        @if ($errors->has('import_file'))
            document.getElementById('importModal').classList.remove('hidden');
        @endif

        function closeImportModal() {
            document.getElementById('importModal').classList.add('hidden');
        }

        const input        = document.getElementById('import_file');
        const defaultView  = document.getElementById('dropContent');
        const selectedView = document.getElementById('fileSelectedContent');
        const fileNameEl   = document.getElementById('fileName');
        const dropZone     = document.getElementById('dropZone');
        const submitBtn    = document.getElementById('submitBtn');

        function showFile(file) {
            fileNameEl.textContent = file.name;
            defaultView.classList.add('hidden');
            defaultView.classList.remove('flex');
            selectedView.classList.remove('hidden');
            selectedView.classList.add('flex');
        }

        input.addEventListener('change', function () {
            if (this.files.length > 0) showFile(this.files[0]);
        });

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-[#FF92C2]', 'bg-white');
        });
        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-[#FF92C2]', 'bg-white');
        });
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-[#FF92C2]', 'bg-white');
            const file = e.dataTransfer.files[0];
            if (file) {
                const dt = new DataTransfer();
                dt.items.add(file);
                input.files = dt.files;
                showFile(file);
            }
        });

        document.getElementById('importForm').addEventListener('submit', function () {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>Importing...';
        });
    </script>

</x-app-layout>