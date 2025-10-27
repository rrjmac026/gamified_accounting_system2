<x-app-layout>
    <style>
        .star-rating {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .star-rating input[type="radio"] {
            display: none;
        }

        .star {
            cursor: pointer;
            width: 40px;
            height: 40px;
            transition: all 0.2s ease;
            position: relative;
        }

        .star svg {
            width: 100%;
            height: 100%;
            fill: #E0E0E0;
            transition: fill 0.2s ease, transform 0.2s ease;
        }

        .star:hover svg,
        .star.hovered svg {
            fill: #FFD700;
            transform: scale(1.15);
        }

        .star.selected svg {
            fill: #FF92C2;
        }

        .star:active {
            transform: scale(0.95);
        }

        .rating-text {
            color: #666;
            font-size: 14px;
            margin-left: 12px;
            min-width: 100px;
            font-weight: 500;
        }

        .dark .rating-text {
            color: #FFC8FB;
        }

        .rating-descriptions {
            display: flex;
            justify-content: space-between;
            margin-top: 8px;
            padding: 0 4px;
            max-width: 240px;
        }

        .rating-desc {
            font-size: 11px;
            color: #999;
            text-align: center;
            width: 40px;
        }

        .dark .rating-desc {
            color: #FFC8FB;
            opacity: 0.7;
        }

        @media (max-width: 640px) {
            .star {
                width: 36px;
                height: 36px;
            }

            .rating-desc {
                font-size: 10px;
                width: 36px;
            }

            .rating-text {
                font-size: 13px;
                min-width: 80px;
            }
        }
    </style>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Back Button --}}
            <div class="mb-6">
                <a href="{{ route('evaluations.index') }}" 
                   class="inline-flex items-center text-gray-600 hover:text-[#FF92C2] transition-colors duration-200 group">
                    <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span class="font-medium">Back to Evaluations</span>
                </a>
            </div>

            <div class="bg-[#FFF0FA] overflow-hidden shadow-2xl sm:rounded-2xl border border-[#FFC8FB]/30">
                {{-- Header Section --}}
                <div class="bg-gradient-to-r from-[#FF92C2] to-[#ff6fb5] p-8 relative overflow-hidden">
                    {{-- Decorative elements --}}
                    <div class="absolute top-0 right-0 w-64 h-64 bg-white/5 rounded-full -mr-32 -mt-32"></div>
                    <div class="absolute bottom-0 left-0 w-48 h-48 bg-white/5 rounded-full -ml-24 -mb-24"></div>
                    
                    <div class="relative flex items-center justify-between">
                        <div>
                            <h2 class="text-3xl font-bold text-white mb-2">Course Evaluation</h2>
                            <p class="text-pink-100 text-lg">Share your feedback to help improve the learning experience</p>
                        </div>
                        <div class="hidden md:block">
                            <div class="w-20 h-20 bg-white/10 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Content --}}
                <div class="p-8">
                    {{-- Error Messages --}}
                    @if($errors->any())
                        <div class="mb-8 p-5 bg-gradient-to-r from-red-50 to-red-100 border-l-4 border-red-400 rounded-xl shadow-sm">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-base font-semibold text-red-800 mb-2">Please correct the following errors:</h3>
                                    <ul class="text-sm text-red-700 space-y-1">
                                        @foreach($errors->all() as $error)
                                            <li class="flex items-center">
                                                <span class="w-1.5 h-1.5 bg-red-400 rounded-full mr-2"></span>
                                                {{ $error }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('evaluations.store') }}" method="POST" class="space-y-8">
                        @csrf

                        {{-- Course and Instructor Selection --}}
                        <div class="bg-white rounded-xl shadow-md border border-[#FFC8FB]/30 p-6">
                            <h3 class="text-lg font-bold text-[#FF92C2] mb-6 flex items-center">
                                <div class="w-8 h-8 bg-gradient-to-br from-[#FF92C2]/20 to-[#FFC8FB]/20 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-[#FF92C2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                </div>
                                Course & Instructor Details
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">
                                        Select Instructor <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <select name="instructor_id" 
                                                class="w-full rounded-xl bg-white border-2 border-gray-200 focus:border-[#FF92C2] focus:ring-4 focus:ring-[#FF92C2]/10 transition-all duration-200 text-gray-900 py-3 px-4 appearance-none" 
                                                required>
                                            <option value="">Choose an instructor</option>
                                            @forelse($instructors as $instructor)
                                                <option value="{{ $instructor->id }}" {{ old('instructor_id') == $instructor->id ? 'selected' : '' }}>
                                                    {{ $instructor->user->name }}
                                                </option>
                                            @empty
                                                <option value="" disabled>No instructors available</option>
                                            @endforelse
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                            <svg class="w-5 h-5 text-[#FF92C2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    @error('instructor_id')
                                        <p class="text-sm text-red-600 flex items-center mt-1">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                <div class="space-y-2">
                                    <label class="block text-sm font-semibold text-gray-700">
                                        Select Course <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <select name="course_id" 
                                                class="w-full rounded-xl bg-white border-2 border-gray-200 focus:border-[#FF92C2] focus:ring-4 focus:ring-[#FF92C2]/10 transition-all duration-200 text-gray-900 py-3 px-4 appearance-none" 
                                                required>
                                            <option value="">Choose a course</option>
                                            @forelse($courses as $course)
                                                <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>
                                                    {{ $course->course_name }}
                                                </option>
                                            @empty
                                                <option value="" disabled>No courses available</option>
                                            @endforelse
                                        </select>
                                        <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none">
                                            <svg class="w-5 h-5 text-[#FF92C2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    @error('course_id')
                                        <p class="text-sm text-red-600 flex items-center mt-1">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        {{-- Evaluation Criteria --}}
                        <div class="space-y-6">
                            <div class="bg-white rounded-xl shadow-md border border-[#FFC8FB]/30 p-6">
                                <h3 class="text-lg font-bold text-[#FF92C2] flex items-center mb-2">
                                    <div class="w-8 h-8 bg-gradient-to-br from-[#FF92C2]/20 to-[#FFC8FB]/20 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-[#FF92C2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                    Evaluation Criteria
                                </h3>
                                <p class="text-sm text-gray-600 ml-11">Please rate each aspect by clicking the stars (1 = Poor, 5 = Excellent)</p>
                            </div>

                            @foreach($criteria as $key => $criterion)
                                <div class="group bg-white rounded-xl shadow-md border border-[#FFC8FB]/30 p-6 hover:shadow-lg hover:border-[#FF92C2]/40 transition-all duration-300">
                                    <label class="block text-base font-semibold text-gray-700 mb-4">
                                        <div class="flex items-center">
                                            <span class="w-6 h-6 bg-gradient-to-br from-[#FF92C2]/10 to-[#FFC8FB]/10 rounded-md flex items-center justify-center text-[#FF92C2] text-sm font-bold mr-3">
                                                {{ $loop->iteration }}
                                            </span>
                                            {{ $criterion }}
                                        </div>
                                    </label>
                                    
                                    <div class="star-rating" data-rating-name="criterion_{{ $key }}">
                                        @for($i = 1; $i <= 5; $i++)
                                            <input type="radio" 
                                                   name="responses[{{ $key }}]" 
                                                   value="{{ $i }}" 
                                                   id="criterion_{{ $key }}_{{ $i }}"
                                                   {{ old("responses.{$key}") == $i ? 'checked' : '' }}
                                                   required>
                                            <label for="criterion_{{ $key }}_{{ $i }}" class="star" data-value="{{ $i }}">
                                                <svg viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                            </label>
                                        @endfor
                                        <span class="rating-text">Not rated</span>
                                    </div>

                                    <div class="rating-descriptions">
                                        <span class="rating-desc">Poor</span>
                                        <span class="rating-desc">Fair</span>
                                        <span class="rating-desc">Good</span>
                                        <span class="rating-desc">Very Good</span>
                                        <span class="rating-desc">Excellent</span>
                                    </div>

                                    @error("responses.{$key}")
                                        <p class="mt-3 text-sm text-red-600 flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>

                        {{-- Comments Section --}}
                        <div class="bg-white rounded-xl shadow-md border border-[#FFC8FB]/30 p-6">
                            <div class="space-y-3">
                                <label class="block text-base font-semibold text-gray-700 flex items-center">
                                    <div class="w-8 h-8 bg-gradient-to-br from-[#FF92C2]/20 to-[#FFC8FB]/20 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-[#FF92C2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                        </svg>
                                    </div>
                                    Additional Comments <span class="text-red-500 ml-1">*</span>
                                </label>
                                <p class="text-sm text-gray-600 ml-11">Share any specific feedback, suggestions, or experiences</p>
                                <textarea name="comments" 
                                          rows="6" 
                                          class="w-full rounded-xl bg-white border-2 border-gray-200 focus:border-[#FF92C2] focus:ring-4 focus:ring-[#FF92C2]/10 transition-all duration-200 text-gray-900 p-4" 
                                          placeholder="Please share your detailed feedback about the course and instructor..."
                                          required>{{ old('comments') }}</textarea>
                                @error('comments')
                                    <p class="text-sm text-red-600 flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 pt-6">
                            <a href="{{ route('evaluations.index') }}" 
                               class="inline-flex items-center justify-center px-8 py-3 bg-white hover:bg-gray-50 text-gray-700 font-semibold rounded-xl border-2 border-gray-200 hover:border-gray-300 transition-all duration-200 shadow-sm hover:shadow-md">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                Cancel
                            </a>
                            <button type="submit" 
                                    class="inline-flex items-center justify-center px-8 py-3 bg-gradient-to-r from-[#FF92C2] to-[#ff6fb5] hover:from-[#ff6fb5] hover:to-[#FF92C2] text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                Submit Evaluation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ratingTexts = {
                1: 'Poor',
                2: 'Fair',
                3: 'Good',
                4: 'Very Good',
                5: 'Excellent'
            };

            // Handle star rating interactions for each rating container
            document.querySelectorAll('.star-rating').forEach(container => {
                const stars = container.querySelectorAll('.star');
                const ratingText = container.querySelector('.rating-text');
                const inputs = container.querySelectorAll('input[type="radio"]');
                let currentRating = 0;

                // Check if there's a pre-selected value (from old input)
                inputs.forEach((input, index) => {
                    if (input.checked) {
                        currentRating = index + 1;
                        ratingText.textContent = ratingTexts[currentRating];
                        updateStarsSelected(currentRating, stars);
                    }
                });

                stars.forEach((star, index) => {
                    // Hover effect
                    star.addEventListener('mouseenter', () => {
                        updateStars(index + 1, stars);
                    });

                    // Click to select
                    star.addEventListener('click', () => {
                        currentRating = index + 1;
                        const input = star.previousElementSibling;
                        input.checked = true;
                        ratingText.textContent = ratingTexts[currentRating];
                        updateStarsSelected(currentRating, stars);
                    });
                });

                // Reset on mouse leave if no rating selected
                container.addEventListener('mouseleave', () => {
                    if (currentRating > 0) {
                        updateStarsSelected(currentRating, stars);
                    } else {
                        resetStars(stars);
                    }
                });
            });

            function updateStars(rating, stars) {
                stars.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.add('hovered');
                        star.classList.remove('selected');
                    } else {
                        star.classList.remove('hovered');
                    }
                });
            }

            function updateStarsSelected(rating, stars) {
                stars.forEach((star, index) => {
                    star.classList.remove('hovered');
                    if (index < rating) {
                        star.classList.add('selected');
                    } else {
                        star.classList.remove('selected');
                    }
                });
            }

            function resetStars(stars) {
                stars.forEach(star => {
                    star.classList.remove('hovered');
                    star.classList.remove('selected');
                });
            }
        });
    </script>
</x-app-layout>