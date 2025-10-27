<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Enhanced Search Bar --}}
            <div class="mb-8">
                <div class="bg-gradient-to-r from-white/90 to-[#FFF0FA]/90 backdrop-blur-sm rounded-2xl shadow-xl border border-[#FFC8FB]/30 p-6 relative overflow-hidden">
                    {{-- Decorative gradient background --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-[#FF92C2]/5 via-transparent to-[#FFC8FB]/10 pointer-events-none"></div>
                    
                    <div class="relative">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center">
                                <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-full mr-4 shadow-md">
                                    <i class="fas fa-search text-white"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800">Find Evaluations</h3>
                                    <p class="text-sm text-gray-600">Search by student, instructor, or course name</p>
                                </div>
                            </div>
                            <a href="{{ route('evaluations.create') }}" 
                               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#FF92C2] to-[#ff6fb5] text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                New Evaluation
                            </a>
                        </div>

                        <form action="{{ route('evaluations.index') }}" method="GET" class="space-y-4">
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors duration-200">
                                    <i class="fas fa-search text-gray-400 group-focus-within:text-[#FF92C2]"></i>
                                </div>
                                <input type="text" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       placeholder="Type student name, instructor, or course name..." 
                                       class="w-full pl-11 pr-12 py-4 text-lg border-2 border-gray-200 rounded-xl bg-white/80 focus:bg-white focus:border-[#FF92C2] focus:ring-4 focus:ring-[#FF92C2]/10 focus:outline-none transition-all duration-300 text-gray-700 placeholder-gray-400 shadow-sm">
                                
                                {{-- Search hint icon --}}
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center">
                                    <div class="text-xs text-gray-400 bg-gray-100 px-2 py-1 rounded-md">
                                        <i class="fas fa-keyboard mr-1"></i>
                                        Enter
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex flex-col sm:flex-row gap-3 justify-end">
                                <button type="submit" 
                                        class="group relative px-8 py-3 bg-gradient-to-r from-[#FF92C2] to-[#ff6fb5] text-white rounded-xl font-semibold shadow-lg hover:shadow-xl focus:outline-none focus:ring-4 focus:ring-[#FF92C2]/30 transition-all duration-300 transform hover:-translate-y-1 hover:scale-105">
                                    <span class="flex items-center justify-center">
                                        <i class="fas fa-search mr-3 group-hover:animate-pulse"></i>
                                        Search Evaluations
                                    </span>
                                </button>
                                
                                @if(request('search'))
                                    <a href="{{ route('evaluations.index') }}" 
                                       class="px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 rounded-xl transition-all duration-200 font-medium border-2 border-gray-200 hover:border-gray-300 flex items-center justify-center shadow-sm hover:shadow-md">
                                        <i class="fas fa-times mr-2"></i>
                                        Clear Search
                                    </a>
                                @endif
                            </div>

                            {{-- Search Results Summary --}}
                            @if(request('search'))
                                <div class="bg-white/90 border-2 border-blue-200 rounded-xl px-5 py-4 backdrop-blur-sm">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-info-circle text-blue-600 text-sm"></i>
                                            </div>
                                            <div>
                                                <span class="text-sm font-medium text-blue-800">
                                                    Search Results for: 
                                                </span>
                                                <span class="text-sm font-bold text-blue-900 bg-blue-100 px-2 py-1 rounded-md ml-1">
                                                    "{{ request('search') }}"
                                                </span>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="text-xs font-semibold text-blue-600 bg-blue-50 px-3 py-1 rounded-full border border-blue-200">
                                                <i class="fas fa-clipboard-list mr-1"></i>
                                                {{ $evaluations->total() ?? 0 }} evaluations found
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            {{-- Success Message --}}
            @if(session('success'))
                <div class="mb-6 p-4 bg-gradient-to-r from-green-50 to-green-100 border-l-4 border-green-400 rounded-xl shadow-md">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-green-400 mr-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-green-800 font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <div class="bg-[#FFF0FA] overflow-hidden shadow-2xl sm:rounded-2xl border border-[#FFC8FB]/30">
                <div class="p-6 text-gray-700">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-3xl font-bold text-[#FF92C2]">Course Evaluations</h2>
                            <p class="text-gray-600 mt-1">Manage and view all course evaluation submissions</p>
                        </div>
                        @if($evaluations->count() > 0 && !request('search'))
                            <div class="flex items-center text-sm text-gray-600 bg-white/80 px-3 py-2 rounded-lg">
                                <i class="fas fa-clipboard-list mr-2"></i>
                                {{ $evaluations->total() }} total evaluations
                            </div>
                        @endif
                    </div>

                    {{-- Statistics Cards --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="group bg-white rounded-xl shadow-md border border-[#FFC8FB]/30 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 mb-1">Total Evaluations</p>
                                        <p class="text-3xl font-bold text-[#FF92C2]">{{ $evaluations->total() }}</p>
                                    </div>
                                    <div class="w-14 h-14 bg-gradient-to-br from-[#FF92C2]/20 to-[#FFC8FB]/20 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-7 h-7 text-[#FF92C2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="h-1 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                        </div>

                        <div class="group bg-white rounded-xl shadow-md border border-[#FFC8FB]/30 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 mb-1">This Month</p>
                                        <p class="text-3xl font-bold text-[#FF92C2]">{{ $evaluations->where('submitted_at', '>=', now()->startOfMonth())->count() }}</p>
                                    </div>
                                    <div class="w-14 h-14 bg-gradient-to-br from-[#FF92C2]/20 to-[#FFC8FB]/20 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-7 h-7 text-[#FF92C2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="h-1 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                        </div>

                        <div class="group bg-white rounded-xl shadow-md border border-[#FFC8FB]/30 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                            <div class="p-6">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500 mb-1">Average Rating</p>
                                        <p class="text-3xl font-bold text-[#FF92C2]">4.2</p>
                                    </div>
                                    <div class="w-14 h-14 bg-gradient-to-br from-[#FF92C2]/20 to-[#FFC8FB]/20 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-7 h-7 text-[#FF92C2]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            <div class="h-1 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                        </div>
                    </div>

                    {{-- Table Section --}}
                    <div class="bg-white rounded-xl shadow-md border border-[#FFC8FB]/30 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-[#FFC8FB]/50">
                                <thead class="bg-gradient-to-r from-[#FFC8FB]/80 to-[#FFC8FB]/60">
                                    <tr>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#595758] uppercase tracking-wider">Student</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#595758] uppercase tracking-wider">Instructor</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#595758] uppercase tracking-wider">Course</th>
                                        <th class="px-6 py-4 text-left text-xs font-semibold text-[#595758] uppercase tracking-wider">Date Submitted</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-[#FFC8FB]/30">
                                    @forelse($evaluations as $evaluation)
                                        <tr class="hover:bg-[#FFF6FD] transition-colors duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="w-10 h-10 bg-gradient-to-br from-[#FF92C2] to-[#ff6fb5] rounded-full flex items-center justify-center text-white font-semibold text-sm shadow-sm">
                                                        {{ substr($evaluation->student->user->name, 0, 2) }}
                                                    </div>
                                                    <div class="ml-3">
                                                        <p class="text-sm font-medium text-gray-900">{{ $evaluation->student->user->name }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <p class="text-sm text-gray-900">{{ $evaluation->instructor->user->name }}</p>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex px-3 py-1 text-xs font-semibold bg-gradient-to-r from-[#FF92C2]/20 to-[#FFC8FB]/20 text-[#FF92C2] rounded-full border border-[#FF92C2]/30">
                                                    {{ $evaluation->course->course_name }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <div class="flex items-center">
                                                    <svg class="w-4 h-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    {{ $evaluation->submitted_at->format('M d, Y H:i') }}
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-16 text-center">
                                                <div class="flex flex-col items-center">
                                                    <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                                                        <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                        </svg>
                                                    </div>
                                                    @if(request('search'))
                                                        <h3 class="text-xl font-bold text-gray-900 mb-3">
                                                            No evaluations found matching your search
                                                        </h3>
                                                        <p class="text-gray-600 mb-4 max-w-md mx-auto">
                                                            We couldn't find any evaluations matching <strong>"{{ request('search') }}"</strong>. 
                                                            Try adjusting your search term or browse all evaluations.
                                                        </p>
                                                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                                            <a href="{{ route('evaluations.index') }}" 
                                                               class="inline-flex items-center px-6 py-3 bg-[#FF92C2] hover:bg-[#ff6fb5] text-white rounded-xl transition-colors duration-200 font-medium">
                                                                <i class="fas fa-list mr-2"></i>
                                                                View All Evaluations
                                                            </a>
                                                            <button onclick="document.querySelector('input[name=search]').focus()" 
                                                                    class="inline-flex items-center px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 border-2 border-gray-200 hover:border-gray-300 rounded-xl transition-colors duration-200 font-medium">
                                                                <i class="fas fa-search mr-2"></i>
                                                                Try Different Search
                                                            </button>
                                                        </div>
                                                    @else
                                                        <h3 class="text-xl font-bold text-gray-900 mb-3">No evaluations found</h3>
                                                        <p class="text-gray-600 mb-4 max-w-md mx-auto">Get started by creating your first evaluation</p>
                                                        <a href="{{ route('evaluations.create') }}" 
                                                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-[#FF92C2] to-[#ff6fb5] text-white rounded-xl hover:shadow-lg transition-all duration-300 font-medium transform hover:scale-105">
                                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                            </svg>
                                                            Create Evaluation
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Pagination --}}
                    @if($evaluations->hasPages())
                        <div class="mt-8 pt-6 border-t border-[#FFC8FB]/30">
                            {{ $evaluations->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>