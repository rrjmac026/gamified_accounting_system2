<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#FFF0FA] overflow-hidden shadow-lg rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-[#FF92C2]">Subject Details</h2>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('admin.subjects.showAssignInstructorsForm', $subject) }}" 
                               class="inline-flex items-center px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5]">
                                <i class="fas fa-user-plus mr-2"></i>Manage Instructors
                            </a>
                            <a href="{{ route('admin.subjects.edit', $subject) }}" 
                               class="inline-flex items-center px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5]">
                                <i class="fas fa-edit mr-2"></i>Edit Subject
                            </a>
                            <button type="button" onclick="document.getElementById('importStudentsModal').classList.remove('hidden')"
                               class="inline-flex items-center px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5]">
                                <i class="fas fa-user-graduate mr-2"></i>Add Students
                            </button>
                        </div>
                    </div>

                    {{-- Success/Error Messages --}}
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-300 text-green-700 rounded-lg">
                            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-700 rounded-lg">
                            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                        </div>
                    @endif

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

                        {{-- Assigned Instructors --}}
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
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-semibold text-[#FF92C2]">Enrolled Students</h3>
                                <span class="text-sm text-gray-500">
                                    {{ $subject->students->count() }} student(s) enrolled
                                </span>
                            </div>

                            @if($subject->students->isEmpty())
                                <div class="text-center py-10">
                                    <i class="fas fa-user-graduate text-4xl text-[#FFC8FB] mb-3"></i>
                                    <p class="text-gray-500 mb-4">No students enrolled in this subject yet.</p>
                                    <button type="button" onclick="document.getElementById('importStudentsModal').classList.remove('hidden')"
                                        class="inline-flex items-center px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5]">
                                        <i class="fas fa-file-import mr-2"></i>Import Students
                                    </button>
                                </div>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-[#FFC8FB]">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student Number</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Course</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Year Level</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($subject->students as $student)
                                                <tr class="hover:bg-[#FFF0FA] transition-colors">
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $student->user->name }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->student_number }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->course->course_name ?? 'N/A' }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $student->year_level ?? 'N/A' }}</td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @php $status = $student->pivot->status ?? 'enrolled'; @endphp
                                                        <span class="px-2 py-1 text-xs rounded-full 
                                                            {{ $status === 'enrolled' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                                            {{ ucfirst($status) }}
                                                        </span>
                                                    </td>
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

    {{-- ============================================================
         IMPORT STUDENTS MODAL
    ============================================================ --}}
    <div id="importStudentsModal"
         class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 px-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg">

            {{-- Modal Header --}}
            <div class="flex justify-between items-center px-6 py-4 border-b border-gray-200 bg-[#FFF0FA] rounded-t-xl">
                <h3 class="text-lg font-bold text-[#FF92C2]">
                    <i class="fas fa-file-import mr-2"></i>Add Students to Subject
                </h3>
                <button type="button" onclick="document.getElementById('importStudentsModal').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            {{-- Modal Body --}}
            <div class="px-6 py-5 space-y-5">

                {{-- Download Template --}}
                <div class="bg-[#FFF0FA] border border-[#FFC8FB] rounded-lg p-4">
                    <p class="text-sm font-semibold text-gray-700 mb-1">
                        <i class="fas fa-download text-[#FF92C2] mr-1"></i> Step 1: Download the CSV Template
                    </p>
                    <p class="text-xs text-gray-500 mb-3">
                        Use this template to ensure your data is in the correct format.
                        Fill in Column A with student numbers, then upload the file as-is (.xlsx) or save as CSV first.
                    </p>
                    <a href="{{ asset('templates/students_import_template.xlsx') }}"
                       class="inline-flex items-center px-4 py-2 bg-white border border-[#FF92C2] text-[#FF92C2] text-sm rounded-lg hover:bg-[#FF92C2] hover:text-white transition-colors">
                        <i class="fas fa-file-excel mr-2"></i>Download Template (.xlsx)
                    </a>
                </div>

                {{-- Upload Form --}}
                <div>
                    <p class="text-sm font-semibold text-gray-700 mb-2">
                        <i class="fas fa-upload text-[#FF92C2] mr-1"></i> Step 2: Upload Completed File
                    </p>
                    <form id="importForm"
                          action="{{ route('admin.subjects.import-students', $subject) }}"
                          method="POST"
                          enctype="multipart/form-data">
                        @csrf

                        <label for="student_file"
                               class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-[#FFC8FB] rounded-lg cursor-pointer bg-[#FFF0FA] hover:bg-pink-50 transition-colors">
                            <div id="uploadLabel" class="flex flex-col items-center justify-center text-center px-4">
                                <i class="fas fa-cloud-upload-alt text-3xl text-[#FF92C2] mb-2"></i>
                                <p class="text-sm text-gray-500">Click to upload or drag & drop</p>
                                <p class="text-xs text-gray-400 mt-1">CSV or Excel (.xlsx) files only</p>
                            </div>
                            <input id="student_file" name="student_file" type="file" accept=".csv,.xlsx" class="hidden"
                                   onchange="handleFileSelect(this)" />
                        </label>

                        @if($errors->has('student_file'))
                            <p class="mt-1 text-xs text-red-500">{{ $errors->first('student_file') }}</p>
                        @endif

                        {{-- Preview area --}}
                        <div id="filePreview" class="hidden mt-3 p-3 bg-gray-50 rounded-lg flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-file-csv text-[#FF92C2] text-lg"></i>
                                <span id="fileName" class="text-sm text-gray-700 font-medium"></span>
                            </div>
                            <button type="button" onclick="clearFile()" class="text-gray-400 hover:text-red-500 transition-colors">
                                <i class="fas fa-times-circle"></i>
                            </button>
                        </div>

                        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <p class="text-xs text-yellow-700">
                                <i class="fas fa-info-circle mr-1"></i>
                                Students not found in the system will be skipped. Existing enrollments will not be duplicated.
                            </p>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Modal Footer --}}
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end gap-3 rounded-b-xl bg-gray-50">
                <button type="button"
                        onclick="document.getElementById('importStudentsModal').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 transition-colors">
                    Cancel
                </button>
                <button type="button" onclick="submitImport()"
                        class="inline-flex items-center px-4 py-2 text-sm bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5] transition-colors disabled:opacity-50">
                    <i class="fas fa-upload mr-2"></i>Import Students
                </button>
            </div>
        </div>
    </div>

    <script>
        function handleFileSelect(input) {
            const file = input.files[0];
            if (!file) return;

            document.getElementById('filePreview').classList.remove('hidden');
            document.getElementById('fileName').textContent = file.name;
            document.getElementById('uploadLabel').classList.add('hidden');
        }

        function clearFile() {
            document.getElementById('student_file').value = '';
            document.getElementById('filePreview').classList.add('hidden');
            document.getElementById('uploadLabel').classList.remove('hidden');
        }

        function submitImport() {
            const file = document.getElementById('student_file').files[0];
            if (!file) {
                alert('Please select a CSV file before importing.');
                return;
            }
            document.getElementById('importForm').submit();
        }

        // Close modal on backdrop click
        document.getElementById('importStudentsModal').addEventListener('click', function(e) {
            if (e.target === this) this.classList.add('hidden');
        });

        // Re-open modal if there are validation errors on the file field
        @if($errors->has('student_file') || session('import_error'))
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('importStudentsModal').classList.remove('hidden');
            });
        @endif
    </script>
</x-app-layout>