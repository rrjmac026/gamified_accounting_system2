<x-app-layout>
    <style>
        .star-display {
            display: inline-flex;
            gap: 4px;
        }

        .star-display svg {
            width: 32px;
            height: 32px;
            transition: all 0.2s ease;
        }

        .star-display .filled {
            fill: #FF92C2;
        }

        .star-display .empty {
            fill: #E0E0E0;
        }

        .dark .star-display .empty {
            fill: #666;
        }

        .rating-label {
            display: inline-block;
            margin-left: 12px;
            padding: 6px 16px;
            background: linear-gradient(135deg, #FF92C2 0%, #ff6fb5 100%);
            color: white;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(255, 146, 194, 0.3);
        }

        @media (max-width: 640px) {
            .star-display svg {
                width: 28px;
                height: 28px;
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
                            <h2 class="text-3xl font-bold text-white mb-2">Evaluation Details</h2>
                            <p class="text-pink-100 text-lg">Review submitted feedback and ratings</p>
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

                {{-- Content --}}
                <div class="p-8">
                    {{-- Information Grid --}}
                    <div class="bg-white rounded-xl shadow-md border border-[#FFC8FB]/30 p-6 mb-8">
                        <h3 class="text-lg font-bold text-[#FF92C2] mb-6 flex items-center">
                            <div class="w-8 h-8 bg-gradient-to-br from-[#FF92C2]/20 to-[#FFC8FB]/20 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-[#FF92C2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            Evaluation Information
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-gradient-to-br from-gray-50 to-white p-4 rounded-xl border border-gray-200">
                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Student</h4>
                                        <p class="text-gray-900 font-semibold">{{ $evaluation->student->user->name }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="bg-gradient-to-br from-gray-50 to-white p-4 rounded-xl border border-gray-200">
                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-gradient-to-br from-purple-100 to-purple-200 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Instructor</h4>
                                        <p class="text-gray-900 font-semibold">{{ $evaluation->instructor->user->name }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gradient-to-br from-gray-50 to-white p-4 rounded-xl border border-gray-200">
                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-gradient-to-br from-green-100 to-green-200 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Course</h4>
                                        <p class="text-gray-900 font-semibold">{{ $evaluation->course->course_name }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gradient-to-br from-gray-50 to-white p-4 rounded-xl border border-gray-200">
                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-gradient-to-br from-orange-100 to-orange-200 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                                        <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">Submitted At</h4>
                                        <p class="text-gray-900 font-semibold">{{ $evaluation->submitted_at->format('F j, Y') }}</p>
                                        <p class="text-sm text-gray-600">{{ $evaluation->submitted_at->format('g:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Evaluation Responses --}}
                    <div class="space-y-6 mb-8">
                        <div class="bg-white rounded-xl shadow-md border border-[#FFC8FB]/30 p-6">
                            <h3 class="text-lg font-bold text-[#FF92C2] flex items-center mb-6">
                                <div class="w-8 h-8 bg-gradient-to-br from-[#FF92C2]/20 to-[#FFC8FB]/20 rounded-lg flex items-center justify-center mr-3">
                                    <svg class="w-5 h-5 text-[#FF92C2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                </div>
                                Evaluation Responses
                            </h3>

                            <div class="space-y-4">
                                @php
                                    $ratingLabels = [
                                        1 => 'Poor',
                                        2 => 'Fair',
                                        3 => 'Good',
                                        4 => 'Very Good',
                                        5 => 'Excellent'
                                    ];
                                @endphp

                                @foreach($evaluation->responses as $criterion => $rating)
                                    <div class="group bg-gradient-to-br from-gray-50 to-white p-5 rounded-xl border border-gray-200 hover:border-[#FF92C2]/40 hover:shadow-md transition-all duration-300">
                                        <div class="flex items-center justify-between mb-3">
                                            <h4 class="font-semibold text-gray-700 flex items-center">
                                                <span class="w-6 h-6 bg-gradient-to-br from-[#FF92C2]/10 to-[#FFC8FB]/10 rounded-md flex items-center justify-center text-[#FF92C2] text-xs font-bold mr-3">
                                                    {{ $loop->iteration }}
                                                </span>
                                                {{ $criterion }}
                                            </h4>
                                        </div>
                                        
                                        <div class="flex items-center flex-wrap gap-2 ml-9">
                                            <div class="star-display">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg viewBox="0 0 24 24" class="{{ $i <= $rating ? 'filled' : 'empty' }}">
                                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="rating-label">{{ $ratingLabels[$rating] }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Comments Section --}}
                    <div class="bg-white rounded-xl shadow-md border border-[#FFC8FB]/30 p-6">
                        <h3 class="text-lg font-bold text-[#FF92C2] flex items-center mb-6">
                            <div class="w-8 h-8 bg-gradient-to-br from-[#FF92C2]/20 to-[#FFC8FB]/20 rounded-lg flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-[#FF92C2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                                </svg>
                            </div>
                            Additional Comments
                        </h3>
                        
                        <div class="bg-gradient-to-br from-gray-50 to-white p-6 rounded-xl border border-gray-200">
                            <p class="text-gray-700 whitespace-pre-line leading-relaxed">{{ $evaluation->comments }}</p>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex justify-end mt-8 pt-6">
                        <a href="{{ route('evaluations.index') }}" 
                           class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-[#FF92C2] to-[#ff6fb5] hover:from-[#ff6fb5] hover:to-[#FF92C2] text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Evaluations
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>