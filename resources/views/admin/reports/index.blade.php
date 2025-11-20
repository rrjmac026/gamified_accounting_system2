@section('title', 'System Reports')

<x-app-layout>
    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Card -->
            <div class="bg-[#FFF0FA] overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300 rounded-lg sm:rounded-2xl mb-6">
                <div class="p-4 sm:p-6 text-gray-700">
                    <h2 class="text-xl sm:text-2xl font-bold text-[#FF92C2] mb-2">System Reports</h2>
                    <p class="text-sm text-gray-600">Export student grades and performance data</p>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Export Student Grades Card -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg sm:rounded-2xl mb-6">
                <div class="p-6 sm:p-8">
                    <div class="flex items-center mb-6">
                        <svg class="w-8 h-8 text-[#FF92C2] mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Export Student Grades</h3>
                            <p class="text-sm text-gray-600">Filter by instructor and section, then export to Excel or PDF</p>
                        </div>
                    </div>

                    <form id="exportForm" class="space-y-6">
                        @csrf
                        
                        <!-- Filters -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Instructor Filter -->
                            <div>
                                <label for="instructor_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Instructor (Optional)
                                    </span>
                                </label>
                                <select id="instructor_id" name="instructor_id" 
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#FF92C2] focus:ring focus:ring-[#FF92C2] focus:ring-opacity-50 transition">
                                    <option value="">All Instructors</option>
                                    @foreach($instructors as $instructor)
                                        <option value="{{ $instructor->id }}">{{ $instructor->user->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Section Filter -->
                            <div>
                                <label for="section_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        Section (Optional)
                                    </span>
                                </label>
                                <select id="section_id" name="section_id" 
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#FF92C2] focus:ring focus:ring-[#FF92C2] focus:ring-opacity-50 transition">
                                    <option value="">All Sections</option>
                                    @foreach($sections as $section)
                                        <option value="{{ $section->id }}">{{ $section->name }} - {{ $section->course->name ?? 'N/A' }}</option>
                                    @endforeach
                                </select>
                                <p class="mt-1 text-xs text-gray-500">Select an instructor to filter sections taught by them</p>
                            </div>
                        </div>

                        <!-- Export Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t border-gray-200">
                            <button type="button" onclick="exportGrades('excel')" 
                                    class="flex items-center justify-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md transition duration-300 transform hover:scale-105">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Export to Excel
                            </button>

                            <button type="button" onclick="exportGrades('pdf')" 
                                    class="flex items-center justify-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-md transition duration-300 transform hover:scale-105">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                Export to PDF
                            </button>

                            <button type="button" onclick="resetFilters()" 
                                    class="flex items-center justify-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg shadow-md transition duration-300">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Reset Filters
                            </button>
                        </div>
                    </form>

                    <!-- Info Box -->
                    <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    <strong>Tip:</strong> Leave filters empty to export all student grades across all instructors and sections. 
                                    Use filters to narrow down to specific instructors or sections.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Export Student Feedback Card -->
            <div class="bg-white overflow-hidden shadow-md rounded-lg sm:rounded-2xl mb-6">
                <div class="p-6 sm:p-8">
                    <div class="flex items-center mb-6">
                        <svg class="w-8 h-8 text-[#FF92C2] mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">Export Student Feedback</h3>
                            <p class="text-sm text-gray-600">Filter by task and instructor, then export feedback records to Excel or PDF</p>
                        </div>
                    </div>

                    <form id="feedbackExportForm" class="space-y-6">
                        @csrf
                        
                        <!-- Filters -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Task Filter -->
                            <div>
                                <label for="task_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                        Performance Task (Optional)
                                    </span>
                                </label>
                                <select id="task_id" name="task_id" 
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#FF92C2] focus:ring focus:ring-[#FF92C2] focus:ring-opacity-50 transition">
                                    <option value="">All Tasks</option>
                                    @foreach($tasks as $task)
                                        <option value="{{ $task->id }}">{{ $task->title }} - {{ $task->instructor->user->name ?? 'N/A' }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Instructor Filter -->
                            <div>
                                <label for="feedback_instructor_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Instructor (Optional)
                                    </span>
                                </label>
                                <select id="feedback_instructor_id" name="instructor_id" 
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-[#FF92C2] focus:ring focus:ring-[#FF92C2] focus:ring-opacity-50 transition">
                                    <option value="">All Instructors</option>
                                    @foreach($instructors as $instructor)
                                        <option value="{{ $instructor->id }}">{{ $instructor->user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Export Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 pt-4 border-t border-gray-200">
                            <button type="button" onclick="exportFeedback('excel')" 
                                    class="flex items-center justify-center px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md transition duration-300 transform hover:scale-105">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Export to Excel
                            </button>

                            <button type="button" onclick="exportFeedback('pdf')" 
                                    class="flex items-center justify-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-lg shadow-md transition duration-300 transform hover:scale-105">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                Export to PDF
                            </button>

                            <button type="button" onclick="resetFeedbackFilters()" 
                                    class="flex items-center justify-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg shadow-md transition duration-300">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Reset Filters
                            </button>
                        </div>
                    </form>

                    <!-- Info Box -->
                    <div class="mt-6 bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    <strong>Tip:</strong> Leave filters empty to export all feedback records. 
                                    Use filters to narrow down to specific tasks or instructors. Each record includes step-wise feedback, ratings, and student information.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Store all sections initially
        const allSections = @json($sections);

        // Fetch sections when instructor is selected
        document.getElementById('instructor_id').addEventListener('change', function() {
            const instructorId = this.value;
            const sectionSelect = document.getElementById('section_id');
            
            // Reset section dropdown
            sectionSelect.innerHTML = '<option value="">All Sections</option>';
            
            if (instructorId) {
                // Fetch sections for this instructor
                fetch(`/admin/reports/instructor/${instructorId}/sections`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length === 0) {
                            // If no sections found, show message
                            const option = document.createElement('option');
                            option.value = '';
                            option.textContent = 'No sections found for this instructor';
                            option.disabled = true;
                            sectionSelect.appendChild(option);
                        } else {
                            data.forEach(section => {
                                const option = document.createElement('option');
                                option.value = section.id;
                                option.textContent = section.name + (section.course ? ' - ' + section.course.name : '');
                                sectionSelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching sections:', error);
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = 'Error loading sections';
                        option.disabled = true;
                        sectionSelect.appendChild(option);
                    });
            } else {
                // If no instructor selected, show all sections
                allSections.forEach(section => {
                    const option = document.createElement('option');
                    option.value = section.id;
                    option.textContent = section.name + (section.course ? ' - ' + section.course.name : '');
                    sectionSelect.appendChild(option);
                });
            }
        });

        // Export function
        function exportGrades(format) {
            const instructorId = document.getElementById('instructor_id').value;
            const sectionId = document.getElementById('section_id').value;
            
            // Build URL with parameters
            let url = `/admin/reports/export-grades-${format}?`;
            const params = new URLSearchParams();
            
            if (instructorId) params.append('instructor_id', instructorId);
            if (sectionId) params.append('section_id', sectionId);
            
            url += params.toString();
            
            // Trigger download
            window.location.href = url;
        }

        // Reset filters
        function resetFilters() {
            document.getElementById('instructor_id').value = '';
            
            // Reset sections to show all
            const sectionSelect = document.getElementById('section_id');
            sectionSelect.innerHTML = '<option value="">All Sections</option>';
            
            allSections.forEach(section => {
                const option = document.createElement('option');
                option.value = section.id;
                option.textContent = section.name + (section.course ? ' - ' + section.course.name : '');
                sectionSelect.appendChild(option);
            });
        }

        // Export feedback function
        function exportFeedback(format) {
            const taskId = document.getElementById('task_id').value;
            const instructorId = document.getElementById('feedback_instructor_id').value;
            
            // Build URL with parameters
            let url = `/admin/reports/export-feedback-${format}?`;
            const params = new URLSearchParams();
            
            if (taskId) params.append('task_id', taskId);
            if (instructorId) params.append('instructor_id', instructorId);
            
            url += params.toString();
            
            // Trigger download
            window.location.href = url;
        }

        // Reset feedback filters
        function resetFeedbackFilters() {
            document.getElementById('task_id').value = '';
            document.getElementById('feedback_instructor_id').value = '';
        }
    </script>
    @endpush
</x-app-layout>