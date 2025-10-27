@section('title', 'Student Management')

<x-app-layout>
    {{-- Header Section with Add Button --}}
    <div class="flex flex-col sm:flex-row justify-between items-center px-4 sm:px-8 mt-4 gap-4">
        <h2 class="text-lg sm:text-xl font-semibold text-[#FF92C2]">Student Management</h2>
        <div class="w-full sm:w-auto flex gap-2">
            <a href="{{ route('admin.student.create') }}" 
               class="flex-1 sm:flex-initial px-4 sm:px-6 py-2 bg-gradient-to-r from-pink-500 to-pink-600 text-white rounded-lg hover:from-pink-600 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center shadow-md hover:shadow-lg">
                <i class="fas fa-plus mr-2"></i>
                Add Student
            </a>
            <button onclick="switchTab('import')"
                    class="flex-1 sm:flex-initial px-4 sm:px-6 py-2 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg hover:from-emerald-600 hover:to-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all duration-200 flex items-center justify-center shadow-md hover:shadow-lg">
                <i class="fas fa-file-import mr-2"></i>
                Import
            </button>
        </div>
    </div>

    {{-- Success/Error Messages --}}
    <div class="px-4 sm:px-8 mt-4">
        @if (session('success'))
            <div class="mb-4 px-4 py-3 bg-green-100 border border-green-200 text-green-700 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 px-4 py-3 bg-red-100 border border-red-200 text-red-700 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <strong>Import Failed:</strong><br>
                        {{ session('error') }}
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-100 border border-red-200 text-red-700 rounded-lg">
                <div class="flex items-start">
                    <svg class="w-5 h-5 mr-2 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <strong>Validation Errors:</strong>
                        <ul class="mt-1 ml-4 list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {{-- Tab Content --}}
    <div id="tab-content">
        {{-- Search Tab Content --}}
        <div id="search-content" class="tab-content active">
            {{-- Enhanced Search Form --}}
            <div class="px-4 sm:px-8 mt-8">
                <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-[#FFC8FB]/30 p-6">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-search text-white text-sm"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800">Search Students</h3>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" 
                                   id="student-search"
                                   placeholder="Search by name, email, ID, course, or section..." 
                                   class="w-full pl-11 pr-4 py-3 border border-[#FFC8FB]/50 rounded-xl bg-white/70 focus:bg-white focus:border-[#FF92C2] focus:ring-2 focus:ring-[#FF92C2]/20 focus:outline-none transition-all duration-200 text-gray-700 placeholder-gray-400">
                        </div>
                        <div class="flex justify-end">
                            <span class="text-xs text-gray-500" id="student-counter">
                                Showing {{ $students->count() }} student(s)
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
                            {{-- Responsive Table --}}
                            <div class="relative">
                                <div class="overflow-x-auto">
                                    <div class="inline-block min-w-full align-middle">
                                        <div class="overflow-hidden shadow-md rounded-lg">
                                            <table class="min-w-full divide-y divide-[#FFC8FB]">
                                                <thead class="bg-[#FFC8FB] text-xs uppercase">
                                                    <tr>
                                                        <th scope="col" class="py-3 px-4">
                                                            Name/Email
                                                        </th>
                                                        <th scope="col" class="hidden md:table-cell py-3 px-4">
                                                            Student ID
                                                        </th>
                                                        <th scope="col" class="hidden sm:table-cell py-3 px-4">
                                                            Course
                                                        </th>
                                                        <th scope="col" class="hidden lg:table-cell py-3 px-4">
                                                            Year/Section
                                                        </th>
                                                        <th scope="col" class="py-3 px-4">
                                                            Status
                                                        </th>
                                                        <th scope="col" class="py-3 px-4">
                                                            Actions
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody id="student-table-body">
                                                    @forelse ($students as $student)
                                                        <tr class="bg-white border-b border-[#FFC8FB] hover:bg-[#FFD9FF] transition-colors duration-150">
                                                            <td class="py-3 px-4 font-medium">
                                                                {{ $student->user->name ?? 'N/A' }}
                                                                <div class="text-xs text-gray-500 mt-1">
                                                                    {{ $student->user->email ?? 'N/A' }}
                                                                </div>
                                                            </td>
                                                            <td class="hidden md:table-cell py-3 px-4">
                                                                {{ $student->user->student_number ?? $student->student_number }}
                                                            </td>
                                                            <td class="hidden sm:table-cell py-3 px-4">
                                                                {{ $student->course->course_name ?? '-' }}
                                                            </td>
                                                            <td class="hidden lg:table-cell py-3 px-4">
                                                                <span class="block font-medium">Year {{ $student->year_level ?? '-' }}</span>
                                                                <span class="text-sm text-gray-500">
                                                                    @if ($student->sections->isNotEmpty())
                                                                        {{ $student->sections->pluck('name')->join(', ') }}
                                                                    @else
                                                                        N/A
                                                                    @endif
                                                                </span>
                                                            </td>
                                                            <td class="py-3 px-4">
                                                                @if($student->user && $student->user->is_active)
                                                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-green-100 text-green-800">
                                                                        Active
                                                                    </span>
                                                                @else
                                                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-red-100 text-red-800">
                                                                        Inactive
                                                                    </span>
                                                                @endif
                                                            </td>
                                                            <td class="py-3 px-4">
                                                                <div class="flex flex-col sm:flex-row gap-2">
                                                                    <a href="{{ route('admin.student.show', $student->id) }}" 
                                                                       class="text-[#FF92C2] hover:text-[#ff6fb5]">
                                                                        <i class="fas fa-eye"></i>
                                                                        <span class="ml-2 sm:hidden">View</span>
                                                                    </a>
                                                                    <a href="{{ route('admin.student.edit', $student->id) }}" 
                                                                       class="text-[#FF92C2] hover:text-[#ff6fb5]">
                                                                        <i class="fas fa-edit"></i>
                                                                        <span class="ml-2 sm:hidden">Edit</span>
                                                                    </a>
                                                                    <button type="button" 
                                                                            onclick="return confirmAction('Are you sure you want to delete this student?', 'delete-student-{{ $student->id }}')"
                                                                            class="text-red-600 hover:text-red-900 inline-flex items-center">
                                                                        <i class="fas fa-trash"></i>
                                                                        <span class="ml-2 sm:hidden">Delete</span>
                                                                    </button>
                                                                    <form id="delete-student-{{ $student->id }}" 
                                                                          action="{{ route('admin.student.destroy', $student->id) }}" 
                                                                          method="POST" 
                                                                          class="hidden">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                    </form>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @empty
                                                        <tr>
                                                            <td colspan="6" class="py-8 px-4 text-center text-gray-500">
                                                                <div class="flex flex-col items-center">
                                                                    <i class="fas fa-user-graduate text-4xl mb-4"></i>
                                                                    <p class="text-lg font-medium text-gray-900 mb-1">No students found</p>
                                                                    <p class="text-gray-600">Add a new student to get started</p>
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
                            @if($students->hasPages())
                                <div class="mt-4 sm:mt-6">
                                    {{ $students->appends(request()->query())->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Import Tab Content --}}
        <div id="import-content" class="tab-content">
            <div class="px-4 sm:px-8 mt-8">
                <div class="max-w-4xl mx-auto">
                    <div class="bg-white/80 backdrop-blur-sm rounded-2xl shadow-lg border border-[#FFC8FB]/30 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center">
                                <div class="w-8 h-8 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-file-import text-white text-sm"></i>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-800">Import Students from File</h3>
                            </div>
                            <button type="button" 
                                    onclick="switchTab('search')"
                                    class="text-sm text-gray-500 hover:text-[#FF92C2] flex items-center">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Back to List
                            </button>
                        </div>

                        <div class="flex justify-end mb-4">
                            <button type="button" 
                                    onclick="toggleSchemaModal()"
                                    class="text-sm text-pink-600 hover:text-pink-800 underline flex items-center">
                                <i class="fas fa-info-circle mr-1"></i>
                                View Required Format
                            </button>
                        </div>

                        <form action="{{ route('admin.student.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="space-y-4">
                                <div>
                                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                                        Select CSV or Excel File
                                    </label>
                                    <div class="relative">
                                        <input type="file" 
                                               name="file" 
                                               id="file" 
                                               accept=".csv,.xlsx,.xls"
                                               class="w-full px-4 py-3 border-2 border-dashed border-[#FFC8FB] rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent bg-white/50 hover:bg-white transition-all duration-200"
                                               required>
                                    </div>
                                    <p class="mt-2 text-xs text-gray-500 italic">
                                        📌 Supported formats: CSV, XLSX, XLS
                                    </p>
                                </div>

                                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-blue-800 mb-2">💡 Quick Tips:</h4>
                                    <ul class="text-xs text-blue-700 list-disc list-inside space-y-1">
                                        <li>Make sure your file has the correct column headers</li>
                                        <li>All required fields must be filled for each student</li>
                                        <li>Email addresses must be unique</li>
                                        <li>Click "View Required Format" to see the schema</li>
                                    </ul>
                                </div>

                                <button type="submit" 
                                        class="w-full px-6 py-3 bg-gradient-to-r from-pink-500 to-pink-600 text-white rounded-lg hover:from-pink-600 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 transition-all duration-200 font-medium shadow-md hover:shadow-lg">
                                    <i class="fas fa-upload mr-2"></i>
                                    Import Students
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Schema Modal --}}
    <div id="schemaModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 hidden">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-2/3 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">CSV/Excel Import Schema</h3>
                    <button type="button" 
                            onclick="toggleSchemaModal()" 
                            class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-3">
                        Your CSV/Excel file must contain the following columns with exact header names (in this order):
                    </p>
                    
                    <div class="border border-pink-200 rounded-lg overflow-hidden">
                        <table class="w-full text-sm text-left border-collapse">
                            <thead class="bg-[#FFC8FB]/30 text-pink-700 uppercase">
                                <tr>
                                    <th class="px-4 py-2 border-r border-pink-200">Column Order</th>
                                    <th class="px-4 py-2 border-r border-pink-200">Column Name</th>
                                    <th class="px-4 py-2 border-r border-pink-200">Description</th>
                                    <th class="px-4 py-2">Example</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white">
                                <tr class="border-b border-pink-100">
                                    <td class="px-4 py-2 border-r border-pink-200 font-bold text-pink-600">1</td>
                                    <td class="px-4 py-2 border-r border-pink-200 font-mono text-pink-600">first_name</td>
                                    <td class="px-4 py-2 border-r border-pink-200">Student's first name</td>
                                    <td class="px-4 py-2">John</td>
                                </tr>
                                <tr class="border-b border-pink-100">
                                    <td class="px-4 py-2 border-r border-pink-200 font-bold text-pink-600">2</td>
                                    <td class="px-4 py-2 border-r border-pink-200 font-mono text-pink-600">middle_name</td>
                                    <td class="px-4 py-2 border-r border-pink-200">Student's middle name (optional)</td>
                                    <td class="px-4 py-2">Michael</td>
                                </tr>
                                <tr class="border-b border-pink-100">
                                    <td class="px-4 py-2 border-r border-pink-200 font-bold text-pink-600">3</td>
                                    <td class="px-4 py-2 border-r border-pink-200 font-mono text-pink-600">last_name</td>
                                    <td class="px-4 py-2 border-r border-pink-200">Student's last name</td>
                                    <td class="px-4 py-2">Doe</td>
                                </tr>
                                <tr class="border-b border-pink-100">
                                    <td class="px-4 py-2 border-r border-pink-200 font-bold text-pink-600">4</td>
                                    <td class="px-4 py-2 border-r border-pink-200 font-mono text-pink-600">email</td>
                                    <td class="px-4 py-2 border-r border-pink-200">Student email address (must be unique)</td>
                                    <td class="px-4 py-2">john.doe@example.com</td>
                                </tr>
                                <tr class="border-b border-pink-100">
                                    <td class="px-4 py-2 border-r border-pink-200 font-bold text-pink-600">5</td>
                                    <td class="px-4 py-2 border-r border-pink-200 font-mono text-pink-600">course</td>
                                    <td class="px-4 py-2 border-r border-pink-200">Course/Program name</td>
                                    <td class="px-4 py-2">Computer Science</td>
                                </tr>
                                <tr class="border-b border-pink-100">
                                    <td class="px-4 py-2 border-r border-pink-200 font-bold text-pink-600">6</td>
                                    <td class="px-4 py-2 border-r border-pink-200 font-mono text-pink-600">year_level</td>
                                    <td class="px-4 py-2 border-r border-pink-200">Year level (1, 2, 3, 4, or 5)</td>
                                    <td class="px-4 py-2">2</td>
                                </tr>
                                <tr class="border-b border-pink-100">
                                    <td class="px-4 py-2 border-r border-pink-200 font-bold text-pink-600">7</td>
                                    <td class="px-4 py-2 border-r border-pink-200 font-mono text-pink-600">section</td>
                                    <td class="px-4 py-2 border-r border-pink-200">Section identifier</td>
                                    <td class="px-4 py-2">A</td>
                                </tr>
                                <tr class="border-b border-pink-100">
                                    <td class="px-4 py-2 border-r border-pink-200 font-bold text-pink-600">8</td>
                                    <td class="px-4 py-2 border-r border-pink-200 font-mono text-pink-600">password</td>
                                    <td class="px-4 py-2 border-r border-pink-200">Default password (defaults to "password123" if empty)</td>
                                    <td class="px-4 py-2">password123</td>
                                </tr>
                                <tr>
                                    <td class="px-4 py-2 border-r border-pink-200 font-bold text-pink-600">9</td>
                                    <td class="px-4 py-2 border-r border-pink-200 font-mono text-pink-600">id_number</td>
                                    <td class="px-4 py-2 border-r border-pink-200">Student ID number (auto-generated if empty)</td>
                                    <td class="px-4 py-2">STU202400001</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <h4 class="text-sm font-medium text-blue-800 mb-2">💡 Sample CSV Format:</h4>
                    <div class="bg-white border rounded p-3 text-xs font-mono overflow-x-auto">
                        <div class="text-blue-600 mb-1">first_name,middle_name,last_name,email,course,year_level,section,password,id_number</div>
                        <div class="text-gray-600 mb-1">John,Michael,Doe,john.doe@example.com,Computer Science,2,A,password123,STU202400001</div>
                        <div class="text-gray-600 mb-1">Jane,,Smith,jane.smith@example.com,Information Technology,1,B,password123,STU202400002</div>
                        <div class="text-gray-600">Bob,Lee,Johnson,bob.j@example.com,Engineering,3,C,mypass456,STU202400003</div>
                    </div>
                </div>

                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h4 class="text-sm font-medium text-yellow-800 mb-2">⚠️ Important Notes:</h4>
                    <ul class="text-xs text-yellow-700 list-disc list-inside space-y-1">
                        <li><strong>Column order matters!</strong> Columns must be in the exact order shown above</li>
                        <li><strong>Required fields:</strong> first_name, last_name, email are mandatory</li>
                        <li><strong>Email must be unique</strong> - duplicate emails will be skipped</li>
                        <li><strong>Password defaults:</strong> If password column is empty, "password123" will be used</li>
                        <li><strong>ID number auto-generation:</strong> If id_number is empty, system generates one</li>
                        <li><strong>Year level:</strong> Should be a number between 1-5</li>
                        <li><strong>Supported formats:</strong> CSV, XLSX, XLS</li>
                    </ul>
                </div>
                
                <div class="flex justify-end mt-4">
                    <button type="button" 
                            onclick="toggleSchemaModal()"
                            class="px-6 py-2 bg-gradient-to-r from-pink-500 to-pink-600 text-white rounded-lg hover:from-pink-600 hover:to-pink-700 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 transition-all duration-200">
                        Got it!
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .tab-content {
            display: none;
            animation: fadeIn 0.3s ease-in;
        }
        
        .tab-content.active {
            display: block;
        }
        
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <script>
        // Tab Switching Function
        function switchTab(tabName) {
            // Remove active class from all contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Add active class to selected content
            document.getElementById(tabName + '-content').classList.add('active');
            
            // Scroll to top when switching tabs
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function toggleSchemaModal() {
            const modal = document.getElementById('schemaModal');
            modal.classList.toggle('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('schemaModal').addEventListener('click', function(e) {
            if (e.target === this) {
                toggleSchemaModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                const modal = document.getElementById('schemaModal');
                if (!modal.classList.contains('hidden')) {
                    toggleSchemaModal();
                }
            }
        });

        function confirmAction(message, formId) {
            if (confirm(message)) {
                document.getElementById(formId).submit();
            }
            return false;
        }

        // Live Search Functionality
        const studentSearch = document.getElementById("student-search");
        const studentTableBody = document.getElementById("student-table-body");
        const studentRows = studentTableBody.getElementsByTagName("tr");
        const studentCounter = document.getElementById("student-counter");

        studentSearch.addEventListener("keyup", function() {
            let searchValue = this.value.toLowerCase();
            let visibleCount = 0;

            for (let i = 0; i < studentRows.length; i++) {
                let rowText = studentRows[i].textContent.toLowerCase();

                if (rowText.includes(searchValue)) {
                    studentRows[i].style.display = "";
                    visibleCount++;
                } else {
                    studentRows[i].style.display = "none";
                }
            }

            studentCounter.textContent = `Showing ${visibleCount} student${visibleCount !== 1 ? 's' : ''}`;
        });
    </script>
</x-app-layout>