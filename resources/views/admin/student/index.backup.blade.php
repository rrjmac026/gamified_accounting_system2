@section('title', 'Student Management')
<x-app-layout>
    <div class="py-6 sm:py-12 bg-gradient-to-br from-pink-50 to-purple-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header Section with improved styling --}}
            <div class="flex flex-col sm:flex-row justify-between items-center mb-6 sm:mb-8 gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-pink-600 to-purple-600">
                        Student Management
                    </h2>
                    <p class="text-gray-600 mt-1">Manage and import student records efficiently</p>
                </div>
                <div class="w-full sm:w-auto">
                    <a href="{{ route('admin.student.create') }}" 
                       class="group w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-xl hover:from-pink-600 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-pink-300 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center justify-center">
                        <i class="fas fa-plus mr-2 group-hover:rotate-90 transition-transform duration-300"></i>
                        Add New Student
                    </a>
                </div>
            </div>

            {{-- Main Content Card --}}
            <div class="bg-white/80 backdrop-blur-sm overflow-hidden shadow-2xl hover:shadow-3xl transition-all duration-500 rounded-2xl border border-pink-100">
                <div class="p-6 sm:p-8 text-gray-700">
                    
                    {{-- Enhanced Success Message --}}
                    @if (session('success'))
                        <div class="mb-6 px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 text-green-700 rounded-xl shadow-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-green-800">Success!</h3>
                                    <p class="text-sm text-green-700 mt-1">{{ session('success') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Enhanced Error Message --}}
                    @if (session('error'))
                        <div class="mb-6 px-6 py-4 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-400 text-red-700 rounded-xl shadow-sm">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Import Failed</h3>
                                    <p class="text-sm text-red-700 mt-1">{{ session('error') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Enhanced Validation Errors --}}
                    @if ($errors->any())
                        <div class="mb-6 px-6 py-4 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-400 text-red-700 rounded-xl shadow-sm">
                            <div class="flex items-start">
                                <div class="flex-shrink-0 mt-0.5">
                                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Validation Errors</h3>
                                    <ul class="mt-2 text-sm text-red-700 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li class="flex items-center">
                                                <i class="fas fa-chevron-right mr-2 text-xs"></i>
                                                {{ $error }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Enhanced Import Section --}}
                    <div class="mb-8 p-6 bg-gradient-to-br from-pink-50 to-purple-50 rounded-2xl border border-pink-200 shadow-sm">
                        <div class="flex justify-between items-center mb-4">
                            <div>
                                <h3 class="text-xl font-semibold text-gray-900 flex items-center">
                                    <div class="w-8 h-8 bg-gradient-to-r from-pink-500 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                                        <i class="fas fa-upload text-white text-sm"></i>
                                    </div>
                                    Import Students
                                </h3>
                                <p class="text-gray-600 text-sm mt-1">Upload CSV or Excel files to add multiple students at once</p>
                            </div>
                            <button type="button" 
                                    onclick="toggleSchemaModal()"
                                    class="group px-4 py-2 bg-white text-pink-600 border-2 border-pink-300 rounded-xl hover:bg-pink-600 hover:text-white hover:border-pink-600 focus:outline-none focus:ring-4 focus:ring-pink-300 transition-all duration-300 transform hover:scale-105 flex items-center text-sm font-medium shadow-sm">
                                <i class="fas fa-info-circle mr-2 group-hover:rotate-12 transition-transform duration-300"></i>
                                View Schema
                            </button>
                        </div>
                        <form action="{{ route('admin.student.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div class="flex flex-col lg:flex-row items-start lg:items-end gap-4">
                                <div class="flex-1 w-full">
                                    <label for="file" class="block text-sm font-semibold text-gray-700 mb-2">
                                        Select File to Import
                                    </label>
                                    <div class="relative">
                                        <input type="file" name="file" id="file" accept=".csv,.xlsx,.xls"
                                               class="w-full px-4 py-3 border-2 border-dashed border-pink-300 rounded-xl focus:outline-none focus:ring-4 focus:ring-pink-200 focus:border-pink-500 transition-all duration-300 bg-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-pink-50 file:text-pink-700 hover:file:bg-pink-100"
                                               required>
                                    </div>
                                </div>
                                <button type="submit" 
                                        class="group px-6 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-xl hover:from-pink-600 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-pink-300 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center font-semibold">
                                    <i class="fas fa-cloud-upload-alt mr-2 group-hover:animate-bounce"></i>
                                    Import Students
                                </button>
                            </div>
                            <div class="flex items-center text-sm text-gray-500 bg-blue-50 p-3 rounded-lg border border-blue-200">
                                <i class="fas fa-lightbulb text-blue-500 mr-2"></i>
                                <span>Click "View Schema" to see the required CSV format and column structure.</span>
                            </div>
                        </form>
                    </div>

                    {{-- Enhanced Search Bar --}}
                    <div class="mb-6 flex flex-col lg:flex-row items-center justify-between gap-4">
                        <form action="{{ route('admin.student.index') }}" method="GET" class="flex w-full lg:w-2/3">
                            <div class="relative flex-grow">
                                <input type="text" 
                                    name="search" 
                                    value="{{ request('search') }}" 
                                    placeholder="Search students by name, email, or student ID..."
                                    class="w-full pl-12 pr-4 py-3 border-2 border-pink-200 rounded-l-xl focus:ring-4 focus:ring-pink-200 focus:border-pink-500 text-sm sm:text-base bg-white shadow-sm transition-all duration-300">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-pink-400">
                                    <i class="fas fa-search"></i>
                                </span>
                            </div>
                            <button type="submit" 
                                    class="px-6 py-3 bg-gradient-to-r from-pink-500 to-purple-600 text-white rounded-r-xl hover:from-pink-600 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-pink-300 transition-all duration-300 shadow-lg flex items-center font-semibold">
                                <i class="fas fa-search mr-2"></i>
                                <span class="hidden sm:inline">Search</span>
                            </button>
                        </form>

                        @if(request('search'))
                            <a href="{{ route('admin.student.index') }}" 
                            class="px-4 py-2 text-sm text-pink-600 hover:text-pink-800 bg-pink-50 hover:bg-pink-100 rounded-lg border border-pink-200 transition-all duration-200 flex items-center">
                                <i class="fas fa-times mr-2"></i>
                                Clear Search
                            </a>
                        @endif
                    </div>

                    {{-- Enhanced Students Table --}}
                    <div class="overflow-hidden rounded-2xl shadow-lg border border-pink-100">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-700">
                                <thead class="bg-gradient-to-r from-pink-100 to-purple-100 text-xs uppercase tracking-wider">
                                    <tr>
                                        <th scope="col" class="py-4 px-6 font-semibold text-gray-700">
                                            <div class="flex items-center">
                                                <i class="fas fa-user mr-2 text-pink-500"></i>
                                                Student Info
                                            </div>
                                        </th>
                                        <th scope="col" class="hidden md:table-cell py-4 px-6 font-semibold text-gray-700">
                                            <div class="flex items-center">
                                                <i class="fas fa-id-card mr-2 text-pink-500"></i>
                                                Student ID
                                            </div>
                                        </th>
                                        <th scope="col" class="hidden sm:table-cell py-4 px-6 font-semibold text-gray-700">
                                            <div class="flex items-center">
                                                <i class="fas fa-graduation-cap mr-2 text-pink-500"></i>
                                                Course
                                            </div>
                                        </th>
                                        <th scope="col" class="hidden lg:table-cell py-4 px-6 font-semibold text-gray-700">
                                            <div class="flex items-center">
                                                <i class="fas fa-layer-group mr-2 text-pink-500"></i>
                                                Year/Section
                                            </div>
                                        </th>
                                        <th scope="col" class="py-4 px-6 font-semibold text-gray-700">
                                            <div class="flex items-center">
                                                <i class="fas fa-toggle-on mr-2 text-pink-500"></i>
                                                Status
                                            </div>
                                        </th>
                                        <th scope="col" class="py-4 px-6 font-semibold text-gray-700 text-center">
                                            <div class="flex items-center justify-center">
                                                <i class="fas fa-cogs mr-2 text-pink-500"></i>
                                                Actions
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-pink-50">
                                    @forelse ($students as $student)
                                        <tr class="hover:bg-gradient-to-r hover:from-pink-25 hover:to-purple-25 transition-all duration-200">
                                            <td class="py-4 px-6">
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 bg-gradient-to-r from-pink-400 to-purple-500 rounded-full flex items-center justify-center text-white font-semibold mr-4">
                                                        {{ strtoupper(substr($student->user->name ?? 'N', 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="font-semibold text-gray-900">{{ $student->user->name ?? 'N/A' }}</div>
                                                        <div class="text-sm text-gray-500">{{ $student->user->email ?? 'N/A' }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="hidden md:table-cell py-4 px-6">
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                                    {{ $student->user->student_number ?? $student->student_number }}
                                                </span>
                                            </td>
                                            <td class="hidden sm:table-cell py-4 px-6">
                                                <span class="text-gray-900 font-medium">{{ $student->course->course_name ?? '-' }}</span>
                                            </td>
                                            <td class="hidden lg:table-cell py-4 px-6">
                                                <div class="space-y-1">
                                                    <div class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-800">
                                                        Year {{ $student->year_level ?? '-' }}
                                                    </div>
                                                    <div class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-purple-100 text-purple-800 ml-2">
                                                        Section {{ $student->section ?? '-' }}
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6">
                                                @if($student->user && $student->user->is_active)
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-green-100 text-green-800 border border-green-200">
                                                        <div class="w-2 h-2 bg-green-400 rounded-full mr-2 animate-pulse"></div>
                                                        Active
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-800 border border-red-200">
                                                        <div class="w-2 h-2 bg-red-400 rounded-full mr-2"></div>
                                                        Inactive
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="py-4 px-6">
                                                <div class="flex items-center justify-center space-x-3">
                                                    <a href="{{ route('admin.student.show', $student->id) }}" 
                                                       class="group p-2 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg transition-all duration-200"
                                                       title="View Details">
                                                        <i class="fas fa-eye group-hover:scale-110 transition-transform duration-200"></i>
                                                    </a>
                                                    <a href="{{ route('admin.student.edit', $student->id) }}" 
                                                       class="group p-2 text-amber-600 hover:text-amber-800 hover:bg-amber-50 rounded-lg transition-all duration-200"
                                                       title="Edit Student">
                                                        <i class="fas fa-edit group-hover:scale-110 transition-transform duration-200"></i>
                                                    </a>
                                                    <button type="button" 
                                                            onclick="return confirmAction('Are you sure you want to delete this student?', 'delete-student-{{ $student->id }}')"
                                                            class="group p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-all duration-200"
                                                            title="Delete Student">
                                                        <i class="fas fa-trash group-hover:scale-110 transition-transform duration-200"></i>
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
                                            <td colspan="6" class="py-12 px-4 text-center">
                                                <div class="flex flex-col items-center justify-center">
                                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                                        <i class="fas fa-users text-2xl text-gray-400"></i>
                                                    </div>
                                                    <h3 class="text-lg font-medium text-gray-900 mb-2">No students found</h3>
                                                    <p class="text-gray-500">Try adjusting your search or add some students.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Enhanced Pagination --}}
                    @if($students->hasPages())
                        <div class="mt-8 flex justify-center">
                            <div class="bg-white rounded-xl shadow-lg p-2 border border-pink-100">
                                {{ $students->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Enhanced Schema Modal --}}
    <div id="schemaModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm overflow-y-auto h-full w-full z-50 hidden flex items-center justify-center p-4">
        <div class="relative bg-white rounded-3xl shadow-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden transform transition-all duration-300 scale-95 opacity-0" id="modalContent">
            <div class="relative">
                {{-- Modal Header --}}
                <div class="bg-gradient-to-r from-pink-500 to-purple-600 px-6 sm:px-8 py-6 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-2xl font-bold flex items-center">
                                <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-table text-lg"></i>
                                </div>
                                CSV Import Schema
                            </h3>
                            <p class="text-pink-100 mt-1">Required format for importing student data</p>
                        </div>
                        <button type="button" 
                                onclick="toggleSchemaModal()" 
                                class="group w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-all duration-200 hover:rotate-90">
                            <i class="fas fa-times text-lg group-hover:scale-110 transition-transform duration-200"></i>
                        </button>
                    </div>
                </div>
                
                {{-- Modal Body --}}
                <div class="p-6 sm:p-8 max-h-[70vh] overflow-y-auto">
                    <div class="space-y-6">
                        {{-- Schema Table --}}
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                                <i class="fas fa-columns text-pink-500 mr-2"></i>
                                Required Columns
                            </h4>
                            <div class="overflow-hidden rounded-xl border border-pink-200 shadow-sm">
                                <table class="w-full text-sm">
                                    <thead class="bg-gradient-to-r from-pink-50 to-purple-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-700 border-r border-pink-200">Column Name</th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-700 border-r border-pink-200">Description</th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-700">Example</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-pink-50">
                                        <tr class="hover:bg-pink-25 transition-colors duration-150">
                                            <td class="px-4 py-3 border-r border-pink-100">
                                                <span class="inline-flex items-center px-2 py-1 rounded-md text-sm font-mono bg-pink-100 text-pink-700">name</span>
                                            </td>
                                            <td class="px-4 py-3 border-r border-pink-100 text-gray-600">Full name of the student</td>
                                            <td class="px-4 py-3 font-medium text-gray-900">John Doe</td>
                                        </tr>
                                        <tr class="hover:bg-pink-25 transition-colors duration-150">
                                            <td class="px-4 py-3 border-r border-pink-100">
                                                <span class="inline-flex items-center px-2 py-1 rounded-md text-sm font-mono bg-pink-100 text-pink-700">email</span>
                                            </td>
                                            <td class="px-4 py-3 border-r border-pink-100 text-gray-600">Student email address (must be unique)</td>
                                            <td class="px-4 py-3 font-medium text-gray-900">john.doe@example.com</td>
                                        </tr>
                                        <tr class="hover:bg-pink-25 transition-colors duration-150">
                                            <td class="px-4 py-3 border-r border-pink-100">
                                                <span class="inline-flex items-center px-2 py-1 rounded-md text-sm font-mono bg-pink-100 text-pink-700">course</span>
                                            </td>
                                            <td class="px-4 py-3 border-r border-pink-100 text-gray-600">Course or program name</td>
                                            <td class="px-4 py-3 font-medium text-gray-900">Computer Science</td>
                                        </tr>
                                        <tr class="hover:bg-pink-25 transition-colors duration-150">
                                            <td class="px-4 py-3 border-r border-pink-100">
                                                <span class="inline-flex items-center px-2 py-1 rounded-md text-sm font-mono bg-pink-100 text-pink-700">year_level</span>
                                            </td>
                                            <td class="px-4 py-3 border-r border-pink-100 text-gray-600">Academic year (1, 2, 3, or 4)</td>
                                            <td class="px-4 py-3 font-medium text-gray-900">2</td>
                                        </tr>
                                        <tr class="hover:bg-pink-25 transition-colors duration-150">
                                            <td class="px-4 py-3 border-r border-pink-100">
                                                <span class="inline-flex items-center px-2 py-1 rounded-md text-sm font-mono bg-pink-100 text-pink-700">section</span>
                                            </td>
                                            <td class="px-4 py-3 border-r border-pink-100 text-gray-600">Section identifier</td>
                                            <td class="px-4 py-3 font-medium text-gray-900">A</td>
                                        </tr>
                                        <tr class="hover:bg-pink-25 transition-colors duration-150">
                                            <td class="px-4 py-3 border-r border-pink-100">
                                                <span class="inline-flex items-center px-2 py-1 rounded-md text-sm font-mono bg-pink-100 text-pink-700">password</span>
                                            </td>
                                            <td class="px-4 py-3 border-r border-pink-100 text-gray-600">Default password for the account</td>
                                            <td class="px-4 py-3 font-medium text-gray-900">password123</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        {{-- Sample CSV Format --}}
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6">
                            <h4 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                                <i class="fas fa-file-csv text-blue-600 mr-2"></i>
                                Sample CSV Format
                            </h4>
                            <div class="bg-white border-2 border-dashed border-blue-300 rounded-lg p-4">
                                <div class="font-mono text-sm space-y-1">
                                    <div class="text-blue-600 font-semibold">name,email,course,year_level,section,password</div>
                                    <div class="text-gray-600">Mike Johnson,mike.johnson@example.com,Business Administration,3,C,password123</div>
                                </div>
                                <div class="mt-3 flex items-center text-xs text-blue-600">
                                    <i class="fas fa-info-circle mr-2"></i>
                                    <span>Copy this format exactly, including the header row</span>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Download Template Button --}}
                        <div class="text-center">
                            <button onclick="downloadTemplate()" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold rounded-xl hover:from-green-600 hover:to-emerald-700 focus:outline-none focus:ring-4 focus:ring-green-300 transition-all duration-300 transform hover:scale-105 shadow-lg">
                                <i class="fas fa-download mr-2"></i>
                                Download CSV Template
                            </button>
                        </div>
                        
                        {{-- Important Notes --}}
                        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-6">
                            <h4 class="text-lg font-semibold text-amber-800 mb-4 flex items-center">
                                <i class="fas fa-exclamation-triangle text-amber-600 mr-2"></i>
                                Important Guidelines
                            </h4>
                            <div class="grid md:grid-cols-2 gap-4">
                                <div class="space-y-3">
                                    <div class="flex items-start">
                                        <div class="w-6 h-6 bg-amber-200 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 mr-3">
                                            <i class="fas fa-check text-amber-700 text-xs"></i>
                                        </div>
                                        <div class="text-sm text-amber-700">
                                            <strong>Header Row:</strong> First row must contain exact column names
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="w-6 h-6 bg-amber-200 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 mr-3">
                                            <i class="fas fa-check text-amber-700 text-xs"></i>
                                        </div>
                                        <div class="text-sm text-amber-700">
                                            <strong>Required Fields:</strong> All columns must have values
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="w-6 h-6 bg-amber-200 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 mr-3">
                                            <i class="fas fa-check text-amber-700 text-xs"></i>
                                        </div>
                                        <div class="text-sm text-amber-700">
                                            <strong>Unique Emails:</strong> Each email must be unique
                                        </div>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex items-start">
                                        <div class="w-6 h-6 bg-amber-200 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 mr-3">
                                            <i class="fas fa-check text-amber-700 text-xs"></i>
                                        </div>
                                        <div class="text-sm text-amber-700">
                                            <strong>Year Level:</strong> Must be 1, 2, 3, or 4
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="w-6 h-6 bg-amber-200 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 mr-3">
                                            <i class="fas fa-check text-amber-700 text-xs"></i>
                                        </div>
                                        <div class="text-sm text-amber-700">
                                            <strong>File Formats:</strong> CSV, XLSX, or XLS supported
                                        </div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="w-6 h-6 bg-amber-200 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 mr-3">
                                            <i class="fas fa-check text-amber-700 text-xs"></i>
                                        </div>
                                        <div class="text-sm text-amber-700">
                                            <strong>File Size:</strong> Maximum 10MB per file
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Modal Footer --}}
                <div class="bg-gray-50 px-6 sm:px-8 py-4 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <div class="text-sm text-gray-600 flex items-center">
                        <i class="fas fa-users mr-2 text-pink-500"></i>
                        Need help? Contact support for assistance with bulk imports.
                    </div>
                    <button type="button" 
                            onclick="toggleSchemaModal()"
                            class="px-6 py-2 bg-gradient-to-r from-pink-500 to-purple-600 text-white font-semibold rounded-lg hover:from-pink-600 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-pink-300 transition-all duration-300 transform hover:scale-105">
                        Got it, thanks!
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Custom animations */
        @keyframes slideInUp {
            from {
                transform: translateY(100px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }
        
        .modal-enter {
            animation: slideInUp 0.3s ease-out forwards;
        }
        
        /* Custom scrollbar for modal */
        .modal-scroll::-webkit-scrollbar {
            width: 6px;
        }
        
        .modal-scroll::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 6px;
        }
        
        .modal-scroll::-webkit-scrollbar-thumb {
            background: #ec4899;
            border-radius: 6px;
        }
        
        .modal-scroll::-webkit-scrollbar-thumb:hover {
            background: #db2777;
        }

        /* Hover effects for table rows */
        .hover\:bg-pink-25:hover {
            background-color: rgba(251, 207, 232, 0.1);
        }
        
        .hover\:from-pink-25:hover {
            background: linear-gradient(to right, rgba(251, 207, 232, 0.1), rgba(196, 181, 253, 0.1));
        }
    </style>

    <script>
        function toggleSchemaModal() {
            const modal = document.getElementById('schemaModal');
            const modalContent = document.getElementById('modalContent');
            
            if (modal.classList.contains('hidden')) {
                modal.classList.remove('hidden');
                setTimeout(() => {
                    modalContent.classList.remove('scale-95', 'opacity-0');
                    modalContent.classList.add('scale-100', 'opacity-100');
                }, 10);
            } else {
                modalContent.classList.remove('scale-100', 'opacity-100');
                modalContent.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                }, 300);
            }
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

        // Download CSV template function
        function downloadTemplate() {
            const csvContent = 'name,email,course,year_level,section,password\n' +
                             'John Doe,john.doe@example.com,Computer Science,2,A,password123\n' +
                             'Jane Smith,jane.smith@example.com,Information Technology,1,B,password123\n' +
                             'Mike Johnson,mike.johnson@example.com,Business Administration,3,C,password123';
            
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);
            
            link.setAttribute('href', url);
            link.setAttribute('download', 'student_import_template.csv');
            link.style.visibility = 'hidden';
            
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Enhanced file input styling
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('file');
            if (fileInput) {
                fileInput.addEventListener('change', function(e) {
                    if (e.target.files.length > 0) {
                        const fileName = e.target.files[0].name;
                        // You could add visual feedback here showing the selected file
                    }
                });
            }
        });
    </script>
</x-app-layout>