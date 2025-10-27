<x-app-layout>
    <!-- Header Section -->
    <div class="bg-gradient-to-br from-pink-50 via-rose-50 to-purple-50 border-b border-pink-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Student Progress Overview</h1>
                    <p class="text-gray-600">Monitor and track your students' learning progress</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-500 bg-white px-3 py-1 rounded-full border border-pink-200">
                        {{ count($students) }} students
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl p-6 shadow-sm border border-pink-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-pink-100 rounded-lg">
                            <i class="fas fa-users text-[#FF92C2]"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Students</p>
                            <p class="text-2xl font-bold text-gray-900">{{ count($students) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl p-6 shadow-sm border border-pink-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-[#FFC8FB] rounded-lg">
                            <i class="fas fa-star text-[#FF92C2]"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total XP Earned</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($students->sum(function($student) { return $student->xpTransactions->sum('amount'); })) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl p-6 shadow-sm border border-pink-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <i class="fas fa-trophy text-purple-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Average Level</p>
                            <p class="text-2xl font-bold text-gray-900">{{ number_format($students->avg(function($student) { return floor($student->xpTransactions->sum('amount') / 1000) + 1; }), 1) }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl p-6 shadow-sm border border-pink-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center">
                        <div class="p-3 bg-rose-100 rounded-lg">
                            <i class="fas fa-chart-line text-rose-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Active Courses</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $students->pluck('course.name')->unique()->count() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Card -->
            <div class="bg-[#FFF0FA] overflow-hidden shadow-xl rounded-2xl border border-pink-100">
                <div class="p-6">
                    <!-- Search and Filter -->
                    <div class="mb-6">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" id="searchInput" 
                                   class="w-full pl-10 pr-4 py-3 border border-pink-200 rounded-xl bg-white text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-[#FF92C2] focus:border-transparent" 
                                   placeholder="Search by name, ID, course, or section...">
                        </div>
                    </div>

                    <!-- Students Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="studentsGrid">
                        @foreach($students as $student)
                            <div class="bg-white border border-pink-100 rounded-xl shadow-sm hover:shadow-lg transition-all duration-200 transform hover:scale-105 student-card" 
                                 data-name="{{ strtolower($student->user->name) }}" 
                                 data-course="{{ strtolower($student->course->name ?? '') }}" 
                                 data-section="{{ strtolower($student->sections->first()?->name ?? '') }}">
                                <div class="p-6">
                                    <!-- Student Header -->
                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center">
                                            <div class="w-12 h-12 bg-gradient-to-br from-[#FF92C2] to-[#ff6fb5] rounded-full flex items-center justify-center text-white font-bold text-lg">
                                                {{ strtoupper(substr($student->user->name, 0, 1)) }}
                                            </div>
                                            <div class="ml-3">
                                                <h3 class="text-lg font-semibold text-gray-900">
                                                    {{ $student->user->name }}
                                                </h3>
                                                <p class="text-xs text-gray-500">ID: {{ $student->id }}</p>
                                            </div>
                                        </div>
                                        
                                        <!-- Level Badge -->
                                        <div class="flex flex-col items-end">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gradient-to-r from-[#FFC8FB] to-pink-200 text-[#FF92C2] border border-[#FFC8FB]">
                                                <i class="fas fa-crown mr-1"></i>
                                                Level {{ floor($student->xpTransactions->sum('amount') / 1000) + 1 }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Student Info -->
                                    <div class="space-y-3 mb-6">
                                        <div class="flex items-center text-sm">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full mr-3"></div>
                                            <span class="font-medium text-gray-600 min-w-0 flex-shrink-0">Course:</span>
                                            <span class="ml-2 text-gray-900 truncate">{{ $student->course->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <div class="w-2 h-2 bg-green-500 rounded-full mr-3"></div>
                                            <span class="font-medium text-gray-600 min-w-0 flex-shrink-0">Section:</span>
                                            <span class="ml-2 text-gray-900 truncate">{{ $student->sections->first()?->name ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center text-sm">
                                            <div class="w-2 h-2 bg-yellow-500 rounded-full mr-3"></div>
                                            <span class="font-medium text-gray-600 min-w-0 flex-shrink-0">Total XP:</span>
                                            <span class="ml-2 text-gray-900 font-semibold">{{ number_format($student->xpTransactions->sum('amount')) }}</span>
                                        </div>
                                    </div>

                                    <!-- Progress Bar -->
                                    @php
                                        $currentXP = $student->xpTransactions->sum('amount');
                                        $currentLevel = floor($currentXP / 1000) + 1;
                                        $xpInCurrentLevel = $currentXP % 1000;
                                        $progressPercentage = ($xpInCurrentLevel / 1000) * 100;
                                    @endphp
                                    
                                    <div class="mb-4">
                                        <div class="flex justify-between text-xs text-gray-600 mb-1">
                                            <span>Level Progress</span>
                                            <span>{{ $xpInCurrentLevel }}/1000 XP</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="bg-gradient-to-r from-[#FF92C2] to-[#ff6fb5] h-2 rounded-full transition-all duration-300" 
                                                 style="width: {{ $progressPercentage }}%"></div>
                                        </div>
                                    </div>

                                    <!-- Action Button -->
                                    <div class="flex justify-end">
                                        <a href="{{ route('instructors.progress.show', $student) }}" 
                                           class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-[#FF92C2] to-[#ff6fb5] hover:from-[#ff6fb5] hover:to-[#FF92C2] text-white font-medium text-sm rounded-lg shadow-sm hover:shadow-md transition-all duration-200">
                                            View Details
                                            <i class="fas fa-arrow-right ml-2"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Empty State -->
                    <div id="emptyState" class="text-center py-12 hidden">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                            <i class="fas fa-search text-gray-400 text-xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No students found</h3>
                        <p class="text-gray-600">Try adjusting your search criteria</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const studentCards = document.querySelectorAll('.student-card');
            const emptyState = document.getElementById('emptyState');
            let visibleCards = 0;

            studentCards.forEach(card => {
                const name = card.dataset.name;
                const course = card.dataset.course;
                const section = card.dataset.section;
                
                if (name.includes(searchTerm) || course.includes(searchTerm) || section.includes(searchTerm)) {
                    card.style.display = 'block';
                    visibleCards++;
                } else {
                    card.style.display = 'none';
                }
            });

            // Show/hide empty state
            if (visibleCards === 0) {
                emptyState.classList.remove('hidden');
            } else {
                emptyState.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>