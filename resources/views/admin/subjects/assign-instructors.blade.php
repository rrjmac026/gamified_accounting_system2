<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <!-- Enhanced Header Section -->
            <div class="bg-white/80 backdrop-blur-xl border border-pink-100/50 rounded-3xl shadow-xl p-8 mb-8">
                <div class="flex justify-between items-start">
                    <div class="space-y-4">
                        <div class="flex items-center space-x-4">
                            <div class="relative">
                                <div class="absolute -inset-1 bg-gradient-to-r from-pink-600 to-rose-600 rounded-xl blur opacity-30"></div>
                                <div class="relative p-3 bg-gradient-to-br from-pink-500 to-rose-500 rounded-xl shadow-lg">
                                    <i class="fas fa-chalkboard-teacher text-white text-xl"></i>
                                </div>
                            </div>
                            <div>
                                <h1 class="text-4xl font-bold bg-gradient-to-r from-pink-600 via-rose-500 to-purple-600 bg-clip-text text-transparent">
                                    Assign Instructors
                                </h1>
                                <div class="flex items-center space-x-3 mt-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-sm font-semibold bg-gradient-to-r from-pink-100 to-rose-100 text-pink-700 border border-pink-200">
                                            <i class="fas fa-code text-xs mr-2"></i>
                                            {{ $subject->subject_code }}
                                        </span>
                                        <span class="text-gray-600 font-medium">{{ $subject->subject_name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <p class="text-gray-600 max-w-2xl leading-relaxed">
                            Select and assign qualified instructors to teach this subject. Multiple instructors can collaborate to provide comprehensive coverage.
                        </p>
                    </div>
                    <a href="{{ route('admin.subjects.show', $subject) }}" 
                       class="group flex items-center space-x-2 px-6 py-3 bg-white border border-pink-200 rounded-xl hover:bg-pink-50 hover:border-pink-300 transition-all duration-200 shadow-sm hover:shadow-md">
                        <i class="fas fa-arrow-left text-pink-600 group-hover:-translate-x-1 transition-transform duration-200"></i>
                        <span class="font-medium text-pink-600">Back to Subject</span>
                    </a>
                </div>
            </div>

            <!-- Main Form -->
            <form action="{{ route('admin.subjects.assignInstructors', $subject) }}" method="POST" class="space-y-8">
                @csrf
                
                <!-- Search and Controls Section -->
                <div class="bg-white/90 backdrop-blur-xl border border-pink-100/50 rounded-2xl shadow-lg p-6">
                    <div class="space-y-6">
                        <!-- Enhanced Search Bar -->
                        <div class="relative group">
                            <div class="absolute -inset-0.5 bg-gradient-to-r from-pink-600 to-rose-600 rounded-xl opacity-0 group-focus-within:opacity-20 blur transition duration-300"></div>
                            <div class="relative">
                                <input type="text" 
                                       id="instructor-search" 
                                       placeholder="Search instructors by name, department, or specialization..." 
                                       class="w-full pl-14 pr-16 py-4 text-gray-800 bg-white border border-pink-200 rounded-xl 
                                              focus:border-pink-400 focus:ring-4 focus:ring-pink-100/50 transition-all duration-300
                                              placeholder-gray-400 shadow-sm text-lg font-medium">
                                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-pink-400 text-xl"></i>
                                </div>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center space-x-2">
                                    <kbd class="hidden sm:inline-flex items-center px-3 py-1.5 text-xs font-medium text-gray-500 bg-gray-100 rounded-lg border border-gray-200">
                                        ⌘K
                                    </kbd>
                                </div>
                            </div>
                        </div>

                        <!-- Enhanced Quick Actions -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between space-y-4 sm:space-y-0">
                            <div class="flex items-center space-x-3">
                                <button type="button" 
                                        id="select-all" 
                                        class="group flex items-center space-x-2 px-5 py-2.5 bg-gradient-to-r from-pink-500 to-rose-500 
                                               hover:from-pink-600 hover:to-rose-600 text-white rounded-xl font-semibold
                                               shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                    <i class="fas fa-check-double group-hover:rotate-12 transition-transform duration-200"></i>
                                    <span>Select All</span>
                                </button>
                                <button type="button" 
                                        id="deselect-all" 
                                        class="group flex items-center space-x-2 px-5 py-2.5 bg-white border-2 border-gray-300 
                                               hover:border-gray-400 text-gray-700 rounded-xl font-semibold
                                               shadow-sm hover:shadow-md transition-all duration-200 hover:bg-gray-50">
                                    <i class="fas fa-times group-hover:rotate-90 transition-transform duration-200"></i>
                                    <span>Clear All</span>
                                </button>
                            </div>
                            
                            <!-- Live Counter -->
                            <div class="flex items-center space-x-3 px-4 py-2 bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl border border-green-200">
                                <div class="relative">
                                    <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                                    <div class="absolute inset-0 w-3 h-3 bg-green-400 rounded-full animate-ping opacity-20"></div>
                                </div>
                                <span class="text-green-700 font-semibold" id="selection-counter">0 instructors selected</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Instructors Grid -->
                <div class="bg-white/90 backdrop-blur-xl border border-pink-100/50 rounded-2xl shadow-lg overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-6 flex items-center space-x-2">
                            <i class="fas fa-users text-pink-500"></i>
                            <span>Available Instructors</span>
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6" id="instructors-container">
                            @foreach($allInstructors as $instructor)
                            <div class="instructor-item group relative bg-white rounded-xl border border-gray-200 p-6 
                                        hover:border-pink-300 hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1
                                        overflow-hidden">
                                
                                <!-- Hover Effect Background -->
                                <div class="absolute inset-0 bg-gradient-to-br from-pink-50 to-rose-50 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                
                                <label class="relative flex items-start space-x-4 cursor-pointer">
                                    <!-- Custom Checkbox -->
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="relative">
                                            <input type="checkbox" 
                                                   name="instructors[]" 
                                                   value="{{ $instructor->id }}"
                                                   {{ $subject->instructors->contains($instructor->id) ? 'checked' : '' }}
                                                   class="instructor-checkbox w-6 h-6 text-pink-600 bg-white border-2 border-pink-300 
                                                          rounded-md focus:ring-pink-500 focus:ring-2 transition-all duration-200
                                                          checked:bg-gradient-to-br checked:from-pink-500 checked:to-rose-500 
                                                          checked:border-transparent">
                                            <!-- Checkmark Animation -->
                                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200 pointer-events-none">
                                                <i class="fas fa-check text-white text-xs"></i>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Instructor Info -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-start justify-between">
                                            <div class="space-y-3 flex-1">
                                                <!-- Name and Department -->
                                                <div>
                                                    <h4 class="font-bold text-gray-900 text-lg group-hover:text-pink-600 transition-colors duration-200">
                                                        {{ $instructor->user->name }}
                                                    </h4>
                                                    <p class="text-pink-600 font-semibold text-sm mt-1">
                                                        {{ $instructor->department }}
                                                    </p>
                                                </div>
                                                
                                                <!-- Specialization -->
                                                @if($instructor->specialization)
                                                <div class="flex items-center space-x-2">
                                                    <i class="fas fa-star text-yellow-400 text-sm"></i>
                                                    <span class="text-sm text-gray-600 font-medium">{{ $instructor->specialization }}</span>
                                                </div>
                                                @endif
                                                
                                                <!-- Stats -->
                                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                                    <div class="flex items-center space-x-1.5">
                                                        <i class="fas fa-book text-blue-400"></i>
                                                        <span class="font-medium">{{ $instructor->subjects->count() ?? 0 }} Subjects</span>
                                                    </div>
                                                    <div class="flex items-center space-x-1.5">
                                                        <i class="fas fa-users text-green-400"></i>
                                                        <span class="font-medium">{{ $instructor->students()->count() ?? 0 }} Students</span>
                                                    </div>
                                                </div>
                                                
                                                <!-- Status Badge -->
                                                <div class="flex items-center space-x-2">
                                                    @if($subject->instructors->contains($instructor->id))
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                        <i class="fas fa-check text-green-600 mr-1"></i>
                                                        Currently Assigned
                                                    </span>
                                                    @else
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                                                        <i class="fas fa-plus text-gray-400 mr-1"></i>
                                                        Available
                                                    </span>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <!-- Avatar -->
                                            <div class="flex-shrink-0 ml-4">
                                                <div class="relative">
                                                    <div class="w-16 h-16 bg-gradient-to-br from-pink-400 via-rose-400 to-purple-500 rounded-full 
                                                                flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow duration-300">
                                                        <span class="text-white font-bold text-xl">
                                                            {{ substr($instructor->user->first_name, 0, 1) }}{{ substr($instructor->user->last_name, 0, 1) }}
                                                        </span>
                                                    </div>
                                                    <!-- Online Status -->
                                                    <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-400 border-2 border-white rounded-full"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            @endforeach
                        </div>

                        <!-- Enhanced Empty State -->
                        <div id="empty-state" class="hidden text-center py-16">
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-pink-100 to-rose-100 rounded-full mb-6">
                                <i class="fas fa-search text-pink-400 text-2xl"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">No instructors found</h3>
                            <p class="text-gray-600 max-w-md mx-auto">Try adjusting your search terms or clearing the search to see all available instructors.</p>
                            <button type="button" 
                                    onclick="document.getElementById('instructor-search').value = ''; filterInstructors('')"
                                    class="mt-4 px-6 py-2 bg-pink-500 hover:bg-pink-600 text-white rounded-lg font-medium transition-colors duration-200">
                                Clear Search
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Enhanced Action Buttons -->
                <div class="flex justify-end space-x-4">
                    <a href="{{ route('admin.subjects.show', $subject) }}" 
                       class="px-8 py-3 bg-white border-2 border-gray-300 text-gray-700 font-semibold rounded-xl 
                              hover:border-gray-400 hover:bg-gray-50 transition-all duration-200 shadow-sm hover:shadow-md">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="relative px-8 py-3 bg-gradient-to-r from-pink-500 to-rose-500 
                                   hover:from-pink-600 hover:to-rose-600 text-white font-semibold rounded-xl 
                                   shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105
                                   disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                        <span class="relative flex items-center space-x-2">
                            <i class="fas fa-save"></i>
                            <span>Save Assignments</span>
                        </span>
                        <!-- Loading Spinner (hidden by default) -->
                        <div class="absolute inset-0 flex items-center justify-center opacity-0 transition-opacity duration-200" id="loading-spinner">
                            <i class="fas fa-spinner fa-spin text-white"></i>
                        </div>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Enhanced JavaScript with improved functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('instructor-search');
            const selectAllBtn = document.getElementById('select-all');
            const deselectAllBtn = document.getElementById('deselect-all');
            const selectionCounter = document.getElementById('selection-counter');
            const instructorsContainer = document.getElementById('instructors-container');
            const emptyState = document.getElementById('empty-state');
            const submitButton = document.querySelector('button[type="submit"]');
            
            let originalInstructorsHtml = instructorsContainer.innerHTML;

            // Enhanced search functionality
            function filterInstructors(query = '') {
                const items = document.querySelectorAll('.instructor-item');
                let visibleCount = 0;
                
                items.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    const isVisible = text.includes(query.toLowerCase());
                    
                    item.style.display = isVisible ? '' : 'none';
                    if (isVisible) visibleCount++;
                    
                    // Add subtle animation
                    if (isVisible) {
                        item.classList.add('animate-fade-in');
                        setTimeout(() => item.classList.remove('animate-fade-in'), 300);
                    }
                });
                
                // Show/hide empty state
                if (visibleCount === 0 && query !== '') {
                    instructorsContainer.style.display = 'none';
                    emptyState.style.display = 'block';
                } else {
                    instructorsContainer.style.display = 'grid';
                    emptyState.style.display = 'none';
                }
            }

            // Enhanced search with debounce
            let searchTimeout;
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    filterInstructors(this.value);
                }, 300);
            });

            // Keyboard shortcut for search (Cmd+K)
            document.addEventListener('keydown', function(e) {
                if ((e.metaKey || e.ctrlKey) && e.key === 'k') {
                    e.preventDefault();
                    searchInput.focus();
                }
            });

            // Enhanced selection counter with animation
            function updateCounter() {
                const checkedBoxes = document.querySelectorAll('.instructor-checkbox:checked');
                const count = checkedBoxes.length;
                
                selectionCounter.textContent = `${count} instructor${count !== 1 ? 's' : ''} selected`;
                
                // Add pulse animation when selection changes
                selectionCounter.parentElement.classList.add('animate-pulse');
                setTimeout(() => {
                    selectionCounter.parentElement.classList.remove('animate-pulse');
                }, 500);
                
                // Enable/disable submit button based on selection
                if (submitButton) {
                    submitButton.disabled = count === 0;
                }
            }

            // Add change event to all checkboxes with enhanced feedback
            function addCheckboxListeners() {
                document.querySelectorAll('.instructor-checkbox').forEach(checkbox => {
                    checkbox.addEventListener('change', function() {
                        updateCounter();
                        
                        // Add visual feedback
                        const card = this.closest('.instructor-item');
                        if (this.checked) {
                            card.classList.add('ring-2', 'ring-pink-500', 'bg-pink-50');
                        } else {
                            card.classList.remove('ring-2', 'ring-pink-500', 'bg-pink-50');
                        }
                    });
                });
            }

            // Enhanced select/deselect all with animation
            selectAllBtn.addEventListener('click', function() {
                const visibleCheckboxes = document.querySelectorAll('.instructor-item:not([style*="display: none"]) .instructor-checkbox');
                
                visibleCheckboxes.forEach((checkbox, index) => {
                    setTimeout(() => {
                        checkbox.checked = true;
                        checkbox.dispatchEvent(new Event('change'));
                    }, index * 50); // Stagger the animation
                });
                
                // Button feedback
                this.classList.add('animate-bounce');
                setTimeout(() => this.classList.remove('animate-bounce'), 500);
            });

            deselectAllBtn.addEventListener('click', function() {
                const visibleCheckboxes = document.querySelectorAll('.instructor-item:not([style*="display: none"]) .instructor-checkbox');
                
                visibleCheckboxes.forEach((checkbox, index) => {
                    setTimeout(() => {
                        checkbox.checked = false;
                        checkbox.dispatchEvent(new Event('change'));
                    }, index * 30); // Faster deselection
                });
                
                // Button feedback
                this.classList.add('animate-spin');
                setTimeout(() => this.classList.remove('animate-spin'), 300);
            });

            // Form submission with loading state
            document.querySelector('form').addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                const spinner = document.getElementById('loading-spinner');
                
                if (submitBtn && spinner) {
                    submitBtn.disabled = true;
                    submitBtn.querySelector('span').style.opacity = '0';
                    spinner.style.opacity = '1';
                }
            });

            // Initialize
            addCheckboxListeners();
            updateCounter();
        });

        // Global function for empty state clear button
        function filterInstructors(query) {
            const event = new Event('input');
            document.getElementById('instructor-search').value = query;
            document.getElementById('instructor-search').dispatchEvent(event);
        }
    </script>

    <!-- Additional CSS for animations -->
    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }
        
        .instructor-checkbox:checked + div {
            background: linear-gradient(135deg, #ec4899, #f43f5e);
        }
        
        /* Smooth transitions for all interactive elements */
        .instructor-item,
        .instructor-checkbox,
        button,
        input {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Enhanced hover effects */
        .instructor-item:hover {
            transform: translateY(-2px);
        }
        
        /* Glass morphism effect for better visual hierarchy */
        .backdrop-blur-xl {
            backdrop-filter: blur(20px);
        }
    </style>
</x-app-layout>