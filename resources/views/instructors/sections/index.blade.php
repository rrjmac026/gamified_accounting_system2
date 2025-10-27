<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Enhanced Search Bar --}}
            <div class="mb-8">
                <div class="bg-gradient-to-r from-white/90 to-[#FFF0FA]/90 backdrop-blur-sm rounded-2xl shadow-xl border border-[#FFC8FB]/30 p-6 relative overflow-hidden">
                    {{-- Decorative gradient background --}}
                    <div class="absolute inset-0 bg-gradient-to-br from-[#FF92C2]/5 via-transparent to-[#FFC8FB]/10 pointer-events-none"></div>
                    
                    <div class="relative">
                        <div class="flex items-center mb-6">
                            <div class="flex items-center justify-center w-10 h-10 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] rounded-full mr-4 shadow-md">
                                <i class="fas fa-search text-white"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">Find Your Sections</h3>
                                <p class="text-sm text-gray-600">Search by section code or name</p>
                            </div>
                        </div>

                        <form action="{{ route('instructors.sections.index') }}" method="GET" class="space-y-4">
                            <div class="relative group">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none transition-colors duration-200">
                                    <i class="fas fa-search text-gray-400 group-focus-within:text-[#FF92C2]"></i>
                                </div>
                                <input type="text" 
                                       name="search" 
                                       value="{{ request('search') }}"
                                       placeholder="Type section code (e.g., BSCS-1A) or section name..." 
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
                                        Search Sections
                                    </span>
                                </button>
                                
                                @if(request('search'))
                                    <a href="{{ route('instructors.sections.index') }}" 
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
                                                <i class="fas fa-chalkboard mr-1"></i>
                                                {{ $sections->total() ?? 0 }} sections found
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </form>
                    </div>
                </div>
            </div>

            <div class="bg-[#FFF0FA] overflow-hidden shadow-2xl sm:rounded-2xl border border-[#FFC8FB]/30">
                <div class="p-6 text-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse($sections as $section)
                            <div class="group bg-white rounded-xl shadow-md border border-[#FFC8FB]/30 overflow-hidden hover:shadow-xl hover:border-[#FF92C2]/50 transition-all duration-300 transform hover:-translate-y-1">
                                <div class="p-6">
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-xl font-bold text-[#595758] group-hover:text-[#FF92C2] transition-colors duration-200">
                                            {{ $section->name }}
                                        </h3>
                                        <span class="px-3 py-1 text-xs font-semibold bg-gradient-to-r from-[#FF92C2]/20 to-[#FFC8FB]/20 text-[#FF92C2] rounded-full border border-[#FF92C2]/30">
                                            {{ $section->section_code }}
                                        </span>
                                    </div>
                                    
                                    <div class="space-y-3 mb-6">
                                        <div class="flex items-center text-gray-600 bg-gray-50 rounded-lg px-3 py-2">
                                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                                <i class="fas fa-users text-blue-600 text-sm"></i>
                                            </div>
                                            <span class="font-medium">{{ $section->students->count() }} Students</span>
                                        </div>
                                    </div>

                                    <div class="flex justify-end">
                                        <a href="{{ route('instructors.sections.show', $section->id) }}" 
                                           class="group/btn inline-flex items-center px-5 py-3 bg-gradient-to-r from-[#FF92C2] to-[#ff6fb5] hover:from-[#ff6fb5] hover:to-[#FF92C2] text-white rounded-xl transition-all duration-300 font-semibold shadow-md hover:shadow-lg transform hover:scale-105">
                                            <i class="fas fa-eye mr-2 group-hover/btn:animate-pulse"></i>
                                            View Details
                                        </a>
                                    </div>
                                </div>
                                
                                {{-- Decorative bottom border --}}
                                <div class="h-1 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300"></div>
                            </div>
                        @empty
                            <div class="col-span-full">
                                <div class="text-center py-16">
                                    <div class="relative">
                                        <div class="w-24 h-24 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                                            <i class="fas fa-chalkboard text-4xl text-gray-400"></i>
                                        </div>
                                        
                                        @if(request('search'))
                                            <h3 class="text-xl font-bold text-gray-900 mb-3">
                                                No sections found matching your search
                                            </h3>
                                            <p class="text-gray-600 mb-4 max-w-md mx-auto">
                                                We couldn't find any sections matching <strong>"{{ request('search') }}"</strong>. 
                                                Try adjusting your search term or browse all sections.
                                            </p>
                                            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                                                <a href="{{ route('instructors.sections.index') }}" 
                                                   class="inline-flex items-center px-6 py-3 bg-[#FF92C2] hover:bg-[#ff6fb5] text-white rounded-xl transition-colors duration-200 font-medium">
                                                    <i class="fas fa-list mr-2"></i>
                                                    View All Sections
                                                </a>
                                                <button onclick="document.querySelector('input[name=search]').focus()" 
                                                        class="inline-flex items-center px-6 py-3 bg-white hover:bg-gray-50 text-gray-700 border-2 border-gray-200 hover:border-gray-300 rounded-xl transition-colors duration-200 font-medium">
                                                    <i class="fas fa-search mr-2"></i>
                                                    Try Different Search
                                                </button>
                                            </div>
                                        @else
                                            <h3 class="text-xl font-bold text-gray-900 mb-3">
                                                No Sections Assigned
                                            </h3>
                                            <p class="text-gray-600 max-w-md mx-auto">
                                                You haven't been assigned to any sections yet. 
                                                Contact your administrator to get sections assigned to you.
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>