<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Header Section --}}
            <div class="mb-8">
                <div class="bg-gradient-to-r from-white/90 to-[#FFF0FA]/90 backdrop-blur-sm rounded-2xl shadow-xl border border-[#FFC8FB]/30 p-6 relative overflow-hidden">
                    {{-- Decorative gradient background --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-[#FF92C2]/5 via-transparent to-[#FFC8FB]/10 pointer-events-none"></div>
                    
                    <div class="relative">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-12 h-12 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-full mr-4 shadow-md">
                                    <i class="fas fa-user-plus text-white text-lg"></i>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-800">Manage Students</h2>
                                    <p class="text-sm text-gray-600">{{ $section->name }} ({{ $section->section_code }})</p>
                                </div>
                            </div>
                            <a href="{{ route('instructors.sections.index') }}" 
                               class="inline-flex items-center px-5 py-3 bg-white hover:bg-gray-50 text-gray-700 border-2 border-gray-200 hover:border-gray-300 rounded-xl transition-all duration-200 font-medium shadow-sm hover:shadow-md">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Back to Sections
                            </a>
                        </div>

                        {{-- Section Info Card --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                            <div class="bg-white/90 rounded-xl p-4 border-2 border-blue-200 shadow-sm">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-users text-blue-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 font-medium">Current Students</p>
                                        <p class="text-2xl font-bold text-blue-600">{{ $section->students->count() }}</p>
                                    </div>
                                </div>
                            </div>

                            @if($section->capacity)
                            <div class="bg-white/90 rounded-xl p-4 border-2 border-purple-200 shadow-sm">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-chart-pie text-purple-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 font-medium">Capacity</p>
                                        <p class="text-2xl font-bold text-purple-600">{{ $section->capacity }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white/90 rounded-xl p-4 border-2 border-green-200 shadow-sm">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                        <i class="fas fa-chair text-green-600"></i>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500 font-medium">Available Slots</p>
                                        <p class="text-2xl font-bold text-green-600">{{ $section->capacity - $section->students->count() }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Success/Error Messages --}}
            @if(session('success'))
                <div class="mb-6 bg-green-50 border-2 border-green-200 rounded-xl p-4 shadow-sm animate-fade-in">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-check-circle text-green-600"></i>
                        </div>
                        <p class="text-green-800 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-red-50 border-2 border-red-200 rounded-xl p-4 shadow-sm animate-fade-in">
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center mr-3 mt-1">
                            <i class="fas fa-exclamation-circle text-red-600"></i>
                        </div>
                        <div>
                            <p class="text-red-800 font-medium mb-2">Please fix the following errors:</p>
                            <ul class="list-disc list-inside text-red-700 space-y-1">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Main Content --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                {{-- Available Students Panel --}}
                <div class="bg-white rounded-2xl shadow-xl border border-[#FFC8FB]/30 overflow-hidden">
                    <div class="bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-user-graduate text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">Available Students</h3>
                                    <p class="text-sm text-white/80">Select students to add</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-white/20 backdrop-blur-sm text-white rounded-full text-sm font-semibold">
                                {{ $availableStudents->count() }} Available
                            </span>
                        </div>
                    </div>

                    <form action="{{ route('instructors.sections.update-students', $section->id) }}" method="POST" id="studentForm">
                        @csrf
                        
                        <div class="p-6">
                            {{-- Search Box --}}
                            <div class="mb-4">
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-search text-gray-400"></i>
                                    </div>
                                    <input type="text" 
                                           id="searchAvailable"
                                           placeholder="Search available students..." 
                                           class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-[#FF92C2] focus:ring-4 focus:ring-[#FF92C2]/10 focus:outline-none transition-all duration-300">
                                </div>
                            </div>

                            {{-- Select All Checkbox --}}
                            <div class="mb-4 p-3 bg-gray-50 rounded-lg border border-gray-200">
                                <label class="flex items-center cursor-pointer group">
                                    <input type="checkbox" 
                                           id="selectAll"
                                           class="w-5 h-5 text-[#FF92C2] border-gray-300 rounded focus:ring-[#FF92C2] focus:ring-2 cursor-pointer">
                                    <span class="ml-3 text-sm font-semibold text-gray-700 group-hover:text-[#FF92C2] transition-colors">
                                        Select All Available Students
                                    </span>
                                </label>
                            </div>

                            {{-- Available Students List --}}
                            <div class="space-y-2 max-h-96 overflow-y-auto" id="availableList">
                                @forelse($availableStudents as $student)
                                    <label class="flex items-center p-3 hover:bg-gradient-to-r hover:from-[#FFF0FA] hover:to-[#FFC8FB]/10 rounded-xl cursor-pointer transition-all duration-200 border-2 border-transparent hover:border-[#FFC8FB]/30 student-item group">
                                        <input type="checkbox" 
                                               name="students[]" 
                                               value="{{ $student->id }}"
                                               {{ $section->students->contains($student->id) ? 'checked' : '' }}
                                               class="w-5 h-5 text-[#FF92C2] border-gray-300 rounded focus:ring-[#FF92C2] focus:ring-2 cursor-pointer student-checkbox">
                                        <div class="ml-3 flex-1">
                                            <p class="font-semibold text-gray-800 group-hover:text-[#FF92C2] transition-colors student-name">
                                                {{ $student->user->name }}
                                            </p>
                                            <p class="text-sm text-gray-500">{{ $student->user->email }}</p>
                                            @if($student->student_id)
                                                <p class="text-xs text-gray-400 mt-1">ID: {{ $student->student_id }}</p>
                                            @endif
                                        </div>
                                        @if($section->students->contains($student->id))
                                            <span class="px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full font-medium">
                                                <i class="fas fa-check mr-1"></i>
                                                Enrolled
                                            </span>
                                        @endif
                                    </label>
                                @empty
                                    <div class="text-center py-8">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                            <i class="fas fa-users-slash text-2xl text-gray-400"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium">No available students</p>
                                        <p class="text-sm text-gray-400 mt-1">All students are already assigned to sections</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="p-6 bg-gray-50 border-t-2 border-gray-100">
                            <div class="flex flex-col sm:flex-row gap-3">
                                <button type="submit" 
                                        class="flex-1 group relative px-6 py-4 bg-gradient-to-r from-[#FF92C2] to-[#ff6fb5] text-white rounded-xl font-semibold shadow-lg hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-[#FF92C2]/30 transition-all duration-300 transform hover:-translate-y-1 hover:scale-105">
                                    <span class="flex items-center justify-center">
                                        <i class="fas fa-save mr-2 group-hover:animate-pulse"></i>
                                        Save Changes
                                    </span>
                                </button>
                                <a href="{{ route('instructors.sections.show', $section->id) }}" 
                                   class="flex-1 px-6 py-4 bg-white hover:bg-gray-100 text-gray-700 rounded-xl transition-all duration-200 font-semibold border-2 border-gray-200 hover:border-gray-300 flex items-center justify-center shadow-sm hover:shadow-md">
                                    <i class="fas fa-times mr-2"></i>
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Current Students Panel --}}
                <div class="bg-white rounded-2xl shadow-xl border border-[#FFC8FB]/30 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-users text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-white">Current Students</h3>
                                    <p class="text-sm text-white/80">Currently enrolled in this section</p>
                                </div>
                            </div>
                            <span class="px-3 py-1 bg-white/20 backdrop-blur-sm text-white rounded-full text-sm font-semibold">
                                {{ $section->students->count() }} Enrolled
                            </span>
                        </div>
                    </div>

                    <div class="p-6">
                        {{-- Search Box --}}
                        <div class="mb-4">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" 
                                       id="searchCurrent"
                                       placeholder="Search current students..." 
                                       class="w-full pl-10 pr-4 py-3 border-2 border-gray-200 rounded-xl focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 focus:outline-none transition-all duration-300">
                            </div>
                        </div>

                        {{-- Current Students List --}}
                        <div class="space-y-2 max-h-96 overflow-y-auto" id="currentList">
                            @forelse($section->students as $student)
                                <div class="flex items-center justify-between p-3 hover:bg-blue-50 rounded-xl transition-all duration-200 border-2 border-transparent hover:border-blue-200 current-student-item group">
                                    <div class="flex items-center flex-1">
                                        <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center mr-3 text-white font-bold">
                                            {{ substr($student->user->name, 0, 1) }}
                                        </div>
                                        <div class="flex-1">
                                            <p class="font-semibold text-gray-800 group-hover:text-blue-600 transition-colors current-student-name">
                                                {{ $student->user->name }}
                                            </p>
                                            <p class="text-sm text-gray-500">{{ $student->user->email }}</p>
                                            @if($student->student_id)
                                                <p class="text-xs text-gray-400 mt-1">ID: {{ $student->student_id }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <form action="{{ route('instructors.sections.remove-student', [$section->id, $student->id]) }}" 
                                          method="POST" 
                                          class="ml-3"
                                          onsubmit="return confirm('Are you sure you want to remove this student from the section?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200 hover:scale-110"
                                                title="Remove student">
                                            <i class="fas fa-user-minus"></i>
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                                        <i class="fas fa-user-slash text-2xl text-gray-400"></i>
                                    </div>
                                    <p class="text-gray-500 font-medium">No students enrolled</p>
                                    <p class="text-sm text-gray-400 mt-1">Add students from the available list</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScript for search and select all functionality --}}
    <script>
        // Search functionality for available students
        document.getElementById('searchAvailable').addEventListener('keyup', function(e) {
            const searchText = e.target.value.toLowerCase();
            const students = document.querySelectorAll('#availableList .student-item');
            
            students.forEach(student => {
                const name = student.querySelector('.student-name').textContent.toLowerCase();
                if (name.includes(searchText)) {
                    student.style.display = '';
                } else {
                    student.style.display = 'none';
                }
            });
        });

        // Search functionality for current students
        document.getElementById('searchCurrent').addEventListener('keyup', function(e) {
            const searchText = e.target.value.toLowerCase();
            const students = document.querySelectorAll('#currentList .current-student-item');
            
            students.forEach(student => {
                const name = student.querySelector('.current-student-name').textContent.toLowerCase();
                if (name.includes(searchText)) {
                    student.style.display = '';
                } else {
                    student.style.display = 'none';
                }
            });
        });

        // Select all functionality
        document.getElementById('selectAll').addEventListener('change', function(e) {
            const checkboxes = document.querySelectorAll('#availableList .student-checkbox');
            const visibleCheckboxes = Array.from(checkboxes).filter(cb => 
                cb.closest('.student-item').style.display !== 'none'
            );
            
            visibleCheckboxes.forEach(checkbox => {
                checkbox.checked = e.target.checked;
            });
        });

        // Update select all checkbox state when individual checkboxes change
        document.querySelectorAll('#availableList .student-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allCheckboxes = document.querySelectorAll('#availableList .student-checkbox');
                const visibleCheckboxes = Array.from(allCheckboxes).filter(cb => 
                    cb.closest('.student-item').style.display !== 'none'
                );
                const checkedCount = visibleCheckboxes.filter(cb => cb.checked).length;
                
                document.getElementById('selectAll').checked = 
                    visibleCheckboxes.length > 0 && checkedCount === visibleCheckboxes.length;
            });
        });
    </script>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }

        /* Custom scrollbar */
        .overflow-y-auto::-webkit-scrollbar {
            width: 8px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #FF92C2;
            border-radius: 10px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: #ff6fb5;
        }
    </style>
</x-app-layout>